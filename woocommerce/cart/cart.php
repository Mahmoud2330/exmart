<?php
/**
 * Cart Page — Figma “Your Cart” layout.
 *
 * Left: product rows (Product / Qty / Price / Remove)
 * Right: Order Summary + coupon + COD note + checkout CTA
 *
 * @see woocommerce/templates/cart/cart.php
 * @package exMart
 * @version 1.0.61
 */

defined( 'ABSPATH' ) || exit;

if ( WC()->cart && WC()->cart->is_empty() ) {
	wc_get_template( 'cart/cart-empty.php' );
	return;
}

do_action( 'woocommerce_before_cart' );
?>

<div class="em-cart-layout em-two-col">
	<div class="em-cart-main">
		<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<?php do_action( 'woocommerce_before_cart_table' ); ?>

			<table class="shop_table cart woocommerce-cart-form__contents em-cart-table" cellspacing="0">
				<thead>
					<tr>
						<th class="product-name" scope="col"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
						<th class="product-quantity" scope="col"><?php esc_html_e( 'Qty', 'exmart' ); ?></th>
						<th class="product-subtotal" scope="col"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
						<th class="product-remove" scope="col"><span class="screen-reader-text"><?php esc_html_e( 'Remove item', 'woocommerce' ); ?></span></th>
					</tr>
				</thead>
				<tbody>
					<?php do_action( 'woocommerce_before_cart_contents' ); ?>

					<?php
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
						$visible    = apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key );

						if ( ! ( $_product instanceof WC_Product ) || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! $visible ) {
							continue;
						}

						$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
						$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
						$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );

						$brand_name = '';
						$brands     = get_the_terms( $product_id, 'product_brand' );
						if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) {
							$brand_name = $brands[0]->name;
						}
						?>
						<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

							<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>" role="rowheader">
								<div class="em-cart-product">
									<div class="em-cart-thumb">
										<?php
										if ( ! $product_permalink ) {
											echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										} else {
											printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										}
										?>
									</div>
									<div class="em-cart-product-meta">
										<?php
										if ( ! $product_permalink ) {
											echo '<span class="em-cart-product-title">' . wp_kses_post( $product_name ) . '</span>';
										} else {
											printf(
												'<a class="em-cart-product-title" href="%s">%s</a>',
												esc_url( $product_permalink ),
												wp_kses_post( $product_name )
											);
										}
										if ( $brand_name ) {
											echo '<span class="em-caption em-cart-product-brand">' . esc_html( $brand_name ) . '</span>';
										}
										echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
											echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
										}
										?>
									</div>
								</div>
							</td>

							<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
								<?php
								if ( $_product->is_sold_individually() ) {
									$min_quantity = 1;
									$max_quantity = 1;
								} else {
									$min_quantity = 0;
									$max_quantity = $_product->get_max_purchase_quantity();
								}

								$product_quantity = woocommerce_quantity_input(
									array(
										'input_name'   => "cart[{$cart_item_key}][qty]",
										'input_value'  => $cart_item['quantity'],
										'max_value'    => $max_quantity,
										'min_value'    => $min_quantity,
										'product_name' => $product_name,
										'classes'      => array( 'input-text', 'qty', 'text', 'em-qty-input' ),
									),
									$_product,
									false
								);

								echo '<div class="em-qty em-cart-qty" data-em-cart-qty>';
								echo '<button type="button" class="em-qty-btn" data-em-cart-qty-minus aria-label="' . esc_attr__( 'Decrease quantity', 'exmart' ) . '">−</button>';
								echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo '<button type="button" class="em-qty-btn" data-em-cart-qty-plus aria-label="' . esc_attr__( 'Increase quantity', 'exmart' ) . '">+</button>';
								echo '</div>';
								?>
							</td>

							<td class="product-subtotal" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
								<?php
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
							</td>

							<td class="product-remove">
								<?php
								echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									'woocommerce_cart_item_remove_link',
									sprintf(
										'<a href="%s" class="remove em-cart-remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
										esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
										esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
										esc_attr( (string) $product_id ),
										esc_attr( $_product->get_sku() )
									),
									$cart_item_key
								);
								?>
							</td>
						</tr>
						<?php
					}
					?>

					<?php do_action( 'woocommerce_cart_contents' ); ?>

					<tr class="em-cart-actions-row">
						<td colspan="4" class="actions">
							<button type="submit" class="button em-cart-update-btn" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>
							<?php do_action( 'woocommerce_cart_actions' ); ?>
							<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
						</td>
					</tr>

					<?php do_action( 'woocommerce_after_cart_contents' ); ?>
				</tbody>
			</table>
			<?php do_action( 'woocommerce_after_cart_table' ); ?>
		</form>
	</div>

	<aside class="em-cart-aside">
		<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
		<div class="cart-collaterals">
			<?php
			/**
			 * Cart collaterals hook.
			 *
			 * @hooked woocommerce_cart_totals - 10
			 */
			do_action( 'woocommerce_cart_collaterals' );
			?>
		</div>
	</aside>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
