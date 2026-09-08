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
		<p><?php esc_html_e( 'Hex color used for this brand\'s avatar/badges.', 'exmart' ); ?></p>
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
