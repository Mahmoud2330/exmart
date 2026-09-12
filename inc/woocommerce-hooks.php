<?php
/**
 * WooCommerce integration: theme wrapper, product tabs, grid settings,
 * mini-cart fragments, and a couple of small admin fields.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * We keep WooCommerce's own bundled CSS enabled (it carries structural
 * rules the product gallery zoom/lightbox JS depends on) and layer our
 * own styles on top — exmart-style is enqueued after it, and the
 * `.woocommerce …`-scoped rules in assets/css/site.css win the cascade.
 */

/**
 * Show "EGP" as plain text next to prices (matches the original design)
 * instead of the £-style currency glyph, whenever the store currency is EGP.
 */
function exmart_currency_symbol( $symbol, $currency ) {
	return 'EGP' === $currency ? 'EGP&nbsp;' : $symbol;
}
add_filter( 'woocommerce_currency_symbol', 'exmart_currency_symbol', 10, 2 );

/**
 * Product grid defaults (Appearance > Customize > WooCommerce reads these).
 */
function exmart_wc_product_grid_support() {
	add_theme_support( 'woocommerce', array(
		'product_grid' => array(
			'default_rows'    => 4,
			'min_rows'        => 1,
			'max_rows'        => 8,
			'default_columns' => 4,
			'min_columns'     => 2,
			'max_columns'     => 5,
		),
	) );
}
add_action( 'after_setup_theme', 'exmart_wc_product_grid_support' );

/**
 * Wrap all WooCommerce page output in our .em-container so the shop,
 * single product, cart, checkout, and account pages sit inside the
 * same layout grid as the rest of the site.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

/**
 * The optional "Shop Sidebar" widget area (Appearance → Widgets) shows
 * up automatically on the Shop page and any product category/tag
 * archive, as long as you've actually added widgets to it — e.g.
 * WooCommerce's own "Filter Products by Price/Rating/Attribute"
 * widgets. Empty by default, so nothing changes until you use it.
 * (Brand archives use their own bespoke template — taxonomy-product_brand.php —
 * which doesn't route through these hooks, so it's intentionally excluded here.)
 */
function exmart_should_show_shop_sidebar() {
	return is_active_sidebar( 'shop-sidebar' )
		&& function_exists( 'is_woocommerce' )
		&& ( is_shop() || is_product_category() || is_product_tag() );
}

function exmart_wc_wrapper_start() {
	echo '<div class="em-container" style="padding-block: var(--s8);">';
	if ( exmart_should_show_shop_sidebar() ) {
		echo '<div class="em-two-col" style="align-items:start;"><div>';
	}
}
add_action( 'woocommerce_before_main_content', 'exmart_wc_wrapper_start', 10 );

function exmart_wc_wrapper_end() {
	if ( exmart_should_show_shop_sidebar() ) {
		echo '</div><aside class="em-sidebar-col">';
		dynamic_sidebar( 'shop-sidebar' );
		echo '</aside></div>';
	}
	echo '</div>';
}
add_action( 'woocommerce_after_main_content', 'exmart_wc_wrapper_end', 10 );

/**
 * Related products: match the original "You may also like" rail (up to 6).
 */
function exmart_related_products_args( $args ) {
	$args['posts_per_page'] = 6;
	$args['columns']        = 4;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'exmart_related_products_args' );

/**
 * Custom "How to Use" field on the product edit screen (Diagnostics/Health
 * categories additionally get an "Accuracy" tab, generated automatically —
 * no extra field needed for that one).
 */
function exmart_product_how_to_use_field() {
	global $post;
	echo '<div class="options_group">';
	woocommerce_wp_textarea_input( array(
		'id'          => '_exmart_how_to_use',
		'label'       => __( 'How to Use', 'exmart' ),
		'description' => __( 'Shown in its own product tab on the front end.', 'exmart' ),
		'desc_tip'    => true,
	) );
	echo '</div>';
}
add_action( 'woocommerce_product_options_general_product_data', 'exmart_product_how_to_use_field' );

function exmart_save_how_to_use_field( $post_id ) {
	if ( isset( $_POST['_exmart_how_to_use'] ) ) {
		update_post_meta( $post_id, '_exmart_how_to_use', sanitize_textarea_field( wp_unslash( $_POST['_exmart_how_to_use'] ) ) );
	}
}
add_action( 'woocommerce_process_product_meta', 'exmart_save_how_to_use_field' );

