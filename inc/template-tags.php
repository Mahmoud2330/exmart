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
/**
 * Star rating SVGs.
 *
 * @param float  $rating  0–5.
 * @param int    $size    Icon size in px.
 * @param string $variant 'default' (product cards) or 'on-dark' (review band).
 */
function exmart_stars( $rating, $size = 14, $variant = 'default' ) {
	$full     = (int) round( $rating );
	$fill_on  = 'on-dark' === $variant ? 'var(--paper)' : 'var(--ink-900)';
	$fill_off = 'on-dark' === $variant ? 'var(--ink-700)' : 'var(--ink-200)';
	echo '<span class="em-stars" aria-label="' . esc_attr( $rating . ' out of 5 stars' ) . '">';
	for ( $i = 0; $i < 5; $i++ ) {
		$fill = $i < $full ? $fill_on : $fill_off;
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
 * Shared args for homepage / collection product queries.
 *
 * @param int $limit
 * @return array
 */
function exmart_product_query_defaults( $limit = 8 ) {
	return array(
		'status'     => 'publish',
		'limit'      => max( 1, (int) $limit ),
		'visibility' => 'visible',
		'return'     => 'objects',
	);
}

/**
 * Whether a product_tag slug exists.
 *
 * @param string $slug
 * @return bool
 */
function exmart_product_tag_exists( $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_tag' );
	return $term && ! is_wp_error( $term );
}

/**
 * Best Sellers — Figma: products flagged isBestSeller.
 *
 * WP mapping:
 * 1. product_tag `best-sellers` (or `best-seller`)
 * 2. fallback: highest total sales (WooCommerce popularity)
 *
 * @param int $limit
 * @return WC_Product[]
 */
function exmart_get_best_sellers( $limit = 8 ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}

	$defaults = exmart_product_query_defaults( $limit );
	$tag_slug = null;
	foreach ( array( 'best-sellers', 'best-seller' ) as $candidate ) {
		if ( exmart_product_tag_exists( $candidate ) ) {
			$tag_slug = $candidate;
			break;
		}
	}

	if ( $tag_slug ) {
		$tagged = wc_get_products(
			array_merge(
				$defaults,
				array(
					'tag'     => array( $tag_slug ),
					'orderby' => 'popularity',
					'order'   => 'DESC',
				)
			)
		);
		if ( ! empty( $tagged ) ) {
			return $tagged;
		}
	}

	return wc_get_products(
		array_merge(
			$defaults,
			array(
				'orderby' => 'popularity',
				'order'   => 'DESC',
			)
		)
	);
}

/**
 * Offers — Figma: products with a compare-at (sale) price.
 *
 * WP mapping: WooCommerce on-sale products (sale price set / scheduled).
 *
 * @param int $limit
 * @return WC_Product[]
 */
function exmart_get_offers( $limit = 8 ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}

	return wc_get_products(
		array_merge(
			exmart_product_query_defaults( $limit ),
			array(
				'on_sale' => true,
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		)
	);
}

/**
 * New Arrivals — Figma: products flagged isNew.
 *
 * WP mapping (matches card "New" badge):
 * 1. product_tag `new` or `new-arrivals`
 * 2. products published within the last 30 days
 * 3. fallback: newest by date (keeps the rail populated)
 *
 * @param int $limit
 * @return WC_Product[]
 */
function exmart_get_new_arrivals( $limit = 8 ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}

	$defaults = exmart_product_query_defaults( $limit );
	$tag_slug = null;
	foreach ( array( 'new-arrivals', 'new' ) as $candidate ) {
		if ( exmart_product_tag_exists( $candidate ) ) {
			$tag_slug = $candidate;
			break;
		}
	}

	if ( $tag_slug ) {
		$tagged = wc_get_products(
			array_merge(
				$defaults,
				array(
					'tag'     => array( $tag_slug ),
					'orderby' => 'date',
					'order'   => 'DESC',
				)
			)
		);
		if ( ! empty( $tagged ) ) {
			return $tagged;
		}
	}

	$recent = wc_get_products(
		array_merge(
			$defaults,
			array(
				'orderby'    => 'date',
				'order'      => 'DESC',
				'date_query' => array(
					array(
						'after'     => '30 days ago',
						'inclusive' => true,
					),
				),
			)
		)
	);
	if ( ! empty( $recent ) ) {
		return $recent;
	}

	return wc_get_products(
		array_merge(
			$defaults,
			array(
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		)
	);
}

/**
 * Shop URL for a homepage rail "View all" link.
 *
 * @param string $rail best_sellers|offers|new_arrivals
 * @return string
 */
function exmart_rail_view_all_url( $rail ) {
	$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

	switch ( $rail ) {
		case 'best_sellers':
			foreach ( array( 'best-sellers', 'best-seller' ) as $slug ) {
				$term = get_term_by( 'slug', $slug, 'product_tag' );
				if ( $term && ! is_wp_error( $term ) ) {
					$link = get_term_link( $term );
					if ( ! is_wp_error( $link ) ) {
						return $link;
					}
				}
			}
			return add_query_arg( 'orderby', 'popularity', $shop );

		case 'offers':
			return add_query_arg( 'on_sale', '1', $shop );

		case 'new_arrivals':
			foreach ( array( 'new-arrivals', 'new' ) as $slug ) {
				$term = get_term_by( 'slug', $slug, 'product_tag' );
				if ( $term && ! is_wp_error( $term ) ) {
					$link = get_term_link( $term );
					if ( ! is_wp_error( $link ) ) {
						return $link;
					}
				}
			}
			return add_query_arg( 'orderby', 'date', $shop );
	}

	return $shop;
}

/**
 * Category image: WooCommerce thumbnail, else Unsplash placeholder.
 *
 * @param WP_Term $term
 * @param int     $size
 * @return string
 */
function exmart_category_image_url( $term, $size = 200 ) {
	$thumb_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
	if ( $thumb_id ) {
		$url = wp_get_attachment_image_url( (int) $thumb_id, 'thumbnail' );
		if ( $url ) {
			return $url;
		}
	}
	return exmart_category_placeholder_image( $term->slug, $size );
}

/**
 * Top-level storefront categories in Figma order (Shop by Category / footer).
 * Excludes Uncategorized and nested Home Care children.
 *
 * @param int $limit
 * @return WP_Term[]
 */
function exmart_get_nav_categories( $limit = 8 ) {
	$order = array(
		'personal-care',
		'hair-care',
		'skin-care',
		'baby-care',
		'feminine-care',
		'home-care',
		'home-diagnostics',
		'health-protection',
	);
	$args  = array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
		'parent'     => 0,
		'number'     => 12,
	);
	$uncat = get_term_by( 'slug', 'uncategorized', 'product_cat' );
	if ( $uncat && ! is_wp_error( $uncat ) ) {
		$args['exclude'] = array( (int) $uncat->term_id );
	}
	$terms = get_terms( $args );
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}
	usort(
		$terms,
		static function ( $a, $b ) use ( $order ) {
			$ai = array_search( $a->slug, $order, true );
			$bi = array_search( $b->slug, $order, true );
			$ai = false === $ai ? 999 : $ai;
			$bi = false === $bi ? 999 : $bi;
			return $ai <=> $bi;
		}
	);
	return array_slice( $terms, 0, max( 1, (int) $limit ) );
}

/**
 * A horizontal-scroll product rail (Best Sellers / Offers / New Arrivals
 * on the homepage, "You may also like" on the product page).
 *
 * @param string        $title
 * @param WC_Product[]  $products
 * @param string        $view_all_url
 * @param string        $empty_message
 */
function exmart_product_rail( $title, $products, $view_all_url, $empty_message = '' ) {
	?>
	<section>
		<div class="em-rail-header">
			<h2 class="em-h3"><?php echo esc_html( $title ); ?></h2>
			<?php if ( ! empty( $products ) ) : ?>
				<a class="em-view-all" href="<?php echo esc_url( $view_all_url ); ?>"><?php esc_html_e( 'View all →', 'exmart' ); ?></a>
			<?php endif; ?>
		</div>
		<?php if ( empty( $products ) ) : ?>
			<p class="em-body em-rail-empty"><?php echo esc_html( $empty_message ? $empty_message : __( 'No products to show yet.', 'exmart' ) ); ?></p>
		<?php else : ?>
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
		<?php endif; ?>
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
