<?php
/**
 * Single payment method — Figma radio card (title + description always visible).
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package exMart
 * @version 1.0.68
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = $gateway->get_title();
$desc  = $gateway->get_description();
// Strip tags for the always-visible caption; keep rich HTML in payment_box if needed.
$desc_text = $desc ? wp_strip_all_tags( $desc ) : '';
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?> em-checkout-pay-card">
	<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />

	<label class="em-checkout-pay-label" for="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
		<span class="em-checkout-pay-title"><?php echo wp_kses_post( $title ); ?></span>
		<?php if ( $desc_text ) : ?>
			<span class="em-checkout-pay-desc"><?php echo esc_html( $desc_text ); ?></span>
		<?php endif; ?>
		<?php
		$icon = $gateway->get_icon();
		if ( $icon ) :
			?>
			<span class="em-checkout-pay-icons"><?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<?php endif; ?>
	</label>

	<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php if ( ! $gateway->chosen ) : ?>style="display:none;"<?php endif; ?>>
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>
</li>