/**
 * Add the "How to Use" and "Accuracy" tabs on the single product page.
 */
function exmart_custom_product_tabs( $tabs ) {
	global $product;
	if ( ! $product ) return $tabs;

	$how_to_use = get_post_meta( $product->get_id(), '_exmart_how_to_use', true );
	if ( $how_to_use ) {
		$tabs['exmart_how_to_use'] = array(
			'title'    => __( 'How to Use', 'exmart' ),
			'priority' => 15,
			'callback' => function () use ( $how_to_use ) {
				echo '<p class="em-body">' . wp_kses_post( nl2br( esc_html( $how_to_use ) ) ) . '</p>';
			},
		);
	}

	$accuracy_categories = array( 'home-diagnostics', 'health-protection' );
	if ( has_term( $accuracy_categories, 'product_cat', $product->get_id() ) ) {
		$tabs['exmart_accuracy'] = array(
			'title'    => __( 'Accuracy', 'exmart' ),
			'priority' => 25, // Between core's "additional_information" (20) and "reviews" (30).
			'callback' => function () {
				?>
				<p class="em-body">This device provides readings for informational purposes only. It is not intended to diagnose, treat, cure, or prevent any disease or health condition.</p>
				<p class="em-body">Always consult a qualified healthcare professional for clinical decisions. Readings may vary based on usage technique. Refer to the included manual for accuracy specifications and limitations.</p>
				<?php
			},
		);
	}

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'exmart_custom_product_tabs' );

/**
 * Trust block under the add-to-cart form on the single product page.
 */
