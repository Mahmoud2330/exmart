<?php
/**
 * My Account navigation — Figma Account tabs.
 *
 * @package exmart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = wc_get_account_menu_items();
?>
<nav class="woocommerce-MyAccount-navigation em-account-nav" aria-label="<?php esc_attr_e( 'Account navigation', 'exmart' ); ?>">
	<ul>
		<?php foreach ( $items as $endpoint => $label ) : ?>
			<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
