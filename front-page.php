<?php
/**
 * Homepage — Phase 3 parity with Figma Home.tsx module order:
 * Hero → Brands strip → Trust strip → Shop by Category →
 * Best Sellers / Offers / New Arrivals rails → Review band → Newsletter.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

/* Categories: top-level only, Figma order (shared with footer). */
$product_cats = exmart_get_nav_categories( 8 );

/* Brands: distributed first, then house (Figma brand strip). */
$product_brands = get_terms(
	array(
		'taxonomy'   => 'product_brand',
		'hide_empty' => false,
	)
);
if ( is_wp_error( $product_brands ) ) {
	$product_brands = array();
} elseif ( $product_brands ) {
	$product_brands = array_values(
		array_filter(
			$product_brands,
			static function ( $brand ) {
				return 'vodlia' !== $brand->slug;
			}
		)
	);
	usort(
		$product_brands,
		static function ( $a, $b ) {
			$ta = get_term_meta( $a->term_id, 'exmart_brand_type', true ) ?: 'distributed';
			$tb = get_term_meta( $b->term_id, 'exmart_brand_type', true ) ?: 'distributed';
			if ( $ta === $tb ) {
				return strcasecmp( $a->name, $b->name );
			}
			return ( 'distributed' === $ta ) ? -1 : 1;
		}
	);
}

$best_sellers = exmart_get_best_sellers( 8 );
$offers       = exmart_get_offers( 8 );
$new_arrivals = exmart_get_new_arrivals( 8 );
$hero_images  = exmart_get_hero_images();
$hero_primary = $hero_images[0];
$hero_multi   = count( $hero_images ) > 1;

