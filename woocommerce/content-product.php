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
$sale_label    = $product->is_type( 'variable' ) ? exmart_discount_badge_text( $product ) : '';
if ( $is_sale && '' === $sale_label ) {
	$sale_label = __( 'Sale', 'exmart' );
}
$is_new        = has_term( array( 'new', 'new-arrivals' ), 'product_tag', $product->get_id() )
	|| ( ( time() - get_the_date( 'U', $product->get_id() ) ) < ( 30 * DAY_IN_SECONDS ) );
$is_bestseller = has_term( array( 'best-sellers', 'best-seller' ), 'product_tag', $product->get_id() );
$out_of_stock  = ! $product->is_in_stock();
?>
<article <?php wc_product_class( 'em-card', $product ); ?>>
	<a href="<?php the_permalink(); ?>" class="em-card-img-wrap<?php echo $out_of_stock ? ' oos' : ''; ?>">
		<div class="em-card-placeholder">
			<?php
			exmart_product_image( $product, 'exmart-card' );
			$gallery_ids = $product->get_gallery_image_ids();
			$shown       = 0;
			foreach ( $gallery_ids as $gallery_id ) {
				if ( $shown >= 4 ) {
					break;
				}
				$gallery_url = wp_get_attachment_image_url( $gallery_id, 'exmart-card' );
				if ( ! $gallery_url ) {
					continue;
				}
				printf( '<img class="em-card-img-extra" src="%s" alt="" loading="lazy" decoding="async" />', esc_url( $gallery_url ) );
				$shown++;
			}
			?>
		</div>
		<div class="em-card-badge-pos">
			<?php if ( $is_sale ) : ?><span class="em-badge em-badge-sale"><?php echo esc_html( $sale_label ); ?></span><?php endif; ?>
			<?php if ( $is_new ) : ?><span class="em-badge em-badge-ink"><?php esc_html_e( 'New', 'exmart' ); ?></span><?php endif; ?>
			<?php if ( $is_bestseller && ! $is_sale && ! $is_new ) : ?><span class="em-badge em-badge-accent"><?php esc_html_e( 'Best Seller', 'exmart' ); ?></span><?php endif; ?>
		</div>
	</a>
	<div class="em-card-body">
		<?php exmart_loop_brand_label(); ?>
		<a href="<?php the_permalink(); ?>">
			<p class="em-card-title"><?php echo esc_html( $product->get_name() ); ?></p>
		</a>
		<div class="em-card-rating">
			<?php exmart_stars( (float) $product->get_average_rating(), 12 ); ?>
			<span class="em-rating-count">(<?php echo esc_html( (string) (int) $product->get_review_count() ); ?>)</span>
		</div>
		<?php exmart_card_price_html( $product ); ?>
		<div class="em-card-actions">
			<button
				type="button"
				class="em-wishlist-btn em-wishlist-toggle"
				data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
				aria-label="<?php esc_attr_e( 'Add to wishlist', 'exmart' ); ?>"
			>
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2 9.1371C2 14 6.01943 16.5914 8.96173 18.9109C10 19.7294 11 20.5 12 20.5C13 20.5 14 19.7294 15.0383 18.9109C17.9806 16.5914 22 14 22 9.1371C22 4.27416 16.4998 0.825464 12 5.50063C7.50016 0.825464 2 4.27416 2 9.1371Z"/></svg>
			</button>
			<?php exmart_card_atc_control( $product ); ?>
		</div>
	</div>
</article>
