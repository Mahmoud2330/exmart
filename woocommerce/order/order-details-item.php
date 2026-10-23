<?php
/**
 * Order item row — thumbnail, name × qty, line total (themed list row).
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package exMart
 * @version 5.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
	return;
}

$is_visible        = $product && $product->is_visible();
$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );
$qty               = $item->get_quantity();
$refunded_qty      = $order->get_qty_refunded_for_item( $item_id );
$qty_display       = $refunded_qty ? '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>' : esc_html( $qty );
?>
<li class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'woocommerce-table__line-item order_item em-od-item', $item, $order ) ); ?>">
	<span class="em-od-thumb"><?php echo $product ? $product->get_image( 'woocommerce_thumbnail' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>

	<div class="em-od-info">
		<span class="em-od-name">
			<?php echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $item->get_name() ) : $item->get_name(), $item, $is_visible ) ); ?>
		</span>
		<?php
		echo apply_filters( 'woocommerce_order_item_quantity_html', ' <span class="em-od-qty product-quantity">' . sprintf( '&times;&nbsp;%s', $qty_display ) . '</span>', $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

		wc_display_item_meta( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
		?>
	</div>

	<span class="em-od-price"><?php echo $order->get_formatted_line_subtotal( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
</li>

<?php if ( $show_purchase_note && $purchase_note ) : ?>
	<li class="woocommerce-table__product-purchase-note product-purchase-note em-od-note">
		<?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</li>
<?php endif; ?>
