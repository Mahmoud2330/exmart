<?php
/**
 * Custom "Brand" taxonomy for WooCommerce products, with a couple of
 * extra term-meta fields (accent color, tagline, distributor/house type)
 * so brand landing pages can render the same trust lockups as the
 * original design without needing a page-builder plugin.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function exmart_register_taxonomies() {
	register_taxonomy( 'product_brand', array( 'product' ), array(
		'labels' => array(
			'name'          => __( 'Brands', 'exmart' ),
			'singular_name' => __( 'Brand', 'exmart' ),
			'menu_name'     => __( 'Brands', 'exmart' ),
			'add_new_item'  => __( 'Add New Brand', 'exmart' ),
			'search_items'  => __( 'Search Brands', 'exmart' ),
		),
		'hierarchical'      => false,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_in_rest'      => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'brands', 'with_front' => false ),
	) );
}
add_action( 'init', 'exmart_register_taxonomies', 0 );

/**
 * Extra fields on the "Add Brand" screen.
 */
function exmart_brand_add_form_fields() {
	?>
	<div class="form-field">
		<label for="exmart_brand_color"><?php esc_html_e( 'Accent color', 'exmart' ); ?></label>
		<input type="text" name="exmart_brand_color" id="exmart_brand_color" value="#2A44E8" placeholder="#2A44E8">
		<p><?php esc_html_e( 'Hex color used for brand hover accents and badges.', 'exmart' ); ?></p>
	</div>
	<div class="form-field">
		<label for="exmart_brand_type"><?php esc_html_e( 'Brand type', 'exmart' ); ?></label>
		<select name="exmart_brand_type" id="exmart_brand_type">
			<option value="distributed"><?php esc_html_e( 'Distributed (sole distributor in Egypt)', 'exmart' ); ?></option>
			<option value="house"><?php esc_html_e( 'House brand (exMart exclusive)', 'exmart' ); ?></option>
		</select>
	</div>
	<div class="form-field">
		<label for="exmart_brand_tagline"><?php esc_html_e( 'Tagline', 'exmart' ); ?></label>
		<input type="text" name="exmart_brand_tagline" id="exmart_brand_tagline" value="">
	</div>
	<div class="form-field">
		<label for="exmart_brand_logo"><?php esc_html_e( 'Logo (Media Library attachment ID)', 'exmart' ); ?></label>
		<input type="number" name="exmart_brand_logo" id="exmart_brand_logo" value="" min="0" step="1">
		<p><?php esc_html_e( 'Optional. Used in the homepage “Our Brands” strip. Leave empty to auto-match from Media Library by brand name.', 'exmart' ); ?></p>
	</div>
	<?php
}
add_action( 'product_brand_add_form_fields', 'exmart_brand_add_form_fields' );

/**
 * Extra fields on the "Edit Brand" screen.
 */
function exmart_brand_edit_form_fields( $term ) {
	$color   = get_term_meta( $term->term_id, 'exmart_brand_color', true ) ?: '#2A44E8';
	$type    = get_term_meta( $term->term_id, 'exmart_brand_type', true ) ?: 'distributed';
	$tagline = get_term_meta( $term->term_id, 'exmart_brand_tagline', true );
	$logo_id = absint( get_term_meta( $term->term_id, 'exmart_brand_logo', true ) );
	$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'thumbnail' ) : '';
	?>
	<tr class="form-field">
		<th scope="row"><label for="exmart_brand_color"><?php esc_html_e( 'Accent color', 'exmart' ); ?></label></th>
		<td><input type="text" name="exmart_brand_color" id="exmart_brand_color" value="<?php echo esc_attr( $color ); ?>"></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="exmart_brand_type"><?php esc_html_e( 'Brand type', 'exmart' ); ?></label></th>
		<td>
			<select name="exmart_brand_type" id="exmart_brand_type">
				<option value="distributed" <?php selected( $type, 'distributed' ); ?>><?php esc_html_e( 'Distributed (sole distributor in Egypt)', 'exmart' ); ?></option>
				<option value="house" <?php selected( $type, 'house' ); ?>><?php esc_html_e( 'House brand (exMart exclusive)', 'exmart' ); ?></option>
			</select>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="exmart_brand_tagline"><?php esc_html_e( 'Tagline', 'exmart' ); ?></label></th>
		<td><input type="text" name="exmart_brand_tagline" id="exmart_brand_tagline" value="<?php echo esc_attr( $tagline ); ?>"></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="exmart_brand_logo"><?php esc_html_e( 'Logo attachment ID', 'exmart' ); ?></label></th>
		<td>
			<input type="number" name="exmart_brand_logo" id="exmart_brand_logo" value="<?php echo esc_attr( $logo_id ? (string) $logo_id : '' ); ?>" min="0" step="1">
			<?php if ( $logo_url ) : ?>
				<p><img src="<?php echo esc_url( $logo_url ); ?>" alt="" style="max-height:40px;width:auto;margin-top:8px;" /></p>
			<?php endif; ?>
			<p class="description"><?php esc_html_e( 'Media Library attachment ID for the brand logo. Leave empty to auto-match by brand name/filename.', 'exmart' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'product_brand_edit_form_fields', 'exmart_brand_edit_form_fields' );

