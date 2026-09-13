<?php
/**
 * Related Products — Figma “You might also like” rail.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package exMart
 * @version 1.0.73
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $related_products ) ) {
	return;
}

$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'You might also like', 'exmart' ) );

$view_all = '';
global $product;
if ( $product instanceof WC_Product ) {
	$cats = wc_get_product_terms( $product->get_id(), 'product_cat', array( 'fields' => 'all' ) );
	if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
		$link = get_term_link( $cats[0] );
		if ( ! is_wp_error( $link ) ) {
			$view_all = $link;
		}
	}
	if ( ! $view_all ) {
		$view_all = wc_get_page_permalink( 'shop' );
	}
}
?>
<section class="related products em-pdp-related">
	<div class="em-pdp-related-head">
		<?php if ( $heading ) : ?>
			<h2 class="em-h3 em-pdp-related-title"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		<?php if ( $view_all ) : ?>
			<a class="em-pdp-related-all" href="<?php echo esc_url( $view_all ); ?>"><?php esc_html_e( 'View all', 'exmart' ); ?></a>
		<?php endif; ?>
	</div>

	<?php woocommerce_product_loop_start(); ?>
		<?php foreach ( $related_products as $related_product ) : ?>
			<?php
			$post_object = get_post( $related_product->get_id() );
			setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			wc_get_template_part( 'content', 'product' );
			?>
		<?php endforeach; ?>
	<?php woocommerce_product_loop_end(); ?>
</section>
<?php
wp_reset_postdata();
