<?php
/**
 * Search results — when searching products (post_type=product, the
 * default from our search forms) this renders exactly like a shop
 * archive; falls back to a simple list for any other post type.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$is_product_search = isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'];
$term = get_search_query();
?>
<div class="em-container" style="padding-block: var(--s8);">
	<?php if ( have_posts() ) : ?>
		<div style="display:flex;align-items:baseline;justify-content:space-between;gap:var(--s4);flex-wrap:wrap;margin-bottom:var(--s6);">
			<h1 class="em-h3">
				<?php
				printf(
					/* translators: 1: result count, 2: search term */
					esc_html( _n( '%1$d result for "%2$s"', '%1$d results for "%2$s"', $wp_query->found_posts, 'exmart' ) ),
					(int) $wp_query->found_posts,
					esc_html( $term )
				);
				?>
			</h1>
		</div>
		<?php if ( $is_product_search ) : ?>
			<ul class="products">
				<?php while ( have_posts() ) : the_post(); wc_get_template_part( 'content', 'product' ); endwhile; ?>
			</ul>
		<?php else : ?>
			<div style="display:flex;flex-direction:column;gap:var(--s8);max-width:72ch;">
				<?php while ( have_posts() ) : the_post(); ?>
					<article <?php post_class(); ?>>
						<h2 class="em-h3"><a href="<?php the_permalink(); ?>" style="text-decoration:none;color:var(--ink-900);"><?php the_title(); ?></a></h2>
						<div class="em-body" style="color:var(--ink-700);"><?php the_excerpt(); ?></div>
					</article>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
		<div style="margin-top:var(--s10);">
			<?php the_posts_pagination( array( 'prev_text' => '←', 'next_text' => '→' ) ); ?>
		</div>
	<?php else : ?>
		<h1 class="em-h3" style="margin-bottom:var(--s6);">
			<?php
			printf(
				/* translators: %s: search term */
				esc_html__( 'No results for "%s"', 'exmart' ),
				esc_html( $term )
			);
			?>
		</h1>
		<p class="em-body" style="color:var(--ink-500);margin-bottom:var(--s8);"><?php esc_html_e( 'Try a different search term, or browse by category or brand below.', 'exmart' ); ?></p>
		<div style="display:flex;flex-direction:column;gap:var(--s8);">
			<div>
				<p class="em-overline" style="color:var(--ink-500);margin-bottom:var(--s4);"><?php esc_html_e( 'Popular Categories', 'exmart' ); ?></p>
				<div style="display:flex;gap:var(--s2);flex-wrap:wrap;">
					<?php foreach ( get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) ) as $cat ) : ?>
						<a class="em-chip" href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
			<div>
				<p class="em-overline" style="color:var(--ink-500);margin-bottom:var(--s4);"><?php esc_html_e( 'Brands', 'exmart' ); ?></p>
				<div style="display:flex;gap:var(--s2);flex-wrap:wrap;">
					<?php foreach ( get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => false ) ) as $b ) : ?>
						<a class="em-chip" href="<?php echo esc_url( get_term_link( $b ) ); ?>"><?php echo esc_html( $b->name ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>
<?php get_footer(); ?>
