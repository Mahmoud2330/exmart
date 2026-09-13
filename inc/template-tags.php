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
 * Format a numeric price for card display (store decimals / separators).
 *
 * @param float|string $amount
 * @return string
 */
function exmart_format_card_price( $amount ) {
	if ( '' === $amount || null === $amount ) {
		return '';
	}
	$decimals = function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : 2;
	$dec_sep  = function_exists( 'wc_get_price_decimal_separator' ) ? wc_get_price_decimal_separator() : '.';
	$tho_sep  = function_exists( 'wc_get_price_thousand_separator' ) ? wc_get_price_thousand_separator() : ',';
	return number_format( (float) $amount, $decimals, $dec_sep, $tho_sep );
}

/**
 * Product-card price lockup (Figma): EGP + sale in red + struck regular.
 * Avoids WooCommerce <ins>/<del> markup that theme/plugin CSS often overrides.
 *
 * @param WC_Product $product Product.
 * @param bool       $large   Larger number (PDP).
 */
function exmart_card_price_html( $product, $large = false ) {
	if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
		return;
	}

	$currency = function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : 'EGP';
	$label    = ( 'EGP' === $currency ) ? 'EGP' : ( function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol( $currency ) : $currency );

	$current  = null;
	$regular  = null;
	$on_sale  = $product->is_on_sale();

	if ( $product->is_type( 'variable' ) ) {
		$current = (float) $product->get_variation_price( 'min', true );
		$regular = (float) $product->get_variation_regular_price( 'min', true );
		$on_sale = $on_sale && $regular > 0 && $current < $regular;
	} else {
		$current = wc_get_price_to_display( $product );
		$reg_raw = $product->get_regular_price();
		if ( '' !== $reg_raw && null !== $reg_raw ) {
			$regular = wc_get_price_to_display( $product, array( 'price' => $reg_raw ) );
		}
		$on_sale = $on_sale && null !== $regular && (float) $current < (float) $regular;
	}

	$num_class = 'em-price-number';
	if ( $large ) {
		$num_class .= ' em-price-number-lg';
	}
	if ( $on_sale && null !== $regular ) {
		$num_class .= ' sale';
	}

	echo '<span class="em-price-lockup">';
	echo '<span class="em-price-currency">' . esc_html( $label ) . '</span>';

	if ( $on_sale && null !== $regular ) {
		echo '<span class="' . esc_attr( $num_class ) . '">' . esc_html( exmart_format_card_price( $current ) ) . '</span>';
		echo '<span class="em-price-compare">' . esc_html( exmart_format_card_price( $regular ) ) . '</span>';
	} else {
		echo '<span class="' . esc_attr( $num_class ) . '">' . esc_html( exmart_format_card_price( $current ) ) . '</span>';
	}

	echo '</span>';
}

/**
 * Store contact details (shared by footer, contact page, WhatsApp CTAs).
 */
function exmart_phone_display() {
	return '+20 1064991378';
}

function exmart_phone_tel() {
	return '+201064991378';
}

/** Digits-only international number for wa.me links. */
function exmart_whatsapp_number() {
	return '201064991378';
}

function exmart_whatsapp_url() {
	return 'https://wa.me/' . exmart_whatsapp_number();
}

function exmart_email() {
	return 'info@exmartegypt.com';
}

/**
 * Whether Super Socializer (Heateor) social login is available.
 *
 * @return bool
 */
function exmart_has_super_socializer() {
	return defined( 'THE_CHAMP_SS_VERSION' )
		|| function_exists( 'the_champ_login_button' )
		|| shortcode_exists( 'TheChamp-Login' );
}

/**
 * Super Socializer Google OAuth start URL.
 *
 * @param string $redirect Post-login redirect.
 * @return string
 */
function exmart_super_socializer_google_url( $redirect ) {
	return add_query_arg(
		array(
			'SuperSocializerAuth'          => 'Google',
			'super_socializer_redirect_to' => $redirect,
		),
		home_url( '/' )
	);
}

/**
 * Google / social login URL.
 *
 * Prefers Super Socializer (exMart’s provider), then Nextend. Always returns a
 * URL so the button can render; override via `exmart_google_login_url`.
 *
 * @return string
 */
