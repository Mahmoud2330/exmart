<?php
/**
 * Brand landing page — hero band + filterable product grid for that brand.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$term  = get_queried_object();
$color = exmart_brand_color( $term->term_id );
$type  = exmart_brand_type( $term->term_id );
?>

<div class="em-brand-hero">
	<div class="em-container">
		<?php exmart_breadcrumb( array(
			array( 'label' => __( 'Home', 'exmart' ), 'href' => home_url( '/' ) ),
			array( 'label' => __( 'Brands', 'exmart' ), 'href' => home_url( '/brands/' ) ),
			array( 'label' => $term->name ),
		) ); ?>
		<div class="em-brand-hero-row">
			<div class="em-brand-hero-avatar" style="background:<?php echo esc_attr( $color ); ?>22;">
				<span style="color:<?php echo esc_attr( $color ); ?>;"><?php echo esc_html( mb_substr( $term->name, 0, 1 ) ); ?></span>
			</div>
			<div style="flex:1;min-width:240px;">
				<h1 class="em-h2" style="margin-bottom:var(--s2);"><?php echo esc_html( $term->name ); ?></h1>
				<span class="em-trust">
					<svg class="em-trust-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M5 8l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
					<span class="em-trust-text"><?php echo $type === 'distributed' ? esc_html__( 'Official & sole distributor in Egypt', 'exmart' ) : esc_html__( 'exMart exclusive · our own brand', 'exmart' ); ?></span>
				</span>
				<p class="em-body em-brand-hero-desc"><?php echo esc_html( $term->description ); ?></p>
			</div>
		</div>
	</div>
</div>

<div class="em-container" style="padding-block: var(--s8);">
	<?php
	global $wp_query;
	$product_count = isset( $wp_query->found_posts ) ? (int) $wp_query->found_posts : (int) $term->count;
	?>
	<div style="display:flex;align-items:center;justify-content:space-between;gap:var(--s4);flex-wrap:wrap;margin-bottom:var(--s6);">
		<p class="em-body-s" style="color:var(--ink-500);"><?php
			printf(
				/* translators: %d: number of products */
				esc_html( _n( '%d product', '%d products', $product_count, 'exmart' ) ),
				$product_count
			);
		?></p>
	</div>

	<?php if ( have_posts() ) : ?>
		<ul class="products">
			<?php while ( have_posts() ) : the_post(); wc_get_template_part( 'content', 'product' ); endwhile; ?>
		</ul>
		<div style="margin-top:var(--s10);">
			<?php the_posts_pagination( array( 'prev_text' => '←', 'next_text' => '→' ) ); ?>
		</div>
	<?php else : ?>
		<p class="em-body" style="color:var(--ink-500);padding-block:var(--s12);"><?php esc_html_e( 'No products in this brand yet.', 'exmart' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