function exmart_save_brand_meta( $term_id ) {
	if ( isset( $_POST['exmart_brand_color'] ) ) {
		update_term_meta( $term_id, 'exmart_brand_color', sanitize_hex_color( wp_unslash( $_POST['exmart_brand_color'] ) ) );
	}
	if ( isset( $_POST['exmart_brand_type'] ) ) {
		$type = sanitize_text_field( wp_unslash( $_POST['exmart_brand_type'] ) );
		update_term_meta( $term_id, 'exmart_brand_type', in_array( $type, array( 'distributed', 'house' ), true ) ? $type : 'distributed' );
	}
	if ( isset( $_POST['exmart_brand_tagline'] ) ) {
		update_term_meta( $term_id, 'exmart_brand_tagline', sanitize_text_field( wp_unslash( $_POST['exmart_brand_tagline'] ) ) );
	}
	if ( isset( $_POST['exmart_brand_logo'] ) ) {
		$logo_id = absint( wp_unslash( $_POST['exmart_brand_logo'] ) );
		if ( $logo_id ) {
			update_term_meta( $term_id, 'exmart_brand_logo', $logo_id );
		} else {
			delete_term_meta( $term_id, 'exmart_brand_logo' );
		}
	}
}
add_action( 'created_product_brand', 'exmart_save_brand_meta' );
add_action( 'edited_product_brand', 'exmart_save_brand_meta' );

/**
 * Small helpers used across templates.
 */
function exmart_brand_color( $term_id ) {
	return get_term_meta( $term_id, 'exmart_brand_color', true ) ?: '#2A44E8';
}
function exmart_brand_type( $term_id ) {
	return get_term_meta( $term_id, 'exmart_brand_type', true ) ?: 'distributed';
}
function exmart_brand_tagline( $term_id ) {
	return get_term_meta( $term_id, 'exmart_brand_tagline', true );
}

/**
 * Exact Media Library basenames for each brand logo (Our Brands strip).
 *
 * @return array<string, string> slug => filename
 */
function exmart_brand_logo_filenames() {
	return array(
		'diversey'  => 'diversey_core_logo_master_usage_rgb.png',
		'grace'     => 'logo_Kero-01.jpg',
		'oview'     => 'Oview-panner-logo-scaled.webp',
		'surecheck' => 'Surecheck-logo2.webp',
		'qualita'   => '327386967_971846660460839_9112051114943853646_n.jpg',
		'eliv'      => 'eliv-logo.webp',
		'verve'     => 'verve-logo.jpeg',
	);
}

/**
 * Brand logo URL for the “Our Brands” strip (and similar chrome).
 *
 * Priority: term meta attachment → exact filename map in Media Library.
 *
 * @param int $term_id
 * @return string Empty when no logo found.
 */
function exmart_brand_logo_url( $term_id ) {
	$term_id = absint( $term_id );
	if ( ! $term_id ) {
		return '';
	}

	$logo_id = absint( get_term_meta( $term_id, 'exmart_brand_logo', true ) );
	if ( $logo_id ) {
		$url = wp_get_attachment_image_url( $logo_id, 'medium' );
		if ( $url ) {
			return $url;
		}
	}

	$term = get_term( $term_id, 'product_brand' );
	if ( ! $term || is_wp_error( $term ) ) {
		return '';
	}

	$slug      = strtolower( $term->slug );
	$filenames = exmart_brand_logo_filenames();
	if ( empty( $filenames[ $slug ] ) ) {
		return '';
	}

	$filename = $filenames[ $slug ];
	global $wpdb;
	$found = absint(
		$wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta}
				WHERE meta_key = '_wp_attached_file'
				AND (
					meta_value = %s
					OR meta_value LIKE %s
				)
				ORDER BY post_id DESC
				LIMIT 1",
				$filename,
				'%/' . $wpdb->esc_like( $filename )
			)
		)
	);

	if ( ! $found ) {
		return '';
	}

	$url = wp_get_attachment_image_url( $found, 'medium' );
	return $url ? $url : '';
}