function exmart_google_login_url() {
	$redirect = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
	$redirect = $redirect ? $redirect : home_url( '/' );

	/**
	 * Filter the Google OAuth / social login URL used on Login & Register.
	 *
	 * @param string $url
	 * @param string $redirect
	 */
	$filtered = apply_filters( 'exmart_google_login_url', '', $redirect );
	if ( $filtered ) {
		return $filtered;
	}

	// Super Socializer / Heateor — primary on this site.
	if ( exmart_has_super_socializer() ) {
		return exmart_super_socializer_google_url( $redirect );
	}

	// Nextend Social Login fallback.
	if ( class_exists( 'NextendSocialLogin', false ) && is_callable( array( 'NextendSocialLogin', 'getLoginUrl' ) ) ) {
		try {
			$url = NextendSocialLogin::getLoginUrl( 'google' );
			if ( is_string( $url ) && $url !== '' ) {
				return add_query_arg( 'redirect', $redirect, $url );
			}
		} catch ( Throwable $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// Fall through.
		}
	}

	if ( class_exists( 'NextendSocialLogin', false ) ) {
		return add_query_arg(
			array(
				'loginSocial' => 'google',
				'redirect'    => $redirect,
			),
			site_url( 'wp-login.php', 'login' )
		);
	}

	// Default to Super Socializer’s Google endpoint.
	return exmart_super_socializer_google_url( $redirect );
}

/**
 * Auth-page social login block (Google), styled to match exMart forms.
 *
 * Wired for Super Socializer (Heateor). Nextend remains a fallback.
 *
 * @param string $context 'login' or 'register'.
 */
function exmart_social_auth_block( $context = 'login' ) {
	$google_url = exmart_google_login_url();

	$label = ( 'register' === $context )
		? __( 'Sign up with Google', 'exmart' )
		: __( 'Continue with Google', 'exmart' );
	?>
	<div class="em-auth-social">
		<div class="em-auth-divider" role="separator" aria-hidden="true">
			<span><?php esc_html_e( 'or', 'exmart' ); ?></span>
		</div>
		<p class="em-auth-social-label"><?php esc_html_e( 'Continue with your Google account', 'exmart' ); ?></p>

		<a class="em-btn em-btn-google" href="<?php echo esc_url( $google_url ); ?>">
			<span class="em-btn-google-icon" aria-hidden="true">
				<svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg"><path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844a4.14 4.14 0 01-1.796 2.716v2.259h2.908c1.702-1.567 2.684-3.875 2.684-6.615z" fill="#4285F4"/><path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 009 18z" fill="#34A853"/><path d="M3.964 10.71A5.41 5.41 0 013.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 000 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/><path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 00.957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z" fill="#EA4335"/></svg>
			</span>
			<span><?php echo esc_html( $label ); ?></span>
		</a>
	</div>
	<?php
}

/**
 * Resolve a Media Library attachment URL by exact filename basename.
 *
 * @param string $filename e.g. Fawry.png
 * @return string Empty when not found.
 */
function exmart_media_url_by_filename( $filename ) {
	$filename = ltrim( (string) $filename, '/' );
	if ( '' === $filename ) {
		return '';
	}

	global $wpdb;
	$found = absint(
		$wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta}
				WHERE meta_key = '_wp_attached_file'
				AND (
					meta_value = %s
					OR meta_value LIKE %s
				)
				ORDER BY post_id DESC
				LIMIT 1",
				$filename,
				'%/' . $wpdb->esc_like( $filename )
			)
		)
	);

	if ( ! $found ) {
		return '';
	}

	$url = wp_get_attachment_image_url( $found, 'medium' );
	return $url ? $url : '';
}

/**
 * Footer payment methods: label + optional Media Library logo filename.
 *
 * @return array<int, array{label: string, file: string}>
 */
function exmart_payment_methods() {
	return array(
		array(
			'label' => 'COD',
			'file'  => '',
		),
		array(
			'label' => 'Fawry',
			'file'  => 'Fawry.png',
		),
		array(
			'label' => 'Meeza',
			'file'  => 'Meeza-Digital-.png',
		),
		array(
			'label' => 'Visa / Mastercard',
			'file'  => 'visa-and-mastercard-logos-logo-visa-png-logo-visa-mastercard-png-visa-logo-white-png-awesome-logos.png',
		),
		array(
			'label' => 'InstaPay',
			'file'  => 'InstaPay-Logo.png',
		),
	);
}

/**
 * Official WhatsApp glyph (green) for CTAs.
 *
 * @param int $size Icon size in px.
 */
function exmart_whatsapp_icon( $size = 20 ) {
	$size = absint( $size );
	printf(
		'<svg class="em-whatsapp-icon" width="%1$d" height="%1$d" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="#25D366" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>',
		$size
	);
}

/**
 * Map WC order status → Figma label + color for account order cards.
 *
 * @param string $status Status slug without wc- prefix.
 * @return array{label: string, color: string}
 */
