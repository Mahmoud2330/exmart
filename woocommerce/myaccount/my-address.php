<?php
/**
 * My Addresses — Figma “Saved Addresses”.
 *
 * @package exmart
 */

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

$get_addresses = apply_filters(
	'woocommerce_my_account_get_addresses',
	array(
		'billing'  => __( 'Billing address', 'exmart' ),
		'shipping' => __( 'Shipping address', 'exmart' ),
	),
	$customer_id
);
?>
<div class="em-account-panel">
	<h2 class="em-h4 em-account-panel-title"><?php esc_html_e( 'Saved Addresses', 'exmart' ); ?></h2>

	<div class="em-account-addresses">
		<?php foreach ( $get_addresses as $name => $title ) :
			$address = wc_get_account_formatted_address( $name );
			$edit_url = wc_get_endpoint_url( 'edit-address', $name );
			?>
			<div class="em-address-card woocommerce-Address">
				<?php if ( $address ) : ?>
					<p class="em-address-card-name"><?php echo esc_html( $title ); ?></p>
					<div class="em-address-card-body"><?php echo wp_kses_post( $address ); ?></div>
					<div class="em-address-card-actions">
						<a href="<?php echo esc_url( $edit_url ); ?>" class="em-btn em-btn-secondary em-btn-sm"><?php esc_html_e( 'Edit', 'exmart' ); ?></a>
					</div>
				<?php else : ?>
					<p class="em-address-card-name"><?php echo esc_html( $title ); ?></p>
					<p class="em-body-s" style="color:var(--ink-500);margin:0 0 var(--s3);"><?php esc_html_e( 'Not set up yet.', 'exmart' ); ?></p>
					<a href="<?php echo esc_url( $edit_url ); ?>" class="em-btn em-btn-secondary em-btn-sm"><?php esc_html_e( 'Add', 'exmart' ); ?></a>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>

	<p style="margin-top:var(--s5);">
		<a class="em-btn em-btn-secondary" href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', 'billing' ) ); ?>">
			<?php esc_html_e( '+ Add new address', 'exmart' ); ?>
		</a>
	</p>
</div>
