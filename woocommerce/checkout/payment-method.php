<?php
/**
 * Single payment method — Figma radio card.
 *
 * FawryPay uses our Media Library logos (Visa/MC, Meeza, Fawry) instead of
 * the plugin’s tiny icon sprites.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package exMart
 * @version 1.0.69
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title     = $gateway->get_title();
$desc      = $gateway->get_description();
$desc_text = $desc ? wp_strip_all_tags( $desc ) : '';
$is_cod    = ( 'cod' === $gateway->id );
$is_fawry  = ( 'fawry_pay' === $gateway->id || false !== stripos( (string) $gateway->id, 'fawry' ) );

$logos = array();
if ( $is_fawry && function_exists( 'exmart_media_url_by_filename' ) ) {
	$logo_files = array(
		array(
			'file'  => 'visa-and-mastercard-logos-logo-visa-png-logo-visa-mastercard-png-visa-logo-white-png-awesome-logos.png',
			'label' => 'Visa / Mastercard',
		),
		array(
			'file'  => 'Meeza-Digital-.png',
			'label' => 'Meeza',
		),
		array(
			'file'  => 'Fawry.png',
			'label' => 'Fawry',
		),
	);
	foreach ( $logo_files as $logo ) {
		$url = exmart_media_url_by_filename( $logo['file'] );
		if ( $url ) {
			$logos[] = array(
				'url'   => $url,
				'label' => $logo['label'],
			);
		}
	}
}

// COD: description is on the card. Fawry: keep plugin fields if it needs them to process.
$show_box = $gateway->has_fields() && ! $is_cod;
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?> em-checkout-pay-card<?php echo $is_fawry ? ' em-checkout-pay-card--fawry' : ''; ?>">
	<label class="em-checkout-pay-hit" for="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
		<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />

		<span class="em-checkout-pay-body">
			<span class="em-checkout-pay-title"><?php echo wp_kses_post( $title ); ?></span>
			<?php if ( $desc_text ) : ?>
				<span class="em-checkout-pay-desc"><?php echo esc_html( $desc_text ); ?></span>
			<?php endif; ?>

			<?php if ( $logos ) : ?>
				<span class="em-checkout-pay-logos" aria-hidden="true">
					<?php foreach ( $logos as $logo ) : ?>
						<span class="em-checkout-pay-logo">
							<img src="<?php echo esc_url( $logo['url'] ); ?>" alt="" width="72" height="28" loading="lazy" decoding="async" />
						</span>
					<?php endforeach; ?>
				</span>
			<?php endif; ?>
		</span>
	</label>

	<?php if ( $show_box ) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php if ( ! $gateway->chosen ) : ?>style="display:none;"<?php endif; ?>>
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>
</li>
