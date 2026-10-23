<?php
/**
 * Mini-cart contents — drawer body.
 *
 * Item rows with a pill quantity stepper, a "Popular picks" row with a
 * round + quick-add, and a pinned total + Checkout footer. Buttons keep the
 * stable em-mini-cart-* classes so styling does not depend on body.woocommerce.
 *
 * @see woocommerce/templates/cart/mini-cart.php
 * @package exMart
 * @version 1.1.44
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' );

$em_cart       = WC()->cart;
$em_has_items  = $em_cart && ! $em_cart->is_empty();
$em_in_cart    = array();
if ( $em_cart ) {
	foreach ( $em_cart->get_cart() as $em_it ) {
		$em_in_cart[] = (int) $em_it['product_id'];
	}
}
$em_picks = function_exists( 'exmart_drawer_picks' ) ? exmart_drawer_picks( $em_in_cart, 8 ) : array();
$em_shop  = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>

<div class="em-mc-scroll">

<?php if ( $em_has_items ) : ?>

	<ul class="woocommerce-mini-cart cart_list product_list_widget em-mc-list <?php echo esc_attr( $args['list_class'] ?? '' ); ?>">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( $em_cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
			$visible    = apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key );

			if ( ! ( $_product instanceof WC_Product ) || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! $visible ) {
				continue;
			}

			$qty               = (int) $cart_item['quantity'];
			$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
			$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
			$unit_amount       = (float) wc_get_price_to_display( $_product );
			$line_amount       = (float) wc_get_price_to_display( $_product, array( 'qty' => $qty ) );
			$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );

			$max = $_product->is_sold_individually() ? 1 : (int) $_product->get_max_purchase_quantity();
			if ( $max < 1 ) {
				$max = 9999;
			}
			?>
			<li class="woocommerce-mini-cart-item mini_cart_item em-mc-item" data-em-mc-item>
				<?php if ( $product_permalink ) : ?>
					<a class="em-mc-thumb" href="<?php echo esc_url( $product_permalink ); ?>" tabindex="-1"><?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				<?php else : ?>
					<span class="em-mc-thumb"><?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<?php endif; ?>

				<div class="em-mc-info">
					<?php if ( $product_permalink ) : ?>
						<a class="em-mc-name" href="<?php echo esc_url( $product_permalink ); ?>"><?php echo wp_kses_post( $product_name ); ?></a>
					<?php else : ?>
						<span class="em-mc-name"><?php echo wp_kses_post( $product_name ); ?></span>
					<?php endif; ?>
					<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<div class="em-mc-price">
						<span class="em-mc-line"><?php echo exmart_price_lockup( $line_amount ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php if ( $qty > 1 ) : ?>
							<span class="em-mc-each"><?php echo exmart_price_lockup( $unit_amount ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php esc_html_e( 'each', 'exmart' ); ?></span>
						<?php endif; ?>
					</div>
				</div>

				<div class="em-mc-qty" data-em-mc-qty data-key="<?php echo esc_attr( $cart_item_key ); ?>" data-qty="<?php echo esc_attr( (string) $qty ); ?>" data-max="<?php echo esc_attr( (string) $max ); ?>">
					<button type="button" class="em-mc-qty-btn<?php echo 1 === $qty ? ' is-remove' : ''; ?>" data-em-mc-minus aria-label="<?php echo 1 === $qty ? esc_attr__( 'Remove from cart', 'exmart' ) : esc_attr__( 'Decrease quantity', 'exmart' ); ?>">
						<span class="em-mc-minus" aria-hidden="true">−</span>
						<span class="em-mc-bin" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M9.1709 4C9.58273 2.83481 10.694 2 12.0002 2C13.3064 2 14.4177 2.83481 14.8295 4"/><path d="M20.5001 6H3.5"/><path d="M18.8332 8.5L18.3732 15.3991C18.1962 18.054 18.1077 19.3815 17.2427 20.1907C16.3777 21 15.0473 21 12.3865 21H11.6132C8.95235 21 7.62195 21 6.75694 20.1907C5.89194 19.3815 5.80344 18.054 5.62644 15.3991L5.1665 8.5"/><path d="M9.5 11L10 16"/><path d="M14.5 11L14 16"/></g></svg></span>
					</button>
					<span class="em-mc-qty-val" data-em-mc-val aria-live="polite"><?php echo esc_html( (string) $qty ); ?></span>
					<button type="button" class="em-mc-qty-btn" data-em-mc-plus aria-label="<?php esc_attr_e( 'Increase quantity', 'exmart' ); ?>"<?php echo $qty >= $max ? ' disabled' : ''; ?>>+</button>
				</div>
			</li>
			<?php
		}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul>

<?php else : ?>

	<div class="woocommerce-mini-cart__empty-message em-mini-cart-empty">
		<span class="em-mc-empty-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M7.5 18C8.32843 18 9 18.6716 9 19.5C9 20.3284 8.32843 21 7.5 21C6.67157 21 6 20.3284 6 19.5C6 18.6716 6.67157 18 7.5 18Z"/><path d="M16.5 18.0001C17.3284 18.0001 18 18.6716 18 19.5001C18 20.3285 17.3284 21.0001 16.5 21.0001C15.6716 21.0001 15 20.3285 15 19.5001C15 18.6716 15.6716 18.0001 16.5 18.0001Z"/><path d="M2 3L2.26121 3.09184C3.5628 3.54945 4.2136 3.77826 4.58584 4.32298C4.95808 4.86771 4.95808 5.59126 4.95808 7.03836V9.76C4.95808 12.7016 5.02132 13.6723 5.88772 14.5862C6.75412 15.5 8.14857 15.5 10.9375 15.5H16.2404C17.8014 15.5 18.5819 15.5 19.1336 15.0504C19.6853 14.6008 19.8429 13.8364 20.158 12.3075L20.6578 9.88275C21.0049 8.14369 21.1784 7.27417 20.7345 6.69708C20.2906 6.12 18.7738 6.12 17.0888 6.12H4.95808"/></g></svg></span>
		<p class="em-mc-empty-title"><?php esc_html_e( 'Your cart is empty', 'exmart' ); ?></p>
		<p class="em-mc-empty-sub"><?php esc_html_e( 'Add a few things you like and they’ll show up here.', 'exmart' ); ?></p>
		<a class="em-mc-start" href="<?php echo esc_url( $em_shop ); ?>"><?php esc_html_e( 'Start shopping', 'exmart' ); ?></a>
	</div>

<?php endif; ?>

<?php if ( $em_picks ) : ?>
	<section class="em-mc-picks" aria-labelledby="em-mc-picks-title">
		<h3 class="em-mc-picks-title" id="em-mc-picks-title"><?php esc_html_e( 'Popular picks', 'exmart' ); ?></h3>
		<div class="em-mc-picks-row">
			<?php foreach ( $em_picks as $em_pick ) : ?>
				<?php
				$pick_badge = exmart_discount_badge_text( $em_pick );
				$pick_pct   = ( $pick_badge && preg_match( '/\d+/', $pick_badge, $pm ) ) ? (int) $pm[0] : 0;
				?>
				<article class="em-mc-pick">
					<div class="em-mc-pick-media">
						<a class="em-mc-pick-link" href="<?php echo esc_url( $em_pick->get_permalink() ); ?>" tabindex="-1" aria-hidden="true"><?php exmart_product_image( $em_pick, 'exmart-card' ); ?></a>
						<?php if ( $pick_pct > 0 ) : ?>
							<span class="em-mc-pick-badge"><?php echo esc_html( sprintf( /* translators: %d: discount percent */ __( 'Save %d%%', 'exmart' ), $pick_pct ) ); ?></span>
						<?php endif; ?>
						<button type="button" class="em-mc-pick-add" data-em-mc-add data-product-id="<?php echo esc_attr( (string) $em_pick->get_id() ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: product name */ __( 'Add %s to cart', 'exmart' ), $em_pick->get_name() ) ); ?>"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M4 12L20 12"/><path d="M12.0204 4L12.0205 20.0003"/></g></svg></button>
					</div>
					<a class="em-mc-pick-name" href="<?php echo esc_url( $em_pick->get_permalink() ); ?>"><?php echo esc_html( $em_pick->get_name() ); ?></a>
					<div class="em-mc-pick-price"><?php exmart_card_price_html( $em_pick ); ?></div>
				</article>
			<?php endforeach; ?>
		</div>
	</section>
<?php endif; ?>

</div>

<?php if ( $em_has_items ) : ?>
	<div class="em-mini-cart-footer">
		<p class="woocommerce-mini-cart__total total">
			<strong><?php esc_html_e( 'Subtotal', 'exmart' ); ?></strong>
			<?php echo exmart_price_lockup( (float) $em_cart->get_displayed_subtotal() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</p>
		<p class="woocommerce-mini-cart__buttons buttons em-mini-cart-buttons">
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="em-mini-cart-btn em-mini-cart-btn--view button wc-forward">
				<?php esc_html_e( 'View cart', 'exmart' ); ?>
			</a>
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="em-mini-cart-btn em-mini-cart-btn--checkout button checkout wc-forward">
				<?php esc_html_e( 'Checkout', 'exmart' ); ?>
			</a>
		</p>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
