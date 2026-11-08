<?php
/**
 * Interactive 3D packaging viewer (Google <model-viewer>) on selected product
 * pages, shown in the product's Description tab. Assets live in
 * assets/3d/<folder>/ as model.glb + poster.png; index.html there is the
 * supplier's standalone viewer, kept for direct testing.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Products with a 3D viewer: product ID => [ asset folder, GLB file name ].
 * 1110 = "Surecheck Pregnancy Kit" (SKU UN-0001), the SureCheck Cassette box;
 * same ID on staging and live.
 *
 * @return array<int, array{0:string,1:string}>
 */
function exmart_3d_viewer_products() {
	return apply_filters(
		'exmart_3d_viewer_products',
		array(
			1110 => array( 'surecheck-cassette', 'surecheck-cassette.glb' ),
		)
	);
}

/**
 * @param int $product_id
 * @return array{0:string,1:string}|null
 */
function exmart_3d_viewer_for( $product_id ) {
	$map = exmart_3d_viewer_products();
	$product_id = absint( $product_id );
	return isset( $map[ $product_id ] ) ? $map[ $product_id ] : null;
}

/**
 * Load <model-viewer> only on product pages that have a viewer.
 */
function exmart_3d_viewer_enqueue() {
	if ( ! function_exists( 'is_product' ) || ! is_product() || ! exmart_3d_viewer_for( get_queried_object_id() ) ) {
		return;
	}
	wp_enqueue_script_module( 'exmart-model-viewer', 'https://unpkg.com/@google/model-viewer@4.1.0/dist/model-viewer.min.js', array(), null );
}
add_action( 'wp_enqueue_scripts', 'exmart_3d_viewer_enqueue' );

/**
 * Viewer markup. Camera angles are the supplier's (glTF units are metres;
 * the box is 150 × 73 × 20 mm).
 *
 * @param int $product_id
 */
function exmart_render_3d_viewer( $product_id ) {
	$viewer = exmart_3d_viewer_for( $product_id );
	if ( ! $viewer ) {
		return;
	}
	$base    = EXMART_URI . '/assets/3d/' . $viewer[0] . '/';
	$product = wc_get_product( $product_id );
	$name    = $product ? $product->get_name() : '';
	$views   = array(
		array( __( 'Front', 'exmart' ), '0deg 90deg 0.29m' ),
		array( __( 'Back', 'exmart' ), '180deg 90deg 0.29m' ),
		array( __( 'Top', 'exmart' ), '0deg 0deg 0.29m' ),
		array( __( 'Bottom', 'exmart' ), '0deg 180deg 0.29m' ),
		array( __( 'Barcode end', 'exmart' ), '-90deg 90deg 0.24m' ),
		array( __( 'Symbols end', 'exmart' ), '90deg 90deg 0.24m' ),
		array( __( '3D view', 'exmart' ), '28deg 70deg 0.29m', true ),
	);
	?>
	<section class="em-3d" data-em-3d aria-label="<?php esc_attr_e( 'Interactive 3D packaging', 'exmart' ); ?>">
		<h3 class="em-h4 em-3d-title"><?php esc_html_e( 'Explore the packaging in 3D', 'exmart' ); ?></h3>
		<model-viewer
			class="em-3d-viewer"
			src="<?php echo esc_url( $base . $viewer[1] ); ?>"
			poster="<?php echo esc_url( $base . 'poster.png' ); ?>"
			alt="<?php echo esc_attr( sprintf( /* translators: %s: product name */ __( '%s packaging. Rotate to inspect the front, back, top, bottom and both ends.', 'exmart' ), $name ) ); ?>"
			camera-controls
			touch-action="pan-y"
			camera-orbit="28deg 70deg 0.29m"
			camera-target="0m 0m 0m"
			min-camera-orbit="auto 0deg 0.15m"
			max-camera-orbit="auto 180deg 0.6m"
			field-of-view="35deg"
			shadow-intensity="0.7"
			shadow-softness="0.9"
			environment-image="neutral"
			exposure="1"
			interaction-prompt="auto"
			loading="lazy"
		></model-viewer>
		<p class="em-3d-hint"><?php esc_html_e( 'Drag to rotate · Pinch or scroll to zoom', 'exmart' ); ?></p>
		<div class="em-3d-views" role="group" aria-label="<?php esc_attr_e( 'View each side', 'exmart' ); ?>">
			<?php foreach ( $views as $view ) : ?>
				<button type="button" class="em-3d-view" data-em-3d-orbit="<?php echo esc_attr( $view[1] ); ?>" aria-pressed="<?php echo empty( $view[2] ) ? 'false' : 'true'; ?>"><?php echo esc_html( $view[0] ); ?></button>
			<?php endforeach; ?>
		</div>
		<p class="em-3d-error" role="status" hidden><?php esc_html_e( 'The 3D preview could not load.', 'exmart' ); ?> <a href="<?php echo esc_url( $base . 'poster.png' ); ?>"><?php esc_html_e( 'View product image', 'exmart' ); ?></a></p>
	</section>
	<?php
}

/**
 * Description tab for products with a viewer: the product description (if
 * any) followed by the viewer. WooCommerce drops the tab when the
 * description is empty, so it's re-added here for these products.
 *
 * @param array $tabs
 * @return array
 */
function exmart_3d_viewer_tab( $tabs ) {
	global $product;
	if ( ! $product instanceof WC_Product || ! exmart_3d_viewer_for( $product->get_id() ) ) {
		return $tabs;
	}
	$product_id  = $product->get_id();
	$original_cb = ! empty( $tabs['description']['callback'] ) ? $tabs['description']['callback'] : null;

	$tabs['description'] = array(
		'title'    => __( 'Description', 'exmart' ),
		'priority' => 10,
		'callback' => function ( $key = 'description', $tab = array() ) use ( $original_cb, $product_id ) {
			if ( $original_cb && is_callable( $original_cb ) ) {
				call_user_func( $original_cb, $key, $tab );
			}
			exmart_render_3d_viewer( $product_id );
		},
	);
	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'exmart_3d_viewer_tab', 20 );
