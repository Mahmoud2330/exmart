<?php
/**
 * Order Summary card for checkout (right column).
 * Shipping method radios live in the Shipping step — here we only show the chosen cost.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package exMart
 * @version 1.0.67
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="em-summary-card woocommerce-checkout-review-order-table">
	<h2 class="em-h4 em-checkout-summary-title"><?php esc_html_e( 'Order Summary', 'exmart' ); ?></h2>

	<?php
	do_action( 'woocommerce_review_order_before_cart_contents' );

	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

		if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
			?>
			<div class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'em-summary-row cart_item', $cart_item, $cart_item_key ) ); ?>">
				<span class="em-checkout-summary-item">
					<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?>
					<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <span class="product-quantity">&times;&nbsp;' . esc_html( $cart_item['quantity'] ) . '</span>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
				<span class="em-summary-value">
					<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
			</div>
			<?php
		}
	}

	do_action( 'woocommerce_review_order_after_cart_contents' );
	?>

	<hr class="em-rule" />

	<div class="em-summary-row cart-subtotal">
		<span class="em-summary-label"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
		<span class="em-summary-value"><?php wc_cart_totals_subtotal_html(); ?></span>
	</div>

	<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
		<div class="em-summary-row cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
			<span class="em-summary-label"><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
			<span class="em-summary-value"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
		</div>
	<?php endforeach; ?>

	<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
		<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
		<?php
		$packages      = WC()->shipping()->get_packages();
		$chosen_methods = WC()->session ? WC()->session->get( 'chosen_shipping_methods' ) : array();
		$ship_label     = __( 'Shipping', 'woocommerce' );
		$ship_value     = '';

		foreach ( $packages as $i => $package ) {
			$chosen = isset( $chosen_methods[ $i ] ) ? $chosen_methods[ $i ] : '';
			if ( $chosen && ! empty( $package['rates'][ $chosen ] ) ) {
				$rate       = $package['rates'][ $chosen ];
				$ship_label = $rate->get_label();
				$ship_value = wc_cart_totals_shipping_method_label( $rate );
				// Label already includes cost via wc_cart_totals_shipping_method_label — split for Figma.
				$cost = (float) $rate->cost;
				if ( WC()->cart->display_prices_including_tax() ) {
					$cost += (float) $rate->get_shipping_tax();
				}
				$ship_value = ( $cost <= 0 ) ? esc_html__( 'Free', 'exmart' ) : wp_kses_post( wc_price( $cost ) );
				break;
			}
		}

		if ( '' === $ship_value ) {
			$ship_value = wp_kses_post( wc_price( WC()->cart->get_shipping_total() ) );
			if ( (float) WC()->cart->get_shipping_total() <= 0 && ! empty( $packages ) ) {
				$ship_value = esc_html__( '—', 'exmart' );
			}
		}
		?>
		<div class="em-summary-row shipping">
			<span class="em-summary-label"><?php echo esc_html( $ship_label ); ?></span>
			<span class="em-summary-value"><?php echo $ship_value; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		</div>
		<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
	<?php endif; ?>

	<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
		<div class="em-summary-row fee">
			<span class="em-summary-label"><?php echo esc_html( $fee->name ); ?></span>
			<span class="em-summary-value"><?php wc_cart_totals_fee_html( $fee ); ?></span>
		</div>
	<?php endforeach; ?>

	<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
		<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
			<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
				<div class="em-summary-row tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
					<span class="em-summary-label"><?php echo esc_html( $tax->label ); ?></span>
					<span class="em-summary-value"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
				</div>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="em-summary-row tax-total">
				<span class="em-summary-label"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
				<span class="em-summary-value"><?php wc_cart_totals_taxes_total_html(); ?></span>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<hr class="em-rule" />

	<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

	<div class="em-summary-row em-summary-total order-total">
		<span class="em-summary-label"><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
		<span class="em-summary-value"><?php wc_cart_totals_order_total_html(); ?></span>
	</div>

	<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
</div>