function exmart_trust_block() {
	$items = array(
		__( 'Authenticated by exMart — direct from manufacturer', 'exmart' ),
		__( 'Cash on delivery available', 'exmart' ),
		__( 'Free returns within 14 days', 'exmart' ),
		__( 'Delivery 2–5 working days across Egypt', 'exmart' ),
	);
	?>
	<div class="em-pdp-trust-block">
		<?php foreach ( $items as $item ) : ?>
			<div class="em-pdp-trust-block-item">
				<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true" style="flex-shrink:0;margin-top:1px">
					<circle cx="8" cy="8" r="7" stroke="var(--ink-700)" stroke-width="1.5" />
					<path d="M5 8l2 2 4-4" stroke="var(--ink-700)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
				<span class="em-body-s" style="color:var(--ink-700)"><?php echo esc_html( $item ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}
add_action( 'woocommerce_after_add_to_cart_form', 'exmart_trust_block' );

/**
 * Brand + trust lockup line above the product title.
 */
function exmart_product_brand_row() {
	global $product;
	if ( ! $product ) return;
	$terms = get_the_terms( $product->get_id(), 'product_brand' );
	if ( empty( $terms ) || is_wp_error( $terms ) ) return;
	$term = $terms[0];
	$type = exmart_brand_type( $term->term_id );
	?>
	<div class="em-pdp-brand-row">
		<a class="em-pdp-brand-link" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
		<span class="em-trust">
			<svg class="em-trust-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5" /><path d="M5 8l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
			<span class="em-trust-text"><?php echo $type === 'distributed' ? esc_html__( 'Official & sole distributor in Egypt', 'exmart' ) : esc_html__( 'exMart exclusive · our own brand', 'exmart' ); ?></span>
		</span>
	</div>
	<?php
}
add_action( 'woocommerce_single_product_summary', 'exmart_product_brand_row', 4 );

/**
 * Small brand label shown above each product card title. Called
 * directly from woocommerce/content-product.php (that template fully
 * replaces WooCommerce's default loop-item hooks, so this isn't wired
 * to woocommerce_before_shop_loop_item_title).
 */
function exmart_loop_brand_label() {
	global $product;
	$terms = get_the_terms( $product->get_id(), 'product_brand' );
	if ( empty( $terms ) || is_wp_error( $terms ) ) return;
	echo '<span class="em-caption" style="color:var(--ink-500)">' . esc_html( $terms[0]->name ) . '</span>';
}

/**
 * Extend the cart fragments so our header cart-count badge updates on AJAX add-to-cart.
 */
function exmart_cart_count_fragment( $fragments ) {
	ob_start();
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	?>
	<span class="em-icon-count" id="em-cart-count" style="<?php echo $count > 0 ? '' : 'display:none;'; ?>"><?php echo esc_html( $count ); ?></span>
	<?php
	$fragments['#em-cart-count'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'exmart_cart_count_fragment' );

/**
 * Open the mini-cart drawer automatically after an AJAX add-to-cart
 * (mirrors the original app's "add to cart opens the drawer" behavior).
 * Handled in assets/js/site.js by listening for the added_to_cart event.
 */

/**
 * Support ?on_sale=1 on the Shop archive so homepage Offers "View all"
 * (and the header Offers quick link) actually filter sale products.
 *
 * @param WP_Query $q
 */
function exmart_on_sale_product_query( $q ) {
	if ( is_admin() || ! isset( $_GET['on_sale'] ) ) {
		return;
	}
	if ( '1' !== wc_clean( wp_unslash( $_GET['on_sale'] ) ) ) {
		return;
	}
	$ids = function_exists( 'wc_get_product_ids_on_sale' ) ? wc_get_product_ids_on_sale() : array();
	$ids = array_values( array_unique( array_map( 'absint', $ids ) ) );
	if ( empty( $ids ) ) {
		$ids = array( 0 );
	}
	$q->set( 'post__in', $ids );
}
add_action( 'woocommerce_product_query', 'exmart_on_sale_product_query' );

/**
 * Login / Register page URLs (separate Figma pages).
 */
function exmart_login_url() {
	$page = get_page_by_path( 'login' );
	if ( $page ) {
		return get_permalink( $page );
	}
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' );
}

function exmart_register_url() {
	$page = get_page_by_path( 'register' );
	if ( $page ) {
		return get_permalink( $page );
	}
	return exmart_login_url();
}

/**
 * Ensure Login + Register pages exist and WooCommerce registration is usable.
 */
function exmart_ensure_auth_pages() {
	$pages = array(
		'login'    => array(
			'title'    => 'Login',
			'template' => 'template-login.php',
		),
		'register' => array(
			'title'    => 'Register',
			'template' => 'template-register.php',
		),
	);

	foreach ( $pages as $slug => $data ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$tpl = get_post_meta( $existing->ID, '_wp_page_template', true );
			if ( $data['template'] !== $tpl ) {
				update_post_meta( $existing->ID, '_wp_page_template', $data['template'] );
			}
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_title'  => $data['title'],
				'post_name'   => $slug,
				'post_status' => 'publish',
				'post_type'   => 'page',
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_wp_page_template', $data['template'] );
		}
	}

	update_option( 'woocommerce_enable_myaccount_registration', 'yes' );
	update_option( 'woocommerce_registration_generate_username', 'yes' );
	update_option( 'woocommerce_registration_generate_password', 'no' );
}
add_action( 'init', 'exmart_ensure_auth_pages', 25 );

/**
 * Guests hitting My Account (not lost-password etc.) go to the Login page.
 */
function exmart_redirect_account_guests_to_login() {
	if ( is_admin() || is_user_logged_in() ) {
		return;
	}
	if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
		return;
	}
	if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url() ) {
		return;
	}
	$login = get_page_by_path( 'login' );
	if ( ! $login ) {
		return;
	}
	wp_safe_redirect( get_permalink( $login ) );
	exit;
}
add_action( 'template_redirect', 'exmart_redirect_account_guests_to_login', 5 );

/**
 * Validate extra register fields (name + phone) before WC creates the user.
 *
 * @param WP_Error $errors
 * @param string   $username
 * @param string   $email
 * @return WP_Error
 */
