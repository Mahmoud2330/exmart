<?php
/**
 * Order confirmation — big blue check + confetti burst so a completed
 * purchase is unmistakable (the default page gave no clear "it worked" cue).
 *
 * @see woocommerce/templates/checkout/thankyou.php
 * @package exMart
 * @version 1.0.89
 */

defined( 'ABSPATH' ) || exit;

if ( ! $order ) {
	return;
}

$is_failed = $order->has_status( 'failed' );
?>
<div class="woocommerce-order em-order-confirm<?php echo $is_failed ? ' em-order-confirm--failed' : ''; ?>">

	<?php if ( ! $is_failed ) : ?>
		<div class="em-order-confirm-confetti" aria-hidden="true">
			<?php for ( $i = 0; $i < 16; $i++ ) : ?>
				<span style="--i:<?php echo (int) $i; ?>"></span>
			<?php endfor; ?>
		</div>
	<?php endif; ?>

	<div class="em-order-confirm-icon<?php echo $is_failed ? ' em-order-confirm-icon--failed' : ''; ?>" aria-hidden="true">
		<?php if ( $is_failed ) : ?>
			<svg width="36" height="36" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6l12 12" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/></svg>
		<?php else : ?>
			<svg width="40" height="40" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
		<?php endif; ?>
	</div>

	<?php if ( $is_failed ) : ?>

		<h1 class="em-h2 em-order-confirm-title"><?php esc_html_e( 'Payment failed', 'exmart' ); ?></h1>

		<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed em-order-confirm-sub">
			<?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'exmart' ); ?>
		</p>

		<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions em-order-confirm-actions">
			<?php if ( $order->needs_payment() ) : ?>
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="em-btn em-btn-primary"><?php esc_html_e( 'Pay', 'exmart' ); ?></a>
			<?php endif; ?>
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="em-btn em-btn-secondary"><?php esc_html_e( 'My account', 'exmart' ); ?></a>
			<?php endif; ?>
		</p>

	<?php else : ?>

		<h1 class="em-h2 em-order-confirm-title"><?php esc_html_e( 'Order confirmed!', 'exmart' ); ?></h1>

		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received em-order-confirm-sub">
			<?php
			echo wp_kses_post(
				apply_filters(
					'woocommerce_thankyou_order_received_text',
					__( 'Thank you — your order has been received and is now being processed.', 'exmart' ),
					$order
				)
			);
			?>
		</p>

		<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details em-order-overview">

			<li class="woocommerce-order-overview__order order">
				<?php esc_html_e( 'Order number:', 'exmart' ); ?>
				<strong><?php echo esc_html( $order->get_order_number() ); ?></strong>
			</li>

			<li class="woocommerce-order-overview__date date">
				<?php esc_html_e( 'Date:', 'exmart' ); ?>
				<strong><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></strong>
			</li>

			<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
				<li class="woocommerce-order-overview__email email">
					<?php esc_html_e( 'Email:', 'exmart' ); ?>
					<strong><?php echo esc_html( $order->get_billing_email() ); ?></strong>
				</li>
			<?php endif; ?>

			<li class="woocommerce-order-overview__total total">
				<?php esc_html_e( 'Total:', 'exmart' ); ?>
				<strong><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong>
			</li>

			<?php if ( $order->get_payment_method_title() ) : ?>
				<li class="woocommerce-order-overview__payment-method method">
					<?php esc_html_e( 'Payment method:', 'exmart' ); ?>
					<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
				</li>
			<?php endif; ?>

		</ul>

	<?php endif; ?>

	<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
	<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

</div>
