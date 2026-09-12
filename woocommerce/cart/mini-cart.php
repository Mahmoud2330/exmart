<?php
/**
 * Mini-cart contents — drawer body.
 * Buttons use stable em-mini-cart-* classes so styling does not depend on
 * the body.woocommerce class (absent on Home/Brand/etc.).
 *
 * @see woocommerce/templates/cart/mini-cart.php
 * @package exMart
 * @version 1.0.60
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' );
?>

<?php if ( WC()->cart && ! WC()->cart->is_empty() ) : ?>

	<ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ?? '' ); ?>">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
			$visible    = apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key );

			if ( ! ( $_product instanceof WC_Product ) || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! $visible ) {
				continue;
			}

			$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
			$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
			$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
			$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
			?>
			<li class="woocommerce-mini-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
				<?php
				echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'woocommerce_cart_item_remove_link',
					sprintf(
						'<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
						esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
						esc_attr( sprintf( __( 'Remove %s from cart', 'exmart' ), wp_strip_all_tags( $product_name ) ) ),
						esc_attr( (string) $product_id ),
						esc_attr( $cart_item_key ),
						esc_attr( $_product->get_sku() )
					),
					$cart_item_key
				);
				?>
				<?php if ( empty( $product_permalink ) ) : ?>
					<?php echo $thumbnail . wp_kses_post( $product_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php else : ?>
					<a href="<?php echo esc_url( $product_permalink ); ?>">
						<?php echo $thumbnail . wp_kses_post( $product_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				<?php endif; ?>
				<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</li>
			<?php
		}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul>

	<div class="em-mini-cart-footer">
		<p class="woocommerce-mini-cart__total total">
			<?php do_action( 'woocommerce_widget_shopping_cart_total' ); ?>
		</p>
		<p class="woocommerce-mini-cart__buttons buttons em-mini-cart-buttons">
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="em-mini-cart-btn em-mini-cart-btn--view button wc-forward">
				<?php esc_html_e( 'View cart', 'woocommerce' ); ?>
			</a>
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="em-mini-cart-btn em-mini-cart-btn--checkout button checkout wc-forward">
				<?php esc_html_e( 'Checkout', 'woocommerce' ); ?>
			</a>
		</p>
	</div>

<?php else : ?>

	<p class="woocommerce-mini-cart__empty-message em-mini-cart-empty">
		<?php esc_html_e( 'No products in the cart.', 'woocommerce' ); ?>
	</p>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
