<?php
/**
 * Template Name: exMart — Payment Methods
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$methods = array(
	array( 'name' => 'Cash on Delivery (COD)', 'desc' => __( 'Pay in cash when your order arrives. Available across all governorates. No surcharge.', 'exmart' ) ),
	array( 'name' => 'Fawry', 'desc' => __( 'Pay at any Fawry point across Egypt using your order reference number. Payment valid for 24 hours.', 'exmart' ) ),
	array( 'name' => 'Meeza', 'desc' => __( 'Egyptian national debit card — accepted directly at checkout via our secure payment gateway.', 'exmart' ) ),
	array( 'name' => 'Credit / Debit Card', 'desc' => __( 'Visa and Mastercard accepted. Payments are processed via a PCI-DSS-compliant gateway. We do not store card numbers.', 'exmart' ) ),
	array( 'name' => 'InstaPay', 'desc' => __( 'Send a bank transfer via InstaPay to our registered number. Your order will be confirmed within 30 minutes during business hours.', 'exmart' ) ),
);
?>
<div class="em-container em-page-narrow" style="padding-block: var(--s12);">
	<?php exmart_breadcrumb( array( array( 'label' => __( 'Home', 'exmart' ), 'href' => home_url( '/' ) ), array( 'label' => __( 'Payment Methods', 'exmart' ) ) ) ); ?>
	<h1 class="em-h1" style="margin-bottom:var(--s8);"><?php esc_html_e( 'Payment Methods', 'exmart' ); ?></h1>
	<div style="display:flex;flex-direction:column;gap:var(--s4);">
		<?php foreach ( $methods as $m ) : ?>
			<div class="em-payment-method-card">
				<p><?php echo esc_html( $m['name'] ); ?></p>
				<p class="em-body-s" style="color:var(--ink-500);"><?php echo esc_html( $m['desc'] ); ?></p>
			</div>
		<?php endforeach; ?>
	</div>
	<p class="em-body-s" style="color:var(--ink-400);margin-top:var(--s8);">
		<?php esc_html_e( 'Note for the store owner: enable/configure the matching WooCommerce payment gateways under WooCommerce → Settings → Payments so these methods actually work at checkout (COD is built in; Fawry/Meeza/InstaPay/cards need an Egypt-compatible gateway plugin such as Paymob, Fawaterak, or Kashier).', 'exmart' ); ?>
	</p>
</div>
<?php get_footer(); ?>
