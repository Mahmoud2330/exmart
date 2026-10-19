<?php
/**
 * Shop / product category / tag archives — Figma PLP.
 *
 * Filters left, multi-column product grid in the middle, and (desktop
 * 1200px+) a tall sticky shop banner on the right, inside .em-container
 * (max-width 1280px).
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
		<div class="em-shop-toolbar-actions">
			<button type="button" class="em-btn em-btn-secondary em-shop-filters-toggle" id="em-shop-filters-open" aria-controls="em-shop-filters" aria-expanded="false">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5" d="M19 3H5C3.58579 3 2.87868 3 2.43934 3.4122C2 3.8244 2 4.48782 2 5.81466V6.50448C2 7.54232 2 8.06124 2.2596 8.49142C2.5192 8.9216 2.99347 9.18858 3.94202 9.72255L6.85504 11.3624C7.49146 11.7206 7.80967 11.8998 8.03751 12.0976C8.51199 12.5095 8.80408 12.9935 8.93644 13.5872C9 13.8722 9 14.2058 9 14.8729L9 17.5424C9 18.452 9 18.9067 9.25192 19.2613C9.50385 19.6158 9.95128 19.7907 10.8462 20.1406C12.7248 20.875 13.6641 21.2422 14.3321 20.8244C15 20.4066 15 19.4519 15 17.5424V14.8729C15 14.2058 15 13.8722 15.0636 13.5872C15.1959 12.9935 15.488 12.5095 15.9625 12.0976C16.1903 11.8998 16.5085 11.7206 17.145 11.3624L20.058 9.72255C21.0065 9.18858 21.4808 8.9216 21.7404 8.49142C22 8.06124 22 7.54232 22 6.50448V5.81466C22 4.48782 22 3.8244 21.5607 3.4122C21.1213 3 20.4142 3 19 3Z"/></svg>
				<?php esc_html_e( 'Filters', 'exmart' ); ?>
			</button>
			<?php woocommerce_catalog_ordering(); ?>
		</div>
	</div>

	<?php woocommerce_output_all_notices(); ?>

	<?php exmart_shop_active_filters(); ?>

	<?php
	$em_shop_banner_slides = exmart_shop_banner_slides();
	exmart_render_shop_banner( $em_shop_banner_slides, 'mobile' );
	?>

	<div class="em-shop-layout<?php echo $em_shop_banner_slides ? ' em-shop-layout--banner' : ''; ?>">
		<div class="em-backdrop" id="em-shop-filters-backdrop" hidden></div>
		<div class="em-shop-side">
			<aside class="em-shop-filters" id="em-shop-filters" aria-label="<?php esc_attr_e( 'Product filters', 'exmart' ); ?>">
				<div class="em-shop-filters-header">
					<h2 class="em-h4"><?php esc_html_e( 'Filters', 'exmart' ); ?></h2>
					<button type="button" class="em-btn-icon" id="em-shop-filters-close" aria-label="<?php esc_attr_e( 'Close filters', 'exmart' ); ?>">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M19.0068 5L5.00684 19"/><path d="M19 19L5 5"/></g></svg>
					</button>
				</div>
				<div class="em-shop-filters-body">
					<?php exmart_render_shop_filters(); ?>
				</div>
			</aside>
		</div>

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

		<?php if ( $em_shop_banner_slides ) : ?>
			<div class="em-shop-aside">
				<?php exmart_render_shop_banner( $em_shop_banner_slides, 'desktop' ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
