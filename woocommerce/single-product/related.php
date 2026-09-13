<?php
/**
 * Related Products — Figma “You may also like” horizontal rail.
 *
 * @package exMart
 * @version 1.0.75
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $related_products ) ) {
	return;
}

$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'You may also like', 'exmart' ) );

$view_all = wc_get_page_permalink( 'shop' );
global $product;
if ( $product instanceof WC_Product ) {
	$cats = wc_get_product_terms( $product->get_id(), 'product_cat', array( 'fields' => 'all' ) );
	if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
		$link = get_term_link( $cats[0] );
		if ( ! is_wp_error( $link ) ) {
			$view_all = $link;
		}
	}
}

echo '<hr class="em-rule" />';
exmart_product_rail( $heading, $related_products, $view_all );
