<?php
/**
 * The template for displaying product content — Figma PDP shell.
 *
 * @package exmart
 * @version 1.0.74
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 * - notices
 * - exmart_pdp_breadcrumb
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}
$em_pdp_banner_slides = exmart_product_banner_slides();
?>
<div class="em-pdp-layout<?php echo $em_pdp_banner_slides ? ' em-pdp-layout--banner' : ''; ?>">
	<div class="em-pdp-main">
		<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'em-pdp', $product ); ?>>

			<div class="em-pdp-grid">
				<div class="em-pdp-gallery">
					<?php
					/**
					 * Hook: woocommerce_before_single_product_summary.
					 * - sale flash
					 * - product images
					 */
					do_action( 'woocommerce_before_single_product_summary' );
					?>
				</div>

				<div class="em-pdp-info summary entry-summary">
					<?php
					/**
					 * Hook: woocommerce_single_product_summary.
					 * - brand row, title, rating, price, ATC, wishlist, trust
					 */
					do_action( 'woocommerce_single_product_summary' );
					?>
				</div>
			</div>

			<?php if ( $em_pdp_banner_slides ) : ?>
				<div class="em-pdp-banner-strip">
					<?php exmart_render_shop_banner( $em_pdp_banner_slides, 'mobile' ); ?>
				</div>
			<?php endif; ?>

			<?php
			/**
			 * Hook: woocommerce_after_single_product_summary.
			 * - tabs
			 * - upsells
			 * - related (You might also like)
			 */
			do_action( 'woocommerce_after_single_product_summary' );
			?>

		</div>
	</div>

	<?php if ( $em_pdp_banner_slides ) : ?>
		<div class="em-pdp-aside">
			<?php exmart_render_shop_banner( $em_pdp_banner_slides, 'desktop' ); ?>
		</div>
	<?php endif; ?>
</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>