$trust_items = array(
	array(
		'label' => __( '100% authentic — direct from manufacturer', 'exmart' ),
		'svg'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="var(--ink-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" stroke="var(--ink-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
	),
	array(
		'label' => __( 'Official sole distributor in Egypt', 'exmart' ),
		'svg'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" stroke="var(--ink-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="10" r="3" stroke="var(--ink-700)" stroke-width="2"/></svg>',
	),
	array(
		'label' => __( 'Cash on delivery available', 'exmart' ),
		'svg'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2" stroke="var(--ink-700)" stroke-width="2"/><path d="M2 10h20" stroke="var(--ink-700)" stroke-width="2"/><path d="M6 15h4" stroke="var(--ink-700)" stroke-width="2" stroke-linecap="round"/></svg>',
	),
	array(
		'label' => __( 'Nationwide delivery across Egypt', 'exmart' ),
		'svg'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M1 3h15v13H1zM16 8h4l3 3v5h-7V8z" stroke="var(--ink-700)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="5.5" cy="18.5" r="2.5" stroke="var(--ink-700)" stroke-width="2"/><circle cx="18.5" cy="18.5" r="2.5" stroke="var(--ink-700)" stroke-width="2"/></svg>',
	),
);
?>

<section class="em-hero">
	<div class="em-hero-bg" style="background-image:url('<?php echo esc_url( $hero_primary['url'] ); ?>');" aria-hidden="true"></div>
	<div class="em-hero-overlay" aria-hidden="true"></div>
	<div class="em-container em-hero-grid">
		<div class="em-hero-copy">
			<p class="em-overline"><?php esc_html_e( 'Authentic health & hygiene — Egypt', 'exmart' ); ?></p>
			<h1 class="em-h1"><?php
				echo wp_kses(
					__( 'Professional-grade<br />health products,<br />delivered to your door.', 'exmart' ),
					array( 'br' => array() )
				);
			?></h1>
			<p class="em-body"><?php esc_html_e( 'Official sole distributor of Diversey, Grace, Oview & SureCheck in Egypt. Plus our own exclusive brands — Qualita, Eliv, and Verve.', 'exmart' ); ?></p>
			<div class="em-hero-ctas">
				<a href="<?php echo esc_url( $shop_url ); ?>" class="em-btn em-btn-lg em-btn-primary"><?php esc_html_e( 'Shop all products', 'exmart' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="em-btn em-btn-lg em-btn-ghost em-hero-ghost"><?php esc_html_e( 'Our story', 'exmart' ); ?></a>
			</div>
		</div>
		<div class="em-hero-img-col">
			<div class="em-hero-img-wrap<?php echo $hero_multi ? ' em-hero-img-wrap--slider' : ''; ?>"<?php echo $hero_multi ? ' data-em-hero-slider' : ''; ?>>
				<?php foreach ( $hero_images as $i => $slide ) : ?>
					<img
						class="em-hero-slide<?php echo 0 === $i ? ' is-active' : ''; ?>"
						src="<?php echo esc_url( $slide['url'] ); ?>"
						alt="<?php echo esc_attr( $slide['alt'] ); ?>"
						width="800"
						height="1000"
						<?php echo 0 === $i ? '' : 'loading="lazy"'; ?>
						decoding="async"
					/>
				<?php endforeach; ?>
				<div class="em-hero-badge">
					<div class="em-hero-badge-icon">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 12l2 2 4-4" stroke="var(--success)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="var(--success)" stroke-width="2"/></svg>
					</div>
					<div>
						<p class="em-hero-badge-title"><?php esc_html_e( '100% Authentic', 'exmart' ); ?></p>
						<p class="em-hero-badge-sub"><?php esc_html_e( 'Direct from manufacturer', 'exmart' ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php if ( ! empty( $product_brands ) ) : ?>
<div class="em-brands-strip">
	<div class="em-brands-strip-row">
		<span class="em-overline em-brands-strip-label"><?php esc_html_e( 'Our Brands', 'exmart' ); ?></span>
		<div class="em-brands-strip-divider" aria-hidden="true"></div>
		<?php foreach ( $product_brands as $brand ) :
			$color    = exmart_brand_color( $brand->term_id );
			$logo_url = exmart_brand_logo_url( $brand->term_id );
			?>
			<a
				class="em-brand-pill"
				href="<?php echo esc_url( get_term_link( $brand ) ); ?>"
				style="--brand-hover:<?php echo esc_attr( $color ); ?>;"
			>
				<?php if ( $logo_url ) : ?>
					<span class="em-brand-pill-logo">
						<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $brand->name ); ?>" width="72" height="28" loading="lazy" decoding="async" />
					</span>
				<?php endif; ?>
				<span class="em-brand-pill-name"><?php echo esc_html( $brand->name ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>

<div class="em-trust-strip">
	<div class="em-container em-trust-strip-row">
		<?php foreach ( $trust_items as $item ) : ?>
			<div class="em-trust-item">
				<span class="em-trust-item-icon"><?php echo $item['svg']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup ?></span>
				<span class="em-trust-item-text em-body-s"><?php echo esc_html( $item['label'] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<section class="em-section-sm">
	<div class="em-container em-cat-heading">
		<h2 class="em-h2"><?php esc_html_e( 'Shop by Category', 'exmart' ); ?></h2>
	</div>
	<?php if ( ! empty( $product_cats ) ) : ?>
		<div class="em-cat-track">
			<?php foreach ( $product_cats as $cat ) : ?>
				<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="em-cat-item">
					<div class="em-cat-circle">
						<img src="<?php echo esc_url( exmart_category_image_url( $cat, 200 ) ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>" width="200" height="200" loading="lazy" decoding="async" />
					</div>
					<span class="em-cat-label"><?php echo esc_html( $cat->name ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<div class="em-container">
			<p class="em-body" style="color:var(--ink-500);"><?php esc_html_e( 'Categories will appear here once products are assigned.', 'exmart' ); ?></p>
		</div>
	<?php endif; ?>
</section>

<section class="em-section-sm">
	<div class="em-container">
		<?php
		exmart_product_rail(
			__( 'Best Sellers', 'exmart' ),
			$best_sellers,
			exmart_rail_view_all_url( 'best_sellers' ),
			__( 'Tag products with “best-sellers”, or wait until orders generate popularity data.', 'exmart' )
		);
		?>
	</div>
</section>
<hr class="em-rule" />

<section class="em-section-sm">
	<div class="em-container">
		<?php
		exmart_product_rail(
			__( 'Offers', 'exmart' ),
			$offers,
			exmart_rail_view_all_url( 'offers' ),
			__( 'No sale products right now. Set a Sale price on products in WooCommerce to fill this rail.', 'exmart' )
		);
		?>
	</div>
</section>
<hr class="em-rule" />

<section class="em-section-sm">
	<div class="em-container">
		<?php
		exmart_product_rail(
			__( 'New Arrivals', 'exmart' ),
			$new_arrivals,
			exmart_rail_view_all_url( 'new_arrivals' ),
			__( 'No new products yet. Publish products (or tag them “new”) to fill this rail.', 'exmart' )
		);
		?>
	</div>
</section>

<section class="em-section-sm em-review-band">
	<div class="em-container">
		<h2 class="em-h2"><?php esc_html_e( 'What customers say', 'exmart' ); ?></h2>
		<div class="em-review-grid">
			<?php
			$reviews = array(
				array(
					'author' => 'Layla M.',
					'text'   => __( 'Authentic products and fast delivery. Finally a trustworthy Egyptian source for Diversey.', 'exmart' ),
					'rating' => 5,
				),
				array(
					'author' => 'Karim A.',
					'text'   => __( 'Ordered Eliv baby wipes three times already. Quality is consistent and pricing is fair.', 'exmart' ),
					'rating' => 5,
				),
				array(
					'author' => 'Nour F.',
					'text'   => __( 'The SureCheck blood pressure monitor I received is exactly the genuine product — box sealed, complete accessories.', 'exmart' ),
					'rating' => 4,
				),
			);
			foreach ( $reviews as $r ) :
				?>
				<div class="em-review-card">
					<div class="em-review-card-stars"><?php exmart_stars( $r['rating'], 14, 'on-dark' ); ?></div>
					<p class="em-body-s em-review-card-text">"<?php echo esc_html( $r['text'] ); ?>"</p>
					<p class="em-caption em-review-card-author">— <?php echo esc_html( $r['author'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="em-section-sm em-newsletter-section">
	<div class="em-container">
		<div class="em-newsletter-inner">
			<h2 class="em-h2"><?php esc_html_e( 'Stay in the loop', 'exmart' ); ?></h2>
			<p class="em-body" style="color:var(--ink-500);"><?php esc_html_e( 'New products, exclusive offers, and health tips — delivered to your inbox.', 'exmart' ); ?></p>
			<div id="em-newsletter-widget-2" class="em-newsletter-widget">
				<form class="em-newsletter-form" id="em-newsletter-form-2">
					<input type="email" class="em-input" placeholder="your@email.com" required aria-label="<?php esc_attr_e( 'Email for newsletter', 'exmart' ); ?>" name="email" />
					<button type="submit" class="em-btn em-btn-primary"><?php esc_html_e( 'Subscribe', 'exmart' ); ?></button>
				</form>
				<p class="em-body em-newsletter-done" id="em-newsletter-done-2" hidden></p>
			</div>
			<a href="https://wa.me/201001234567" class="em-whatsapp-link">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12c0 1.89.52 3.66 1.43 5.17L2 22l4.95-1.41A9.97 9.97 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm-1.5 13.5c-2.5-1.5-4-4-4-4s.5-1 1-1.5c.5-.5.5-1 0-1.5s-1.5-2-2-2c-.5 0-1 .5-1.5 1.5S3 9.5 4 11s3.5 4.5 6 5.5 4 .5 4.5 0 1-1.5.5-2-1.5-1.5-2-2-.5-.5-1-.5c-.5.5-.5.5-1.5 1.5z" fill="var(--success)"/></svg>
				<?php esc_html_e( 'Chat with us on WhatsApp', 'exmart' ); ?>
			</a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
