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
/* Guests use the public /wishlist/ page (localStorage). Logged-in users
   stay in the Account shell at /my-account/wishlist/. */
$wishlist_page = get_page_by_path( 'wishlist' );
$wishlist_url  = ( is_user_logged_in() && function_exists( 'wc_get_account_endpoint_url' ) )
	? wc_get_account_endpoint_url( 'wishlist' )
	: ( $wishlist_page ? get_permalink( $wishlist_page ) : home_url( '/wishlist/' ) );
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
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M19.0068 5L5.00684 19"/><path d="M19 19L5 5"/></g></svg>
		</button>
	</div>

	<header class="em-site-header">
		<div class="em-container em-header-bar">
			<div class="em-header-left">
				<button class="em-btn-icon em-mobile-only" id="em-hamburger" aria-label="<?php esc_attr_e( 'Open menu', 'exmart' ); ?>">
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M20 7L4 7"/><path d="M20 12L4 12"/><path d="M20 17L4 17"/></g></svg>
				</button>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="em-header-logo" aria-label="<?php esc_attr_e( 'exMart home', 'exmart' ); ?>">
					<img src="<?php echo esc_url( EXMART_URI . '/assets/images/logo.png' ); ?>" alt="<?php esc_attr_e( 'exMart Online Shopping', 'exmart' ); ?>" width="180" height="48" />
				</a>
			</div>

			<div class="em-header-search-bar--inline">
				<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search" class="em-header-search-form">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><circle cx="11.5" cy="11.5" r="9.5"/><path d="M18.2173 18.2178L21.9999 22.0004"/></g></svg>
					<input type="hidden" name="post_type" value="product" />
					<input class="em-input" type="search" name="s" placeholder="<?php esc_attr_e( 'Search products, brands…', 'exmart' ); ?>" aria-label="<?php esc_attr_e( 'Search', 'exmart' ); ?>" />
				</form>
			</div>

			<div class="em-header-right">
				<a href="<?php echo esc_url( $wishlist_url ); ?>" class="em-btn-icon em-icon-badge" aria-label="<?php esc_attr_e( 'Wishlist', 'exmart' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2 9.1371C2 14 6.01943 16.5914 8.96173 18.9109C10 19.7294 11 20.5 12 20.5C13 20.5 14 19.7294 15.0383 18.9109C17.9806 16.5914 22 14 22 9.1371C22 4.27416 16.4998 0.825464 12 5.50063C7.50016 0.825464 2 4.27416 2 9.1371Z"/></svg>
					<span class="em-icon-dot" id="em-wishlist-dot" hidden></span>
				</a>
				<a href="<?php echo esc_url( $account_url ); ?>" class="em-btn-icon em-desktop-only" aria-label="<?php esc_attr_e( 'My account', 'exmart' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><circle cx="12" cy="6" r="4"/><path d="M20 17.5C20 19.9853 20 22 12 22C4 22 4 19.9853 4 17.5C4 15.0147 7.58172 13 12 13C16.4183 13 20 15.0147 20 17.5Z"/></g></svg>
				</a>
				<button class="em-btn-icon em-icon-badge" id="em-cart-toggle" aria-label="<?php esc_attr_e( 'Cart', 'exmart' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M7.5 18C8.32843 18 9 18.6716 9 19.5C9 20.3284 8.32843 21 7.5 21C6.67157 21 6 20.3284 6 19.5C6 18.6716 6.67157 18 7.5 18Z"/><path d="M16.5 18.0001C17.3284 18.0001 18 18.6716 18 19.5001C18 20.3285 17.3284 21.0001 16.5 21.0001C15.6716 21.0001 15 20.3285 15 19.5001C15 18.6716 15.6716 18.0001 16.5 18.0001Z"/><path d="M2 3L2.26121 3.09184C3.5628 3.54945 4.2136 3.77826 4.58584 4.32298C4.95808 4.86771 4.95808 5.59126 4.95808 7.03836V9.76C4.95808 12.7016 5.02132 13.6723 5.88772 14.5862C6.75412 15.5 8.14857 15.5 10.9375 15.5H16.2404C17.8014 15.5 18.5819 15.5 19.1336 15.0504C19.6853 14.6008 19.8429 13.8364 20.158 12.3075L20.6578 9.88275C21.0049 8.14369 21.1784 7.27417 20.7345 6.69708C20.2906 6.12 18.7738 6.12 17.0888 6.12H4.95808"/></g></svg>
					<span class="em-icon-count em-cart-count-badge" id="em-cart-count" <?php echo $cart_count > 0 ? '' : 'hidden'; ?>><?php echo esc_html( (string) $cart_count ); ?></span>
				</button>
			</div>
		</div>

		<div class="em-header-search-bar">
			<div class="em-container">
				<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search" class="em-header-search-form">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><circle cx="11.5" cy="11.5" r="9.5"/><path d="M18.2173 18.2178L21.9999 22.0004"/></g></svg>
					<input type="hidden" name="post_type" value="product" />
					<input class="em-input" type="search" name="s" placeholder="<?php esc_attr_e( 'Search products, brands…', 'exmart' ); ?>" aria-label="<?php esc_attr_e( 'Search', 'exmart' ); ?>" />
				</form>
			</div>
		</div>

		<nav class="em-main-nav" aria-label="<?php esc_attr_e( 'Main navigation', 'exmart' ); ?>" id="em-desktop-nav">
			<div class="em-container em-main-nav-row">
				<button class="em-nav-btn" id="em-cats-toggle" aria-expanded="false" aria-haspopup="true">
					<?php esc_html_e( 'Categories', 'exmart' ); ?>
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9L12 15L5 9"/></svg>
				</button>
				<button class="em-nav-btn" id="em-brands-toggle" aria-expanded="false" aria-haspopup="true">
					<?php esc_html_e( 'Brands', 'exmart' ); ?>
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9L12 15L5 9"/></svg>
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
			<img class="em-drawer-logo" src="<?php echo esc_url( EXMART_URI . '/assets/images/logo.png' ); ?>" alt="exMart" width="120" height="28" />
			<button class="em-btn-icon" id="em-mobile-nav-close" aria-label="<?php esc_attr_e( 'Close menu', 'exmart' ); ?>">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M19.0068 5L5.00684 19"/><path d="M19 19L5 5"/></g></svg>
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
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M19.0068 5L5.00684 19"/><path d="M19 19L5 5"/></g></svg>
			</button>
		</div>
		<div class="em-drawer-body">
			<div class="widget_shopping_cart_content">
				<?php if ( function_exists( 'woocommerce_mini_cart' ) ) { woocommerce_mini_cart(); } ?>
			</div>
		</div>
	</aside>

	<main id="main-content" style="flex:1;">
