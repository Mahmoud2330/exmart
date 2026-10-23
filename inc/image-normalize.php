<?php
/**
 * Product image normaliser (wp-admin → Tools → Normalize Images).
 *
 * Gives every product photo the same canvas (square, product centred, equal margins) and
 * removes flat studio backgrounds. Nothing is overwritten: each processed image is saved as a
 * NEW file next to the original and the attachment is pointed at it, so "Restore originals"
 * can undo the whole run. Images with a busy background (shadows, scenery, gradients) are
 * skipped and reported rather than mangled — those need a manual cut-out.
 *
 * Runs in the browser in small batches, so it needs no SSH / WP-CLI.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

const EXMART_IMG_META_ORIGINAL = '_exmart_img_original';

/**
 * Attachment IDs used by products: main image, gallery, and variation images.
 *
 * @return int[]
 */
function exmart_img_collect_ids() {
	global $wpdb;
	$ids = array();

	$thumb = $wpdb->get_col(
		"SELECT pm.meta_value FROM {$wpdb->postmeta} pm
		 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		 WHERE pm.meta_key = '_thumbnail_id' AND p.post_type IN ( 'product', 'product_variation' ) AND p.post_status <> 'trash'"
	);
	foreach ( $thumb as $v ) {
		$ids[] = absint( $v );
	}

	$gallery = $wpdb->get_col(
		"SELECT pm.meta_value FROM {$wpdb->postmeta} pm
		 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		 WHERE pm.meta_key = '_product_image_gallery' AND p.post_type = 'product' AND p.post_status <> 'trash'"
	);
	foreach ( $gallery as $csv ) {
		foreach ( array_filter( array_map( 'absint', explode( ',', (string) $csv ) ) ) as $v ) {
			$ids[] = $v;
		}
	}

	$ids = array_values( array_unique( array_filter( $ids ) ) );
	sort( $ids );
	return $ids;
}

/**
 * Load an image file as a GD truecolor resource.
 *
 * @return \GdImage|resource|false
 */
function exmart_img_load( $file ) {
	$info = @getimagesize( $file );
	if ( ! $info ) {
		return false;
	}
	switch ( $info[2] ) {
		case IMAGETYPE_PNG:
			$im = @imagecreatefrompng( $file );
			break;
		case IMAGETYPE_JPEG:
			$im = @imagecreatefromjpeg( $file );
			break;
		case IMAGETYPE_WEBP:
			$im = function_exists( 'imagecreatefromwebp' ) ? @imagecreatefromwebp( $file ) : false;
			break;
		case IMAGETYPE_GIF:
			$im = @imagecreatefromgif( $file );
			break;
		default:
			$im = false;
	}
	if ( $im && ! imageistruecolor( $im ) ) {
		imagepalettetotruecolor( $im );
	}
	return $im;
}

/**
 * Normalise one image file.
 *
 * @param string $src  Source path.
 * @param string $dest Destination PNG path ('' = analyse only, write nothing).
 * @param array  $opts size (px), padding (0–0.3 fraction per side), remove_bg (bool), tolerance (0–255).
 * @return array{status:string,note:string}  status: ok | skipped | error
 */
