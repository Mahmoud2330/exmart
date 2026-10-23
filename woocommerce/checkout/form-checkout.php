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
		<p class="em-body-s em-confirm-sub">
			<?php esc_html_e( 'We’ll place it automatically in', 'exmart' ); ?>
			<strong><span data-em-confirm-secs>12</span><?php esc_html_e( 's', 'exmart' ); ?></strong>
			<?php esc_html_e( '— last chance to make changes.', 'exmart' ); ?>
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

		<div class="em-confirm-actions">
			<button type="button" class="em-btn em-btn-secondary em-btn-lg" data-em-confirm-cancel><?php esc_html_e( 'Edit my order', 'exmart' ); ?></button>
			<button type="button" class="em-btn em-btn-primary em-btn-lg" data-em-confirm-now><?php esc_html_e( 'Place order now', 'exmart' ); ?></button>
		</div>
	</div>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
