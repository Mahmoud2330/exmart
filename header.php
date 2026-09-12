<?php
/**
 * Header: announcement bar, logo, search, mega menus, mini-cart drawer,
 * mobile nav drawer.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$product_cats         = exmart_get_nav_categories( 8 );
$product_brands       = exmart_get_nav_brands();
$brands_distributed   = array_filter( $product_brands, fn( $t ) => exmart_brand_type( $t->term_id ) === 'distributed' );
$brands_house         = array_filter( $product_brands, fn( $t ) => exmart_brand_type( $t->term_id ) === 'house' );

$cart_count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
$wishlist_url = home_url( '/wishlist/' );
$account_url  = is_user_logged_in()
	? ( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' ) )
	: ( function_exists( 'exmart_login_url' ) ? exmart_login_url() : home_url( '/login/' ) );
$cart_url     = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
$checkout_url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout/' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="em-skip-link" href="#main-content"><?php esc_html_e( 'Skip to content', 'exmart' ); ?></a>

<div id="page" style="min-height:100vh;display:flex;flex-direction:column;">

	<div class="em-announce" id="em-announce" role="banner">
		<span><?php esc_html_e( 'Free shipping on orders over EGP 300 · Cash on delivery available nationwide', 'exmart' ); ?></span>
		<button class="em-announce-close" id="em-announce-close" aria-label="<?php esc_attr_e( 'Dismiss announcement', 'exmart' ); ?>">
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
		</button>
	</div>

	<header class="em-site-header">
		<div class="em-container em-header-bar">
			<div class="em-header-left">
				<button class="em-btn-icon em-mobile-only" id="em-hamburger" aria-label="<?php esc_attr_e( 'Open menu', 'exmart' ); ?>">
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
				</button>
				<button class="em-btn-icon em-desktop-only" id="em-header-search-toggle" aria-label="<?php esc_attr_e( 'Search', 'exmart' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
				</button>
				<a href="<?php echo esc_url( $wishlist_url ); ?>" class="em-btn-icon em-desktop-only em-icon-badge" aria-label="<?php esc_attr_e( 'Wishlist', 'exmart' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" stroke="currentColor" stroke-width="2"/></svg>
					<span class="em-icon-dot" id="em-wishlist-dot" hidden></span>
				</a>
			</div>

			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="em-header-logo" aria-label="<?php esc_attr_e( 'exMart home', 'exmart' ); ?>">
				<img src="<?php echo esc_url( EXMART_URI . '/assets/images/logo.png' ); ?>" alt="<?php esc_attr_e( 'exMart Online Shopping', 'exmart' ); ?>" width="180" height="48" />
			</a>

			<div class="em-header-right">
				<a href="<?php echo esc_url( $wishlist_url ); ?>" class="em-btn-icon em-mobile-only em-icon-badge" aria-label="<?php esc_attr_e( 'Wishlist', 'exmart' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" stroke="currentColor" stroke-width="2"/></svg>
				</a>
				<a href="<?php echo esc_url( $account_url ); ?>" class="em-btn-icon em-desktop-only" aria-label="<?php esc_attr_e( 'My account', 'exmart' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="2"/></svg>
				</a>
				<button class="em-btn-icon em-desktop-only em-icon-badge" id="em-cart-toggle" aria-label="<?php esc_attr_e( 'Cart', 'exmart' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 3h2l2.4 12.4a2 2 0 002 1.6h8.2a2 2 0 002-1.6L21 8H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="21" r="1" fill="currentColor"/><circle cx="18" cy="21" r="1" fill="currentColor"/></svg>
					<span class="em-icon-count" id="em-cart-count" <?php echo $cart_count > 0 ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $cart_count ); ?></span>
				</button>
			</div>
		</div>

		<div id="em-header-search-overlay" class="em-header-search-overlay" hidden>
			<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search" style="flex:1;position:relative;">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--ink-400);pointer-events:none;"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
				<input type="hidden" name="post_type" value="product" />
				<input class="em-input" style="padding-inline-start:40px;min-height:48px;" type="search" name="s" placeholder="<?php esc_attr_e( 'Search products, brands…', 'exmart' ); ?>" aria-label="<?php esc_attr_e( 'Search', 'exmart' ); ?>" />
			</form>
			<button class="em-btn-icon" id="em-header-search-close" aria-label="<?php esc_attr_e( 'Close search', 'exmart' ); ?>">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
			</button>
		</div>

		<nav class="em-main-nav" aria-label="<?php esc_attr_e( 'Main navigation', 'exmart' ); ?>" id="em-desktop-nav">
			<div class="em-container em-main-nav-row">
				<button class="em-nav-btn" id="em-cats-toggle" aria-expanded="false" aria-haspopup="true">
					<?php esc_html_e( 'Categories', 'exmart' ); ?>
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
				<button class="em-nav-btn" id="em-brands-toggle" aria-expanded="false" aria-haspopup="true">
					<?php esc_html_e( 'Brands', 'exmart' ); ?>
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
				<hr class="em-nav-sep" aria-hidden="true" />
				<?php
				$quick_links = array();
				if ( $tag = get_term_by( 'slug', 'wipes', 'product_tag' ) ) $quick_links[] = array( 'label' => __( 'Wipes', 'exmart' ), 'href' => get_term_link( $tag ) );
				if ( $tag = get_term_by( 'slug', 'disinfectants-sanitizers', 'product_tag' ) ) $quick_links[] = array( 'label' => __( 'Disinfectants', 'exmart' ), 'href' => get_term_link( $tag ) );
				$quick_links[] = array( 'label' => __( 'Offers', 'exmart' ), 'href' => exmart_rail_view_all_url( 'offers' ) );
				$quick_links[] = array( 'label' => __( 'New', 'exmart' ), 'href' => exmart_rail_view_all_url( 'new_arrivals' ) );
				foreach ( $quick_links as $l ) :
					if ( is_wp_error( $l['href'] ) ) continue;
					?>
					<a class="em-nav-link" href="<?php echo esc_url( $l['href'] ); ?>"><?php echo esc_html( $l['label'] ); ?></a>
				<?php endforeach; ?>
			</div>

			<div class="em-mega" id="em-cats-mega" role="dialog" aria-label="<?php esc_attr_e( 'Categories menu', 'exmart' ); ?>">
				<div class="em-mega-inner em-container">
					<div class="em-mega-grid-cats">
						<?php foreach ( $product_cats as $cat ) : ?>
							<div>
								<a class="em-mega-cat-title" href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<div class="em-mega" id="em-brands-mega" role="dialog" aria-label="<?php esc_attr_e( 'Brands menu', 'exmart' ); ?>">
				<div class="em-mega-inner em-container">
					<div class="em-mega-grid-brands">
						<div>
							<p class="em-overline em-mega-brand-col-title"><?php esc_html_e( 'Brands we distribute', 'exmart' ); ?></p>
							<div class="em-mega-brand-list">
								<?php foreach ( $brands_distributed as $b ) : ?>
									<a class="em-mega-brand-item" href="<?php echo esc_url( get_term_link( $b ) ); ?>">
										<span class="em-mega-brand-name"><?php echo esc_html( $b->name ); ?></span>
										<span class="em-mega-brand-sub"><?php esc_html_e( 'Sole distributor in Egypt', 'exmart' ); ?></span>
									</a>
								<?php endforeach; ?>
							</div>
						</div>
						<div>
							<p class="em-overline em-mega-brand-col-title"><?php esc_html_e( 'exMart brands', 'exmart' ); ?></p>
							<div class="em-mega-brand-list">
								<?php foreach ( $brands_house as $b ) : ?>
									<a class="em-mega-brand-item" href="<?php echo esc_url( get_term_link( $b ) ); ?>">
										<span class="em-mega-brand-name"><?php echo esc_html( $b->name ); ?></span>
										<span class="em-mega-brand-sub"><?php esc_html_e( 'exMart exclusive brand', 'exmart' ); ?></span>
									</a>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</nav>
	</header>

	<!-- Mobile nav drawer -->
	<div class="em-backdrop" id="em-mobile-nav-backdrop" hidden></div>
	<nav class="em-drawer em-drawer-l" id="em-mobile-nav" aria-label="<?php esc_attr_e( 'Mobile navigation', 'exmart' ); ?>" hidden>
		<div class="em-drawer-header">
			<img src="<?php echo esc_url( EXMART_URI . '/assets/images/logo.png' ); ?>" alt="exMart" height="36" style="width:auto;" />
			<button class="em-btn-icon" id="em-mobile-nav-close" aria-label="<?php esc_attr_e( 'Close menu', 'exmart' ); ?>">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
			</button>
		</div>
		<div class="em-drawer-body" style="padding:0;">
			<div class="em-mobile-nav-section">
				<p class="em-overline" style="color:var(--ink-500);margin-bottom:var(--s3)"><?php esc_html_e( 'Shop', 'exmart' ); ?></p>
				<a class="em-mobile-nav-link strong" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'All Products', 'exmart' ); ?></a>
				<?php foreach ( $product_cats as $cat ) : ?>
					<a class="em-mobile-nav-link" href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
				<?php endforeach; ?>
			</div>
			<div class="em-mobile-nav-section">
				<p class="em-overline" style="color:var(--ink-500);margin-bottom:var(--s3)"><?php esc_html_e( 'Brands', 'exmart' ); ?></p>
				<?php foreach ( $product_brands as $b ) : ?>
					<a class="em-mobile-nav-link" href="<?php echo esc_url( get_term_link( $b ) ); ?>"><?php echo esc_html( $b->name ); ?></a>
				<?php endforeach; ?>
			</div>
			<div class="em-mobile-nav-section">
				<a class="em-mobile-nav-link" href="<?php echo esc_url( $account_url ); ?>"><?php esc_html_e( 'My Account', 'exmart' ); ?></a>
				<a class="em-mobile-nav-link" href="<?php echo esc_url( $wishlist_url ); ?>"><?php esc_html_e( 'Wishlist', 'exmart' ); ?></a>
				<a class="em-mobile-nav-link" href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About', 'exmart' ); ?></a>
				<a class="em-mobile-nav-link" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'exmart' ); ?></a>
			</div>
		</div>
	</nav>

	<!-- Mini-cart drawer -->
	<div class="em-backdrop" id="em-cart-backdrop" hidden></div>
	<aside class="em-drawer" id="em-cart-drawer" aria-label="<?php esc_attr_e( 'Shopping cart', 'exmart' ); ?>" hidden>
		<div class="em-drawer-header">
			<h2 class="em-h4"><?php esc_html_e( 'Cart', 'exmart' ); ?></h2>
			<button class="em-btn-icon" id="em-cart-close" aria-label="<?php esc_attr_e( 'Close cart', 'exmart' ); ?>">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
			</button>
		</div>
		<div class="em-drawer-body widget_shopping_cart_content">
			<?php if ( function_exists( 'woocommerce_mini_cart' ) ) woocommerce_mini_cart(); ?>
		</div>
	</aside>

	<style>@media(max-width:767px){#main-content{padding-bottom:88px;}}</style>
	<main id="main-content" style="flex:1;">