function exmart_img_normalize( $src, $dest, $opts = array() ) {
	$o = wp_parse_args(
		$opts,
		array(
			'size'      => 1200,
			'padding'   => 0.07,
			'remove_bg' => true,
			'tolerance' => 26,
		)
	);
	$size = max( 300, min( 3000, (int) $o['size'] ) );
	$pad  = max( 0, min( 0.3, (float) $o['padding'] ) );
	$tol  = max( 4, min( 90, (int) $o['tolerance'] ) );

	if ( ! function_exists( 'imagecreatetruecolor' ) ) {
		return array( 'status' => 'error', 'note' => 'PHP GD is not available' );
	}
	$im = exmart_img_load( $src );
	if ( ! $im ) {
		return array( 'status' => 'error', 'note' => 'cannot read file' );
	}

	// Work at no more than 1400px on the long side (keeps PHP fast and memory low).
	$w = imagesx( $im );
	$h = imagesy( $im );
	$cap = 1400;
	if ( max( $w, $h ) > $cap ) {
		$k  = $cap / max( $w, $h );
		$nw = max( 1, (int) round( $w * $k ) );
		$nh = max( 1, (int) round( $h * $k ) );
		$sm = imagecreatetruecolor( $nw, $nh );
		imagealphablending( $sm, false );
		imagesavealpha( $sm, true );
		imagefill( $sm, 0, 0, imagecolorallocatealpha( $sm, 255, 255, 255, 127 ) );
		imagecopyresampled( $sm, $im, 0, 0, 0, 0, $nw, $nh, $w, $h );
		imagedestroy( $im );
		$im = $sm;
		$w  = $nw;
		$h  = $nh;
	}
	imagealphablending( $im, false );
	imagesavealpha( $im, true );

	// Background colour = average of the border pixels (transparent pixels count as background).
	$r = 0; $g = 0; $b = 0; $n = 0;
	$edge = array();
	for ( $x = 0; $x < $w; $x++ ) {
		$edge[] = array( $x, 0 );
		$edge[] = array( $x, $h - 1 );
	}
	for ( $y = 1; $y < $h - 1; $y++ ) {
		$edge[] = array( 0, $y );
		$edge[] = array( $w - 1, $y );
	}
	foreach ( $edge as $p ) {
		$c = imagecolorat( $im, $p[0], $p[1] );
		if ( ( ( $c >> 24 ) & 0x7F ) > 100 ) {
			continue;
		}
		$r += ( $c >> 16 ) & 0xFF;
		$g += ( $c >> 8 ) & 0xFF;
		$b += $c & 0xFF;
		$n++;
	}
	$has_alpha_bg = $n < count( $edge ) * 0.5;
	if ( $n ) {
		$r = (int) round( $r / $n );
		$g = (int) round( $g / $n );
		$b = (int) round( $b / $n );
	} else {
		$r = $g = $b = 255;
	}

	$is_bg = function ( $c ) use ( $r, $g, $b, $tol ) {
		if ( ( ( $c >> 24 ) & 0x7F ) > 100 ) {
			return true;
		}
		return max(
			abs( ( ( $c >> 16 ) & 0xFF ) - $r ),
			abs( ( ( $c >> 8 ) & 0xFF ) - $g ),
			abs( ( $c & 0xFF ) - $b )
		) <= $tol;
	};

	// Is the border uniform enough to cut out safely?
	$good = 0;
	foreach ( $edge as $p ) {
		if ( $is_bg( imagecolorat( $im, $p[0], $p[1] ) ) ) {
			$good++;
		}
	}
	$uniform = $good / max( 1, count( $edge ) );
	if ( $uniform < 0.9 ) {
		imagedestroy( $im );
		return array( 'status' => 'skipped', 'note' => sprintf( 'busy background (%d%% of the border is plain)', round( $uniform * 100 ) ) );
	}

	// Flood-fill the background inward from the border, so white INSIDE the product is kept.
	$vis   = str_repeat( "\0", $w * $h );
	$stack = array();
	foreach ( $edge as $p ) {
		$i = $p[1] * $w + $p[0];
		if ( "\0" === $vis[ $i ] && $is_bg( imagecolorat( $im, $p[0], $p[1] ) ) ) {
			$vis[ $i ] = "\1";
			$stack[]   = $i;
		}
	}
	while ( $stack ) {
		$i = array_pop( $stack );
		$x = $i % $w;
		$y = intdiv( $i, $w );
		if ( $x > 0 && "\0" === $vis[ $i - 1 ] && $is_bg( imagecolorat( $im, $x - 1, $y ) ) ) { $vis[ $i - 1 ] = "\1"; $stack[] = $i - 1; }
		if ( $x < $w - 1 && "\0" === $vis[ $i + 1 ] && $is_bg( imagecolorat( $im, $x + 1, $y ) ) ) { $vis[ $i + 1 ] = "\1"; $stack[] = $i + 1; }
		if ( $y > 0 && "\0" === $vis[ $i - $w ] && $is_bg( imagecolorat( $im, $x, $y - 1 ) ) ) { $vis[ $i - $w ] = "\1"; $stack[] = $i - $w; }
		if ( $y < $h - 1 && "\0" === $vis[ $i + $w ] && $is_bg( imagecolorat( $im, $x, $y + 1 ) ) ) { $vis[ $i + $w ] = "\1"; $stack[] = $i + $w; }
	}

	// Bounding box of everything that is not background.
	$top = $h; $bottom = -1; $left = $w; $right = -1;
	for ( $y = 0; $y < $h; $y++ ) {
		$row = $y * $w;
		for ( $x = 0; $x < $w; $x++ ) {
			if ( "\0" === $vis[ $row + $x ] ) {
				if ( $y < $top ) { $top = $y; }
				if ( $y > $bottom ) { $bottom = $y; }
				if ( $x < $left ) { $left = $x; }
				if ( $x > $right ) { $right = $x; }
			}
		}
	}
	if ( $bottom < 0 || ( $right - $left ) < 8 || ( $bottom - $top ) < 8 ) {
		imagedestroy( $im );
		return array( 'status' => 'skipped', 'note' => 'no product found (image is all background)' );
	}
	$cw = $right - $left + 1;
	$ch = $bottom - $top + 1;
	$coverage = ( $cw * $ch ) / ( $w * $h );

	if ( '' === $dest ) { // analysis only
		imagedestroy( $im );
		return array(
			'status' => 'ok',
			'note'   => sprintf( 'would crop to %d×%d (product fills %d%% of the frame) → %d×%d', $cw, $ch, round( $coverage * 100 ), $size, $size ),
		);
	}

	// Cut the product out (soft 1px edge on the boundary) onto a transparent square canvas.
	$inner = (int) floor( $size * ( 1 - 2 * $pad ) );
	$scale = min( $inner / $cw, $inner / $ch );
	$dw    = max( 1, (int) round( $cw * $scale ) );
	$dh    = max( 1, (int) round( $ch * $scale ) );

	$crop = imagecreatetruecolor( $cw, $ch );
	imagealphablending( $crop, false );
	imagesavealpha( $crop, true );
	imagefill( $crop, 0, 0, imagecolorallocatealpha( $crop, 255, 255, 255, 127 ) );
	imagecopy( $crop, $im, 0, 0, $left, $top, $cw, $ch );

	if ( $o['remove_bg'] ) {
		$clear = imagecolorallocatealpha( $crop, 255, 255, 255, 127 );
		for ( $y = 0; $y < $ch; $y++ ) {
			for ( $x = 0; $x < $cw; $x++ ) {
				$i = ( $y + $top ) * $w + $x + $left;
				if ( "\1" === $vis[ $i ] ) {
					imagesetpixel( $crop, $x, $y, $clear );
				}
			}
		}
	}

	$canvas = imagecreatetruecolor( $size, $size );
	imagealphablending( $canvas, false );
	imagesavealpha( $canvas, true );
	// Without background removal keep a white canvas instead of transparent.
	$fill = $o['remove_bg'] ? imagecolorallocatealpha( $canvas, 255, 255, 255, 127 ) : imagecolorallocate( $canvas, 255, 255, 255 );
	imagefill( $canvas, 0, 0, $fill );
	imagealphablending( $canvas, true );
	imagecopyresampled( $canvas, $crop, (int) floor( ( $size - $dw ) / 2 ), (int) floor( ( $size - $dh ) / 2 ), 0, 0, $dw, $dh, $cw, $ch );
	imagealphablending( $canvas, false );

	if ( ! wp_mkdir_p( dirname( $dest ) ) ) {
		return array( 'status' => 'error', 'note' => 'cannot write to uploads folder' );
	}
	$ok = imagepng( $canvas, $dest, 6 );
	imagedestroy( $im );
	imagedestroy( $crop );
	imagedestroy( $canvas );
	return $ok ? array( 'status' => 'ok', 'note' => sprintf( 'cropped %d×%d → %d×%d%s', $cw, $ch, $size, $size, $has_alpha_bg ? ' (was transparent)' : '' ) ) : array( 'status' => 'error', 'note' => 'could not save' );
}

