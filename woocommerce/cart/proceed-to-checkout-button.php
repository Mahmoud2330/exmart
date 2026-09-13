<?php
/**
 * Proceed to checkout button.
 *
 * @package exMart
 * @version 1.0.61
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="checkout-button button alt wc-forward em-btn em-btn-primary em-btn-lg em-cart-checkout-btn">
	<?php esc_html_e( 'Proceed to checkout', 'woocommerce' ); ?>
</a>
