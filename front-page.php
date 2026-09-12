<?php
/**
 * Homepage: hero, brand strip, trust strip, shop-by-category, rails,
 * review band, newsletter.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$product_cats   = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 8 ) );
$product_brands = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => false ) );
$shop_url       = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

$best_sellers_term = get_term_by( 'slug', 'best-sellers', 'product_tag' );
$best_sellers = $best_sellers_term ? wc_get_products( array( 'tag' => array( 'best-sellers' ), 'limit' => 8, 'status' => 'publish' ) ) : array();

$on_sale_ids = wc_get_product_ids_on_sale();
$offers = ! empty( $on_sale_ids ) ? wc_get_products( array( 'include' => array_slice( $on_sale_ids, 0, 8 ), 'limit' => 8, 'status' => 'publish' ) ) : array();

$new_arrivals = wc_get_products( array( 'orderby' => 'date', 'order' => 'DESC', 'limit' => 8, 'status' => 'publish' ) );
?>

<section class="em-hero">
	<div class="em-hero-bg" style="background-image:url('https://images.unsplash.com/photo-1600709206786-f96656b15a3c?w=1600&fit=crop&auto=format');" aria-hidden="true"></div>
	<div class="em-hero-overlay" aria-hidden="true"></div>
	<div class="em-container em-hero-grid">
		<div class="em-hero-copy">
			<p class="em-overline"><?php esc_html_e( 'Authentic health & hygiene — Egypt', 'exmart' ); ?></p>
			<h1 class="em-h1"><?php esc_html_e( 'Professional-grade health products, delivered to your door.', 'exmart' ); ?></h1>
			<p class="em-body"><?php esc_html_e( 'Official sole distributor of Diversey, Grace, Oview & SureCheck in Egypt. Plus our own exclusive brands — Qualita, Vodlia, Eliv, and Verve.', 'exmart' ); ?></p>
			<div class="em-hero-ctas">
				<a href="<?php echo esc_url( $shop_url ); ?>" class="em-btn em-btn-lg em-btn-primary"><?php esc_html_e( 'Shop all products', 'exmart' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="em-btn em-btn-lg em-btn-ghost" style="color:var(--ink-300);border:1px solid var(--ink-700);"><?php esc_html_e( 'Our story', 'exmart' ); ?></a>
			</div>
		</div>
		<div class="em-hero-img-col">
			<div class="em-hero-img-wrap">
				<img src="https://images.unsplash.com/photo-1627495395570-d2c94e3319f5?w=800&fit=crop&auto=format" alt="<?php esc_attr_e( 'Professional hygiene products', 'exmart' ); ?>" />
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

<div class="em-brands-strip">
	<div class="em-brands-strip-row">
		<span class="em-overline" style="color:var(--ink-400);flex-shrink:0;"><?php esc_html_e( 'Our Brands', 'exmart' ); ?></span>
		<div style="width:1px;height:20px;background:var(--ink-200);flex-shrink:0;"></div>
		<?php foreach ( $product_brands as $brand ) :
			$color = exmart_brand_color( $brand->term_id );
			?>
			<a class="em-brand-pill" href="<?php echo esc_url( get_term_link( $brand ) ); ?>">
				<span class="em-brand-pill-dot" style="background:<?php echo esc_attr( $color ); ?>;"><?php echo esc_html( mb_substr( $brand->name, 0, 1 ) ); ?></span>
				<span class="em-brand-pill-name"><?php echo esc_html( $brand->name ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
</div>

<div class="em-trust-strip">
	<div class="em-container em-trust-strip-row">
		<?php
		$trust_items = array(
			array( 'icon' => '🛡️', 'text' => __( '100% authentic — direct from manufacturer', 'exmart' ) ),
			array( 'icon' => '📍', 'text' => __( 'Official sole distributor in Egypt', 'exmart' ) ),
			array( 'icon' => '💳', 'text' => __( 'Cash on delivery available', 'exmart' ) ),
			array( 'icon' => '🚚', 'text' => __( 'Nationwide delivery across Egypt', 'exmart' ) ),
		);
		foreach ( $trust_items as $item ) : ?>
			<div class="em-trust-item">
				<span class="em-trust-item-icon"><?php echo esc_html( $item['icon'] ); ?></span>
				<span class="em-trust-item-text"><?php echo esc_html( $item['text'] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<section class="em-section-sm">
	<div class="em-container" style="margin-bottom:var(--s5);">
		<h2 class="em-h2"><?php esc_html_e( 'Shop by Category', 'exmart' ); ?></h2>
	</div>
	<div class="em-cat-track">
		<?php foreach ( $product_cats as $cat ) : ?>
			<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="em-cat-item">
				<div class="em-cat-circle">
					<img src="<?php echo esc_url( exmart_category_placeholder_image( $cat->slug, 200 ) ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>" loading="lazy" decoding="async" />
				</div>
				<span class="em-cat-label"><?php echo esc_html( $cat->name ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
</section>

<?php if ( ! empty( $best_sellers ) ) : ?>
	<section class="em-section-sm">
		<div class="em-container">
			<?php exmart_product_rail( __( 'Best Sellers', 'exmart' ), $best_sellers, add_query_arg( 'product_tag', 'best-sellers', $shop_url ) ); ?>
		</div>
	</section>
	<hr class="em-rule" />
<?php endif; ?>

<?php if ( ! empty( $offers ) ) : ?>
	<section class="em-section-sm">
		<div class="em-container">
			<?php exmart_product_rail( __( 'Offers', 'exmart' ), $offers, add_query_arg( 'on_sale', '1', $shop_url ) ); ?>
		</div>
	</section>
	<hr class="em-rule" />
<?php endif; ?>

<?php if ( ! empty( $new_arrivals ) ) : ?>
	<section class="em-section-sm">
		<div class="em-container">
			<?php exmart_product_rail( __( 'New Arrivals', 'exmart' ), $new_arrivals, add_query_arg( 'orderby', 'date', $shop_url ) ); ?>
		</div>
	</section>
<?php endif; ?>

<section class="em-section-sm em-review-band">
	<div class="em-container">
		<h2 class="em-h2"><?php esc_html_e( 'What customers say', 'exmart' ); ?></h2>
		<div class="em-review-grid">
			<?php
			$reviews = array(
				array( 'author' => 'Layla M.', 'text' => __( 'Authentic products and fast delivery. Finally a trustworthy Egyptian source for Diversey.', 'exmart' ), 'rating' => 5 ),
				array( 'author' => 'Karim A.', 'text' => __( 'Ordered Eliv baby wipes three times already. Quality is consistent and pricing is fair.', 'exmart' ), 'rating' => 5 ),
				array( 'author' => 'Nour F.', 'text' => __( 'The SureCheck blood pressure monitor I received is exactly the genuine product — box sealed, complete accessories.', 'exmart' ), 'rating' => 4 ),
			);
			foreach ( $reviews as $r ) : ?>
				<div class="em-review-card">
					<div class="em-review-card-stars"><?php exmart_stars( $r['rating'] ); ?></div>
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
			<div id="em-newsletter-widget-2" style="width:100%;">
				<form class="em-newsletter-form" id="em-newsletter-form-2">
					<input type="email" class="em-input" placeholder="your@email.com" required aria-label="<?php esc_attr_e( 'Email for newsletter', 'exmart' ); ?>" name="email" style="flex:1;" />
					<button type="submit" class="em-btn em-btn-primary"><?php esc_html_e( 'Subscribe', 'exmart' ); ?></button>
				</form>
				<p class="em-body" id="em-newsletter-done-2" style="color:var(--success);font-weight:600;display:none;"></p>
			</div>
			<a href="https://wa.me/201001234567" class="em-whatsapp-link">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12c0 1.89.52 3.66 1.43 5.17L2 22l4.95-1.41A9.97 9.97 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z" fill="var(--success)"/></svg>
				<?php esc_html_e( 'Chat with us on WhatsApp', 'exmart' ); ?>
			</a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
