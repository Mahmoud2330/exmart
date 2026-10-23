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
			<svg width="36" height="36" viewBox="0 0 24 24" fill="none"><g fill="none" stroke="#fff" stroke-linecap="round" stroke-width="2"><path d="M19.0068 5L5.00684 19"/><path d="M19 19L5 5"/></g></svg>
		<?php else : ?>
			<svg width="40" height="40" viewBox="0 0 24 24" fill="none"><path fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13L9 17L19 7"/></svg>
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

		<span data-em-order-placed="<?php echo esc_attr( $order->get_order_number() ); ?>" hidden></span>

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

		<?php if ( $order->has_status( array( 'pending', 'processing', 'on-hold' ) ) ) : ?>
			<div class="em-order-cancel-row">
				<button type="button" class="em-btn em-btn-secondary em-order-cancel-btn" data-em-cancel-open aria-haspopup="dialog" aria-controls="em-cancel-sheet">
					<?php esc_html_e( 'Cancel order', 'exmart' ); ?>
				</button>
				<p class="em-caption"><?php esc_html_e( 'Made a mistake? We’ll connect you with customer service.', 'exmart' ); ?></p>
			</div>

			<div class="em-confirm em-cancel-sheet" id="em-cancel-sheet" hidden>
				<div class="em-confirm-backdrop" data-em-cancel-close></div>
				<div class="em-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="em-cancel-title" tabindex="-1">
					<button type="button" class="em-cancel-x" data-em-cancel-close aria-label="<?php esc_attr_e( 'Close', 'exmart' ); ?>"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M19.0068 5L5.00684 19"/><path d="M19 19L5 5"/></g></svg></button>
					<h2 id="em-cancel-title" class="em-h4">
						<?php
						printf(
							/* translators: %s: order number */
							esc_html__( 'Need to cancel order #%s?', 'exmart' ),
							esc_html( $order->get_order_number() )
						);
						?>
					</h2>
					<p class="em-body-s em-cancel-text"><?php esc_html_e( 'Cancellations are handled by our customer service team so nothing gets missed. Call us and mention your order number — we’ll take care of it right away.', 'exmart' ); ?></p>

					<a class="em-cancel-number" href="tel:<?php echo esc_attr( exmart_phone_tel() ); ?>">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5" d="M18.6763 19.9631C19.1917 19.9122 19.6399 19.6343 20.0011 19.254L21.4217 17.7584C22.3806 16.7489 22.1102 15.0182 20.8833 14.312L18.9728 13.2123C18.1672 12.7486 17.1858 12.8848 16.5562 13.5477L16.1007 14.0272C16.1007 14.0272 15.0181 15.167 12.0631 12.0559C9.10812 8.94484 10.1907 7.80507 10.1907 7.80507L10.4775 7.50311C11.1841 6.75924 11.2507 5.56497 10.6342 4.6931L9.37326 2.90961C8.61028 1.8305 7.13596 1.68795 6.26145 2.60864L4.69185 4.26114C4.25823 4.71766 3.96765 5.30945 4.00289 5.96594C4.09304 7.64546 4.81072 11.259 8.81536 15.4752C13.0621 19.9462 17.0468 20.1239 18.6763 19.9631Z"/></svg>
						<span><?php echo esc_html( exmart_phone_display() ); ?></span>
					</a>

					<div class="em-confirm-actions">
						<a class="em-btn em-btn-primary em-btn-lg" href="tel:<?php echo esc_attr( exmart_phone_tel() ); ?>">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5" d="M18.6763 19.9631C19.1917 19.9122 19.6399 19.6343 20.0011 19.254L21.4217 17.7584C22.3806 16.7489 22.1102 15.0182 20.8833 14.312L18.9728 13.2123C18.1672 12.7486 17.1858 12.8848 16.5562 13.5477L16.1007 14.0272C16.1007 14.0272 15.0181 15.167 12.0631 12.0559C9.10812 8.94484 10.1907 7.80507 10.1907 7.80507L10.4775 7.50311C11.1841 6.75924 11.2507 5.56497 10.6342 4.6931L9.37326 2.90961C8.61028 1.8305 7.13596 1.68795 6.26145 2.60864L4.69185 4.26114C4.25823 4.71766 3.96765 5.30945 4.00289 5.96594C4.09304 7.64546 4.81072 11.259 8.81536 15.4752C13.0621 19.9462 17.0468 20.1239 18.6763 19.9631Z"/></svg>
							<?php esc_html_e( 'Call now', 'exmart' ); ?>
						</a>
						<button type="button" class="em-btn em-btn-secondary em-btn-lg" data-em-copy="<?php echo esc_attr( exmart_phone_tel() ); ?>">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M6 11C6 8.17157 6 6.75736 6.87868 5.87868C7.75736 5 9.17157 5 12 5H15C17.8284 5 19.2426 5 20.1213 5.87868C21 6.75736 21 8.17157 21 11V16C21 18.8284 21 20.2426 20.1213 21.1213C19.2426 22 17.8284 22 15 22H12C9.17157 22 7.75736 22 6.87868 21.1213C6 20.2426 6 18.8284 6 16V11Z"/><path d="M6 19C4.34315 19 3 17.6569 3 16V10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H15C16.6569 2 18 3.34315 18 5"/></g></svg>
							<span data-em-copy-label><?php esc_html_e( 'Copy number', 'exmart' ); ?></span>
						</button>
					</div>
					<p class="em-caption em-cancel-order-no">
						<?php
						printf(
							/* translators: %s: order number */
							esc_html__( 'Keep your order number handy: #%s', 'exmart' ),
							esc_html( $order->get_order_number() )
						);
						?>
					</p>
				</div>
			</div>
		<?php endif; ?>

	<?php endif; ?>

	<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
	<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

</div>
