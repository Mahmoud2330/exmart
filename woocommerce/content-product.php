<?php
/**
 * Product loop card — overrides WooCommerce's default content-product.php
 * to match the exMart .em-card design (badges, wishlist heart, price lockup).
 *
 * @package exmart
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product || ! $product->is_visible() ) return;

$is_sale       = $product->is_on_sale();
$is_new        = ( time() - get_the_date( 'U' ) ) < ( 30 * DAY_IN_SECONDS );
$is_bestseller = has_term( 'best-sellers', 'product_tag', $product->get_id() );
$out_of_stock  = ! $product->is_in_stock();
?>
<article <?php wc_product_class( 'em-card', $product ); ?>>
	<a href="<?php the_permalink(); ?>" class="em-card-img-wrap<?php echo $out_of_stock ? ' oos' : ''; ?>">
		<div class="em-card-placeholder"><?php exmart_product_image( $product, 'exmart-card' ); ?></div>
		<div class="em-card-badge-pos">
			<?php if ( $is_sale ) : ?><span class="em-badge em-badge-sale"><?php esc_html_e( 'Sale', 'exmart' ); ?></span><?php endif; ?>
			<?php if ( $is_new ) : ?><span class="em-badge em-badge-ink"><?php esc_html_e( 'New', 'exmart' ); ?></span><?php endif; ?>
			<?php if ( $is_bestseller && ! $is_sale && ! $is_new ) : ?><span class="em-badge em-badge-accent"><?php esc_html_e( 'Best Seller', 'exmart' ); ?></span><?php endif; ?>
		</div>
	</a>
	<div class="em-card-body">
		<?php exmart_loop_brand_label(); ?>
		<a href="<?php the_permalink(); ?>">
			<p class="em-card-title"><?php echo esc_html( $product->get_name() ); ?></p>
		</a>
		<div style="display:flex;align-items:center;">
			<?php echo wc_get_rating_html( $product->get_average_rating() ); ?>
			<?php if ( $product->get_review_count() ) : ?>
				<span class="em-rating-count">(<?php echo esc_html( $product->get_review_count() ); ?>)</span>
			<?php endif; ?>
		</div>
		<span class="em-price-lockup">
			<?php echo $product->get_price_html(); ?>
		</span>
		<div class="em-card-actions">
			<button
				type="button"
				class="em-wishlist-btn em-wishlist-toggle"
				data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
				aria-label="<?php esc_attr_e( 'Add to wishlist', 'exmart' ); ?>"
			>
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" stroke="currentColor" stroke-width="2"/></svg>
			</button>
			<?php woocommerce_template_loop_add_to_cart(); ?>
		</div>
	</div>
</article>
