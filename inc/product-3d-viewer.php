<?php
/**
 * Interactive 3D packaging viewer (Google <model-viewer>) as the first item
 * of the product page gallery, for selected products. The product photos
 * follow as thumbnails; product cards elsewhere keep the normal photo.
 * Assets live in assets/3d/<folder>/ (model .glb + poster.png); index.html
 * there is the supplier's standalone viewer, kept for direct testing.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Products with a 3D viewer: product ID => [ asset folder, GLB file name ].
 * 1110 = "Surecheck Pregnancy Kit" (SKU UN-0001), the SureCheck Cassette box;
 * same ID on staging and live. Simple products only — this gallery replaces
 * WooCommerce's, which variation image switching relies on.
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
 * Swap WooCommerce's product gallery for the 3D gallery on these products.
 */
function exmart_3d_gallery_swap() {
	global $product;
	if ( ! $product instanceof WC_Product || ! exmart_3d_viewer_for( $product->get_id() ) ) {
		return;
	}
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
	add_action( 'woocommerce_before_single_product_summary', 'exmart_render_3d_gallery', 20 );
}
add_action( 'woocommerce_before_single_product_summary', 'exmart_3d_gallery_swap', 1 );

/**
 * Gallery: the 3D model first (with the supplier's camera angles as side-view
 * buttons), then the product photos; thumbnails switch between them.
 * glTF units are metres; the SureCheck box is 150 × 73 × 20 mm.
 */
function exmart_render_3d_gallery() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	$viewer = exmart_3d_viewer_for( $product->get_id() );
	if ( ! $viewer ) {
		return;
	}
	$base      = EXMART_URI . '/assets/3d/' . $viewer[0] . '/';
	$poster    = $base . 'poster.png';
	$image_ids = array_values( array_filter( array_unique( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) ) ) );
	$views     = array(
		array( __( 'Front', 'exmart' ), '0deg 90deg 0.38m' ),
		array( __( 'Back', 'exmart' ), '180deg 90deg 0.38m' ),
		array( __( 'Top', 'exmart' ), '0deg 0deg 0.38m' ),
		array( __( 'Bottom', 'exmart' ), '0deg 180deg 0.38m' ),
		array( __( 'Barcode end', 'exmart' ), '-90deg 90deg 0.31m' ),
		array( __( 'Symbols end', 'exmart' ), '90deg 90deg 0.31m' ),
		array( __( '3D view', 'exmart' ), '28deg 70deg 0.38m', true ),
	);
	?>
	<div class="em-3d-gallery" data-em-3d>
		<div class="em-3d-stage">
			<div class="em-3d-pane is-active" data-em-3d-pane="model">
				<model-viewer
					class="em-3d-viewer"
					src="<?php echo esc_url( $base . $viewer[1] ); ?>"
					poster="<?php echo esc_url( $poster ); ?>"
					alt="<?php echo esc_attr( sprintf( /* translators: %s: product name */ __( '%s packaging. Rotate to inspect the front, back, top, bottom and both ends.', 'exmart' ), $product->get_name() ) ); ?>"
					camera-controls
					touch-action="pan-y"
					camera-orbit="28deg 70deg 0.38m"
					camera-target="0m 0m 0m"
					min-camera-orbit="auto 0deg 0.15m"
					max-camera-orbit="auto 180deg 0.6m"
					field-of-view="35deg"
					shadow-intensity="0.7"
					shadow-softness="0.9"
					environment-image="neutral"
					exposure="1"
					interaction-prompt="auto"
					loading="eager"
				></model-viewer>
			</div>
			<?php foreach ( $image_ids as $i => $image_id ) : ?>
				<div class="em-3d-pane" data-em-3d-pane="image-<?php echo (int) $i; ?>" hidden>
					<?php echo wp_get_attachment_image( $image_id, 'woocommerce_single', false, array( 'class' => 'em-3d-photo', 'loading' => 'lazy' ) ); ?>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="em-3d-controls" data-em-3d-controls>
			<p class="em-3d-hint"><?php esc_html_e( 'Drag to rotate · Pinch or scroll to zoom', 'exmart' ); ?></p>
			<div class="em-3d-views" role="group" aria-label="<?php esc_attr_e( 'View each side', 'exmart' ); ?>">
				<?php foreach ( $views as $view ) : ?>
					<button type="button" class="em-3d-view" data-em-3d-orbit="<?php echo esc_attr( $view[1] ); ?>" aria-pressed="<?php echo empty( $view[2] ) ? 'false' : 'true'; ?>"><?php echo esc_html( $view[0] ); ?></button>
				<?php endforeach; ?>
			</div>
			<p class="em-3d-error" role="status" hidden><?php esc_html_e( 'The 3D preview could not load.', 'exmart' ); ?></p>
		</div>

		<?php if ( $image_ids ) : ?>
			<div class="em-3d-thumbs" role="group" aria-label="<?php esc_attr_e( 'Product images', 'exmart' ); ?>">
				<button type="button" class="em-3d-thumb is-active" data-em-3d-show="model" aria-pressed="true" aria-label="<?php esc_attr_e( 'Show 3D view', 'exmart' ); ?>">
					<img src="<?php echo esc_url( $poster ); ?>" alt="" width="72" height="72" loading="lazy" />
					<span class="em-3d-thumb-badge" aria-hidden="true">3D</span>
				</button>
				<?php foreach ( $image_ids as $i => $image_id ) : ?>
					<button type="button" class="em-3d-thumb" data-em-3d-show="image-<?php echo (int) $i; ?>" aria-pressed="false" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: image number */ __( 'Show image %d', 'exmart' ), $i + 1 ) ); ?>">
						<?php echo wp_get_attachment_image( $image_id, 'woocommerce_gallery_thumbnail', false, array( 'alt' => '' ) ); ?>
					</button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
}
