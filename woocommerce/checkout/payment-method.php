<?php
/**
 * Single payment method — radio card (Figma).
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package exMart
 * @version 1.0.67
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?> em-checkout-pay-card">
	<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />

	<label class="em-checkout-pay-label" for="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
		<span class="em-checkout-pay-title"><?php echo $gateway->get_title(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<?php echo $gateway->get_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</label>

	<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php if ( ! $gateway->chosen ) : ?>style="display:none;"<?php endif; ?>>
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>
</li>