/**
 * Map theme brand slugs → how products are tagged on the live catalog.
 *
 * Existing products use WooCommerce product tags (and sometimes Perfect
 * Brands /pwb-brand/), not product_brand — so brand archives stay empty
 * until we bridge those sources.
 *
 * @return array<string, array{tags: string[], tag_names: string[], pwb: string[], titles: string[], require_title: bool}>
 */
function exmart_brand_product_sources() {
	return array(
		'diversey'  => array(
			'tags'          => array( 'diversey' ),
			'tag_names'     => array( 'Diversey' ),
			'pwb'           => array( 'diversey' ),
			'titles'        => array( 'Diversey' ),
			'require_title' => false,
		),
		'grace'     => array(
			'tags'          => array( 'grace' ),
			'tag_names'     => array( 'Grace' ),
			'pwb'           => array( 'grace' ),
			'titles'        => array( 'Grace' ),
			'require_title' => false,
		),
		'oview'     => array(
			'tags'          => array( 'surecheck-oview', 'oview' ),
			'tag_names'     => array( 'Surecheck & Oview', 'Oview' ),
			'pwb'           => array( 'oview' ),
			'titles'        => array( 'Oview' ),
			'require_title' => true,
		),
		'surecheck' => array(
			'tags'          => array( 'surecheck-oview', 'surecheck' ),
			'tag_names'     => array( 'Surecheck & Oview', 'Surecheck', 'SureCheck' ),
			'pwb'           => array( 'surecheck' ),
			'titles'        => array( 'Surecheck', 'SureCheck' ),
			'require_title' => true,
		),
		'qualita'   => array(
			'tags'          => array( 'qualita' ),
			'tag_names'     => array( 'Qualita' ),
			'pwb'           => array( 'qualita' ),
			'titles'        => array( 'Qualita' ),
			'require_title' => false,
		),
		'eliv'      => array(
			'tags'          => array( 'eliv' ),
			'tag_names'     => array( 'éliv', 'eliv', 'Eliv' ),
			'pwb'           => array( 'eliv' ),
			'titles'        => array( 'eliv', 'éliv', 'Eliv' ),
			'require_title' => false,
		),
		'verve'     => array(
			'tags'          => array( 'verve' ),
			'tag_names'     => array( 'Verve' ),
			'pwb'           => array( 'verve' ),
			'titles'        => array( 'Verve' ),
			'require_title' => false,
		),
		'vodlia'    => array(
			'tags'          => array( 'vodlia' ),
			'tag_names'     => array( 'Vodlia' ),
			'pwb'           => array( 'vodlia' ),
			'titles'        => array( 'Vodlia' ),
			'require_title' => false,
		),
	);
}

/**
 * Whether a product title matches any of the brand title prefixes.
 *
 * @param string   $title
 * @param string[] $prefixes
 */