function exmart_account_order_status_meta( $status ) {
	$map = array(
		'completed'  => array( 'label' => __( 'Delivered', 'exmart' ), 'color' => 'var(--success)' ),
		'processing' => array( 'label' => __( 'Shipped', 'exmart' ), 'color' => 'var(--accent-600)' ),
		'on-hold'    => array( 'label' => __( 'Confirmed', 'exmart' ), 'color' => 'var(--warning)' ),
		'pending'    => array( 'label' => __( 'Placed', 'exmart' ), 'color' => 'var(--ink-500)' ),
		'cancelled'  => array( 'label' => __( 'Cancelled', 'exmart' ), 'color' => 'var(--error)' ),
		'refunded'   => array( 'label' => __( 'Refunded', 'exmart' ), 'color' => 'var(--ink-500)' ),
		'failed'     => array( 'label' => __( 'Failed', 'exmart' ), 'color' => 'var(--error)' ),
	);
	if ( isset( $map[ $status ] ) ) {
		return $map[ $status ];
	}
	return array(
		'label' => function_exists( 'wc_get_order_status_name' ) ? wc_get_order_status_name( 'wc-' . $status ) : $status,
		'color' => 'var(--ink-500)',
	);
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
 * Find a Media Library attachment by (partial) filename and resolve it
 * to a usable image array. Used to auto-pick bento hero tile images
 * that were uploaded with a position-based filename (e.g.
 * "big-block-image-left-centered.jpg") instead of being wired up
 * through the Customizer.
 *
 * @param string $needle Filename substring to search for (case-insensitive).
 * @return array{url:string,alt:string}|null
 */
function exmart_find_media_by_filename( $needle ) {
	global $wpdb;
	$like = '%' . $wpdb->esc_like( $needle ) . '%';
	$id   = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s ORDER BY post_id DESC LIMIT 1",
			$like
		)
	);
	if ( ! $id ) {
		return null;
	}
	$id  = absint( $id );
	$url = wp_get_attachment_image_url( $id, 'large' );
	if ( ! $url ) {
		return null;
	}
	$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
	return array(
		'url' => $url,
		'alt' => $alt ? $alt : get_the_title( $id ),
	);
}

/**
 * Bento hero tile image: a Customizer pick (Homepage Hero section)
 * takes priority; otherwise falls back to a Media Library filename
 * search. Returns null if neither resolves to anything yet.
 *
 * @param string $mod_key       Theme mod key, e.g. 'exmart_promo1_image'.
 * @param string $filename_hint Filename substring to fall back to, e.g. 'top-right'.
 * @return array{url:string,alt:string}|null
 */
function exmart_get_promo_image( $mod_key, $filename_hint = '' ) {
	$id = absint( get_theme_mod( $mod_key, 0 ) );
	if ( $id ) {
		$url = wp_get_attachment_image_url( $id, 'large' );
		if ( $url ) {
			$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
			return array(
				'url' => $url,
				'alt' => $alt ? $alt : get_the_title( $id ),
			);
		}
	}
	if ( $filename_hint ) {
		return exmart_find_media_by_filename( $filename_hint );
	}
	return null;
}

/**
 * Category icon (Solar icon set, bold-duotone style, recolored to the
 * theme's accent blue + sale red instead of Solar's default single-color
 * + 50% opacity duotone) for the homepage 'Shop by Category' circles.
 * Source: https://www.npmjs.com/package/@iconify-json/solar (MIT).
 *
 * @param string $slug Category slug.
 * @return string Inline <svg> markup.
 */
