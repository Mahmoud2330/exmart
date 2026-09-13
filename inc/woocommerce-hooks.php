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
 * Shop / category / tag archives use woocommerce/archive-product.php
 * (filters + product grid). Other WC surfaces still get .em-container.
 */
function exmart_is_shop_plp() {
	return function_exists( 'is_shop' )
		&& ( is_shop() || is_product_category() || is_product_tag() );
}

function exmart_wc_wrapper_start() {
	if ( exmart_is_shop_plp() ) {
		return;
	}
	echo '<div class="em-container" style="padding-block: var(--s8);">';
}
add_action( 'woocommerce_before_main_content', 'exmart_wc_wrapper_start', 10 );

function exmart_wc_wrapper_end() {
	if ( exmart_is_shop_plp() ) {
		return;
	}
	echo '</div>';
}
add_action( 'woocommerce_after_main_content', 'exmart_wc_wrapper_end', 10 );

/* Default WC sidebar dumps blog widgets beside the shop — remove it. */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 );
remove_action( 'woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header', 10 );

add_filter( 'loop_shop_columns', function () {
	return 4;
} );

/**
 * Free-shipping message threshold (matches announcement bar copy).
 *
 * @return float
 */
function exmart_free_shipping_threshold() {
	return (float) apply_filters( 'exmart_free_shipping_threshold', 300 );
}

/* Cart page: keep Order Summary clean — cross-sells go below if needed later. */
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

/**
 * Figma empty-cart state (icon + copy + Continue shopping).
 */
function exmart_render_cart_empty_state() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;

	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	?>
	<div class="em-empty-state em-cart-empty">
		<div class="em-empty-icon" aria-hidden="true">
			<svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M3 3h2l2.4 12.4a2 2 0 002 1.6h8.2a2 2 0 002-1.6L21 8H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				<circle cx="9" cy="21" r="1" fill="currentColor"/>
				<circle cx="18" cy="21" r="1" fill="currentColor"/>
			</svg>
		</div>
		<h1 class="em-h3"><?php esc_html_e( 'Your cart is empty', 'exmart' ); ?></h1>
		<p class="em-body em-cart-empty-copy"><?php esc_html_e( 'Add products from the shop and they will appear here.', 'exmart' ); ?></p>
		<a class="em-btn em-btn-primary" href="<?php echo esc_url( $shop_url ? $shop_url : home_url( '/' ) ); ?>"><?php esc_html_e( 'Continue shopping', 'exmart' ); ?></a>
	</div>
	<?php
}
add_action( 'woocommerce_cart_is_empty', 'exmart_render_cart_empty_state', 10 );

/**
 * Prefer our empty state over WC’s default info banner.
 */
add_action( 'wp', function () {
	remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
}, 20 );

/**
 * Apply Brand / Category / In-stock filters from the PLP sidebar.
 *
 * Query args: filter_brand[], filter_cat[], in_stock=1
 */
