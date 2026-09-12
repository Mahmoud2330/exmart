<?php
/**
 * Fallback template (blog index / anything not covered by a more
 * specific template). exMart is a storefront, so this only really
 * gets hit if you add regular blog posts.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<div class="em-container" style="padding-block: var(--s8);">
	<?php if ( have_posts() ) : ?>
		<div style="display:flex;flex-direction:column;gap:var(--s8);max-width:72ch;">
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?>>
					<h2 class="em-h3"><a href="<?php the_permalink(); ?>" style="text-decoration:none;color:var(--ink-900);"><?php the_title(); ?></a></h2>
					<p class="em-body" style="color:var(--ink-500);"><?php echo esc_html( get_the_date() ); ?></p>
					<div class="em-body" style="color:var(--ink-700);"><?php the_excerpt(); ?></div>
				</article>
			<?php endwhile; ?>
		</div>
		<div style="margin-top:var(--s10);">
			<?php the_posts_pagination( array( 'prev_text' => '←', 'next_text' => '→' ) ); ?>
		</div>
	<?php else : ?>
		<p class="em-body"><?php esc_html_e( 'Nothing found.', 'exmart' ); ?></p>
	<?php endif; ?>
</div>
<?php get_footer(); ?>
