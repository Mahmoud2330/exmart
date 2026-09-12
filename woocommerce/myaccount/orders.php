<?php
/**
 * Orders — Figma Account “Order History” cards.
 *
 * @see woocommerce/templates/myaccount/orders.php
 * @package exmart
 */

defined( 'ABSPATH' ) || exit;

$current_page    = empty( $current_page ) ? 1 : absint( $current_page );
$customer_orders = wc_get_orders(
	apply_filters(
		'woocommerce_my_account_my_orders_query',
		array(
			'customer' => get_current_user_id(),
			'page'     => $current_page,
			'paginate' => true,
			'limit'    => 15,
		)
	)
);
?>

<div class="em-account-panel">
	<h2 class="em-h4 em-account-panel-title"><?php esc_html_e( 'Order History', 'exmart' ); ?></h2>

	<?php if ( $customer_orders && ! empty( $customer_orders->orders ) ) : ?>
		<div class="em-account-orders">
			<?php
			foreach ( $customer_orders->orders as $customer_order ) {
				$order = wc_get_order( $customer_order );
				if ( ! $order ) {
					continue;
				}
				$item_count  = $order->get_item_count() - $order->get_item_count_refunded();
				$status_meta = exmart_account_order_status_meta( $order->get_status() );
				$date        = $order->get_date_created() ? $order->get_date_created()->date_i18n( 'Y-m-d' ) : '';
				/* translators: %d: item count */
				$items_label = sprintf( _n( '%d item', '%d items', $item_count, 'exmart' ), $item_count );
				?>
				<article class="em-order-card">
					<div class="em-order-card-main">
						<p class="em-order-card-id">#<?php echo esc_html( $order->get_order_number() ); ?></p>
						<p class="em-caption em-order-card-meta"><?php echo esc_html( $date . ' · ' . $items_label ); ?></p>
					</div>
					<div class="em-order-card-aside">
						<span class="em-order-card-total"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
						<span class="em-badge em-order-status" style="background: color-mix(in srgb, <?php echo esc_attr( $status_meta['color'] ); ?> 14%, transparent); color: <?php echo esc_attr( $status_meta['color'] ); ?>;">
							<?php echo esc_html( strtoupper( $status_meta['label'] ) ); ?>
						</span>
						<a class="em-btn em-btn-secondary em-btn-sm" href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
							<?php esc_html_e( 'Track', 'exmart' ); ?>
						</a>
					</div>
				</article>
				<?php
			}
			?>
		</div>

		<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
			<nav class="em-account-orders-pagination woocommerce-pagination">
				<?php
				echo paginate_links(
					array(
						'base'      => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
						'format'    => '',
						'current'   => $current_page,
						'total'     => $customer_orders->max_num_pages,
						'prev_text' => '&larr;',
						'next_text' => '&rarr;',
						'type'      => 'list',
					)
				);
				?>
			</nav>
		<?php endif; ?>

	<?php else : ?>
		<div class="em-empty-state">
			<p class="em-body" style="color:var(--ink-500);margin-bottom:var(--s4);"><?php esc_html_e( 'No orders yet.', 'exmart' ); ?></p>
			<a class="em-btn em-btn-primary" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>">
				<?php esc_html_e( 'Discover products', 'exmart' ); ?>
			</a>
		</div>
	<?php endif; ?>
</div>
