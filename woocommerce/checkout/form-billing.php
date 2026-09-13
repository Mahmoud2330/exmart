<?php
/**
 * Checkout billing — split into Contact + Address steps (Figma).
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package exMart
 * @version 1.0.67
 * @global WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;

$fields        = $checkout->get_checkout_fields( 'billing' );
$contact_keys  = array( 'billing_email', 'billing_phone' );
$contact_fields = array();
$address_fields = array();

foreach ( $fields as $key => $field ) {
	if ( in_array( $key, $contact_keys, true ) ) {
		$contact_fields[ $key ] = $field;
	} else {
		$address_fields[ $key ] = $field;
	}
}

$login_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
?>
<div class="woocommerce-billing-fields em-checkout-billing">

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<section class="em-checkout-step is-active" data-step="contact">
		<h2 class="em-h4 em-checkout-step-title"><?php esc_html_e( 'Contact Information', 'exmart' ); ?></h2>
		<div class="woocommerce-billing-fields__field-wrapper em-checkout-fields">
			<?php
			foreach ( $contact_fields as $key => $field ) {
				if ( 'billing_phone' === $key && empty( $field['description'] ) ) {
					$field['description'] = __( 'For delivery updates via SMS.', 'exmart' );
				}
				woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
			}
			?>
		</div>
		<?php if ( ! is_user_logged_in() ) : ?>
			<p class="em-caption em-checkout-login-hint">
				<?php esc_html_e( 'Already have an account?', 'exmart' ); ?>
				<a href="<?php echo esc_url( $login_url ); ?>"><?php esc_html_e( 'Log in', 'exmart' ); ?></a>
			</p>
		<?php endif; ?>
	</section>

	<section class="em-checkout-step" data-step="address" hidden>
		<h2 class="em-h4 em-checkout-step-title"><?php esc_html_e( 'Delivery Address', 'exmart' ); ?></h2>
		<div class="woocommerce-billing-fields__field-wrapper em-checkout-fields">
			<?php
			foreach ( $address_fields as $key => $field ) {
				woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
			}
			?>
		</div>
	</section>

	<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
</div>

<?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
	<div class="woocommerce-account-fields em-checkout-account-fields" data-em-checkout-with="address" hidden>
		<?php if ( ! $checkout->is_registration_required() ) : ?>
			<p class="form-row form-row-wide create-account">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1" />
					<span><?php esc_html_e( 'Create an account?', 'woocommerce' ); ?></span>
				</label>
			</p>
		<?php endif; ?>

		<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

		<?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>
			<div class="create-account">
				<?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>
					<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
				<?php endforeach; ?>
				<div class="clear"></div>
			</div>
		<?php endif; ?>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
	</div>
<?php endif; ?>
