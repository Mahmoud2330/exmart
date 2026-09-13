<?php
/**
 * Empty cart — Figma empty state.
 *
 * @package exMart
 * @version 1.0.61
 */

defined( 'ABSPATH' ) || exit;

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>
<div class="em-empty-state em-cart-empty">
	<div class="em-empty-icon" aria-hidden="true">
		<svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M3 3h2l2.4 12.4a2 2 0 002 1.6h8.2a2 2 0 002-1.6L21 8H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="21" r="1" fill="currentColor"/><circle cx="18" cy="21" r="1" fill="currentColor"/></svg>
	</div>
	<h2 class="em-h3"><?php esc_html_e( 'Your cart is empty', 'exmart' ); ?></h2>
	<p class="em-body" style="color:var(--ink-500);max-width:36ch;"><?php esc_html_e( 'Add products from the shop and they will appear here.', 'exmart' ); ?></p>
	<a class="em-btn em-btn-primary" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Continue shopping', 'exmart' ); ?></a>
</div>
<?php
/* Suppress default WC empty message — we render our own empty state above. */
remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
do_action( 'woocommerce_cart_is_empty' );
