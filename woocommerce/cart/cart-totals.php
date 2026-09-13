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
			<rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/>
			<path d="M2 10h20" stroke="currentColor" stroke-width="1.5"/>
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
