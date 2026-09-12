<?php
/**
 * Generic page fallback — used for any WP Page that isn't one of the
 * bespoke "Template Name:" templates. This notably includes
 * WooCommerce's own Cart / Checkout / My Account pages: they're plain
 * WordPress Pages containing a shortcode/block, not swapped templates
 * (unlike Shop, which WooCommerce swaps for archive-product.php), so
 * they need the wide WooCommerce-page container here rather than the
 * narrow prose one used for About/Privacy/etc.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$is_wc_page = function_exists( 'is_woocommerce' ) && ( is_cart() || is_checkout() || is_account_page() );
?>

<?php if ( $is_wc_page ) : ?>
	<div class="em-container" style="padding-block: var(--s8);">
		<?php exmart_breadcrumb( array( array( 'label' => __( 'Home', 'exmart' ), 'href' => home_url( '/' ) ), array( 'label' => get_the_title() ) ) ); ?>
		<h1 class="em-h2" style="margin-bottom:var(--s6);"><?php the_title(); ?></h1>
		<div class="<?php echo is_account_page() ? 'em-account-page' : ''; ?>">
			<?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
		</div>
	</div>
<?php else : ?>
	<div class="em-container em-page-narrow" style="padding-block: var(--s12);">
		<?php exmart_breadcrumb( array( array( 'label' => __( 'Home', 'exmart' ), 'href' => home_url( '/' ) ), array( 'label' => get_the_title() ) ) ); ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<h1 class="em-h1" style="margin-bottom:var(--s8);"><?php the_title(); ?></h1>
			<div class="em-body" style="color:var(--ink-700);line-height:1.6;">
				<?php the_content(); ?>
			</div>
		<?php endwhile; ?>
	</div>
<?php endif; ?>

<?php get_footer(); ?>
