<?php
/**
 * One-time setup that runs when the theme is activated: seeds the
 * brand taxonomy terms, creates the theme's content pages (About,
 * Contact, FAQ, etc.) with their templates pre-assigned, sets Home as
 * the static front page, and points WooCommerce at Egypt/EGP.
 *
 * Everything here is idempotent and guarded by a one-time flag, so
 * re-activating the theme later won't duplicate pages or clobber
 * settings you've since changed.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function exmart_activate() {
	exmart_seed_brands();
	exmart_seed_pages();

	if ( ! get_option( 'exmart_activated_once' ) ) {
		exmart_seed_woocommerce_locale();
		update_option( 'exmart_activated_once', 1 );
	}

	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'exmart_activate' );

function exmart_seed_brands() {
	$brands = array(
		array( 'name' => 'Diversey',  'slug' => 'diversey',  'type' => 'distributed', 'color' => '#0066CC', 'tagline' => 'Professional hygiene & cleaning — trusted in 175 countries.', 'desc' => 'Diversey is a world leader in professional cleaning and hygiene solutions. exMart is the official and sole distributor of Diversey products in Egypt, supplying the same formulations used in hospitals, hotels, and food-service facilities worldwide.' ),
		array( 'name' => 'Grace',     'slug' => 'grace',     'type' => 'distributed', 'color' => '#2D6A4F', 'tagline' => 'Everyday surface care made to professional standards.', 'desc' => 'Grace delivers high-performance household cleaning products developed for the Egyptian home. As the official and sole distributor in Egypt, exMart guarantees genuine Grace formulations with full manufacturer backing.' ),
		array( 'name' => 'Oview',     'slug' => 'oview',     'type' => 'distributed', 'color' => '#6A0572', 'tagline' => 'Precision diagnostics for the home.', 'desc' => 'Oview designs accurate, CE-marked home diagnostic devices including pulse oximeters, blood glucose monitors, and smart weighing scales. exMart is the official and sole distributor of Oview products in Egypt.' ),
		array( 'name' => 'SureCheck', 'slug' => 'surecheck', 'type' => 'distributed', 'color' => '#C41230', 'tagline' => 'Reliable health monitoring you can trust.', 'desc' => 'SureCheck develops clinically validated health and diagnostics products — from blood pressure monitors to rapid antigen tests. exMart is the official and sole distributor of SureCheck products in Egypt.' ),
		array( 'name' => 'Qualita',   'slug' => 'qualita',   'type' => 'house',       'color' => '#E07B39', 'tagline' => 'Egyptian-made quality, globally sourced ingredients.', 'desc' => 'Qualita is an exMart exclusive brand — formulated and quality-tested by our in-house team. From anti-bacterial wipes to scalp care, Qualita brings lab-grade standards to everyday personal and home care.' ),
		array( 'name' => 'Vodlia',    'slug' => 'vodlia',    'type' => 'house',       'color' => '#E76F51', 'tagline' => 'Science-led personal care for Egyptian skin.', 'desc' => 'Vodlia is an exMart exclusive brand focused on evidence-based skincare, feminine care, and intimate hygiene. Every Vodlia formula is dermatologist-reviewed and pH-appropriate for Egyptian climate conditions.' ),
		array( 'name' => 'Eliv',      'slug' => 'eliv',      'type' => 'house',       'color' => '#74B3CE', 'tagline' => 'Gentle care crafted for babies from day one.', 'desc' => 'Eliv is an exMart exclusive baby care brand. Every product is hypoallergenic, paediatrician-reviewed, and free from harsh preservatives, fragrances, and sulphates — developed specifically for sensitive newborn skin.' ),
		array( 'name' => 'Verve',     'slug' => 'verve',     'type' => 'house',       'color' => '#9B5DE5', 'tagline' => 'Hair & skin science with visible results.', 'desc' => 'Verve is an exMart exclusive brand combining active dermatology ingredients with appealing textures. The range covers hair growth, keratin repair, vitamin C brightening, and SPF protection — all developed for the Egyptian market.' ),
	);

	foreach ( $brands as $brand ) {
		$existing = get_term_by( 'slug', $brand['slug'], 'product_brand' );
		if ( $existing ) {
			$term_id = $existing->term_id;
		} else {
			$result = wp_insert_term( $brand['name'], 'product_brand', array(
				'slug'        => $brand['slug'],
				'description' => $brand['desc'],
			) );
			if ( is_wp_error( $result ) ) continue;
			$term_id = $result['term_id'];
		}
		update_term_meta( $term_id, 'exmart_brand_color', $brand['color'] );
		update_term_meta( $term_id, 'exmart_brand_type', $brand['type'] );
		update_term_meta( $term_id, 'exmart_brand_tagline', $brand['tagline'] );
	}
}

function exmart_seed_pages() {
	$pages = array(
		'home'     => array( 'title' => 'Home', 'template' => '' ),
		'about'    => array( 'title' => 'About exMart', 'template' => 'template-about.php' ),
		'contact'  => array( 'title' => 'Contact Us', 'template' => 'template-contact.php' ),
		'faq'      => array( 'title' => 'FAQ', 'template' => 'template-faq.php' ),
		'shipping' => array( 'title' => 'Shipping & Returns', 'template' => 'template-shipping.php' ),
		'payment'  => array( 'title' => 'Payment Methods', 'template' => 'template-payment.php' ),
		'privacy'  => array( 'title' => 'Privacy & Terms', 'template' => 'template-privacy.php' ),
		'brands'   => array( 'title' => 'Brands', 'template' => 'template-brands.php' ),
		'wishlist' => array( 'title' => 'Wishlist', 'template' => 'template-wishlist.php' ),
	);

	$ids = array();

	foreach ( $pages as $slug => $data ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$ids[ $slug ] = $existing->ID;
			continue;
		}
		$id = wp_insert_post( array(
			'post_title'  => $data['title'],
			'post_name'   => $slug,
			'post_status' => 'publish',
			'post_type'   => 'page',
		) );
		if ( $id && ! is_wp_error( $id ) ) {
			if ( $data['template'] ) {
				update_post_meta( $id, '_wp_page_template', $data['template'] );
			}
			$ids[ $slug ] = $id;
		}
	}

	if ( ! empty( $ids['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
	}
}

/**
 * Point a fresh WooCommerce install at Egypt/EGP — only runs once, so
 * it never overwrites settings you've deliberately changed afterwards.
 */
function exmart_seed_woocommerce_locale() {
	if ( get_option( 'woocommerce_default_country' ) === 'US' || ! get_option( 'woocommerce_default_country' ) ) {
		update_option( 'woocommerce_default_country', 'EG' );
	}
	if ( get_option( 'woocommerce_currency' ) === 'USD' || ! get_option( 'woocommerce_currency' ) ) {
		update_option( 'woocommerce_currency', 'EGP' );
	}
}
