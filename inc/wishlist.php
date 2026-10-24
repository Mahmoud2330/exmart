<?php
/**
 * Lightweight wishlist: product IDs are kept in the visitor's browser
 * (localStorage) exactly like the original React app — no account
 * required. This AJAX endpoint turns a list of IDs back into rendered
 * product cards for the Wishlist page.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function exmart_ajax_get_wishlist_products() {
	check_ajax_referer( 'exmart_ajax', 'nonce' );

	$ids = isset( $_POST['ids'] ) ? (array) $_POST['ids'] : array();
	$ids = array_filter( array_map( 'absint', $ids ) );

	if ( empty( $ids ) ) {
		wp_send_json_success( array( 'html' => '', 'count' => 0 ) );
	}

	$query = new WC_Product_Query( array(
		'include' => $ids,
		'limit'   => count( $ids ),
		'status'  => 'publish',
	) );
	$products = $query->get_products();

	ob_start();
	global $post, $product;
	foreach ( $products as $wc_product ) {
		// content-product.php reads the current item from global $product,
		// not from $post — setup_postdata() alone won't set it (that
		// normally happens via the "the_post" hook fired by the real
		// WP Loop, which we're intentionally bypassing here), so set it
		// explicitly or every card fatal-errors on a null $product.
		$post    = get_post( $wc_product->get_id() );
		$product = $wc_product;
		setup_postdata( $post );
		wc_get_template_part( 'content', 'product' );
	}
	wp_reset_postdata();
	$html = ob_get_clean();

	wp_send_json_success( array( 'html' => $html, 'count' => count( $products ) ) );
}
add_action( 'wp_ajax_exmart_get_wishlist_products', 'exmart_ajax_get_wishlist_products' );
add_action( 'wp_ajax_nopriv_exmart_get_wishlist_products', 'exmart_ajax_get_wishlist_products' );

/**
 * The /wishlist/ page always renders the theme's wishlist template, even if
 * the page was created by the old TI Wishlist plugin with the default template
 * and a [ti_wishlistsview] body.
 *
 * @param string $template
 * @return string
 */
function exmart_force_wishlist_template( $template ) {
	if ( ! is_page( 'wishlist' ) ) {
		return $template;
	}
	$wishlist_template = locate_template( 'template-wishlist.php' );
	return $wishlist_template ? $wishlist_template : $template;
}
add_filter( 'template_include', 'exmart_force_wishlist_template', 99 );

/**
 * Leftover TI Wishlist shortcodes (plugin no longer active) render nothing,
 * and strip_shortcodes() — used by SEO plugins for meta descriptions — now
 * removes them instead of leaking the raw tag.
 */
function exmart_register_legacy_wishlist_shortcodes() {
	foreach ( array( 'ti_wishlistsview', 'ti_wishlists_addtowishlist', 'ti_wishlist_products_counter' ) as $tag ) {
		if ( ! shortcode_exists( $tag ) ) {
			add_shortcode( $tag, '__return_empty_string' );
		}
	}
}
add_action( 'init', 'exmart_register_legacy_wishlist_shortcodes', 20 );
