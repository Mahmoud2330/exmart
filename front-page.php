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

$trust_items = array(
	array(
		'label' => __( '100% authentic — direct from manufacturer', 'exmart' ),
		'svg'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" style="color:var(--accent-600)"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M3 10.4167C3 7.21907 3 5.62028 3.37752 5.08241C3.75503 4.54454 5.25832 4.02996 8.26491 3.00079L8.83772 2.80472C10.405 2.26824 11.1886 2 12 2C12.8114 2 13.595 2.26824 15.1623 2.80472L15.7351 3.00079C18.7417 4.02996 20.245 4.54454 20.6225 5.08241C21 5.62028 21 7.21907 21 10.4167C21 10.8996 21 11.4234 21 11.9914C21 17.6294 16.761 20.3655 14.1014 21.5273C13.38 21.8424 13.0193 22 12 22C10.9807 22 10.62 21.8424 9.89856 21.5273C7.23896 20.3655 3 17.6294 3 11.9914C3 11.4234 3 10.8996 3 10.4167Z"/><path stroke-linejoin="round" d="M9.5 12.4L10.9286 14L14.5 10"/></g></svg>',
	),
	array(
		'label' => __( 'Official sole distributor in Egypt', 'exmart' ),
		'svg'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" style="color:var(--accent-600)"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M4 10.1433C4 5.64588 7.58172 2 12 2C16.4183 2 20 5.64588 20 10.1433C20 14.6055 17.4467 19.8124 13.4629 21.6744C12.5343 22.1085 11.4657 22.1085 10.5371 21.6744C6.55332 19.8124 4 14.6055 4 10.1433Z"/><circle cx="12" cy="10" r="3"/></g></svg>',
	),
	array(
		'label' => __( 'Cash on delivery available', 'exmart' ),
		'svg'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" style="color:var(--accent-600)"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 17V17.5V18"/><path d="M12 6V6.5V7"/><path d="M15 9.5C15 8.11929 13.6569 7 12 7C10.3431 7 9 8.11929 9 9.5C9 10.8807 10.3431 12 12 12C13.6569 12 15 13.1193 15 14.5C15 15.8807 13.6569 17 12 17C10.3431 17 9 15.8807 9 14.5"/></g></svg>',
	),
	array(
		'label' => __( 'Nationwide delivery across Egypt', 'exmart' ),
		'svg'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" style="color:var(--accent-600)"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M9.56541 8.73049C9.0804 6.97492 8.8379 6.09714 9.24954 5.40562C9.66119 4.71409 10.5662 4.47889 12.3763 4.00849L14.2962 3.50955C16.1062 3.03915 17.0113 2.80394 17.7242 3.20319C18.4372 3.60244 18.6797 4.48023 19.1647 6.2358L19.6792 8.09786C20.1642 9.85343 20.4067 10.7312 19.995 11.4227C19.5834 12.1143 18.6784 12.3495 16.8683 12.8199L14.9484 13.3188C13.1384 13.7892 12.2333 14.0244 11.5203 13.6252C10.8073 13.2259 10.5648 12.3481 10.0798 10.5926L9.56541 8.73049Z"/><path d="M8.23 15.0602L6.08 7.27014C5.89453 6.61732 5.38563 6.12036 4.7 5.92L3 5.4502"/><circle cx="8.05" cy="18.1" r="2.9"/><path d="M10.9199 17.36L19.9999 15"/></g></svg>',
	),
);
?>

