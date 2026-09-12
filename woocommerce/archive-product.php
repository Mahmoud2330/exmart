<?php
/**
 * Shop / product category / tag archives — Figma PLP.
 *
 * Filters left, multi-column product grid filling the remaining width
 * inside .em-container (max-width 1280px).
 *
 * @package exMart
 * @version 1.0.42
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Structured data / plugin hooks — wrappers are no-ops on this archive
 * so this template owns the page shell.
 */
do_action( 'woocommerce_before_main_content' );

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
global $wp_query;
$total = isset( $wp_query->found_posts ) ? (int) $wp_query->found_posts : 0;

if ( is_product_category() || is_product_tag() ) {
	$term   = get_queried_object();
	$title  = $term instanceof WP_Term ? $term->name : __( 'Products', 'exmart' );
	$crumbs = array(
		array( 'label' => __( 'Home', 'exmart' ), 'href' => home_url( '/' ) ),
		array( 'label' => __( 'Shop', 'exmart' ), 'href' => $shop_url ),
		array( 'label' => $title ),
	);
} else {
	$title  = __( 'All Products', 'exmart' );
	$crumbs = array(
		array( 'label' => __( 'Home', 'exmart' ), 'href' => home_url( '/' ) ),
		array( 'label' => __( 'Shop', 'exmart' ) ),
	);
}
?>
<div class="em-container em-shop-page" style="padding-block: var(--s8);">
	<?php exmart_breadcrumb( $crumbs ); ?>

	<div class="em-shop-toolbar">
		<h1 class="em-h2">
			<?php echo esc_html( $title ); ?>
			<span class="em-shop-count">(<?php echo esc_html( (string) $total ); ?>)</span>
		</h1>
		<?php woocommerce_catalog_ordering(); ?>
	</div>

	<?php woocommerce_output_all_notices(); ?>

	<?php exmart_shop_active_filters(); ?>

	<div class="em-shop-layout">
		<aside class="em-shop-filters" aria-label="<?php esc_attr_e( 'Product filters', 'exmart' ); ?>">
			<?php exmart_render_shop_filters(); ?>
		</aside>

		<div class="em-shop-main">
			<?php if ( woocommerce_product_loop() ) : ?>
				<?php woocommerce_product_loop_start(); ?>

				<?php if ( wc_get_loop_prop( 'total' ) ) : ?>
					<?php while ( have_posts() ) : ?>
						<?php the_post(); ?>
						<?php do_action( 'woocommerce_shop_loop' ); ?>
						<?php wc_get_template_part( 'content', 'product' ); ?>
					<?php endwhile; ?>
				<?php endif; ?>

				<?php woocommerce_product_loop_end(); ?>

				<?php do_action( 'woocommerce_after_shop_loop' ); ?>
			<?php else : ?>
				<p class="em-body" style="color:var(--ink-500);padding-block:var(--s12);">
					<?php esc_html_e( 'No products match your filters.', 'exmart' ); ?>
				</p>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