function exmart_product_title_matches_brand( $title, $prefixes ) {
	$title = (string) $title;
	foreach ( (array) $prefixes as $prefix ) {
		$prefix = (string) $prefix;
		if ( '' === $prefix ) {
			continue;
		}
		if ( 0 === stripos( $title, $prefix ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Collect product IDs that belong to a brand via tags / Perfect Brands / title.
 *
 * @param string $brand_slug
 * @return int[]
 */
function exmart_find_legacy_brand_product_ids( $brand_slug ) {
	$sources = exmart_brand_product_sources();
	if ( empty( $sources[ $brand_slug ] ) ) {
		return array();
	}

	$cfg = $sources[ $brand_slug ];
	$ids = array();

	$tag_term_ids = array();
	foreach ( $cfg['tags'] as $tag_slug ) {
		$term = get_term_by( 'slug', $tag_slug, 'product_tag' );
		if ( $term && ! is_wp_error( $term ) ) {
			$tag_term_ids[] = (int) $term->term_id;
		}
	}
	foreach ( $cfg['tag_names'] as $tag_name ) {
		$term = get_term_by( 'name', $tag_name, 'product_tag' );
		if ( $term && ! is_wp_error( $term ) ) {
			$tag_term_ids[] = (int) $term->term_id;
		}
	}
	$tag_term_ids = array_values( array_unique( array_filter( $tag_term_ids ) ) );

	if ( $tag_term_ids ) {
		$tagged = get_posts(
			array(
				'post_type'              => 'product',
				'post_status'            => 'publish',
				'posts_per_page'         => -1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'tax_query'              => array(
					array(
						'taxonomy' => 'product_tag',
						'field'    => 'term_id',
						'terms'    => $tag_term_ids,
					),
				),
			)
		);
		foreach ( $tagged as $pid ) {
			if ( ! empty( $cfg['require_title'] ) ) {
				if ( ! exmart_product_title_matches_brand( get_the_title( $pid ), $cfg['titles'] ) ) {
					continue;
				}
			}
			$ids[] = (int) $pid;
		}
	}

	if ( taxonomy_exists( 'pwb-brand' ) && ! empty( $cfg['pwb'] ) ) {
		$pwb = get_posts(
			array(
				'post_type'              => 'product',
				'post_status'            => 'publish',
				'posts_per_page'         => -1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'tax_query'              => array(
					array(
						'taxonomy' => 'pwb-brand',
						'field'    => 'slug',
						'terms'    => $cfg['pwb'],
					),
				),
			)
		);
		foreach ( $pwb as $pid ) {
			$ids[] = (int) $pid;
		}
	}

	global $wpdb;
	foreach ( $cfg['titles'] as $prefix ) {
		$prefix = (string) $prefix;
		if ( '' === $prefix ) {
			continue;
		}
		$found = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts}
				WHERE post_type = 'product'
				AND post_status = 'publish'
				AND post_title LIKE %s",
				$wpdb->esc_like( $prefix ) . '%'
			)
		);
		foreach ( $found as $pid ) {
			$ids[] = (int) $pid;
		}
	}

	return array_values( array_unique( array_filter( $ids ) ) );
}

/**
 * Assign product_brand terms from existing product tags / titles (idempotent).
 * Re-runs when EXMART_VERSION changes so new mappings apply after deploys.
 */
function exmart_sync_brand_product_assignments() {
	$flag = 'exmart_brand_sync_' . EXMART_VERSION;
	if ( get_option( $flag ) ) {
		return;
	}

	$sources = exmart_brand_product_sources();
	foreach ( $sources as $slug => $cfg ) {
		$term = get_term_by( 'slug', $slug, 'product_brand' );
		if ( ! $term || is_wp_error( $term ) ) {
			continue;
		}

		$ids = exmart_find_legacy_brand_product_ids( $slug );
		foreach ( $ids as $product_id ) {
			wp_set_object_terms( $product_id, (int) $term->term_id, 'product_brand', true );
		}
	}

	$tt_ids = get_terms(
		array(
			'taxonomy'   => 'product_brand',
			'hide_empty' => false,
			'fields'     => 'tt_ids',
		)
	);
	if ( ! is_wp_error( $tt_ids ) && $tt_ids ) {
		wp_update_term_count_now( $tt_ids, 'product_brand' );
	}

	update_option( $flag, 1, false );
}
add_action( 'init', 'exmart_sync_brand_product_assignments', 30 );

/**
 * Brand archives: also include products matched via product tags / Perfect Brands
 * (covers the period before sync runs, and any products sync missed).
 *
 * @param WP_Query $query
 */
function exmart_brand_archive_include_legacy_sources( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_tax( 'product_brand' ) ) {
		return;
	}

	$term = get_queried_object();
	if ( ! $term || empty( $term->slug ) || empty( $term->term_id ) ) {
		return;
	}

	$legacy_ids = exmart_find_legacy_brand_product_ids( $term->slug );

	$brand_ids = get_posts(
		array(
			'post_type'              => 'product',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'tax_query'              => array(
				array(
					'taxonomy' => 'product_brand',
					'field'    => 'term_id',
					'terms'    => array( (int) $term->term_id ),
				),
			),
		)
	);

	$all_ids = array_values(
		array_unique(
			array_merge(
				array_map( 'intval', (array) $brand_ids ),
				array_map( 'intval', (array) $legacy_ids )
			)
		)
	);

	if ( ! $all_ids ) {
		return;
	}

	// Membership via post__in. Clear taxonomy query vars so WP does not AND
	// an empty product_brand constraint with these IDs.
	unset( $query->query_vars['product_brand'] );
	$query->set( 'taxonomy', '' );
	$query->set( 'term', '' );
	$query->set( 'term_id', '' );
	$query->set( 'tax_query', array() );
	$query->set( 'post_type', 'product' );
	$query->set( 'post__in', $all_ids );
	$query->set( 'orderby', 'title' );
	$query->set( 'order', 'ASC' );
}
add_action( 'pre_get_posts', 'exmart_brand_archive_include_legacy_sources' );