/**
 * Process one attachment (or just analyse it).
 *
 * @return array{id:int,file:string,status:string,note:string}
 */
function exmart_img_process_attachment( $id, $opts, $dry ) {
	$id   = absint( $id );
	$file = get_attached_file( $id );
	$name = $file ? basename( $file ) : '#' . $id;
	$out  = array( 'id' => $id, 'file' => $name, 'status' => 'error', 'note' => '' );

	if ( ! $file || ! file_exists( $file ) ) {
		$out['note'] = 'file missing';
		return $out;
	}
	if ( get_post_meta( $id, EXMART_IMG_META_ORIGINAL, true ) ) {
		$out['status'] = 'skipped';
		$out['note']   = 'already normalised';
		return $out;
	}

	$info = pathinfo( $file );
	$dest = $dry ? '' : $info['dirname'] . '/' . $info['filename'] . '-exmart.png';
	$res  = exmart_img_normalize( $file, $dest, $opts );
	$out['status'] = $res['status'];
	$out['note']   = $res['note'];

	if ( 'ok' === $res['status'] && ! $dry ) {
		update_post_meta( $id, EXMART_IMG_META_ORIGINAL, array( 'file' => get_post_meta( $id, '_wp_attached_file', true ), 'mime' => get_post_mime_type( $id ) ) );
		update_attached_file( $id, $dest );
		wp_update_post( array( 'ID' => $id, 'post_mime_type' => 'image/png' ) );
		require_once ABSPATH . 'wp-admin/includes/image.php';
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $dest ) );
	}
	return $out;
}

