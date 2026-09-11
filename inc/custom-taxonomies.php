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
 * Brand logo URL for the “Our Brands” strip (and similar chrome).
 *
 * Priority: term meta attachment → Media Library filename match → known live logos.
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

	$slug = strtolower( $term->slug );
	$name = strtolower( sanitize_title( $term->name ) );

	global $wpdb;
	$patterns = array_unique( array_filter( array( $slug, $name ) ) );
	foreach ( $patterns as $needle ) {
		$like = '%' . $wpdb->esc_like( $needle ) . '%';
		$found = absint(
			$wpdb->get_var(
				$wpdb->prepare(
					"SELECT post_id FROM {$wpdb->postmeta}
					WHERE meta_key = '_wp_attached_file'
					AND meta_value LIKE %s
					ORDER BY
						CASE WHEN meta_value LIKE %s THEN 0 ELSE 1 END,
						post_id DESC
					LIMIT 1",
					$like,
					'%logo%'
				)
			)
		);
		if ( $found ) {
			$url = wp_get_attachment_image_url( $found, 'medium' );
			if ( $url ) {
				return $url;
			}
		}
	}

	$known = array(
		'diversey'  => 'https://exmartegypt.com/wp-content/uploads/2025/03/diversey-logo-300x150.jpg',
		'oview'     => 'https://exmartegypt.com/wp-content/uploads/2023/03/Oview-panner-logo-300x129.webp',
		'surecheck' => 'https://exmartegypt.com/wp-content/uploads/2023/03/surecheck_vector.svg',
		'grace'     => 'https://exmartegypt.com/wp-content/uploads/2023/03/logo-w.png-300x74.webp',
	);
	if ( ! empty( $known[ $slug ] ) ) {
		$mapped = absint( attachment_url_to_postid( $known[ $slug ] ) );
		if ( $mapped ) {
			$url = wp_get_attachment_image_url( $mapped, 'medium' );
			if ( $url ) {
				return $url;
			}
		}
		return $known[ $slug ];
	}

	return '';
}
