<?php
/**
 * Cart totals — Figma Order Summary card.
 *
 * @see woocommerce/templates/cart/cart-totals.php
 * @package exMart
 * @version 1.0.61
 */

defined( 'ABSPATH' ) || exit;

$item_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
$subtotal   = WC()->cart ? (float) WC()->cart->get_displayed_subtotal() : 0;
$threshold  = function_exists( 'exmart_free_shipping_threshold' ) ? exmart_free_shipping_threshold() : 300.0;
$remaining  = max( 0, $threshold - $subtotal );
$shop_url   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>
<div class="cart_totals em-cart-summary <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<div class="em-summary-card">
		<h2 class="em-h4 em-cart-summary-title"><?php esc_html_e( 'Order Summary', 'exmart' ); ?></h2>

		<div class="em-summary-row cart-subtotal">
			<span class="em-summary-label">
				<?php
				printf(
					/* translators: %d: item count */
					esc_html( _n( 'Subtotal (%d item)', 'Subtotal (%d items)', $item_count, 'exmart' ) ),
					(int) $item_count
				);
				?>
			</span>
			<span class="em-summary-value"><?php wc_cart_totals_subtotal_html(); ?></span>
		</div>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div class="em-summary-row cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<span class="em-summary-label"><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
				<span class="em-summary-value"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>
			<table class="em-cart-shipping-table" cellspacing="0">
				<?php wc_cart_totals_shipping_html(); ?>
			</table>
			<?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>
		<?php elseif ( WC()->cart->needs_shipping() && 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>
			<div class="em-summary-row shipping">
				<span class="em-summary-label"><?php esc_html_e( 'Shipping', 'exmart' ); ?></span>
				<span class="em-summary-value"><?php woocommerce_shipping_calculator(); ?></span>
			</div>
		<?php endif; ?>

		<?php if ( $remaining > 0 && $threshold > 0 ) : ?>
			<p class="em-caption em-cart-free-ship">
				<?php
				printf(
					/* translators: %s: amount remaining for free shipping */
					esc_html__( 'Add %s more for free shipping', 'exmart' ),
					wp_kses_post( wc_price( $remaining ) )
				);
				?>
			</p>
		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<div class="em-summary-row fee">
				<span class="em-summary-label"><?php echo esc_html( $fee->name ); ?></span>
				<span class="em-summary-value"><?php wc_cart_totals_fee_html( $fee ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php
		if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
			if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
				foreach ( WC()->cart->get_tax_totals() as $code => $tax ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					?>
					<div class="em-summary-row tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<span class="em-summary-label"><?php echo esc_html( $tax->label ); ?></span>
						<span class="em-summary-value"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
					</div>
					<?php
				}
			} else {
				?>
				<div class="em-summary-row tax-total">
					<span class="em-summary-label"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
					<span class="em-summary-value"><?php wc_cart_totals_taxes_total_html(); ?></span>
				</div>
				<?php
			}
		}
		?>

		<hr class="em-rule" />

		<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>
		<div class="em-summary-row em-summary-total order-total">
			<span class="em-summary-label"><?php esc_html_e( 'Total', 'exmart' ); ?></span>
			<span class="em-summary-value"><?php wc_cart_totals_order_total_html(); ?></span>
		</div>
		<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>
	</div>

	<?php if ( wc_coupons_enabled() ) : ?>
		<form class="em-cart-coupon" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<input type="text" name="coupon_code" class="em-input input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'exmart' ); ?>" aria-label="<?php esc_attr_e( 'Coupon code', 'exmart' ); ?>" />
			<button type="submit" class="em-btn em-btn-secondary" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'exmart' ); ?>"><?php esc_html_e( 'Apply', 'exmart' ); ?></button>
			<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
			<?php do_action( 'woocommerce_cart_coupon' ); ?>
		</form>
	<?php endif; ?>

	<div class="em-cart-cod-note">
		<svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
			<g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5">
				<path d="M17.4142 10.4142C18 9.82843 18 8.88562 18 7C18 5.11438 18 4.17157 17.4142 3.58579M17.4142 3.58579C16.8284 3 15.8856 3 14 3L10 3C8.11438 3 7.17157 3 6.58579 3.58579C6 4.17157 6 5.11438 6 7C6 8.88562 6 9.82843 6.58579 10.4142C7.17157 11 8.11438 11 10 11H14C15.8856 11 16.8284 11 17.4142 10.4142M17.4142 10.4142V10.4142ZM17.4142 3.58579V3.58579ZM6.58579 3.58579V3.58579ZM6.58579 10.4142V10.4142Z"/>
				<path d="M13 7C13 7.55228 12.5523 8 12 8C11.4477 8 11 7.55228 11 7C11 6.44772 11.4477 6 12 6C12.5523 6 13 6.44772 13 7Z"/>
				<path d="M18 6C16.3431 6 15 4.65685 15 3"/>
				<path d="M18 8C16.3431 8 15 9.34315 15 11"/>
				<path d="M6 6C7.65685 6 9 4.65685 9 3"/>
				<path d="M6 8C7.65685 8 9 9.34315 9 11"/>
				<path d="M5 20.3884H7.25993C8.27079 20.3884 9.29253 20.4937 10.2763 20.6964C12.0166 21.0549 13.8488 21.0983 15.6069 20.8138C16.4738 20.6734 17.326 20.4589 18.0975 20.0865C18.7939 19.7504 19.6469 19.2766 20.2199 18.7459C20.7921 18.216 21.388 17.3487 21.8109 16.6707C22.1736 16.0894 21.9982 15.3762 21.4245 14.943C20.7873 14.4619 19.8417 14.462 19.2046 14.9433L17.3974 16.3084C16.697 16.8375 15.932 17.3245 15.0206 17.4699C14.911 17.4874 14.7962 17.5033 14.6764 17.5172M12.7518 17.5326C13.4312 17.5968 14.0434 17.5829 14.5668 17.5292C14.6038 17.5254 14.6403 17.5214 14.6764 17.5172M14.6764 17.5172C14.8222 17.486 14.9669 17.396 15.1028 17.2775C15.746 16.7161 15.7866 15.77 15.2285 15.1431C15.0991 14.9977 14.9475 14.8764 14.7791 14.7759C11.9817 13.1074 7.62942 14.3782 5 16.2429M14.6764 17.5172C14.6399 17.525 14.6033 17.5292 14.5668 17.5292"/>
				<rect width="3" height="8" x="2" y="14" rx="1.5"/>
			</g>
		</svg>
		<span class="em-caption"><?php esc_html_e( 'Cash on delivery available at checkout', 'exmart' ); ?></span>
	</div>

	<div class="wc-proceed-to-checkout">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>

	<a class="em-cart-continue" href="<?php echo esc_url( $shop_url ); ?>">
		<?php esc_html_e( 'Continue shopping', 'exmart' ); ?>
	</a>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