function exmart_img_restore_attachment( $id ) {
	$orig = get_post_meta( $id, EXMART_IMG_META_ORIGINAL, true );
	if ( ! is_array( $orig ) || empty( $orig['file'] ) ) {
		return false;
	}
	$upload = wp_upload_dir();
	$path   = trailingslashit( $upload['basedir'] ) . $orig['file'];
	if ( ! file_exists( $path ) ) {
		return false;
	}
	$new = get_attached_file( $id );
	update_attached_file( $id, $path );
	wp_update_post( array( 'ID' => $id, 'post_mime_type' => $orig['mime'] ? $orig['mime'] : 'image/jpeg' ) );
	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $path ) );
	delete_post_meta( $id, EXMART_IMG_META_ORIGINAL );
	if ( $new && $new !== $path && file_exists( $new ) ) {
		wp_delete_file( $new );
	}
	return true;
}

/* ── Admin UI ───────────────────────────────────────────── */

add_action(
	'admin_menu',
	function () {
		add_management_page( __( 'Normalize Images', 'exmart' ), __( 'Normalize Images', 'exmart' ), 'manage_options', 'exmart-normalize-images', 'exmart_img_render_page' );
	}
);

add_action(
	'wp_ajax_exmart_img_scan',
	function () {
		check_ajax_referer( 'exmart_img', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'forbidden', 403 );
		}
		wp_send_json_success( array( 'ids' => exmart_img_collect_ids() ) );
	}
);

