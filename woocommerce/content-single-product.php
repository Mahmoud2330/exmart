<?php
/**
 * The template for displaying product content in the single-product.php
 * template — overridden only to wrap gallery + summary in the exMart
 * two-column grid. Everything else (hooks, tabs, related products)
 * stays on WooCommerce's standard machinery.
 *
 * @package exmart
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

	<div class="em-pdp-grid">
		<div class="em-pdp-gallery">
			<?php
			/**
			 * woocommerce_before_single_product_summary hook: this is where
			 * WooCommerce core already hangs both the sale-flash badge
			 * (priority 10) and the product gallery itself (priority 20) —
			 * firing it once here, inside our gallery column, gives us both
			 * in the right place without calling woocommerce_show_product_images()
			 * a second time (which would render the gallery twice).
			 */
			do_action( 'woocommerce_before_single_product_summary' );
			?>
		</div>

		<div class="em-pdp-info summary entry-summary">
			<?php
			/**
			 * woocommerce_single_product_summary hook: title, rating, price,
			 * excerpt, add-to-cart form, meta, sharing — plus our own
			 * brand row (priority 4) and trust block (after this hook fires,
			 * see woocommerce_after_add_to_cart_form).
			 */
			do_action( 'woocommerce_single_product_summary' );
			?>
		</div>
	</div>

	<?php do_action( 'woocommerce_after_single_product_summary' ); ?>

</div>
