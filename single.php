<?php
/**
 * Single blog post fallback (exMart is a storefront; this only matters
 * if you publish regular posts, e.g. a "Health tips" blog later).
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<div class="em-container em-page-narrow" style="padding-block: var(--s12);">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php exmart_breadcrumb( array( array( 'label' => __( 'Home', 'exmart' ), 'href' => home_url( '/' ) ), array( 'label' => get_the_title() ) ) ); ?>
		<h1 class="em-h1" style="margin-bottom:var(--s4);"><?php the_title(); ?></h1>
		<p class="em-caption" style="color:var(--ink-500);margin-bottom:var(--s8);"><?php echo esc_html( get_the_date() ); ?></p>
		<div class="em-body" style="color:var(--ink-700);line-height:1.6;">
			<?php the_content(); ?>
		</div>
	<?php endwhile; ?>
</div>
<?php get_footer(); ?>