function exmart_category_icon_svg( $slug ) {
	$icons = array(
		'personal-care' => '<g fill="var(--accent-600)"><path d="M2 11.5C2 11.2239 2.22386 11 2.5 11H7.5C7.77614 11 8 11.2239 8 11.5V18C8 19.6569 6.65685 21 5 21C3.34315 21 2 19.6569 2 18V11.5Z" fill="var(--sale)"/><path d="M3 11H7V5.99983C7 5.25644 6.21769 4.77295 5.55279 5.1054L3.55279 6.1054C3.214 6.27479 3 6.62105 3 6.99983V11Z"/><path d="M11 10.5C11 7.46243 13.4624 5 16.5 5C19.5376 5 22 7.46243 22 10.5C22 13.5376 19.5376 16 16.5 16C13.4624 16 11 13.5376 11 10.5Z"/><path d="M15.75 15.9492V19.5001H13.5C13.0858 19.5001 12.75 19.8359 12.75 20.2501C12.75 20.6643 13.0858 21.0001 13.5 21.0001H19.5C19.9142 21.0001 20.25 20.6643 20.25 20.2501C20.25 19.8359 19.9142 19.5001 19.5 19.5001H17.25V15.9492C17.0048 15.9827 16.7544 15.9999 16.5 15.9999C16.2456 15.9999 15.9952 15.9827 15.75 15.9492Z" fill="var(--sale)"/></g>', // Personal Care
		'hair-care' => '<g fill="var(--accent-600)"><path d="M6.65389 1.63257C6.45089 1.27151 5.99363 1.14338 5.63257 1.34638C5.27151 1.54938 5.14338 2.00664 5.34638 2.3677L15.7041 20.7901C16.3395 21.9577 17.5773 22.7501 19.0001 22.7501C21.0712 22.7501 22.7501 21.0712 22.7501 19.0001C22.7501 16.9291 21.0712 15.2501 19.0001 15.2501C17.4674 15.2501 16.1495 16.1697 15.5679 17.4871L6.65389 1.63257Z" fill="var(--sale)"/><path d="M17.3462 1.63257C17.5492 1.27151 18.0065 1.14338 18.3676 1.34638C18.7286 1.54938 18.8568 2.00664 18.6538 2.3677L8.29606 20.7901C7.66064 21.9577 6.42286 22.7501 5 22.7501C2.92893 22.7501 1.25 21.0712 1.25 19.0001C1.25 16.9291 2.92893 15.2501 5 15.2501C6.5327 15.2501 7.85064 16.1697 8.43226 17.4871L17.3462 1.63257Z"/></g>', // Hair Care
		'skin-care' => '<g fill="var(--accent-600)"><path d="M22 13.75V16.5069C22 17.1639 21.723 17.7906 21.2371 18.2329C18.2685 20.935 13.7315 20.935 10.7629 18.2329C10.277 17.7906 10 17.1639 10 16.5069V10C10 8.11438 10 7.17157 10.5858 6.58579C11.1716 6 12.1144 6 14 6H18C19.8856 6 20.8284 6 21.4142 6.58579C21.9166 7.08814 21.9881 7.85306 21.9983 9.25H18C17.5858 9.25 17.25 9.58579 17.25 10C17.25 10.4142 17.5858 10.75 18 10.75H22V12.25H19C18.5858 12.25 18.25 12.5858 18.25 13C18.25 13.4142 18.5858 13.75 19 13.75H22Z"/><path d="M15.25 20.2236V21.2502C15.25 21.6645 15.5858 22.0002 16 22.0002C16.4142 22.0002 16.75 21.6645 16.75 21.2502V20.2236C16.2512 20.2717 15.7488 20.2717 15.25 20.2236Z" fill="var(--sale)"/><path d="M14.2937 2.10195C13.6301 1.55699 12.7996 1.25627 11.9409 1.25009L11.8927 1.25H7.94513C6.57755 1.24998 5.47521 1.24997 4.60825 1.36653C3.70814 1.48754 2.95027 1.74644 2.34835 2.34835C1.74644 2.95027 1.48754 3.70814 1.36653 4.60825C1.24997 5.47521 1.24998 6.57752 1.25 7.9451L1.25 22.25C1.25 22.6642 1.58579 23 2 23C2.41422 23 2.75 22.6642 2.75 22.25V8C2.75 6.56459 2.7516 5.56347 2.85315 4.80812C2.9518 4.07435 3.13225 3.68577 3.40901 3.40901C3.68577 3.13225 4.07435 2.9518 4.80812 2.85315L8 2.75H11.8907L11.9301 2.75006L11.932 2.75007C12.4459 2.75421 12.9429 2.93416 13.3403 3.26L13.3418 3.26117L13.3726 3.28677L13.3767 3.2902C13.4124 3.31999 13.4373 3.34073 13.4597 3.35879C13.9929 3.78944 14.6125 4.05173 15.25 4.14872V6H16.75V4.07349C17.3633 3.91609 17.9437 3.60173 18.4266 3.13349C18.4473 3.11334 18.47 3.09066 18.5033 3.05738L19.0303 2.53033C19.3232 2.23744 19.3232 1.76257 19.0303 1.46967C18.7374 1.17678 18.2626 1.17678 17.9697 1.46967L17.4471 1.99228C17.4075 2.03182 17.3937 2.04566 17.3823 2.05668C17.1927 2.24051 16.9785 2.38474 16.75 2.48894V2C16.75 1.58579 16.4142 1.25 16 1.25C15.5858 1.25 15.25 1.58579 15.25 2V2.61957C14.9482 2.54134 14.6588 2.39908 14.4022 2.19186L14.3318 2.13359L14.2969 2.10454L14.2937 2.10195Z" fill="var(--sale)"/></g>', // Skin Care
		'baby-care' => '<g fill="var(--accent-600)"><path d="M7 4.82936C7 6.37714 8.72593 8.00761 10.1497 9.08932C10.9489 9.69644 11.3484 10 12 10C12.6516 10 13.0512 9.69644 13.8503 9.08933C15.2741 8.00763 17 6.37717 17 4.82935C17 2.03918 14.2499 0.997463 12 3.15285C9.75008 0.997463 7 2.03918 7 4.82936Z"/><path d="M6.25993 21.3884H6C5.05719 21.3884 4.58579 21.3884 4.29289 21.0955C4 20.8026 4 20.3312 4 19.3884V18.2764C4 17.7579 4 17.4987 4.13318 17.2672C4.26636 17.0356 4.46727 16.9188 4.8691 16.6851C7.51457 15.1464 11.2715 14.2803 13.7791 15.7759C13.9475 15.8764 14.0991 15.9977 14.2285 16.1431C14.7866 16.77 14.746 17.7161 14.1028 18.2775C13.9669 18.396 13.8222 18.486 13.6764 18.5172C13.7962 18.5033 13.911 18.4874 14.0206 18.4699C14.932 18.3245 15.697 17.8375 16.3974 17.3084L18.2046 15.9433C18.8417 15.462 19.7873 15.4619 20.4245 15.943C20.9982 16.3762 21.1736 17.0894 20.8109 17.6707C20.388 18.3487 19.7921 19.216 19.2199 19.7459C18.6469 20.2766 17.7939 20.7504 17.0975 21.0865C16.326 21.4589 15.4738 21.6734 14.6069 21.8138C12.8488 22.0983 11.0166 22.0549 9.27633 21.6964C8.29253 21.4937 7.27079 21.3884 6.25993 21.3884Z" fill="var(--sale)"/></g>', // Baby Care
		'feminine-care' => '<g fill="var(--accent-600)"><path fill-rule="evenodd" d="M8.10627 18.2468C5.29819 16.0833 2 13.5422 2 9.1371C2 4.27416 7.50016 0.825464 12 5.50063V20.5C11 20.5 10 19.7294 8.96173 18.9109C8.68471 18.6925 8.39814 18.4717 8.10627 18.2468Z" clip-rule="evenodd" fill="var(--sale)"/><path d="M15.0383 18.9109C17.9806 16.5914 22 14 22 9.1371C22 4.27416 16.4998 0.825464 12 5.50063V20.5C13 20.5 14 19.7294 15.0383 18.9109Z"/></g>', // Feminine Care
		'home-care' => '<g fill="var(--accent-600)"><path d="M2 12.2039C2 9.91549 2 8.77128 2.5192 7.82274C3.0384 6.87421 3.98695 6.28551 5.88403 5.10813L7.88403 3.86687C9.88939 2.62229 10.8921 2 12 2C13.1079 2 14.1106 2.62229 16.116 3.86687L18.116 5.10812C20.0131 6.28551 20.9616 6.87421 21.4808 7.82274C22 8.77128 22 9.91549 22 12.2039V13.725C22 17.6258 22 19.5763 20.8284 20.7881C19.6569 22 17.7712 22 14 22H10C6.22876 22 4.34315 22 3.17157 20.7881C2 19.5763 2 17.6258 2 13.725V12.2039Z" fill="var(--sale)"/><path d="M11.25 18C11.25 18.4142 11.5858 18.75 12 18.75C12.4142 18.75 12.75 18.4142 12.75 18V15C12.75 14.5858 12.4142 14.25 12 14.25C11.5858 14.25 11.25 14.5858 11.25 15V18Z"/></g>', // Home Care
		'home-diagnostics' => '<g fill="var(--accent-600)"><circle cx="19" cy="16" r="3"/><path d="M12 1.25C12.4142 1.25 12.75 1.58579 12.75 2V2.25143C12.8612 2.25311 12.9561 2.25675 13.0446 2.26458C14.8548 2.42465 16.2896 3.85953 16.4497 5.66968C16.4643 5.83513 16.4643 6.02257 16.4643 6.29788L16.4643 7.521C16.4643 11.6434 13.1224 14.9853 9.00001 14.9853C4.7198 14.9853 1.25001 11.5155 1.25001 7.23529L1.25 6.29791C1.24997 6.02259 1.24995 5.83514 1.26458 5.66968C1.42465 3.85953 2.85954 2.42465 4.66969 2.26458C4.82536 2.25081 5.00051 2.25002 5.25001 2.24999V2C5.25001 1.58579 5.58579 1.25 6.00001 1.25C6.41422 1.25 6.75001 1.58579 6.75001 2V4C6.75001 4.41421 6.41422 4.75 6.00001 4.75C5.58579 4.75 5.25001 4.41421 5.25001 4V3.75002C4.9866 3.7502 4.88393 3.75148 4.80181 3.75875C3.71573 3.85479 2.85479 4.71572 2.75875 5.80181C2.75074 5.8924 2.75001 6.00802 2.75001 6.3369V7.23529C2.75001 10.6871 5.54823 13.4853 9.00001 13.4853C12.294 13.4853 14.9643 10.815 14.9643 7.521V6.3369C14.9643 6.00802 14.9636 5.8924 14.9555 5.80181C14.8595 4.71572 13.9986 3.85479 12.9125 3.75875C12.8702 3.755 12.8224 3.75285 12.75 3.75162V4C12.75 4.41421 12.4142 4.75 12 4.75C11.5858 4.75 11.25 4.41421 11.25 4V2C11.25 1.58579 11.5858 1.25 12 1.25Z"/><path d="M8.25 14.9496V17.0002C8.25 20.1759 10.8244 22.7502 14 22.7502H14.8824C16.6952 22.7502 18.2756 21.7588 19.1126 20.2922C19.3594 19.8596 19.4822 19.3964 19.542 18.9513C19.3662 18.9834 19.1851 19.0002 19 19.0002C18.6649 19.0002 18.3426 18.9452 18.0417 18.8438C17.9986 19.1109 17.926 19.345 17.8098 19.5487C17.2289 20.5667 16.135 21.2502 14.8824 21.2502H14C11.6528 21.2502 9.75 19.3474 9.75 17.0002V14.9482C9.50334 14.9729 9.25314 14.9855 9.00001 14.9855C8.74699 14.9855 8.4968 14.9733 8.25 14.9496Z" fill="var(--sale)"/></g>', // Home Diagnostics
		'health-protection' => '<g fill="var(--accent-600)"><path d="M3.37752 5.08241C3 5.62028 3 7.21907 3 10.4167V11.9914C3 17.6294 7.23896 20.3655 9.89856 21.5273C10.62 21.8424 10.9807 22 12 22C13.0193 22 13.38 21.8424 14.1014 21.5273C16.761 20.3655 21 17.6294 21 11.9914V10.4167C21 7.21907 21 5.62028 20.6225 5.08241C20.245 4.54454 18.7417 4.02996 15.7351 3.00079L15.1623 2.80472C13.595 2.26824 12.8114 2 12 2C11.1886 2 10.405 2.26824 8.83772 2.80472L8.26491 3.00079C5.25832 4.02996 3.75503 4.54454 3.37752 5.08241Z" fill="var(--sale)"/><path d="M15.0595 10.4995C15.3353 10.1905 15.3085 9.71643 14.9995 9.44055C14.6905 9.16468 14.2164 9.19152 13.9406 9.5005L10.9286 12.8739L10.0595 11.9005C9.78359 11.5915 9.30947 11.5647 9.0005 11.8406C8.69152 12.1164 8.66468 12.5905 8.94055 12.8995L10.3691 14.4995C10.5114 14.6589 10.7149 14.75 10.9286 14.75C11.1422 14.75 11.3457 14.6589 11.488 14.4995L15.0595 10.4995Z"/></g>', // Health & Protection
		'default' => '<g fill="var(--accent-600)"><path d="M4.0828 10.8943C4.52171 8.55339 4.74117 7.38295 5.57434 6.69147C6.40752 6 7.59835 6 9.98003 6H14.0209C16.4026 6 17.5934 6 18.4266 6.69147C19.2598 7.38295 19.4792 8.55339 19.9181 10.8943L20.6681 14.8943C21.2853 18.186 21.5939 19.8318 20.6942 20.9159C19.7945 22 18.12 22 14.7709 22H9.23003C5.88097 22 4.20644 22 3.30672 20.9159C2.40701 19.8318 2.7156 18.186 3.3328 14.8943L4.0828 10.8943Z" fill="var(--sale)"/><path d="M9.75 5C9.75 3.75736 10.7574 2.75 12 2.75C13.2426 2.75 14.25 3.75736 14.25 5V6C14.25 5.99999 14.25 6.00001 14.25 6C14.816 6.00018 15.3119 6.00174 15.7499 6.01488C15.75 6.00993 15.75 6.00497 15.75 6V5C15.75 2.92893 14.0711 1.25 12 1.25C9.92893 1.25 8.25 2.92893 8.25 5V6C8.25 6.00498 8.25005 6.00995 8.25015 6.01491C8.68814 6.00175 9.18397 6.00021 9.75 6.00002C9.75 6.00002 9.75 6.00003 9.75 6.00002V5Z"/><path d="M8.26032 10.8768C8.32842 10.4682 8.71484 10.1922 9.12342 10.2603C9.53199 10.3284 9.80801 10.7148 9.73991 11.1234L8.73991 17.1234C8.67182 17.532 8.2854 17.808 7.87682 17.7399C7.46824 17.6718 7.19223 17.2854 7.26032 16.8768L8.26032 10.8768Z"/><path d="M14.8771 10.2603C15.2856 10.1922 15.672 10.4682 15.7401 10.8768L16.7401 16.8768C16.8082 17.2854 16.5322 17.6718 16.1236 17.7399C15.7151 17.808 15.3287 17.532 15.2606 17.1234L14.2606 11.1234C14.1925 10.7148 14.4685 10.3284 14.8771 10.2603Z"/></g>', // fallback
	);
	$body = isset( $icons[ $slug ] ) ? $icons[ $slug ] : $icons['default'];
	return '<svg width="100%" height="100%" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' . $body . '</svg>';
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
 * Brands for chrome (header mega / footer): distributed first, then house.
 *
 * @return WP_Term[]
 */
function exmart_get_nav_brands() {
	$brands = get_terms(
		array(
			'taxonomy'   => 'product_brand',
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $brands ) || empty( $brands ) ) {
		return array();
	}
	usort(
		$brands,
		static function ( $a, $b ) {
			$ta = get_term_meta( $a->term_id, 'exmart_brand_type', true ) ?: 'distributed';
			$tb = get_term_meta( $b->term_id, 'exmart_brand_type', true ) ?: 'distributed';
			if ( $ta === $tb ) {
				return strcasecmp( $a->name, $b->name );
			}
			return ( 'distributed' === $ta ) ? -1 : 1;
		}
	);
	return $brands;
}

/**
 * Homepage hero banners — the old site's TOP Elementor Image Carousel
 * (filenames like main-banner-*, main-2-2, main-3-2), NOT product/logo carousels.
 *
 * Priority:
 * 1. Appearance → Customize → Homepage Hero
 * 2. First Elementor `image-carousel` on the Home page (carousel attachment IDs)
 * 3. Media Library files matching main-banner / main-N naming
 * 4. Known live main-banner URLs (maps to local attachment when present)
 * 5. Bundled fallback
 *
 * @return array<int, array{id:int,url:string,alt:string,href?:string}>
 */
function exmart_get_hero_images() {
	$images = array();
	$seen   = array();
	$shop   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

	$add = static function ( $attachment_id = 0, $url = '', $alt = '', $href = '' ) use ( &$images, &$seen ) {
		$attachment_id = absint( $attachment_id );
		if ( $attachment_id ) {
			if ( isset( $seen[ 'id:' . $attachment_id ] ) ) {
				return;
			}
			$resolved = wp_get_attachment_image_url( $attachment_id, 'full' );
			if ( ! $resolved ) {
				return;
			}
			$meta_alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
			if ( ! $meta_alt ) {
				$meta_alt = get_the_title( $attachment_id );
			}
			$seen[ 'id:' . $attachment_id ] = true;
			$images[]                       = array(
				'id'   => $attachment_id,
				'url'  => $resolved,
				'alt'  => $meta_alt ? $meta_alt : __( 'exMart banner', 'exmart' ),
				'href' => $href,
			);
			return;
		}

		$url = esc_url_raw( $url );
		if ( ! $url || isset( $seen[ 'url:' . $url ] ) ) {
			return;
		}
		$mapped = absint( attachment_url_to_postid( $url ) );
		if ( $mapped && ! isset( $seen[ 'id:' . $mapped ] ) ) {
			$resolved = wp_get_attachment_image_url( $mapped, 'full' );
			if ( $resolved ) {
				$meta_alt                    = get_post_meta( $mapped, '_wp_attachment_image_alt', true );
				$seen[ 'id:' . $mapped ]     = true;
				$seen[ 'url:' . $url ]       = true;
				$images[]                    = array(
					'id'   => $mapped,
					'url'  => $resolved,
					'alt'  => $meta_alt ? $meta_alt : ( $alt ? $alt : __( 'exMart banner', 'exmart' ) ),
					'href' => $href,
				);
				return;
			}
		}
		$seen[ 'url:' . $url ] = true;
		$images[]              = array(
			'id'   => 0,
			'url'  => $url,
			'alt'  => $alt ? $alt : __( 'exMart banner', 'exmart' ),
			'href' => $href,
		);
	};

	// 1) Customizer overrides.
	$custom_id = absint( get_theme_mod( 'exmart_hero_image', 0 ) );
	if ( $custom_id ) {
		$add( $custom_id );
	}
	$extra_ids = (string) get_theme_mod( 'exmart_hero_image_ids', '' );
	if ( '' !== $extra_ids ) {
		foreach ( preg_split( '/[\s,]+/', $extra_ids ) as $piece ) {
			$add( absint( $piece ) );
		}
	}
	if ( ! empty( $images ) ) {
		return array_values( $images );
	}

	$front_id = (int) get_option( 'page_on_front' );

	// 2) First Elementor Image Carousel on the Home page (= top main banner).
	if ( $front_id ) {
		$raw = get_post_meta( $front_id, '_elementor_data', true );
		if ( is_string( $raw ) && $raw !== '' ) {
			$data = json_decode( $raw, true );
			if ( is_array( $data ) ) {
				foreach ( exmart_elementor_first_image_carousel_ids( $data ) as $cid ) {
					$add( (int) $cid );
				}
			}
		}
	}
	if ( ! empty( $images ) ) {
		return array_values( $images );
	}

	// 3) Media Library by main-banner filename (same assets as live Elementor hero).
	global $wpdb;
	$like_rows = $wpdb->get_col(
		"SELECT post_id FROM {$wpdb->postmeta}
		WHERE meta_key = '_wp_attached_file'
		AND (
			meta_value LIKE '%main-banner%'
			OR meta_value LIKE '%/main-2-%'
			OR meta_value LIKE '%/main-3-%'
			OR meta_value LIKE '%main-2-2%'
			OR meta_value LIKE '%main-3-2%'
		)
		ORDER BY post_id DESC
		LIMIT 12"
	);
	$candidates = array_map( 'absint', $like_rows ? $like_rows : array() );
	usort(
		$candidates,
		static function ( $a, $b ) {
			$fa    = strtolower( (string) get_post_meta( $a, '_wp_attached_file', true ) );
			$fb    = strtolower( (string) get_post_meta( $b, '_wp_attached_file', true ) );
			$score = static function ( $f ) {
				if ( false !== strpos( $f, 'main-banner' ) ) {
					return 0;
				}
				if ( preg_match( '/main-?2/', $f ) ) {
					return 1;
				}
				if ( preg_match( '/main-?3/', $f ) ) {
					return 2;
				}
				return 9;
			};
			return $score( $fa ) <=> $score( $fb );
		}
	);
	foreach ( array_slice( $candidates, 0, 6 ) as $cid ) {
		$add( (int) $cid );
	}
	if ( ! empty( $images ) ) {
		return array_values( $images );
	}

	// 4) Known live-site main banner URLs.
	$known = array(
		array(
			'url' => 'https://exmartegypt.com/wp-content/uploads/2025/12/main-banner-good-sense-spring.jpg',
			'alt' => 'main banner good sense spring',
		),
		array(
			'url' => 'https://exmartegypt.com/wp-content/uploads/2025/07/main-2-2.jpg',
			'alt' => 'main 2-2',
		),
		array(
			'url' => 'https://exmartegypt.com/wp-content/uploads/2025/07/main-3-2.png',
			'alt' => 'main 3-2',
		),
	);
	foreach ( $known as $slide ) {
		$add( 0, $slide['url'], $slide['alt'], $shop );
	}
	if ( ! empty( $images ) ) {
		return array_values( $images );
	}

	return array(
		array(
			'id'   => 0,
			'url'  => EXMART_URI . '/assets/images/hero-product.jpg',
			'alt'  => __( 'exMart banner', 'exmart' ),
			'href' => $shop,
		),
	);
}

/**
 * Walk Elementor JSON and return attachment IDs from the first image-carousel widget.
 *
 * @param array $elements
 * @return int[]
 */
function exmart_elementor_first_image_carousel_ids( $elements ) {
	foreach ( $elements as $el ) {
		if ( ! is_array( $el ) ) {
			continue;
		}
		$widget = isset( $el['widgetType'] ) ? $el['widgetType'] : '';
		if ( 'image-carousel' === $widget ) {
			$carousel = array();
			if ( ! empty( $el['settings']['carousel'] ) && is_array( $el['settings']['carousel'] ) ) {
				$carousel = $el['settings']['carousel'];
			}
			$ids = array();
			foreach ( $carousel as $item ) {
				if ( is_array( $item ) && ! empty( $item['id'] ) ) {
					$ids[] = absint( $item['id'] );
				} elseif ( is_numeric( $item ) ) {
					$ids[] = absint( $item );
				}
			}
			if ( $ids ) {
				return $ids;
			}
		}
		if ( ! empty( $el['elements'] ) && is_array( $el['elements'] ) ) {
			$nested = exmart_elementor_first_image_carousel_ids( $el['elements'] );
			if ( $nested ) {
				return $nested;
			}
		}
	}
	return array();
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
