<?php
/**
 * Profile — Figma Account “Profile” tab.
 *
 * @package exmart
 */

defined( 'ABSPATH' ) || exit;

$user = wp_get_current_user();
?>
<div class="em-account-panel em-account-profile">
	<h2 class="em-h4 em-account-panel-title"><?php esc_html_e( 'Profile', 'exmart' ); ?></h2>

	<form class="woocommerce-EditAccountForm edit-account em-auth-form em-profile-form" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?>>

		<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

		<div class="em-form-row">
			<p class="em-input-wrap woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
				<label class="em-input-label" for="account_first_name"><?php esc_html_e( 'First name', 'exmart' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="text" class="em-input woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" required />
			</p>
			<p class="em-input-wrap woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
				<label class="em-input-label" for="account_last_name"><?php esc_html_e( 'Last name', 'exmart' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="text" class="em-input woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" required />
			</p>
		</div>
		<div class="clear"></div>

		<p class="em-input-wrap woocommerce-form-row form-row form-row-wide">
			<label class="em-input-label" for="account_display_name"><?php esc_html_e( 'Display name', 'exmart' ); ?>&nbsp;<span class="required">*</span></label>
			<input type="text" class="em-input woocommerce-Input input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" required />
			<span class="em-helper"><?php esc_html_e( 'This is how your name will appear on orders and reviews.', 'exmart' ); ?></span>
		</p>

		<p class="em-input-wrap woocommerce-form-row form-row">
			<label class="em-input-label" for="account_email"><?php esc_html_e( 'Email', 'exmart' ); ?>&nbsp;<span class="required">*</span></label>
			<input type="email" class="em-input woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" required />
		</p>

		<p class="em-input-wrap woocommerce-form-row form-row">
			<label class="em-input-label" for="billing_phone"><?php esc_html_e( 'Phone', 'exmart' ); ?></label>
			<input type="tel" class="em-input woocommerce-Input input-text" name="billing_phone" id="billing_phone" autocomplete="tel" value="<?php echo esc_attr( get_user_meta( $user->ID, 'billing_phone', true ) ); ?>" placeholder="<?php esc_attr_e( '+20 10 0000 0000', 'exmart' ); ?>" />
		</p>

		<p class="em-profile-actions">
			<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
			<button type="submit" class="em-btn em-btn-primary woocommerce-Button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'exmart' ); ?>">
				<?php esc_html_e( 'Save changes', 'exmart' ); ?>
			</button>
			<input type="hidden" name="action" value="save_account_details" />
		</p>

		<hr class="em-rule" />

		<h3 class="em-h4"><?php esc_html_e( 'Change password', 'exmart' ); ?></h3>

		<p class="em-input-wrap woocommerce-form-row form-row">
			<label class="em-input-label" for="password_current"><?php esc_html_e( 'Current password', 'exmart' ); ?></label>
			<input type="password" class="em-input woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" autocomplete="current-password" />
		</p>
		<p class="em-input-wrap woocommerce-form-row form-row">
			<label class="em-input-label" for="password_1"><?php esc_html_e( 'New password', 'exmart' ); ?></label>
			<input type="password" class="em-input woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="new-password" />
		</p>
		<p class="em-input-wrap woocommerce-form-row form-row">
			<label class="em-input-label" for="password_2"><?php esc_html_e( 'Confirm new password', 'exmart' ); ?></label>
			<input type="password" class="em-input woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="new-password" />
		</p>

		<p class="em-profile-actions">
			<button type="submit" class="em-btn em-btn-secondary woocommerce-Button button" name="save_account_details" value="<?php esc_attr_e( 'Update password', 'exmart' ); ?>">
				<?php esc_html_e( 'Update password', 'exmart' ); ?>
			</button>
		</p>

		<?php do_action( 'woocommerce_edit_account_form' ); ?>
		<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
	</form>
</div>
