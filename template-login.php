<?php
/**
 * Template Name: exMart — Login
 * Separate Log in page (Figma), not the combined My Account form.
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

$register_url = function_exists( 'exmart_register_url' ) ? exmart_register_url() : home_url( '/register/' );
?>
<div class="em-container em-auth">
	<?php
	exmart_breadcrumb(
		array(
			array( 'label' => __( 'Home', 'exmart' ), 'href' => home_url( '/' ) ),
			array( 'label' => __( 'Login', 'exmart' ) ),
		)
	);
	?>
	<h1 class="em-h1 em-auth-title"><?php esc_html_e( 'Log in', 'exmart' ); ?></h1>

	<?php
	if ( function_exists( 'wc_print_notices' ) ) {
		wc_print_notices();
	}
	?>

	<form class="em-auth-form woocommerce-form woocommerce-form-login login" method="post" novalidate>
		<p class="em-input-wrap">
			<label class="em-input-label" for="em-login-username"><?php esc_html_e( 'Email', 'exmart' ); ?></label>
			<input
				class="em-input"
				type="text"
				name="username"
				id="em-login-username"
				autocomplete="username"
				required
				value="<?php echo ! empty( $_POST['username'] ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>"
			/>
		</p>
		<p class="em-input-wrap">
			<label class="em-input-label" for="em-login-password"><?php esc_html_e( 'Password', 'exmart' ); ?></label>
			<input
				class="em-input"
				type="password"
				name="password"
				id="em-login-password"
				autocomplete="current-password"
				required
			/>
		</p>

		<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
		<input type="hidden" name="redirect" value="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' ) ); ?>" />

		<button type="submit" class="em-btn em-btn-primary em-btn-lg em-auth-submit" name="login" value="<?php esc_attr_e( 'Log in', 'exmart' ); ?>">
			<?php esc_html_e( 'Log in', 'exmart' ); ?>
		</button>
	</form>

	<p class="em-auth-switch">
		<?php esc_html_e( 'No account?', 'exmart' ); ?>
		<a href="<?php echo esc_url( $register_url ); ?>"><?php esc_html_e( 'Register', 'exmart' ); ?></a>
	</p>
	<p class="em-auth-switch em-auth-switch--muted">
		<a href="<?php echo esc_url( function_exists( 'wc_lostpassword_url' ) ? wc_lostpassword_url() : wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'exmart' ); ?></a>
	</p>
</div>
<?php
get_footer();