function exmart_apply_shop_filters( $q ) {
	if ( is_admin() ) {
		return;
	}

	$tax_query = $q->get( 'tax_query' );
	if ( ! is_array( $tax_query ) ) {
		$tax_query = array();
	}

	if ( ! empty( $_GET['filter_brand'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$brands = array_filter( array_map( 'sanitize_title', (array) wp_unslash( $_GET['filter_brand'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $brands && taxonomy_exists( 'product_brand' ) ) {
			$tax_query[] = array(
				'taxonomy' => 'product_brand',
				'field'    => 'slug',
				'terms'    => $brands,
				'operator' => 'IN',
			);
		}
	}

	if ( ! empty( $_GET['filter_cat'] ) && ! is_product_category() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$cats = array_filter( array_map( 'sanitize_title', (array) wp_unslash( $_GET['filter_cat'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $cats ) {
			$tax_query[] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $cats,
				'operator' => 'IN',
			);
		}
	}

	if ( count( $tax_query ) > 1 && empty( $tax_query['relation'] ) ) {
		$tax_query['relation'] = 'AND';
	}
	$q->set( 'tax_query', $tax_query );

	if ( ! empty( $_GET['in_stock'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$meta_query = $q->get( 'meta_query' );
		if ( ! is_array( $meta_query ) ) {
			$meta_query = array();
		}
		$meta_query[] = array(
			'key'     => '_stock_status',
			'value'   => 'instock',
			'compare' => '=',
		);
		$q->set( 'meta_query', $meta_query );
	}
}
add_action( 'woocommerce_product_query', 'exmart_apply_shop_filters' );

/**
 * Selected filter GET values (sanitized).
 *
 * @return array{brands: string[], cats: string[], in_stock: bool}
 */
function exmart_shop_filter_state() {
	$brands = array();
	$cats   = array();
	if ( ! empty( $_GET['filter_brand'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$brands = array_values( array_filter( array_map( 'sanitize_title', (array) wp_unslash( $_GET['filter_brand'] ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	if ( ! empty( $_GET['filter_cat'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$cats = array_values( array_filter( array_map( 'sanitize_title', (array) wp_unslash( $_GET['filter_cat'] ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	return array(
		'brands'   => $brands,
		'cats'     => $cats,
		'in_stock' => ! empty( $_GET['in_stock'] ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	);
}

/**
 * Left-rail Brand / Category / Availability filters (Figma Shop).
 */
function exmart_render_shop_filters() {
	$state  = exmart_shop_filter_state();
	$action = '';
	if ( function_exists( 'is_shop' ) && is_shop() && function_exists( 'wc_get_page_permalink' ) ) {
		$action = wc_get_page_permalink( 'shop' );
	} elseif ( is_product_taxonomy() ) {
		$link = get_term_link( get_queried_object() );
		if ( ! is_wp_error( $link ) ) {
			$action = $link;
		}
	}

	$orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$brands = taxonomy_exists( 'product_brand' )
		? get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => true ) )
		: array();
	$cats = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
	) );
	if ( is_wp_error( $brands ) ) {
		$brands = array();
	}
	if ( is_wp_error( $cats ) ) {
		$cats = array();
	}

	echo '<form class="em-shop-filter-form" method="get" action="' . esc_url( $action ? $action : '' ) . '">';
	if ( $orderby ) {
		echo '<input type="hidden" name="orderby" value="' . esc_attr( $orderby ) . '">';
	}

	if ( $brands ) {
		echo '<div class="em-shop-filter-group">';
		echo '<p class="em-overline em-shop-filter-label">' . esc_html__( 'Brand', 'exmart' ) . '</p>';
		foreach ( $brands as $brand ) {
			$checked = in_array( $brand->slug, $state['brands'], true );
			printf(
				'<label class="em-shop-filter-row"><input type="checkbox" name="filter_brand[]" value="%s"%s><span class="em-body-s">%s</span></label>',
				esc_attr( $brand->slug ),
				$checked ? ' checked' : '',
				esc_html( $brand->name )
			);
		}
		echo '</div>';
	}

	if ( $cats && ! is_product_category() ) {
		echo '<div class="em-shop-filter-group">';
		echo '<p class="em-overline em-shop-filter-label">' . esc_html__( 'Category', 'exmart' ) . '</p>';
		foreach ( $cats as $cat ) {
			$checked = in_array( $cat->slug, $state['cats'], true );
			printf(
				'<label class="em-shop-filter-row"><input type="checkbox" name="filter_cat[]" value="%s"%s><span class="em-body-s">%s</span></label>',
				esc_attr( $cat->slug ),
				$checked ? ' checked' : '',
				esc_html( $cat->name )
			);
		}
		echo '</div>';
	}

	echo '<div class="em-shop-filter-group">';
	echo '<p class="em-overline em-shop-filter-label">' . esc_html__( 'Availability', 'exmart' ) . '</p>';
	printf(
		'<label class="em-shop-filter-row"><input type="checkbox" name="in_stock" value="1"%s><span class="em-body-s">%s</span></label>',
		$state['in_stock'] ? ' checked' : '',
		esc_html__( 'In stock only', 'exmart' )
	);
	echo '</div>';

	echo '<noscript><button type="submit" class="em-btn em-btn-secondary" style="width:100%;margin-top:var(--s3);">' . esc_html__( 'Apply filters', 'exmart' ) . '</button></noscript>';
	echo '</form>';
}

/**
 * Active filter chips above the product grid.
 */
function exmart_shop_active_filters() {
	$state = exmart_shop_filter_state();
	if ( ! $state['brands'] && ! $state['cats'] && ! $state['in_stock'] ) {
		return;
	}

	$base = '';
	if ( function_exists( 'is_shop' ) && is_shop() && function_exists( 'wc_get_page_permalink' ) ) {
		$base = wc_get_page_permalink( 'shop' );
	} elseif ( is_product_taxonomy() ) {
		$link = get_term_link( get_queried_object() );
		if ( ! is_wp_error( $link ) ) {
			$base = $link;
		}
	}
	$orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$clear_url = $base ? remove_query_arg( array( 'filter_brand', 'filter_cat', 'in_stock' ), $base ) : '';
	if ( $orderby && $clear_url ) {
		$clear_url = add_query_arg( 'orderby', $orderby, $clear_url );
	}

	echo '<div class="em-shop-active-filters">';

	foreach ( $state['brands'] as $slug ) {
		$term = get_term_by( 'slug', $slug, 'product_brand' );
		$label = $term ? $term->name : $slug;
		$next  = array_values( array_diff( $state['brands'], array( $slug ) ) );
		$url   = $base ? remove_query_arg( 'filter_brand', $base ) : '';
		if ( $url && $next ) {
			$url = add_query_arg( 'filter_brand', $next, $url );
		}
		if ( $url && $state['cats'] ) {
			$url = add_query_arg( 'filter_cat', $state['cats'], $url );
		}
		if ( $url && $state['in_stock'] ) {
			$url = add_query_arg( 'in_stock', '1', $url );
		}
		if ( $url && $orderby ) {
			$url = add_query_arg( 'orderby', $orderby, $url );
		}
		printf(
			'<a class="em-chip active" href="%s">%s <span aria-hidden="true">×</span></a>',
			esc_url( $url ? $url : '#' ),
			esc_html( $label )
		);
	}

	foreach ( $state['cats'] as $slug ) {
		$term  = get_term_by( 'slug', $slug, 'product_cat' );
		$label = $term ? $term->name : $slug;
		$next  = array_values( array_diff( $state['cats'], array( $slug ) ) );
		$url   = $base ? remove_query_arg( 'filter_cat', $base ) : '';
		if ( $url && $next ) {
			$url = add_query_arg( 'filter_cat', $next, $url );
		}
		if ( $url && $state['brands'] ) {
			$url = add_query_arg( 'filter_brand', $state['brands'], $url );
		}
		if ( $url && $state['in_stock'] ) {
			$url = add_query_arg( 'in_stock', '1', $url );
		}
		if ( $url && $orderby ) {
			$url = add_query_arg( 'orderby', $orderby, $url );
		}
		printf(
			'<a class="em-chip active" href="%s">%s <span aria-hidden="true">×</span></a>',
			esc_url( $url ? $url : '#' ),
			esc_html( $label )
		);
	}

	if ( $state['in_stock'] ) {
		$url = $base ? remove_query_arg( 'in_stock', $base ) : '';
		if ( $url && $state['brands'] ) {
			$url = add_query_arg( 'filter_brand', $state['brands'], $url );
		}
		if ( $url && $state['cats'] ) {
			$url = add_query_arg( 'filter_cat', $state['cats'], $url );
		}
		if ( $url && $orderby ) {
			$url = add_query_arg( 'orderby', $orderby, $url );
		}
		printf(
			'<a class="em-chip active" href="%s">%s <span aria-hidden="true">×</span></a>',
			esc_url( $url ? $url : '#' ),
			esc_html__( 'In stock only', 'exmart' )
		);
	}

	if ( $clear_url ) {
		printf(
			'<a class="em-chip em-shop-clear-filters" href="%s">%s</a>',
			esc_url( $clear_url ),
			esc_html__( 'Clear all', 'exmart' )
		);
	}

	echo '</div>';
}

/**
 * Related products: “You might also like” — same brand first, then same
 * category, preferring in-stock items (matches Figma getRelated logic).
 *
 * @param int[] $related_posts Related product IDs.
 * @param int   $product_id    Current product ID.
 * @param array $args          Query args (posts_per_page / limit).
 * @return int[]
 */
function exmart_smart_related_products( $related_posts, $product_id, $args ) {
	$product_id = absint( $product_id );
	$limit      = isset( $args['posts_per_page'] ) ? absint( $args['posts_per_page'] ) : ( isset( $args['limit'] ) ? absint( $args['limit'] ) : 6 );
	if ( $limit < 1 ) {
		$limit = 6;
	}

	$exclude = array_filter( array_merge( array( $product_id ), wc_get_product( $product_id ) ? wc_get_product( $product_id )->get_upsell_ids() : array() ) );
	$ids     = array();

	$collect = static function ( $tax_query ) use ( &$ids, $exclude, $limit ) {
		if ( count( $ids ) >= $limit ) {
			return;
		}
		$query = new WP_Query(
			array(
				'post_type'              => 'product',
				'post_status'            => 'publish',
				'posts_per_page'         => $limit * 2,
				'post__not_in'           => array_merge( $exclude, $ids ),
				'fields'                 => 'ids',
				'orderby'                => 'rand',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'tax_query'              => array_merge( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array( 'relation' => 'AND' ),
					$tax_query,
					array(
						array(
							'taxonomy' => 'product_visibility',
							'field'    => 'name',
							'terms'    => array( 'exclude-from-catalog', 'exclude-from-search' ),
							'operator' => 'NOT IN',
						),
					)
				),
				'meta_query'             => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					array(
						'key'   => '_stock_status',
						'value' => 'instock',
					),
				),
			)
		);
		if ( ! empty( $query->posts ) ) {
			$ids = array_values( array_unique( array_merge( $ids, array_map( 'absint', $query->posts ) ) ) );
		}
	};

	// 1) Same brand.
	if ( taxonomy_exists( 'product_brand' ) ) {
		$brands = wp_get_post_terms( $product_id, 'product_brand', array( 'fields' => 'ids' ) );
		if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) {
			$collect(
				array(
					array(
						'taxonomy' => 'product_brand',
						'field'    => 'term_id',
						'terms'    => $brands,
					),
				)
			);
		}
	}

	// 2) Same category (fill remaining).
	$cats = wc_get_product_term_ids( $product_id, 'product_cat' );
	if ( ! empty( $cats ) && count( $ids ) < $limit ) {
		$collect(
			array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $cats,
				),
			)
		);
	}

	// 3) Fallback: any in-stock catalog products.
	if ( count( $ids ) < $limit ) {
		$collect( array() );
	}

	$ids = array_slice( $ids, 0, $limit );
	return ! empty( $ids ) ? $ids : $related_posts;
}
add_filter( 'woocommerce_related_products', 'exmart_smart_related_products', 10, 3 );

/**
 * Related products rail: match Figma “You might also like” (up to 6 / 4 cols).
 */
function exmart_related_products_args( $args ) {
	$args['posts_per_page'] = 6;
	$args['columns']        = 4;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'exmart_related_products_args' );

add_filter( 'woocommerce_product_related_products_heading', function () {
	return __( 'You might also like', 'exmart' );
} );

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
 * Add the "How to Use", "Accuracy", and "Shipping & Returns" tabs.
 */
function exmart_custom_product_tabs( $tabs ) {
	global $product;
	if ( ! $product ) {
		return $tabs;
	}

	if ( isset( $tabs['additional_information'] ) ) {
		$tabs['additional_information']['title'] = __( 'Specifications', 'exmart' );
	}

	$how_to_use = get_post_meta( $product->get_id(), '_exmart_how_to_use', true );
	if ( $how_to_use ) {
		$tabs['exmart_how_to_use'] = array(
			'title'    => __( 'How to Use', 'exmart' ),
			'priority' => 15,
			'callback' => function () use ( $how_to_use ) {
				echo '<p class="em-body" style="color:var(--ink-700)">' . wp_kses_post( nl2br( esc_html( $how_to_use ) ) ) . '</p>';
			},
		);
	}

	$accuracy_categories = array( 'home-diagnostics', 'health-protection' );
	if ( has_term( $accuracy_categories, 'product_cat', $product->get_id() ) ) {
		$tabs['exmart_accuracy'] = array(
			'title'    => __( 'Accuracy', 'exmart' ),
			'priority' => 25,
			'callback' => function () {
				?>
				<p class="em-body" style="color:var(--ink-700)"><?php esc_html_e( 'This device provides readings for informational purposes only. It is not intended to diagnose, treat, cure, or prevent any disease or health condition.', 'exmart' ); ?></p>
				<p class="em-body" style="color:var(--ink-700)"><?php esc_html_e( 'Always consult a qualified healthcare professional for clinical decisions. Readings may vary based on usage technique. Refer to the included manual for accuracy specifications and limitations.', 'exmart' ); ?></p>
				<?php
			},
		);
	}

	$tabs['exmart_shipping'] = array(
		'title'    => __( 'Shipping & Returns', 'exmart' ),
		'priority' => 35,
		'callback' => function () {
			?>
			<p class="em-body" style="color:var(--ink-700)"><?php esc_html_e( 'Standard delivery: 2–5 working days across Egypt. Free shipping on orders over EGP 300.', 'exmart' ); ?></p>
			<p class="em-body" style="color:var(--ink-700)"><?php
				printf(
					/* translators: %s: contact email */
					esc_html__( 'Returns accepted within 14 days of delivery. Item must be unused and in original packaging. Contact us at %s to initiate a return.', 'exmart' ),
					esc_html( function_exists( 'exmart_email' ) ? exmart_email() : 'info@exmartegypt.com' )
				);
			?></p>
			<?php
		},
	);

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'exmart_custom_product_tabs' );

/**
 * Trust block under ATC on the single product page (summary pri 35 so it
 * still shows for simple products that skip Woo’s cart form).
 */
function exmart_trust_block() {
	if ( ! is_product() ) {
		return;
	}
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
add_action( 'woocommerce_single_product_summary', 'exmart_trust_block', 35 );

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
 * PDP Add to Cart: reuse the exact same working control as the product
 * cards (exmart_card_atc_control()) instead of a bespoke PDP stepper —
 * a from-scratch attempt at this overlapped/misaligned with the rest of
 * the row. Only for simple, non-variable products: exmart_card_atc_control()
 * falls back to a "Select options" link to the product's own permalink
 * for anything else, which would just point back at this same page, so
 * variable/grouped/external products keep WooCommerce's own form.
 */
function exmart_pdp_add_to_cart() {
	global $product;
	if ( $product instanceof WC_Product && $product->is_type( 'simple' ) && ! $product->has_child() ) {
		// Stock lives in WC’s cart form — print it ourselves for the card ATC path.
		echo wc_get_stock_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="em-card-actions em-pdp-atc-row">';
		exmart_card_atc_control( $product );
		exmart_pdp_wishlist_button();
		echo '</div>';
	} else {
		woocommerce_template_single_add_to_cart();
	}
}
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
add_action( 'woocommerce_single_product_summary', 'exmart_pdp_add_to_cart', 30 );

/**
 * Wishlist heart next to the PDP Add to Cart button.
 * Hooked after WC’s button for variable/grouped; simple products call this
 * directly from exmart_pdp_add_to_cart() (no WC form → hook never fires).
 */
function exmart_pdp_wishlist_button() {
	global $product;
	if ( ! $product ) {
		return;
	}
	?>
	<button
		type="button"
		class="em-wishlist-btn em-wishlist-toggle em-pdp-wishlist"
		data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
		aria-label="<?php esc_attr_e( 'Add to wishlist', 'exmart' ); ?>"
	>
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" stroke="currentColor" stroke-width="2"/></svg>
	</button>
	<?php
}
add_action( 'woocommerce_after_add_to_cart_button', 'exmart_pdp_wishlist_button' );

/**
 * Figma stock line copy.
 *
 * @param string     $text     Availability text.
 * @param WC_Product $product  Product.
 * @return string
 */
function exmart_pdp_availability_text( $text, $product ) {
	if ( ! is_product() || ! $product instanceof WC_Product ) {
		return $text;
	}
	if ( ! $product->is_in_stock() ) {
		return __( 'Out of stock', 'exmart' );
	}
	$qty = $product->managing_stock() ? (int) $product->get_stock_quantity() : null;
	if ( null !== $qty && $qty > 0 && $qty < 10 ) {
		return sprintf(
			/* translators: %d: remaining stock */
			__( 'Only %d left in stock', 'exmart' ),
			$qty
		);
	}
	return __( 'In stock — ready to ship', 'exmart' );
}
add_filter( 'woocommerce_get_availability_text', 'exmart_pdp_availability_text', 10, 2 );

/**
 * Low-stock warning class (Figma em-stock-low).
 *
 * @param array      $availability Availability text + class.
 * @param WC_Product $product      Product.
 * @return array
 */
function exmart_pdp_availability_class( $availability, $product ) {
	if ( ! is_product() || ! $product instanceof WC_Product || empty( $availability['class'] ) ) {
		return $availability;
	}
	if ( $product->is_in_stock() && $product->managing_stock() ) {
		$qty = (int) $product->get_stock_quantity();
		if ( $qty > 0 && $qty < 10 ) {
			$availability['class'] = trim( str_replace( 'in-stock', '', $availability['class'] ) . ' em-stock-low' );
		}
	}
	return $availability;
}
add_filter( 'woocommerce_get_availability', 'exmart_pdp_availability_class', 10, 2 );

/**
 * Sale badge as −X% (Figma).
 *
 * @param string     $html    Default flash HTML.
 * @param WP_Post    $post    Product post.
 * @param WC_Product $product Product.
 * @return string
 */
function exmart_pdp_sale_flash( $html, $post, $product ) {
	if ( ! $product instanceof WC_Product || ! $product->is_on_sale() ) {
		return $html;
	}
	$regular = (float) $product->get_regular_price();
	$sale    = (float) $product->get_sale_price();
	if ( $product->is_type( 'variable' ) ) {
		$regular = (float) $product->get_variation_regular_price( 'min', true );
		$sale    = (float) $product->get_variation_sale_price( 'min', true );
	}
	if ( $regular <= 0 || $sale <= 0 || $sale >= $regular ) {
		return $html;
	}
	$pct = (int) round( ( 1 - ( $sale / $regular ) ) * 100 );
	return '<span class="onsale em-badge em-badge-sale">−' . esc_html( (string) $pct ) . '%</span>';
}
add_filter( 'woocommerce_sale_flash', 'exmart_pdp_sale_flash', 10, 3 );

/**
 * PDP breadcrumb: Home / Category / Product (Figma).
 */
function exmart_pdp_breadcrumb() {
	if ( ! is_product() ) {
		return;
	}
	global $product;
	if ( ! $product instanceof WC_Product ) {
		$product = wc_get_product( get_the_ID() );
	}
	if ( ! $product ) {
		return;
	}

	$crumbs = array(
		array(
			'label' => __( 'Home', 'exmart' ),
			'href'  => home_url( '/' ),
		),
	);

	$terms = wc_get_product_terms(
		$product->get_id(),
		'product_cat',
		array(
			'orderby' => 'parent',
			'order'   => 'DESC',
		)
	);
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		$term     = $terms[0];
		$crumbs[] = array(
			'label' => $term->name,
			'href'  => get_term_link( $term ),
		);
	}

	$crumbs[] = array( 'label' => $product->get_name() );
	exmart_breadcrumb( $crumbs );
}
add_action( 'woocommerce_before_single_product', 'exmart_pdp_breadcrumb', 5 );

/* Figma PDP info column does not show short description or SKU meta. */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

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
 * Extend cart fragments: header + floating-pill count badges + qty map.
 */
function exmart_cart_count_badge_html( $count, $id ) {
	$count = max( 0, (int) $count );
	ob_start();
	?>
	<span class="em-icon-count em-cart-count-badge" id="<?php echo esc_attr( $id ); ?>" <?php echo $count > 0 ? '' : 'hidden'; ?>><?php echo esc_html( (string) $count ); ?></span>
	<?php
	return ob_get_clean();
}

function exmart_cart_count_fragment( $fragments ) {
	$count = ( WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
	$fragments['#em-cart-count']      = exmart_cart_count_badge_html( $count, 'em-cart-count' );
	$fragments['#em-pill-cart-count'] = exmart_cart_count_badge_html( $count, 'em-pill-cart-count' );
	$fragments['#exmart-cart-qty-map'] = '<script type="application/json" id="exmart-cart-qty-map">' . wp_json_encode( exmart_get_cart_qty_map() ) . '</script>';
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'exmart_cart_count_fragment' );

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

/**
 * Ensure WooCommerce cart + customer session exist (needed for guest AJAX adds).
 *
 * @return bool
 */
function exmart_ensure_wc_cart() {
	if ( ! function_exists( 'WC' ) ) {
		return false;
	}

	if ( is_null( WC()->cart ) && function_exists( 'wc_load_cart' ) ) {
		wc_load_cart();
	}

	if ( WC()->session && ! WC()->session->has_session() ) {
		WC()->session->set_customer_session_cookie( true );
	}

	return (bool) WC()->cart;
}

/**
 * Render mini-cart HTML for the drawer.
 *
 * @return string
 */
function exmart_get_mini_cart_html() {
	if ( ! function_exists( 'woocommerce_mini_cart' ) ) {
		return '';
	}
	ob_start();
	woocommerce_mini_cart();
	return ob_get_clean();
}

/**
 * How many units of a product are already in the cart (simple product id).
 *
 * @param int $product_id
 * @return int
 */
function exmart_cart_qty_for_product( $product_id ) {
	$product_id = absint( $product_id );
	if ( ! $product_id || ! exmart_ensure_wc_cart() ) {
		return 0;
	}

	$qty = 0;
	foreach ( WC()->cart->get_cart() as $item ) {
		if ( (int) $item['product_id'] === $product_id && empty( $item['variation_id'] ) ) {
			$qty += (int) $item['quantity'];
		} elseif ( (int) $item['variation_id'] === $product_id ) {
			$qty += (int) $item['quantity'];
		}
	}
	return $qty;
}

/**
 * Simple product_id => qty map for card steppers.
 *
 * @return array<string, int>
 */
function exmart_get_cart_qty_map() {
	$map = array();
	if ( ! exmart_ensure_wc_cart() ) {
		return $map;
	}
	foreach ( WC()->cart->get_cart() as $item ) {
		if ( ! empty( $item['variation_id'] ) ) {
			continue;
		}
		$pid = (string) absint( $item['product_id'] );
		if ( ! $pid ) {
			continue;
		}
		$map[ $pid ] = ( isset( $map[ $pid ] ) ? (int) $map[ $pid ] : 0 ) + (int) $item['quantity'];
	}
	return $map;
}

/**
 * AJAX: cart snapshot for card steppers + mini-cart drawer.
 */
function exmart_ajax_get_cart_qtys() {
	check_ajax_referer( 'exmart_ajax', 'nonce' );
	exmart_ensure_wc_cart();
	wp_send_json_success(
		array(
			'quantities'     => exmart_get_cart_qty_map(),
			'count'          => WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0,
			'mini_cart_html' => exmart_get_mini_cart_html(),
		)
	);
}
add_action( 'wp_ajax_exmart_get_cart_qtys', 'exmart_ajax_get_cart_qtys' );
add_action( 'wp_ajax_nopriv_exmart_get_cart_qtys', 'exmart_ajax_get_cart_qtys' );

/**
 * Product card add-to-cart / quantity stepper (Figma ProductCard behaviour).
 *
 * @param WC_Product $product
 */
function exmart_card_atc_control( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	if ( ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		printf(
			'<button type="button" class="em-btn em-card-atc-btn" disabled>%s</button>',
			esc_html__( 'Out of stock', 'exmart' )
		);
		return;
	}

	// Variable / grouped / external → product page.
	if ( ! $product->is_type( 'simple' ) || $product->has_child() ) {
		printf(
			'<a href="%1$s" class="em-btn em-btn-secondary em-card-atc-btn">%2$s</a>',
			esc_url( $product->get_permalink() ),
			esc_html__( 'Select options', 'exmart' )
		);
		return;
	}

	$product_id = $product->get_id();
	$qty        = exmart_cart_qty_for_product( $product_id );
	$max        = $product->get_max_purchase_quantity();
	if ( $max < 1 ) {
		$max = 9999;
	}
	?>
	<div
		class="em-card-atc"
		data-em-card-atc
		data-product-id="<?php echo esc_attr( (string) $product_id ); ?>"
		data-qty="<?php echo esc_attr( (string) $qty ); ?>"
		data-max="<?php echo esc_attr( (string) $max ); ?>"
	>
		<button
			type="button"
			class="em-btn em-btn-secondary em-card-atc-btn"
			data-em-atc-add
			<?php echo $qty > 0 ? 'hidden' : ''; ?>
			aria-label="<?php echo esc_attr( sprintf( __( 'Add %s to cart', 'exmart' ), $product->get_name() ) ); ?>"
		>
			<?php esc_html_e( 'Add to cart', 'exmart' ); ?>
		</button>
		<div class="em-card-qty" data-em-atc-stepper <?php echo $qty > 0 ? '' : 'hidden'; ?>>
			<button
				type="button"
				class="em-card-qty-btn<?php echo 1 === (int) $qty ? ' is-remove' : ''; ?>"
				data-em-qty-minus
				aria-label="<?php echo 1 === (int) $qty ? esc_attr__( 'Remove from cart', 'exmart' ) : esc_attr__( 'Decrease quantity', 'exmart' ); ?>"
			>
				<span class="em-card-qty-minus" aria-hidden="true">−</span>
				<span class="em-card-qty-bin" aria-hidden="true">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2m3 0v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6h14zM10 11v6M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</span>
			</button>
			<span class="em-card-qty-val" data-em-qty-val><?php echo esc_html( (string) max( 1, $qty ) ); ?></span>
			<button type="button" class="em-card-qty-btn" data-em-qty-plus aria-label="<?php esc_attr_e( 'Increase quantity', 'exmart' ); ?>">+</button>
		</div>
	</div>
	<?php
}

/**
 * AJAX: set cart quantity for a simple product (0 removes; creates line if needed).
 */
function exmart_ajax_set_cart_qty() {
	check_ajax_referer( 'exmart_ajax', 'nonce' );

	if ( ! exmart_ensure_wc_cart() ) {
		wp_send_json_error( array( 'message' => 'Cart unavailable' ), 400 );
	}

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$quantity   = isset( $_POST['quantity'] ) ? (int) $_POST['quantity'] : 0;

	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => 'Invalid product' ), 400 );
	}

	$product = wc_get_product( $product_id );
	if ( ! $product || ! $product->is_purchasable() ) {
		wp_send_json_error( array( 'message' => 'Product unavailable' ), 400 );
	}

	if ( $quantity < 0 ) {
		$quantity = 0;
	}

	$max = $product->get_max_purchase_quantity();
	if ( $max > 0 && $quantity > $max ) {
		$quantity = $max;
	}

	$cart      = WC()->cart;
	$found_key = null;
	foreach ( $cart->get_cart() as $key => $item ) {
		if ( (int) $item['product_id'] === $product_id && empty( $item['variation_id'] ) ) {
			$found_key = $key;
			break;
		}
	}

	if ( 0 === $quantity ) {
		if ( $found_key ) {
			$cart->remove_cart_item( $found_key );
		}
	} elseif ( $found_key ) {
		$cart->set_quantity( $found_key, $quantity, true );
	} else {
		if ( ! $product->is_in_stock() ) {
			wp_send_json_error( array( 'message' => 'Out of stock' ), 400 );
		}
		$added = $cart->add_to_cart( $product_id, $quantity );
		if ( ! $added ) {
			wp_send_json_error( array( 'message' => 'Could not add to cart' ), 400 );
		}
	}

	$cart->calculate_totals();
	if ( method_exists( $cart, 'maybe_set_cart_cookies' ) ) {
		$cart->maybe_set_cart_cookies();
	}

	$final_qty = exmart_cart_qty_for_product( $product_id );

	wp_send_json_success(
		array(
			'product_id'     => $product_id,
			'quantity'       => $final_qty,
			'count'          => (int) $cart->get_cart_contents_count(),
			'quantities'     => exmart_get_cart_qty_map(),
			'mini_cart_html' => exmart_get_mini_cart_html(),
			'cart_hash'      => $cart->get_cart_hash(),
		)
	);
}
add_action( 'wp_ajax_exmart_set_cart_qty', 'exmart_ajax_set_cart_qty' );
add_action( 'wp_ajax_nopriv_exmart_set_cart_qty', 'exmart_ajax_set_cart_qty' );

/**
 * Checkout — Figma 4-step wizard.
 * Payment lives in the Payment step (left), not inside the Order Summary aside.
 */
remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
add_action( 'exmart_checkout_payment_step', 'woocommerce_checkout_payment', 10 );

/**
 * Shipping method radio cards for the Shipping step.
 */
function exmart_checkout_shipping_methods() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart || ! WC()->cart->needs_shipping() ) {
		echo '<p class="em-caption">' . esc_html__( 'No shipping is required for this order.', 'exmart' ) . '</p>';
		return;
	}

	if ( ! WC()->cart->show_shipping() ) {
		echo '<p class="em-caption">' . esc_html__( 'Enter your address to see shipping options.', 'exmart' ) . '</p>';
		return;
	}

	$packages       = WC()->shipping()->get_packages();
	$chosen_methods = WC()->session ? WC()->session->get( 'chosen_shipping_methods' ) : array();

	if ( empty( $packages ) ) {
		echo '<p class="em-caption">' . esc_html__( 'Enter your address to see shipping options.', 'exmart' ) . '</p>';
		return;
	}

	foreach ( $packages as $index => $package ) {
		$available = isset( $package['rates'] ) ? $package['rates'] : array();
		$chosen    = isset( $chosen_methods[ $index ] ) ? $chosen_methods[ $index ] : '';

		if ( empty( $available ) ) {
			echo '<p class="em-caption">' . esc_html__( 'There are no shipping options available for your address.', 'exmart' ) . '</p>';
			continue;
		}

		echo '<div class="em-checkout-ship-package" data-index="' . esc_attr( (string) $index ) . '">';

		foreach ( $available as $method ) {
			$method_id = $method->id;
			$input_id  = 'shipping_method_' . $index . '_' . sanitize_title( $method_id );
			$checked   = checked( $method_id, $chosen, false );
			$cost      = (float) $method->cost;
			if ( WC()->cart->display_prices_including_tax() ) {
				$cost += (float) $method->get_shipping_tax();
			}
			$price_html = ( $cost <= 0 )
				? esc_html__( 'Free', 'exmart' )
				: wp_kses_post( wc_price( $cost ) );

			$meta = $method->get_meta_data();
			$eta  = '';
			if ( ! empty( $meta['eta'] ) ) {
				$eta = (string) $meta['eta'];
			} elseif ( false !== stripos( $method->get_label(), 'express' ) ) {
				$eta = __( 'Next working day', 'exmart' );
			} else {
				$eta = __( '2–5 working days', 'exmart' );
			}
			?>
			<label class="em-checkout-ship-card" for="<?php echo esc_attr( $input_id ); ?>">
				<?php if ( count( $available ) > 1 ) : ?>
					<input type="radio" name="shipping_method[<?php echo esc_attr( (string) $index ); ?>]" data-index="<?php echo esc_attr( (string) $index ); ?>" id="<?php echo esc_attr( $input_id ); ?>" value="<?php echo esc_attr( $method_id ); ?>" class="shipping_method" <?php echo $checked; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> />
				<?php else : ?>
					<input type="hidden" name="shipping_method[<?php echo esc_attr( (string) $index ); ?>]" data-index="<?php echo esc_attr( (string) $index ); ?>" id="<?php echo esc_attr( $input_id ); ?>" value="<?php echo esc_attr( $method_id ); ?>" class="shipping_method" />
					<span class="em-checkout-ship-radio" aria-hidden="true"></span>
				<?php endif; ?>
				<span class="em-checkout-ship-body">
					<span class="em-checkout-ship-name"><?php echo esc_html( $method->get_label() ); ?></span>
					<?php if ( $eta ) : ?>
						<span class="em-caption em-checkout-ship-eta"><?php echo esc_html( $eta ); ?></span>
					<?php endif; ?>
				</span>
				<span class="em-checkout-ship-price"><?php echo $price_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			</label>
			<?php
			do_action( 'woocommerce_after_shipping_rate', $method, $index );
		}

		echo '</div>';
	}
}

/**
 * Refresh shipping method cards when checkout AJAX updates.
 *
 * @param array $fragments Checkout fragments.
 * @return array
 */
function exmart_checkout_shipping_fragment( $fragments ) {
	ob_start();
	echo '<div class="em-checkout-shipping-methods">';
	exmart_checkout_shipping_methods();
	echo '</div>';
	$fragments['.em-checkout-shipping-methods'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_update_order_review_fragments', 'exmart_checkout_shipping_fragment' );

/**
 * Checkout coupon notice is noisy and not in Figma — remove it.
 * Coupons remain available on the cart page.
 */
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

/**
 * “Returning customer? Click here to login” info banner — not in Figma.
 * Contact step already has “Already have an account? Log in”.
 */
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );

/**
 * Present gateways as Figma cards:
 * - COD: Cash on Delivery
 * - fawry_pay (live FawryPay plugin): one grouped Visa | Mastercard | Meeza | Fawry option
 *
 * @param WC_Payment_Gateway[] $gateways Available gateways.
 * @return WC_Payment_Gateway[]
 */
function exmart_checkout_gateway_presentation( $gateways ) {
	if ( empty( $gateways ) || ! is_array( $gateways ) ) {
		return $gateways;
	}

	foreach ( $gateways as $id => $gateway ) {
		if ( ! is_object( $gateway ) ) {
			continue;
		}

		if ( 'cod' === $id ) {
			$gateway->title       = __( 'Cash on Delivery', 'exmart' );
			$gateway->description = __( 'Pay when your order arrives. No additional fee.', 'exmart' );
			$gateway->icon        = '';
		}

		// Confirmed on live site analytics: payment_options includes "fawry_pay".
		if ( 'fawry_pay' === $id || false !== stripos( (string) $id, 'fawry' ) ) {
			$gateway->title       = __( 'Visa | Mastercard | Meeza | Fawry', 'exmart' );
			$gateway->description = __( 'Pay securely with card, Meeza, or at any Fawry point via FawryPay.', 'exmart' );
			$gateway->icon        = ''; // Theme renders Media Library logos in the card.
		}
	}

	return $gateways;
}
add_filter( 'woocommerce_available_payment_gateways', 'exmart_checkout_gateway_presentation', 30 );

/**
 * Put COD first, then FawryPay / other gateways.
 *
 * @param WC_Payment_Gateway[] $gateways Available gateways.
 * @return WC_Payment_Gateway[]
 */
function exmart_checkout_gateway_order( $gateways ) {
	if ( empty( $gateways ) || ! is_array( $gateways ) ) {
		return $gateways;
	}

	$ordered = array();
	if ( isset( $gateways['cod'] ) ) {
		$ordered['cod'] = $gateways['cod'];
		unset( $gateways['cod'] );
	}
	foreach ( $gateways as $id => $gateway ) {
		$ordered[ $id ] = $gateway;
	}
	return $ordered;
}
add_filter( 'woocommerce_available_payment_gateways', 'exmart_checkout_gateway_order', 40 );
