<?php
/**
 * Order details — themed card (items + totals) for the order confirmation and
 * My Account → View order. Same data, hooks and actions as WooCommerce's table
 * version; only the markup changed so it matches the rest of the theme.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package exMart
 * @version 9.8.0
 *
 * @var bool $show_downloads Controls whether the downloads table should be rendered.
 */

// phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if ( ! $order ) {
	return;
}

$order_items        = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$downloads          = $order->get_downloadable_items();
$actions            = array_filter(
	wc_get_account_orders_actions( $order ),
	function ( $key ) {
		return 'view' !== $key;
	},
	ARRAY_FILTER_USE_KEY
);

// The order must belong to the current user (also true for a guest viewing their own guest order).
$show_customer_details = $order->get_user_id() === get_current_user_id();

if ( $show_downloads ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}
?>
<section class="woocommerce-order-details em-od">
	<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

	<h2 class="woocommerce-order-details__title em-od-title"><?php esc_html_e( 'Order details', 'exmart' ); ?></h2>

	<ul class="em-od-items">
		<?php
		do_action( 'woocommerce_order_details_before_order_table_items', $order );

		foreach ( $order_items as $item_id => $item ) {
			$product = $item->get_product();

			wc_get_template(
				'order/order-details-item.php',
				array(
					'order'              => $order,
					'item_id'            => $item_id,
					'item'               => $item,
					'show_purchase_note' => $show_purchase_note,
					'purchase_note'      => $product ? $product->get_purchase_note() : '',
					'product'            => $product,
				)
			);
		}

		do_action( 'woocommerce_order_details_after_order_table_items', $order );
		?>
	</ul>

	<div class="em-od-totals">
		<?php foreach ( $order->get_order_item_totals() as $key => $total ) : ?>
			<div class="em-od-row em-od-row--<?php echo esc_attr( sanitize_html_class( $key ) ); ?><?php echo 'order_total' === $key ? ' is-total' : ''; ?>">
				<span class="em-od-row-label"><?php echo esc_html( rtrim( $total['label'], ':' ) ); ?></span>
				<span class="em-od-row-value"><?php echo wp_kses_post( $total['value'] ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php if ( $order->get_customer_note() ) : ?>
			<div class="em-od-row em-od-row--note">
				<span class="em-od-row-label"><?php esc_html_e( 'Note', 'exmart' ); ?></span>
				<span class="em-od-row-value"><?php echo wp_kses( nl2br( wptexturize( $order->get_customer_note() ) ), array( 'br' => array() ) ); ?></span>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $actions ) ) : ?>
		<div class="em-od-actions">
			<?php
			$wp_button_class = wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '';
			foreach ( $actions as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				if ( empty( $action['aria-label'] ) ) {
					/* translators: %1$s Action name, %2$s Order number. */
					$action_aria_label = sprintf( __( '%1$s order number %2$s', 'exmart' ), $action['name'], $order->get_order_number() );
				} else {
					$action_aria_label = $action['aria-label'];
				}
				echo '<a href="' . esc_url( $action['url'] ) . '" class="woocommerce-button' . esc_attr( $wp_button_class ) . ' button em-btn em-btn-secondary ' . sanitize_html_class( $key ) . ' order-actions-button" aria-label="' . esc_attr( $action_aria_label ) . '">' . esc_html( $action['name'] ) . '</a>';
				unset( $action_aria_label );
			}
			?>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
</section>

<?php
do_action( 'woocommerce_after_order_details', $order );

if ( $show_customer_details ) {
	wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
}
