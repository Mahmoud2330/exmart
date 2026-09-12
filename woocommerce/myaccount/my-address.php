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
			?>
			<div class="em-address-card woocommerce-Address">
				<header class="em-address-card-header">
					<h3 class="em-address-card-title"><?php echo esc_html( $title ); ?></h3>
					<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="em-btn em-btn-secondary em-btn-sm">
						<?php echo $address ? esc_html__( 'Edit', 'exmart' ) : esc_html__( 'Add', 'exmart' ); ?>
					</a>
				</header>
				<address class="em-address-card-body">
					<?php
					echo $address ? wp_kses_post( $address ) : esc_html__( 'You have not set up this type of address yet.', 'exmart' );
					?>
				</address>
			</div>
		<?php endforeach; ?>
	</div>
</div>
