<?php
/**
 * Checkout Form — Figma 4-step wizard (Contact → Address → Shipping → Payment)
 * with sticky Order Summary.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package exMart
 * @version 1.0.67
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'exmart' ) ) );
	return;
}

$steps = array(
	'contact'  => __( 'Contact', 'exmart' ),
	'address'  => __( 'Address', 'exmart' ),
	'shipping' => __( 'Shipping', 'exmart' ),
	'payment'  => __( 'Payment', 'exmart' ),
);
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout em-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'exmart' ); ?>" data-em-checkout-steps>

	<div class="em-pipeline em-checkout-pipeline" role="list" aria-label="<?php esc_attr_e( 'Checkout steps', 'exmart' ); ?>">
		<?php
		$i = 0;
		foreach ( $steps as $id => $label ) :
			$current = ( 0 === $i );
			?>
			<div class="em-pipeline-step<?php echo $current ? '' : ''; ?>" data-step-id="<?php echo esc_attr( $id ); ?>" role="listitem">
				<button type="button" class="em-pipeline-dot<?php echo $current ? ' current' : ''; ?>" data-em-checkout-goto="<?php echo esc_attr( $id ); ?>" aria-label="<?php echo esc_attr( $label ); ?>" <?php echo $current ? '' : 'disabled'; ?>>
				</button>
				<span class="em-pipeline-label<?php echo $current ? ' current' : ''; ?>"><?php echo esc_html( $label ); ?></span>
			</div>
			<?php
			++$i;
		endforeach;
		?>
	</div>

	<div class="em-checkout-layout em-two-col">
		<div class="em-checkout-main">

			<?php if ( $checkout->get_checkout_fields() ) : ?>

				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

				<div id="customer_details" class="em-checkout-customer">
					<?php do_action( 'woocommerce_checkout_billing' ); ?>
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			<?php endif; ?>

			<section class="em-checkout-step" data-step="shipping" hidden>
				<h2 class="em-h4 em-checkout-step-title"><?php esc_html_e( 'Shipping Method', 'exmart' ); ?></h2>
				<div class="em-checkout-shipping-methods">
					<?php
					if ( function_exists( 'exmart_checkout_shipping_methods' ) ) {
						exmart_checkout_shipping_methods();
					}
					?>
				</div>
			</section>

			<section class="em-checkout-step" data-step="payment" hidden>
				<h2 class="em-h4 em-checkout-step-title"><?php esc_html_e( 'Payment Method', 'exmart' ); ?></h2>
				<?php do_action( 'exmart_checkout_payment_step' ); ?>
			</section>

			<div class="em-checkout-nav">
				<button type="button" class="em-btn em-btn-primary em-btn-lg em-checkout-continue">
					<?php esc_html_e( 'Continue', 'exmart' ); ?>
				</button>
			</div>

		</div>

		<aside class="em-checkout-aside">
			<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
			<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

			<div id="order_review" class="woocommerce-checkout-review-order em-checkout-summary-wrap">
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			</div>

			<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
		</aside>
	</div>

</form>

<div class="em-confirm" id="em-confirm" hidden>
	<div class="em-confirm-backdrop" data-em-confirm-cancel></div>
	<div class="em-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="em-confirm-title" tabindex="-1">
		<h2 id="em-confirm-title" class="em-h4"><?php esc_html_e( 'Double-check your order', 'exmart' ); ?></h2>

		<div class="em-confirm-dup" data-em-confirm-dup role="alert" hidden>
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M5.31171 10.7615C8.23007 5.58716 9.68925 3 12 3C14.3107 3 15.7699 5.58716 18.6883 10.7615L19.0519 11.4063C21.4771 15.7061 22.6897 17.856 21.5937 19.428C20.4978 21 17.7864 21 12.3637 21H11.6363C6.21356 21 3.50217 21 2.40626 19.428C1.31034 17.856 2.52291 15.7061 4.94805 11.4063L5.31171 10.7615Z"/><path d="M12 8V13"/><path stroke-linejoin="round" d="M12 16H12.0001"/></g></svg>
			<p>
				<strong><?php esc_html_e( 'Did you already order?', 'exmart' ); ?></strong>
				<?php esc_html_e( 'You placed order', 'exmart' ); ?> #<span data-em-confirm-dup-n></span> <?php esc_html_e( 'a few minutes ago. Only continue if you really want a second order — each order is shipped and charged separately.', 'exmart' ); ?>
			</p>
		</div>

		<p class="em-body-s em-confirm-sub" data-em-confirm-sub-timer>
			<?php esc_html_e( 'Your order will be placed automatically in', 'exmart' ); ?>
			<strong><span data-em-confirm-secs>12</span><?php esc_html_e( 's', 'exmart' ); ?></strong>.
			<?php esc_html_e( 'Take a moment to check everything below.', 'exmart' ); ?>
		</p>
		<p class="em-body-s em-confirm-sub" data-em-confirm-sub-dup hidden>
			<?php esc_html_e( 'We won’t place this one automatically. Please confirm it isn’t a duplicate.', 'exmart' ); ?>
		</p>
		<div class="em-confirm-progress" role="progressbar" aria-label="<?php esc_attr_e( 'Time until your order is placed', 'exmart' ); ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-em-confirm-bar>
			<span class="em-confirm-progress-fill" data-em-confirm-fill></span>
		</div>

		<div class="em-confirm-body">
			<ul class="em-confirm-items" data-em-confirm-items></ul>
			<dl class="em-confirm-meta">
				<div>
					<dt><?php esc_html_e( 'Deliver to', 'exmart' ); ?></dt>
					<dd data-em-confirm-address></dd>
				</div>
				<div>
					<dt><?php esc_html_e( 'Phone', 'exmart' ); ?></dt>
					<dd data-em-confirm-phone></dd>
				</div>
				<div>
					<dt><?php esc_html_e( 'Payment', 'exmart' ); ?></dt>
					<dd data-em-confirm-payment></dd>
				</div>
			</dl>
			<div class="em-confirm-total">
				<span><?php esc_html_e( 'Total', 'exmart' ); ?></span>
				<strong data-em-confirm-total></strong>
			</div>
		</div>

		<div class="em-confirm-terms">
			<p class="em-confirm-terms-title"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 17V11"/><path stroke-linejoin="round" d="M12 8H12.0001"/></g></svg><?php esc_html_e( 'Before you confirm', 'exmart' ); ?></p>
			<ul>
				<li><?php esc_html_e( 'Once you confirm, your order goes straight to our team — you won’t be able to change the items, address or phone number online.', 'exmart' ); ?></li>
				<li><?php esc_html_e( 'Need a change after ordering? Call customer service right away.', 'exmart' ); ?></li>
				<li><?php esc_html_e( 'Please don’t place the same order twice — each order is shipped and charged separately.', 'exmart' ); ?></li>
			</ul>
		</div>

		<div class="em-confirm-actions">
			<button type="button" class="em-btn em-btn-secondary em-btn-lg" data-em-confirm-cancel><?php esc_html_e( 'Edit my order', 'exmart' ); ?></button>
			<button type="button" class="em-btn em-btn-primary em-btn-lg" data-em-confirm-now data-label-normal="<?php esc_attr_e( 'Yes, place my order', 'exmart' ); ?>" data-label-dup="<?php esc_attr_e( 'Yes, place another order', 'exmart' ); ?>"><?php esc_html_e( 'Yes, place my order', 'exmart' ); ?></button>
		</div>
	</div>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
