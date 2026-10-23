<?php
/**
 * Order customer details — delivery / billing / shipping as cards.
 *
 * When billing and shipping are the same address, one "Delivery details"
 * card is shown instead of two identical boxes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package exMart
 * @version 8.7.0
 */

defined( 'ABSPATH' ) || exit;

$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
$billing_addr  = $order->get_formatted_billing_address( '' );
$shipping_addr = $show_shipping ? $order->get_formatted_shipping_address( '' ) : '';
$is_single     = ! $show_shipping || '' === $shipping_addr || $shipping_addr === $billing_addr;
?>
<section class="woocommerce-customer-details em-od-customer">
	<div class="em-od-cards<?php echo $is_single ? ' is-single' : ''; ?>">

		<div class="em-od-card">
			<h2 class="woocommerce-column__title em-od-card-title">
				<?php echo $is_single ? esc_html__( 'Delivery details', 'exmart' ) : esc_html__( 'Billing address', 'exmart' ); ?>
			</h2>
			<address class="em-od-address">
				<?php echo wp_kses_post( $order->get_formatted_billing_address( esc_html__( 'N/A', 'exmart' ) ) ); ?>

				<?php if ( $order->get_billing_phone() ) : ?>
					<p class="woocommerce-customer-details--phone em-od-contact"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5" d="M18.6763 19.9631C19.1917 19.9122 19.6399 19.6343 20.0011 19.254L21.4217 17.7584C22.3806 16.7489 22.1102 15.0182 20.8833 14.312L18.9728 13.2123C18.1672 12.7486 17.1858 12.8848 16.5562 13.5477L16.1007 14.0272C16.1007 14.0272 15.0181 15.167 12.0631 12.0559C9.10812 8.94484 10.1907 7.80507 10.1907 7.80507L10.4775 7.50311C11.1841 6.75924 11.2507 5.56497 10.6342 4.6931L9.37326 2.90961C8.61028 1.8305 7.13596 1.68795 6.26145 2.60864L4.69185 4.26114C4.25823 4.71766 3.96765 5.30945 4.00289 5.96594C4.09304 7.64546 4.81072 11.259 8.81536 15.4752C13.0621 19.9462 17.0468 20.1239 18.6763 19.9631Z"/></svg><span><?php echo esc_html( $order->get_billing_phone() ); ?></span></p>
				<?php endif; ?>

				<?php if ( $order->get_billing_email() ) : ?>
					<p class="woocommerce-customer-details--email em-od-contact"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M2 12C2 8.22876 2 6.34315 3.17157 5.17157C4.34315 4 6.22876 4 10 4H14C17.7712 4 19.6569 4 20.8284 5.17157C22 6.34315 22 8.22876 22 12C22 15.7712 22 17.6569 20.8284 18.8284C19.6569 20 17.7712 20 14 20H10C6.22876 20 4.34315 20 3.17157 18.8284C2 17.6569 2 15.7712 2 12Z"/><path d="M6 8L8.1589 9.79908C9.99553 11.3296 10.9139 12.0949 12 12.0949C13.0861 12.0949 14.0045 11.3296 15.8411 9.79908L18 8"/></g></svg><span><?php echo str_replace( '@', '<wbr>@', esc_html( $order->get_billing_email() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — escaped first; <wbr> lets long addresses wrap at the @. ?></span></p>
				<?php endif; ?>

				<?php do_action( 'woocommerce_order_details_after_customer_address', 'billing', $order ); ?>
			</address>
		</div>

		<?php if ( ! $is_single ) : ?>
			<div class="em-od-card">
				<h2 class="woocommerce-column__title em-od-card-title"><?php esc_html_e( 'Shipping address', 'exmart' ); ?></h2>
				<address class="em-od-address">
					<?php echo wp_kses_post( $order->get_formatted_shipping_address( esc_html__( 'N/A', 'exmart' ) ) ); ?>

					<?php if ( $order->get_shipping_phone() ) : ?>
						<p class="woocommerce-customer-details--phone em-od-contact"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5" d="M18.6763 19.9631C19.1917 19.9122 19.6399 19.6343 20.0011 19.254L21.4217 17.7584C22.3806 16.7489 22.1102 15.0182 20.8833 14.312L18.9728 13.2123C18.1672 12.7486 17.1858 12.8848 16.5562 13.5477L16.1007 14.0272C16.1007 14.0272 15.0181 15.167 12.0631 12.0559C9.10812 8.94484 10.1907 7.80507 10.1907 7.80507L10.4775 7.50311C11.1841 6.75924 11.2507 5.56497 10.6342 4.6931L9.37326 2.90961C8.61028 1.8305 7.13596 1.68795 6.26145 2.60864L4.69185 4.26114C4.25823 4.71766 3.96765 5.30945 4.00289 5.96594C4.09304 7.64546 4.81072 11.259 8.81536 15.4752C13.0621 19.9462 17.0468 20.1239 18.6763 19.9631Z"/></svg><span><?php echo esc_html( $order->get_shipping_phone() ); ?></span></p>
					<?php endif; ?>

					<?php do_action( 'woocommerce_order_details_after_customer_address', 'shipping', $order ); ?>
				</address>
			</div>
		<?php endif; ?>

	</div>

	<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>
</section>