function exmart_validate_register_fields( $errors, $username, $email ) {
	$name  = isset( $_POST['exmart_full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['exmart_full_name'] ) ) : '';
	$phone = isset( $_POST['exmart_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['exmart_phone'] ) ) : '';
	$pass  = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '';

	if ( '' === $name ) {
		$errors->add( 'exmart_full_name', __( 'Please enter your full name.', 'exmart' ) );
	}
	if ( '' === $phone ) {
		$errors->add( 'exmart_phone', __( 'Please enter your phone number.', 'exmart' ) );
	}
	if ( strlen( $pass ) > 0 && strlen( $pass ) < 8 ) {
		$errors->add( 'exmart_password', __( 'Password must be at least 8 characters.', 'exmart' ) );
	}

	return $errors;
}
add_filter( 'woocommerce_registration_errors', 'exmart_validate_register_fields', 10, 3 );

/**
 * Save name + phone after customer is created.
 *
 * @param int $customer_id
 */
function exmart_save_register_fields( $customer_id ) {
	$name  = isset( $_POST['exmart_full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['exmart_full_name'] ) ) : '';
	$phone = isset( $_POST['exmart_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['exmart_phone'] ) ) : '';

	if ( $name ) {
		$parts = preg_split( '/\s+/', $name, 2 );
		$first = $parts[0];
		$last  = isset( $parts[1] ) ? $parts[1] : '';
		wp_update_user(
			array(
				'ID'           => $customer_id,
				'first_name'   => $first,
				'last_name'    => $last,
				'display_name' => $name,
			)
		);
		update_user_meta( $customer_id, 'billing_first_name', $first );
		if ( $last ) {
			update_user_meta( $customer_id, 'billing_last_name', $last );
		}
	}
	if ( $phone ) {
		update_user_meta( $customer_id, 'billing_phone', $phone );
	}
}
add_action( 'woocommerce_created_customer', 'exmart_save_register_fields' );

/**
 * Account nav — Figma tabs: Orders · Addresses · Wishlist · Profile.
 *
 * @param array $items
 * @return array
 */
function exmart_account_menu_items( $items ) {
	return array(
		'orders'       => __( 'Orders', 'exmart' ),
		'edit-address' => __( 'Addresses', 'exmart' ),
		'wishlist'     => __( 'Wishlist', 'exmart' ),
		'edit-account' => __( 'Profile', 'exmart' ),
	);
}
add_filter( 'woocommerce_account_menu_items', 'exmart_account_menu_items', 99 );

/**
 * Register /my-account/wishlist/ as a real WooCommerce endpoint
 * so wishlist stays inside the Account shell (Figma behaviour).
 */
function exmart_register_wishlist_endpoint() {
	add_rewrite_endpoint( 'wishlist', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'exmart_register_wishlist_endpoint', 0 );

/**
 * @param array $vars
 * @return array
 */
function exmart_wishlist_query_vars( $vars ) {
	$vars['wishlist'] = 'wishlist';
	return $vars;
}
add_filter( 'woocommerce_get_query_vars', 'exmart_wishlist_query_vars' );

/**
 * Flush rewrites once after introducing the wishlist endpoint.
 */
function exmart_maybe_flush_wishlist_endpoint() {
	if ( get_option( 'exmart_flush_wishlist_endpoint_v2' ) ) {
		return;
	}
	flush_rewrite_rules( false );
	update_option( 'exmart_flush_wishlist_endpoint_v2', 1, false );
}
add_action( 'init', 'exmart_maybe_flush_wishlist_endpoint', 99 );

/**
 * Wishlist panel content inside My Account.
 */
function exmart_account_wishlist_content() {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	?>
	<div class="em-account-panel em-account-wishlist" data-em-wishlist-panel>
		<h2 class="em-h4 em-account-panel-title">
			<?php esc_html_e( 'Wishlist', 'exmart' ); ?>
			<span id="em-wishlist-count" class="em-account-wishlist-count"></span>
		</h2>

		<div id="em-wishlist-loading" class="em-body" style="color:var(--ink-500);"><?php esc_html_e( 'Loading…', 'exmart' ); ?></div>

		<div id="em-wishlist-empty" class="em-empty-state" hidden>
			<p class="em-body" style="color:var(--ink-500);margin-bottom:var(--s4);"><?php esc_html_e( 'Your wishlist is empty.', 'exmart' ); ?></p>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="em-btn em-btn-primary"><?php esc_html_e( 'Discover products', 'exmart' ); ?></a>
		</div>

		<ul class="products em-grid em-account-wishlist-grid" id="em-wishlist-grid" hidden></ul>
	</div>
	<?php
}
add_action( 'woocommerce_account_wishlist_endpoint', 'exmart_account_wishlist_content' );

/**
 * Logged-in users landing on /my-account/ go to Orders (Figma default tab).
 */
function exmart_account_default_to_orders() {
	if ( is_admin() || ! is_user_logged_in() ) {
		return;
	}
	if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
		return;
	}
	if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url() ) {
		return;
	}
	wp_safe_redirect( wc_get_account_endpoint_url( 'orders' ) );
	exit;
}
add_action( 'template_redirect', 'exmart_account_default_to_orders', 20 );

/**
 * Save phone from Profile form.
 *
 * @param int $user_id
 */
function exmart_save_account_phone( $user_id ) {
	if ( isset( $_POST['billing_phone'] ) ) {
		update_user_meta( $user_id, 'billing_phone', sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) );
	}
}
add_action( 'woocommerce_save_account_details', 'exmart_save_account_phone' );
