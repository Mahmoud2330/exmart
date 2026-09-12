<?php
/**
 * Template Name: exMart — Register
 * Separate Create account page (Figma), not the combined My Account form.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_user_logged_in() ) {
	$dest = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
	wp_safe_redirect( $dest );
	exit;
}

get_header();

$login_url = function_exists( 'exmart_login_url' ) ? exmart_login_url() : home_url( '/login/' );
$full_name = isset( $_POST['exmart_full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['exmart_full_name'] ) ) : '';
$email     = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
$phone     = isset( $_POST['exmart_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['exmart_phone'] ) ) : '';
?>
<div class="em-container em-auth">
	<?php
	exmart_breadcrumb(
		array(
			array( 'label' => __( 'Home', 'exmart' ), 'href' => home_url( '/' ) ),
			array( 'label' => __( 'Register', 'exmart' ) ),
		)
	);
	?>
	<h1 class="em-h1 em-auth-title"><?php esc_html_e( 'Create account', 'exmart' ); ?></h1>

	<?php
	if ( function_exists( 'wc_print_notices' ) ) {
		wc_print_notices();
	}
	?>

	<form class="em-auth-form woocommerce-form woocommerce-form-register register" method="post" novalidate>
		<p class="em-input-wrap">
			<label class="em-input-label" for="em-reg-name"><?php esc_html_e( 'Full name', 'exmart' ); ?></label>
			<input
				class="em-input"
				type="text"
				name="exmart_full_name"
				id="em-reg-name"
				autocomplete="name"
				required
				placeholder="<?php esc_attr_e( 'Ahmed Hassan', 'exmart' ); ?>"
				value="<?php echo esc_attr( $full_name ); ?>"
			/>
		</p>
		<p class="em-input-wrap">
			<label class="em-input-label" for="em-reg-email"><?php esc_html_e( 'Email', 'exmart' ); ?></label>
			<input
				class="em-input"
				type="email"
				name="email"
				id="em-reg-email"
				autocomplete="email"
				required
				value="<?php echo esc_attr( $email ); ?>"
			/>
		</p>
		<p class="em-input-wrap">
			<label class="em-input-label" for="em-reg-password"><?php esc_html_e( 'Password', 'exmart' ); ?></label>
			<input
				class="em-input"
				type="password"
				name="password"
				id="em-reg-password"
				autocomplete="new-password"
				required
				minlength="8"
			/>
			<span class="em-helper"><?php esc_html_e( 'Minimum 8 characters.', 'exmart' ); ?></span>
		</p>
		<p class="em-input-wrap">
			<label class="em-input-label" for="em-reg-phone"><?php esc_html_e( 'Phone', 'exmart' ); ?></label>
			<input
				class="em-input"
				type="tel"
				name="exmart_phone"
				id="em-reg-phone"
				autocomplete="tel"
				required
				placeholder="<?php esc_attr_e( '+20 10 0000 0000', 'exmart' ); ?>"
				value="<?php echo esc_attr( $phone ); ?>"
			/>
		</p>

		<?php exmart_social_auth_block( 'register' ); ?>

		<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>

		<button type="submit" class="em-btn em-btn-primary em-btn-lg em-auth-submit" name="register" value="<?php esc_attr_e( 'Register', 'exmart' ); ?>">
			<?php esc_html_e( 'Create account', 'exmart' ); ?>
		</button>
	</form>

	<p class="em-auth-switch">
		<?php esc_html_e( 'Already have an account?', 'exmart' ); ?>
		<a href="<?php echo esc_url( $login_url ); ?>"><?php esc_html_e( 'Log in', 'exmart' ); ?></a>
	</p>
</div>
<?php
get_footer();
