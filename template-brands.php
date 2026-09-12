<?php
/**
 * Template Name: exMart — Brands Index
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$brands = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => false ) );
$distributed = array_filter( $brands, fn( $t ) => exmart_brand_type( $t->term_id ) === 'distributed' );
$house       = array_filter( $brands, fn( $t ) => exmart_brand_type( $t->term_id ) === 'house' );
?>
<div class="em-container" style="padding-block: var(--s8);">
	<?php exmart_breadcrumb( array( array( 'label' => __( 'Home', 'exmart' ), 'href' => home_url( '/' ) ), array( 'label' => __( 'Brands', 'exmart' ) ) ) ); ?>
	<div style="display:flex;align-items:center;justify-content:space-between;gap:var(--s4);flex-wrap:wrap;margin-bottom:var(--s8);">
		<h1 class="em-h2"><?php esc_html_e( 'Our Brands', 'exmart' ); ?></h1>
		<div class="em-search-wrap" style="max-width:280px;">
			<svg class="em-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
			<input class="em-input em-search-input" id="em-brand-filter" placeholder="<?php esc_attr_e( 'Search brands…', 'exmart' ); ?>" style="min-height:44px;" aria-label="<?php esc_attr_e( 'Search brands', 'exmart' ); ?>" />
		</div>
	</div>

	<?php if ( ! empty( $distributed ) ) : ?>
		<section style="margin-bottom:var(--s12);" data-brand-group>
			<h2 class="em-h3" style="margin-bottom:var(--s2);"><?php esc_html_e( 'Brands we distribute', 'exmart' ); ?></h2>
			<p class="em-body-s" style="color:var(--ink-500);margin-bottom:var(--s6);"><?php esc_html_e( 'exMart is the official and sole distributor of these brands in Egypt — sourcing directly from the manufacturer.', 'exmart' ); ?></p>
			<div class="em-brand-grid">
				<?php foreach ( $distributed as $brand ) : $color = exmart_brand_color( $brand->term_id ); $count = $brand->count; ?>
					<a class="em-brand-card" data-brand-name="<?php echo esc_attr( strtolower( $brand->name . ' ' . exmart_brand_tagline( $brand->term_id ) ) ); ?>" href="<?php echo esc_url( get_term_link( $brand ) ); ?>">
						<div class="em-brand-card-avatar" style="background:<?php echo esc_attr( $color ); ?>22;"><span style="color:<?php echo esc_attr( $color ); ?>;"><?php echo esc_html( mb_substr( $brand->name, 0, 1 ) ); ?></span></div>
						<div>
							<p class="em-brand-card-name"><?php echo esc_html( $brand->name ); ?></p>
							<span class="em-trust"><svg class="em-trust-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M5 8l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg><span class="em-trust-text"><?php esc_html_e( 'Official & sole distributor in Egypt', 'exmart' ); ?></span></span>
						</div>
						<p class="em-body-s em-brand-card-tagline"><?php echo esc_html( exmart_brand_tagline( $brand->term_id ) ); ?></p>
						<p class="em-caption em-brand-card-count"><?php
							printf( esc_html( _n( '%d product →', '%d products →', $count, 'exmart' ) ), (int) $count );
						?></p>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( ! empty( $house ) ) : ?>
		<section data-brand-group>
			<h2 class="em-h3" style="margin-bottom:var(--s2);"><?php esc_html_e( 'exMart brands', 'exmart' ); ?></h2>
			<p class="em-body-s" style="color:var(--ink-500);margin-bottom:var(--s6);"><?php esc_html_e( 'Developed and quality-tested by our in-house team — made exclusively for exMart customers.', 'exmart' ); ?></p>
			<div class="em-brand-grid">
				<?php foreach ( $house as $brand ) : $color = exmart_brand_color( $brand->term_id ); $count = $brand->count; ?>
					<a class="em-brand-card" data-brand-name="<?php echo esc_attr( strtolower( $brand->name . ' ' . exmart_brand_tagline( $brand->term_id ) ) ); ?>" href="<?php echo esc_url( get_term_link( $brand ) ); ?>">
						<div class="em-brand-card-avatar" style="background:<?php echo esc_attr( $color ); ?>22;"><span style="color:<?php echo esc_attr( $color ); ?>;"><?php echo esc_html( mb_substr( $brand->name, 0, 1 ) ); ?></span></div>
						<div>
							<p class="em-brand-card-name"><?php echo esc_html( $brand->name ); ?></p>
							<span class="em-trust"><svg class="em-trust-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M5 8l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg><span class="em-trust-text"><?php esc_html_e( 'exMart exclusive · our own brand', 'exmart' ); ?></span></span>
						</div>
						<p class="em-body-s em-brand-card-tagline"><?php echo esc_html( exmart_brand_tagline( $brand->term_id ) ); ?></p>
						<p class="em-caption em-brand-card-count"><?php
							printf( esc_html( _n( '%d product →', '%d products →', $count, 'exmart' ) ), (int) $count );
						?></p>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<p class="em-body" id="em-brand-empty" style="color:var(--ink-400);padding-block:var(--s12);display:none;"><?php esc_html_e( 'No brands match your search.', 'exmart' ); ?></p>
</div>
<?php get_footer(); ?>