add_action(
	'wp_ajax_exmart_img_batch',
	function () {
		check_ajax_referer( 'exmart_img', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'forbidden', 403 );
		}
		if ( function_exists( 'wp_raise_memory_limit' ) ) {
			wp_raise_memory_limit( 'image' );
		}
		@set_time_limit( 120 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$ids     = array_slice( array_map( 'absint', (array) ( $_POST['ids'] ?? array() ) ), 0, 4 );
		$mode    = isset( $_POST['mode'] ) ? sanitize_key( wp_unslash( $_POST['mode'] ) ) : 'preview';
		$opts    = array(
			'size'      => isset( $_POST['size'] ) ? absint( $_POST['size'] ) : 1200,
			'padding'   => isset( $_POST['padding'] ) ? ( (float) $_POST['padding'] ) / 100 : 0.07,
			'remove_bg' => ! empty( $_POST['remove_bg'] ) && '0' !== $_POST['remove_bg'],
			'tolerance' => isset( $_POST['tolerance'] ) ? absint( $_POST['tolerance'] ) : 26,
		);
		$results = array();
		foreach ( $ids as $id ) {
			if ( 'restore' === $mode ) {
				$ok        = exmart_img_restore_attachment( $id );
				$results[] = array( 'id' => $id, 'file' => basename( (string) get_attached_file( $id ) ), 'status' => $ok ? 'ok' : 'skipped', 'note' => $ok ? 'restored' : 'nothing to restore' );
			} else {
				$results[] = exmart_img_process_attachment( $id, $opts, 'preview' === $mode );
			}
		}
		wp_send_json_success( array( 'results' => $results ) );
	}
);

