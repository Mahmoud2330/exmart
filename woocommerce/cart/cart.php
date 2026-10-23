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

			<?php
			$em_count     = WC()->cart->get_cart_contents_count();
			$em_threshold = function_exists( 'exmart_free_shipping_threshold' ) ? exmart_free_shipping_threshold() : 300.0;
			$em_subtotal  = (float) WC()->cart->get_displayed_subtotal();
			$em_left      = max( 0, $em_threshold - $em_subtotal );
			$em_pct       = $em_threshold > 0 ? (int) min( 100, round( ( $em_subtotal / $em_threshold ) * 100 ) ) : 100;
			?>
			<div class="em-cart-head">
				<h2 class="em-h4 em-cart-head-title">
					<?php esc_html_e( 'Your items', 'exmart' ); ?>
					<span class="em-cart-head-count"><?php echo esc_html( sprintf( /* translators: %d: item count */ _n( '%d item', '%d items', $em_count, 'exmart' ), $em_count ) ); ?></span>
				</h2>
				<a class="em-cart-head-link" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'Continue shopping', 'exmart' ); ?></a>
			</div>

			<?php if ( $em_threshold > 0 ) : ?>
				<div class="em-cart-ship<?php echo $em_left <= 0 ? ' is-unlocked' : ''; ?>">
					<span class="em-cart-ship-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M9.56541 8.73049C9.0804 6.97492 8.8379 6.09714 9.24954 5.40562C9.66119 4.71409 10.5662 4.47889 12.3763 4.00849L14.2962 3.50955C16.1062 3.03915 17.0113 2.80394 17.7242 3.20319C18.4372 3.60244 18.6797 4.48023 19.1647 6.2358L19.6792 8.09786C20.1642 9.85343 20.4067 10.7312 19.995 11.4227C19.5834 12.1143 18.6784 12.3495 16.8683 12.8199L14.9484 13.3188C13.1384 13.7892 12.2333 14.0244 11.5203 13.6252C10.8073 13.2259 10.5648 12.3481 10.0798 10.5926L9.56541 8.73049Z"/><path d="M8.23 15.0602L6.08 7.27014C5.89453 6.61732 5.38563 6.12036 4.7 5.92L3 5.4502"/><circle cx="8.05" cy="18.1" r="2.9"/><path d="M10.9199 17.36L19.9999 15"/></g></svg></span>
					<div class="em-cart-ship-body">
						<p class="em-cart-ship-text">
							<?php
							if ( $em_left <= 0 ) {
								esc_html_e( 'You’ve unlocked free shipping', 'exmart' );
							} else {
								printf(
									/* translators: %s: amount remaining for free shipping */
									esc_html__( 'Add %s more to get free shipping', 'exmart' ),
									'<strong>' . wp_kses_post( wc_price( $em_left ) ) . '</strong>'
								);
							}
							?>
						</p>
						<div class="em-cart-ship-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr( (string) $em_pct ); ?>" aria-label="<?php esc_attr_e( 'Progress to free shipping', 'exmart' ); ?>">
							<span style="width:<?php echo esc_attr( (string) $em_pct ); ?>%"></span>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<table class="shop_table cart woocommerce-cart-form__contents em-cart-table" cellspacing="0">
				<thead>
					<tr>
						<th class="product-name" scope="col"><?php esc_html_e( 'Product', 'exmart' ); ?></th>
						<th class="product-quantity" scope="col"><?php esc_html_e( 'Qty', 'exmart' ); ?></th>
						<th class="product-subtotal" scope="col"><?php esc_html_e( 'Price', 'exmart' ); ?></th>
						<th class="product-remove" scope="col"><span class="screen-reader-text"><?php esc_html_e( 'Remove item', 'exmart' ); ?></span></th>
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

							<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'exmart' ); ?>" role="rowheader">
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
										echo '<span class="em-cart-unit">' . wp_kses_post( apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ) ) . ' ' . esc_html__( 'each', 'exmart' ) . '</span>';
										echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										if ( $_product->managed_stock() ) {
											$em_stock = $_product->get_stock_quantity();
											if ( null !== $em_stock && $em_stock > 0 && $em_stock <= 5 ) {
												/* translators: %d: units left in stock */
												echo '<span class="em-cart-lowstock">' . esc_html( sprintf( __( 'Only %d left', 'exmart' ), $em_stock ) ) . '</span>';
											}
										}
										if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
											echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'exmart' ) . '</p>', $product_id ) );
										}
										?>
									</div>
								</div>
							</td>

							<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'exmart' ); ?>">
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

							<td class="product-subtotal" data-title="<?php esc_attr_e( 'Price', 'exmart' ); ?>">
								<?php
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
							</td>

							<td class="product-remove">
								<?php
								echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									'woocommerce_cart_item_remove_link',
									sprintf(
										'<a href="%s" class="remove em-cart-remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M9.1709 4C9.58273 2.83481 10.694 2 12.0002 2C13.3064 2 14.4177 2.83481 14.8295 4"/><path d="M20.5001 6H3.5"/><path d="M18.8332 8.5L18.3732 15.3991C18.1962 18.054 18.1077 19.3815 17.2427 20.1907C16.3777 21 15.0473 21 12.3865 21H11.6132C8.95235 21 7.62195 21 6.75694 20.1907C5.89194 19.3815 5.80344 18.054 5.62644 15.3991L5.1665 8.5"/><path d="M9.5 11L10 16"/><path d="M14.5 11L14 16"/></g></svg></a>',
										esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
										esc_attr( sprintf( __( 'Remove %s from cart', 'exmart' ), wp_strip_all_tags( $product_name ) ) ),
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
							<button type="submit" class="button em-cart-update-btn" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'exmart' ); ?>"><?php esc_html_e( 'Update cart', 'exmart' ); ?></button>
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