<section class="em-hero-bento">
	<div class="em-container">
		<div class="em-hero-carousel">
		<div class="em-hero-bento-grid">

			<?php
			$em_hero_config = exmart_hero_tiles_config();
			$em_main_kicker = exmart_hero_tile_text( 'main', 'kicker' );
			$em_main_title  = exmart_hero_tile_text( 'main', 'title' );
			$em_main_desc   = exmart_hero_tile_text( 'main', 'desc' );
			$em_main_cta    = exmart_hero_tile_text( 'main', 'cta' );
			?>
			<div class="em-hero-tile em-hero-tile--main">
				<div class="em-hero-tile-content">
					<?php if ( '' !== $em_main_kicker ) : ?>
						<p class="em-overline"><?php echo esc_html( $em_main_kicker ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $em_main_title ) : ?>
						<h1 class="em-h1"><?php echo esc_html( $em_main_title ); ?></h1>
					<?php endif; ?>
					<?php if ( '' !== $em_main_desc ) : ?>
						<p class="em-body"><?php echo esc_html( $em_main_desc ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $em_main_cta ) : ?>
						<a href="<?php echo esc_url( exmart_hero_tile_link( 'main' ) ); ?>" class="em-btn em-btn-primary em-hero-tile-cta"><?php echo esc_html( $em_main_cta ); ?></a>
					<?php endif; ?>
				</div>
				<?php $em_main_image = exmart_get_promo_image( $em_hero_config['main']['image_mod'], $em_hero_config['main']['image_hint'] ); ?>
				<?php if ( $em_main_image ) : ?>
					<img class="em-hero-tile-img em-hero-tile-img--main" src="<?php echo esc_url( $em_main_image['url'] ); ?>" alt="<?php echo esc_attr( $em_main_image['alt'] ); ?>" decoding="async" />
				<?php endif; ?>
			</div>

			<?php
			$em_promo_tiles = array();
			foreach ( array( 'offers', 'new', 'best' ) as $em_tile_key ) {
				$em_promo_tiles[] = array(
					'modifier' => $em_tile_key,
					'kicker'   => exmart_hero_tile_text( $em_tile_key, 'kicker' ),
					'title'    => exmart_hero_tile_text( $em_tile_key, 'title' ),
					'desc'     => exmart_hero_tile_text( $em_tile_key, 'desc' ),
					'cta'      => exmart_hero_tile_text( $em_tile_key, 'cta' ),
					'href'     => exmart_hero_tile_link( $em_tile_key ),
					'image'    => exmart_get_promo_image( $em_hero_config[ $em_tile_key ]['image_mod'], $em_hero_config[ $em_tile_key ]['image_hint'] ),
				);
			}
			foreach ( $em_promo_tiles as $tile ) :
				?>
				<a href="<?php echo esc_url( $tile['href'] ); ?>" class="em-hero-tile em-hero-tile--<?php echo esc_attr( $tile['modifier'] ); ?>">
					<div class="em-hero-tile-content">
						<?php if ( '' !== $tile['kicker'] ) : ?>
							<span class="em-overline"><?php echo esc_html( $tile['kicker'] ); ?></span>
						<?php endif; ?>
						<?php if ( '' !== $tile['title'] ) : ?>
							<h2 class="em-h3 em-hero-tile-title"><?php echo esc_html( $tile['title'] ); ?></h2>
						<?php endif; ?>
						<?php if ( '' !== $tile['desc'] ) : ?>
							<p class="em-body-s em-hero-tile-desc"><?php echo esc_html( $tile['desc'] ); ?></p>
						<?php endif; ?>
						<?php if ( '' !== $tile['cta'] ) : ?>
							<span class="em-btn em-btn-primary em-hero-tile-cta"><?php echo esc_html( $tile['cta'] ); ?></span>
						<?php endif; ?>
					</div>
					<?php if ( $tile['image'] ) : ?>
						<img class="em-hero-tile-img" src="<?php echo esc_url( $tile['image']['url'] ); ?>" alt="<?php echo esc_attr( $tile['image']['alt'] ); ?>" loading="lazy" decoding="async" />
					<?php endif; ?>
				</a>
			<?php endforeach; ?>

		</div>
		<button type="button" class="em-hero-nav em-hero-nav--prev" id="em-hero-prev" aria-label="<?php esc_attr_e( 'Previous', 'exmart' ); ?>">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5L9 12L15 19"/></svg>
		</button>
		<button type="button" class="em-hero-nav em-hero-nav--next" id="em-hero-next" aria-label="<?php esc_attr_e( 'Next', 'exmart' ); ?>">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5L15 12L9 19"/></svg>
		</button>
		</div>
	</div>
</section>

<?php if ( ! empty( $product_brands ) ) : ?>
<div class="em-brands-strip">
	<div class="em-container em-brands-strip-row">
		<span class="em-overline em-brands-strip-label"><?php esc_html_e( 'Our Brands', 'exmart' ); ?></span>
		<div class="em-brands-strip-divider" aria-hidden="true"></div>
		<?php foreach ( $product_brands as $brand ) :
			$logo_url = exmart_brand_logo_url( $brand->term_id );
			?>
			<a
				class="em-brand-pill"
				href="<?php echo esc_url( get_term_link( $brand ) ); ?>"
				aria-label="<?php echo esc_attr( $brand->name ); ?>"
				title="<?php echo esc_attr( $brand->name ); ?>"
			>
				<?php if ( $logo_url ) : ?>
					<span class="em-brand-pill-logo">
						<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $brand->name ); ?>" width="72" height="28" loading="lazy" decoding="async" />
					</span>
				<?php else : ?>
					<span class="em-brand-pill-name"><?php echo esc_html( $brand->name ); ?></span>
				<?php endif; ?>
			</a>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>

<div class="em-trust-strip">
	<div class="em-container em-trust-strip-viewport">
		<div class="em-trust-strip-row">
			<?php foreach ( $trust_items as $item ) : ?>
				<div class="em-trust-item">
					<span class="em-trust-item-icon"><?php echo $item['svg']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup ?></span>
					<span class="em-trust-item-text em-body-s"><?php echo esc_html( $item['label'] ); ?></span>
				</div>
			<?php endforeach; ?>
			<?php foreach ( $trust_items as $item ) : ?>
				<div class="em-trust-item em-trust-item--dup" aria-hidden="true">
					<span class="em-trust-item-icon"><?php echo $item['svg']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup ?></span>
					<span class="em-trust-item-text em-body-s"><?php echo esc_html( $item['label'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<section class="em-section-sm">
	<div class="em-container em-cat-heading">
		<h2 class="em-h2"><?php esc_html_e( 'Shop by Category', 'exmart' ); ?></h2>
	</div>
	<?php if ( ! empty( $product_cats ) ) : ?>
		<div class="em-container">
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
			<a href="<?php echo esc_url( exmart_whatsapp_url() ); ?>" class="em-whatsapp-link" target="_blank" rel="noopener noreferrer">
				<?php exmart_whatsapp_icon( 20 ); ?>
				<?php esc_html_e( 'Chat with us on WhatsApp', 'exmart' ); ?>
			</a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
