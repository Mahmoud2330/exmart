<?php
/**
 * Core theme setup: supports, menus, assets.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function exmart_setup() {
	load_theme_textdomain( 'exmart', EXMART_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );

	// WooCommerce support.
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	set_post_thumbnail_size( 600, 600, true );
	add_image_size( 'exmart-thumb', 220, 220, true );
	add_image_size( 'exmart-card', 400, 400, true );

	register_nav_menus( array(
		'primary'        => __( 'Primary Navigation (top links after Categories/Brands)', 'exmart' ),
		'footer-service'  => __( 'Footer — Service links', 'exmart' ),
	) );
}
add_action( 'after_setup_theme', 'exmart_setup' );

/**
 * Register widget areas (kept minimal; theme mostly uses hard-coded footer columns).
 */
function exmart_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Shop Sidebar', 'exmart' ),
		'id'            => 'shop-sidebar',
		'description'   => __( 'Shown beside the product grid on the Shop page and category/tag archives — add WooCommerce\'s "Filter by Price/Rating/Attribute" widgets here. Empty by default: nothing changes until you add a widget.', 'exmart' ),
		'before_widget' => '<div class="em-widget">',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="em-overline" style="color:var(--ink-500);margin-bottom:var(--s3)">',
		'after_title'   => '</p>',
	) );
}
add_action( 'widgets_init', 'exmart_widgets_init' );

/**
 * Enqueue styles & scripts.
 */
function exmart_scripts() {
	wp_enqueue_style( 'exmart-fonts', 'https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&display=swap', array(), null );

	// Depend on WooCommerce's own stylesheets (when present) so ours loads
	// after them and wins the cascade, without having to disable them.
	$wc_deps = array();
	foreach ( array( 'woocommerce-general', 'woocommerce-layout', 'woocommerce-smallscreen' ) as $handle ) {
		if ( wp_style_is( $handle, 'registered' ) ) $wc_deps[] = $handle;
	}
	wp_enqueue_style( 'exmart-style', get_stylesheet_uri(), $wc_deps, EXMART_VERSION );

	// Layout/component CSS (header, footer, mega menus, drawers, homepage,
	// PDP grid, WooCommerce class-name styling) lives in its own file so it
	// loads as a real <link> tag rather than a CSS @import.
	wp_enqueue_style( 'exmart-site', EXMART_URI . '/assets/css/site.css', array( 'exmart-style' ), EXMART_VERSION );

	wp_enqueue_script( 'exmart-main', EXMART_URI . '/assets/js/site.js', array(), EXMART_VERSION, true );

	wp_localize_script( 'exmart-main', 'exmartData', array(
		'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
		'nonce'     => wp_create_nonce( 'exmart_ajax' ),
		'shopUrl'   => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ),
		'searchUrl' => home_url( '/' ),
	) );

	if ( is_singular( 'product' ) ) {
		wp_enqueue_script( 'wc-add-to-cart-variation' );
	}
}
add_action( 'wp_enqueue_scripts', 'exmart_scripts' );

/**
 * Preconnect to Google Fonts for a touch of performance.
 */
function exmart_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'exmart_resource_hints', 10, 2 );

/**
 * Nicer excerpt length for search/blog fallback contexts (not used by product loop).
 */
function exmart_excerpt_length( $length ) {
	return 24;
}
add_filter( 'excerpt_length', 'exmart_excerpt_length' );

/**
 * Customizer: override hero banner slides (main-banner images).
 *
 * @param WP_Customize_Manager $wp_customize
 */
function exmart_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'exmart_hero',
		array(
			'title'       => __( 'Homepage Hero', 'exmart' ),
			'description' => __( 'Hero image slot uses the old site’s main-banner carousel (main-banner / main-2 / main-3). Override here if needed — the Figma hero layout stays the same.', 'exmart' ),
			'priority'    => 30,
		)
	);

	$wp_customize->add_setting(
		'exmart_hero_image',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'exmart_hero_image',
			array(
				'label'     => __( 'Hero image', 'exmart' ),
				'section'   => 'exmart_hero',
				'mime_type' => 'image',
			)
		)
	);

	$wp_customize->add_setting(
		'exmart_hero_image_ids',
		array(
			'default'           => '',
			'sanitize_callback' => static function ( $value ) {
				$ids = array_filter( array_map( 'absint', preg_split( '/[\s,]+/', (string) $value ) ) );
				return implode( ',', $ids );
			},
		)
	);
	$wp_customize->add_control(
		'exmart_hero_image_ids',
		array(
			'label'       => __( 'Extra hero slide IDs (optional)', 'exmart' ),
			'description' => __( 'Comma-separated Media Library IDs for rotating slides inside the hero image slot.', 'exmart' ),
			'section'     => 'exmart_hero',
			'type'        => 'text',
		)
	);
}
add_action( 'customize_register', 'exmart_customize_register' );
