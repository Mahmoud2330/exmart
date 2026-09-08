<?php
/**
 * Small reusable template helpers.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Breadcrumb for non-WooCommerce pages (WooCommerce pages use
 * woocommerce_breadcrumb() instead, styled to match via CSS).
 *
 * @param array $crumbs [ ['label' => 'Home', 'href' => '/'], ['label' => 'Current'] ]
 */
function exmart_breadcrumb( $crumbs ) {
	echo '<nav class="em-breadcrumb" aria-label="Breadcrumb">';
	$last = count( $crumbs ) - 1;
	foreach ( $crumbs as $i => $crumb ) {
		echo '<span style="display:flex;align-items:center;gap:var(--s2)">';
		if ( $i > 0 ) {
			echo '<span class="em-breadcrumb-sep" aria-hidden="true">/</span>';
		}
		if ( ! empty( $crumb['href'] ) && $i !== $last ) {
			printf( '<a class="em-breadcrumb-item" href="%s">%s</a>', esc_url( $crumb['href'] ), esc_html( $crumb['label'] ) );
		} else {
			printf( '<span class="em-breadcrumb-current">%s</span>', esc_html( $crumb['label'] ) );
		}
		echo '</span>';
	}
	echo '</nav>';
}

/**
 * Render a 5-star rating (rounded) as inline SVGs — used on the
 * homepage testimonial band which isn't backed by WooCommerce reviews.
 */
function exmart_stars( $rating, $size = 14 ) {
	$full = round( $rating );
	echo '<span class="em-stars" aria-label="' . esc_attr( $rating . ' out of 5 stars' ) . '">';
	for ( $i = 0; $i < 5; $i++ ) {
		$fill = $i < $full ? 'var(--ink-900)' : 'var(--ink-200)';
		printf(
			'<svg width="%1$d" height="%1$d" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M8 1.5l1.545 3.13 3.455.502-2.5 2.436.59 3.432L8 9.25l-3.09 1.75.59-3.432L3 5.132l3.455-.502L8 1.5z" fill="%2$s"/></svg>',
			absint( $size ),
			esc_attr( $fill )
		);
	}
	echo '</span>';
}

/**
 * Category emoji used on the Category Index page tiles (matches the
 * original design's lightweight iconography — swap for real icons any time).
 */
function exmart_category_emoji( $slug ) {
	$map = array(
		'personal-care'     => '🧴',
		'hair-care'         => '💆',
		'skin-care'         => '✨',
		'baby-care'         => '👶',
		'feminine-care'     => '🌸',
		'home-care'         => '🏠',
		'home-diagnostics'  => '🔬',
		'health-protection' => '💊',
	);
	return $map[ $slug ] ?? '🛍️';
}

/**
 * Category tile background tint (matches CATEGORY_COLORS from the original app).
 */
function exmart_category_color( $slug ) {
	$map = array(
		'personal-care'     => '#FFE5CC',
		'hair-care'         => '#E8D5F5',
		'skin-care'         => '#D5F0E8',
		'baby-care'         => '#D5E8F5',
		'feminine-care'     => '#F5D5E8',
		'home-care'         => '#D5F5F0',
		'home-diagnostics'  => '#D5E8FF',
		'health-protection' => '#E0F5D5',
	);
	return $map[ $slug ] ?? '#F1F2F4';
}

/**
 * Placeholder category photography (Unsplash) used until real product
 * photos are uploaded — mirrors the original app's placeholder system.
 * Swap these for real photos, or just upload featured images per product
 * and this is bypassed automatically (see template-parts/product-image.php).
 */
function exmart_category_placeholder_image( $slug, $size = 400 ) {
	$map = array(
		'personal-care'     => 'photo-1599210822756-5c4f400b90ac',
		'hair-care'         => 'photo-1701992678972-d5a053ad0fb0',
		'skin-care'         => 'photo-1748543668646-e81cda0890f3',
		'baby-care'         => 'photo-1705155726507-8e1b9119349b',
		'feminine-care'     => 'photo-1748543668676-ea8241cb3886',
		'home-care'         => 'photo-1550963295-019d8a8a61c5',
		'home-diagnostics'  => 'photo-1580281658460-2d1114999983',
		'health-protection' => 'photo-1584744982491-665216d95f8b',
	);
	$id = $map[ $slug ] ?? $map['personal-care'];
	return "https://images.unsplash.com/{$id}?w={$size}&fit=crop&auto=format";
}

/**
 * Output <img> for a product: uses the featured image if one has been
 * uploaded, otherwise falls back to the category placeholder photo.
 */
function exmart_product_image( $product, $size = 'exmart-card' ) {
	if ( ! $product ) return;
	if ( has_post_thumbnail( $product->get_id() ) ) {
		echo get_the_post_thumbnail( $product->get_id(), $size, array( 'loading' => 'lazy', 'decoding' => 'async' ) );
		return;
	}
	$cats = get_the_terms( $product->get_id(), 'product_cat' );
	$slug = ( ! empty( $cats ) && ! is_wp_error( $cats ) ) ? $cats[0]->slug : 'personal-care';
	printf(
		'<img src="%s" alt="%s" loading="lazy" decoding="async" style="width:100%%;height:100%%;object-fit:cover" />',
		esc_url( exmart_category_placeholder_image( $slug ) ),
		esc_attr( $product->get_name() )
	);
}

/**
 * A horizontal-scroll product rail (Best Sellers / Offers / New Arrivals
 * on the homepage, "You may also like" on the product page).
 *
 * @param string        $title
 * @param WC_Product[]  $products
 * @param string        $view_all_url
 */
function exmart_product_rail( $title, $products, $view_all_url ) {
	if ( empty( $products ) ) return;
	?>
	<section>
		<div class="em-rail-header">
			<h2 class="em-h3"><?php echo esc_html( $title ); ?></h2>
			<a class="em-view-all" href="<?php echo esc_url( $view_all_url ); ?>"><?php esc_html_e( 'View all →', 'exmart' ); ?></a>
		</div>
		<div class="em-rail-track">
			<?php
			global $post, $product;
			foreach ( $products as $rail_product ) {
				// content-product.php reads the current item from global
				// $product, not $post — setup_postdata() alone won't set
				// it (that normally happens via the "the_post" hook fired
				// by the real WP Loop, which we're bypassing here), so set
				// it explicitly or every card fatal-errors on a null $product.
				$post    = get_post( $rail_product->get_id() );
				$product = $rail_product;
				setup_postdata( $post );
				echo '<div class="em-rail-card">';
				wc_get_template_part( 'content', 'product' );
				echo '</div>';
			}
			wp_reset_postdata();
			?>
		</div>
	</section>
	<?php
}

/**
 * Governorates list for the checkout Governorate <select> (WooCommerce
 * ships Egypt states out of the box once EG is set as the base country;
 * this is a fallback list mirroring the original app for reference/import use).
 */
function exmart_governorates() {
	return array(
		'Cairo', 'Giza', 'Alexandria', 'Dakahlia', 'Sharqia', 'Qalyubia', 'Kafr El Sheikh',
		'Gharbia', 'Menoufia', 'Beheira', 'Ismailia', 'Suez', 'Port Said', 'Damietta',
		'Faiyum', 'Beni Suef', 'Minya', 'Asyut', 'Sohag', 'Qena', 'Luxor', 'Aswan',
		'Red Sea', 'New Valley', 'Matrouh', 'North Sinai', 'South Sinai',
	);
}