function exmart_img_render_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$nonce = wp_create_nonce( 'exmart_img' );
	?>
	<div class="wrap" id="exmart-img">
		<h1><?php esc_html_e( 'Normalize Images', 'exmart' ); ?></h1>
		<p><?php esc_html_e( 'Makes every product photo (main, gallery and variation images) the same square size with the product centred, and removes plain studio backgrounds. Originals are never deleted — “Restore originals” undoes everything. Photos with busy backgrounds are skipped and listed so you can cut them out by hand.', 'exmart' ); ?></p>
		<table class="form-table" role="presentation">
			<tr><th><label for="em-size"><?php esc_html_e( 'Canvas size (px)', 'exmart' ); ?></label></th><td><input id="em-size" type="number" value="1200" min="300" max="3000" step="100"></td></tr>
			<tr><th><label for="em-pad"><?php esc_html_e( 'Margin around product (%)', 'exmart' ); ?></label></th><td><input id="em-pad" type="number" value="7" min="0" max="30"> <span class="description"><?php esc_html_e( 'Per side. Raise it for more breathing room.', 'exmart' ); ?></span></td></tr>
			<tr><th><?php esc_html_e( 'Background', 'exmart' ); ?></th><td><label><input id="em-bg" type="checkbox" checked> <?php esc_html_e( 'Remove plain backgrounds (transparent PNG)', 'exmart' ); ?></label></td></tr>
			<tr><th><label for="em-tol"><?php esc_html_e( 'Background tolerance', 'exmart' ); ?></label></th><td><input id="em-tol" type="number" value="26" min="4" max="90"> <span class="description"><?php esc_html_e( 'Higher removes more off-white/greyish background but can eat pale products.', 'exmart' ); ?></span></td></tr>
		</table>
		<p>
			<button class="button" id="em-preview"><?php esc_html_e( '1. Preview (changes nothing)', 'exmart' ); ?></button>
			<button class="button button-primary" id="em-run"><?php esc_html_e( '2. Normalize all', 'exmart' ); ?></button>
			<button class="button" id="em-restore"><?php esc_html_e( 'Restore originals', 'exmart' ); ?></button>
		</p>
		<p><progress id="em-prog" value="0" max="1" style="width:100%;max-width:520px;display:none"></progress> <strong id="em-count"></strong></p>
		<table class="widefat striped" id="em-log" style="max-width:900px;display:none"><thead><tr><th>ID</th><th><?php esc_html_e( 'File', 'exmart' ); ?></th><th><?php esc_html_e( 'Result', 'exmart' ); ?></th></tr></thead><tbody></tbody></table>
	</div>
	<script>
	( function () {
		var nonce = <?php echo wp_json_encode( $nonce ); ?>, ajax = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
		var $ = function ( id ) { return document.getElementById( id ); };
		var busy = false;
		function post( data ) {
			var body = new URLSearchParams();
			Object.keys( data ).forEach( function ( k ) {
				[].concat( data[ k ] ).forEach( function ( v ) { body.append( k, v ); } );
			} );
			body.set( 'nonce', nonce );
			return fetch( ajax, { method: 'POST', credentials: 'same-origin', body: body } ).then( function ( r ) {
				return r.text().then( function ( t ) {
					try { return JSON.parse( t ); } catch ( e ) { throw new Error( 'Server answered with: ' + ( t.replace( /<[^>]*>/g, ' ' ).trim().slice( 0, 160 ) || 'nothing (HTTP ' + r.status + ')' ) ); }
				} );
			} );
		}
		function run( mode ) {
			if ( busy ) return;
			if ( mode === 'run' && ! confirm( 'Normalize all product images now? Originals are kept and can be restored.' ) ) return;
			if ( mode === 'restore' && ! confirm( 'Restore every normalised image to its original?' ) ) return;
			busy = true;
			$( 'em-count' ).textContent = 'Looking for product images…';
			$( 'em-prog' ).style.display = '';
			var tbody = $( 'em-log' ).querySelector( 'tbody' ); tbody.textContent = '';
			$( 'em-log' ).style.display = ''; $( 'em-prog' ).style.display = '';
			var stats = { ok: 0, skipped: 0, error: 0 };
			post( { action: 'exmart_img_scan' } ).then( function ( res ) {
				var ids = ( res && res.success ) ? res.data.ids : [];
				var i = 0;
				if ( ! ids.length ) { busy = false; $( 'em-count' ).textContent = 'No product images found (products need a main or gallery image).'; return; }
				$( 'em-count' ).textContent = '0 / ' + ids.length;
				$( 'em-prog' ).max = Math.max( 1, ids.length ); $( 'em-prog' ).value = 0;
				function next() {
					if ( i >= ids.length ) {
						busy = false;
						$( 'em-count' ).textContent = 'Done — ' + stats.ok + ' ' + ( mode === 'preview' ? 'ready' : 'processed' ) + ', ' + stats.skipped + ' skipped, ' + stats.error + ' errors.';
						return;
					}
					var chunk = ids.slice( i, i + 3 ); i += 3;
					post( {
						action: 'exmart_img_batch', mode: mode === 'run' ? 'run' : mode,
						size: $( 'em-size' ).value, padding: $( 'em-pad' ).value, tolerance: $( 'em-tol' ).value,
						remove_bg: $( 'em-bg' ).checked ? '1' : '0', 'ids[]': chunk
					} ).then( function ( r ) {
						( r && r.success ? r.data.results : chunk.map( function ( id ) { return { id: id, file: '', status: 'error', note: 'request failed' }; } ) ).forEach( function ( x ) {
							stats[ x.status ] = ( stats[ x.status ] || 0 ) + 1;
							var tr = document.createElement( 'tr' );
							[ x.id, x.file, x.status.toUpperCase() + ' — ' + x.note ].forEach( function ( t ) { var td = document.createElement( 'td' ); td.textContent = t; tr.appendChild( td ); } );
							tbody.appendChild( tr );
						} );
						$( 'em-prog' ).value = Math.min( i, ids.length );
						$( 'em-count' ).textContent = Math.min( i, ids.length ) + ' / ' + ids.length;
						next();
					} ).catch( function () { busy = false; $( 'em-count' ).textContent = 'Stopped: request failed. Run again — finished images are skipped.'; } );
				}
				next();
			} ).catch( function ( e ) { busy = false; $( 'em-count' ).textContent = 'Could not start: ' + e.message; } );
		}
		$( 'em-preview' ).addEventListener( 'click', function () { run( 'preview' ); } );
		$( 'em-run' ).addEventListener( 'click', function () { run( 'run' ); } );
		$( 'em-restore' ).addEventListener( 'click', function () { run( 'restore' ); } );
	} )();
	</script>
	<?php
}
