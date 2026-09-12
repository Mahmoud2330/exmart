<?php
/**
 * Template Name: exMart — About
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$distributed = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => false, 'meta_key' => 'exmart_brand_type', 'meta_value' => 'distributed' ) );
$house       = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => false, 'meta_key' => 'exmart_brand_type', 'meta_value' => 'house' ) );
?>
<div class="em-container" style="padding-block: var(--s12);">
	<?php exmart_breadcrumb( array( array( 'label' => __( 'Home', 'exmart' ), 'href' => home_url( '/' ) ), array( 'label' => __( 'About', 'exmart' ) ) ) ); ?>
	<h1 class="em-h1" style="margin-bottom:var(--s8);"><?php esc_html_e( 'About exMart', 'exmart' ); ?></h1>

	<section style="margin-bottom:var(--s10);">
		<h2 class="em-h3" style="margin-bottom:var(--s4);"><?php esc_html_e( 'Our story', 'exmart' ); ?></h2>
		<p class="em-body" style="color:var(--ink-700);margin-bottom:var(--s4);"><?php esc_html_e( 'exMart was founded in Cairo with a clear mission: to give Egyptian households and healthcare professionals reliable access to authentic, professional-grade health and hygiene products — without the uncertainty of grey-market imports or counterfeits.', 'exmart' ); ?></p>
		<p class="em-body" style="color:var(--ink-700);margin-bottom:var(--s4);"><?php esc_html_e( 'We began as a distributor, earning sole-distributor agreements with Diversey, Grace, Oview, and SureCheck — four internationally recognised brands in professional cleaning, diagnostics, and health monitoring. Being the sole authorised distributor means every product on our shelves is sourced directly from the manufacturer, sealed and complete, with full warranty support.', 'exmart' ); ?></p>
		<p class="em-body" style="color:var(--ink-700);"><?php esc_html_e( 'As we grew, we identified gaps the imported brands could not fill at accessible price points. That led us to develop our own exclusive brands — Qualita, Vodlia, Eliv, and Verve — formulated in collaboration with specialist laboratories and dermatologist advisors, and developed specifically for the Egyptian climate, water chemistry, and skin types.', 'exmart' ); ?></p>
	</section>

	<section style="margin-bottom:var(--s10);">
		<h2 class="em-h3" style="margin-bottom:var(--s6);"><?php esc_html_e( 'Brands we distribute', 'exmart' ); ?></h2>
		<div class="em-about-brand-grid">
			<?php foreach ( $distributed as $brand ) : $color = exmart_brand_color( $brand->term_id ); ?>
				<a class="em-about-brand-card" href="<?php echo esc_url( get_term_link( $brand ) ); ?>">
					<div class="em-about-brand-head">
						<div class="em-about-brand-avatar" style="background:<?php echo esc_attr( $color ); ?>22;"><span style="font-weight:700;color:<?php echo esc_attr( $color ); ?>;"><?php echo esc_html( mb_substr( $brand->name, 0, 1 ) ); ?></span></div>
						<span style="font-weight:700;color:var(--ink-900);"><?php echo esc_html( $brand->name ); ?></span>
					</div>
					<span class="em-trust"><svg class="em-trust-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M5 8l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg><span class="em-trust-text"><?php esc_html_e( 'Official & sole distributor in Egypt', 'exmart' ); ?></span></span>
					<p class="em-body-s" style="color:var(--ink-500);"><?php echo esc_html( exmart_brand_tagline( $brand->term_id ) ); ?></p>
				</a>
			<?php endforeach; ?>
		</div>
	</section>

	<section style="margin-bottom:var(--s10);">
		<h2 class="em-h3" style="margin-bottom:var(--s6);"><?php esc_html_e( 'Our own brands', 'exmart' ); ?></h2>
		<div class="em-about-brand-grid">
			<?php foreach ( $house as $brand ) : $color = exmart_brand_color( $brand->term_id ); ?>
				<a class="em-about-brand-card" href="<?php echo esc_url( get_term_link( $brand ) ); ?>">
					<div class="em-about-brand-head">
						<div class="em-about-brand-avatar" style="background:<?php echo esc_attr( $color ); ?>22;"><span style="font-weight:700;color:<?php echo esc_attr( $color ); ?>;"><?php echo esc_html( mb_substr( $brand->name, 0, 1 ) ); ?></span></div>
						<span style="font-weight:700;color:var(--ink-900);"><?php echo esc_html( $brand->name ); ?></span>
					</div>
					<span class="em-trust"><svg class="em-trust-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M5 8l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg><span class="em-trust-text"><?php esc_html_e( 'exMart exclusive · our own brand', 'exmart' ); ?></span></span>
					<p class="em-body-s" style="color:var(--ink-500);"><?php echo esc_html( exmart_brand_tagline( $brand->term_id ) ); ?></p>
				</a>
			<?php endforeach; ?>
		</div>
	</section>

	<section>
		<h2 class="em-h3" style="margin-bottom:var(--s4);"><?php esc_html_e( 'Our commitment', 'exmart' ); ?></h2>
		<div class="em-commitment-list">
			<?php
			$commitments = array(
				__( 'Every product shipped by exMart is authentic — sourced directly from the manufacturer or produced under our own quality protocols.', 'exmart' ),
				__( 'We never sell counterfeit, expired, or grey-market products. If you suspect a product\'s authenticity, contact us immediately.', 'exmart' ),
				__( 'Our in-house brands are developed with dermatologist input and follow Egyptian regulatory standards for cosmetics and personal care.', 'exmart' ),
				__( 'We offer hassle-free returns within 14 days for any product that does not meet your expectations.', 'exmart' ),
			);
			foreach ( $commitments as $c ) : ?>
				<div class="em-commitment-item">
					<svg width="18" height="18" viewBox="0 0 18 18" fill="none" style="flex-shrink:0;margin-top:2px" aria-hidden="true"><circle cx="9" cy="9" r="8" stroke="var(--ink-700)" stroke-width="1.5"/><path d="M6 9l2 2 4-4" stroke="var(--ink-700)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
					<p class="em-body-s" style="color:var(--ink-700);"><?php echo esc_html( $c ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
</div>
<?php get_footer(); ?>
