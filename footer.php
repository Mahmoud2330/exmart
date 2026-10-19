<?php
/**
 * Footer: four-column link grid, brand/payment strip, copyright.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
	</main><!-- #main-content -->

	<?php
	$product_cats   = exmart_get_nav_categories( 8 );
	$product_brands = exmart_get_nav_brands();
	$distributed    = array_slice(
		array_filter(
			$product_brands,
			static function ( $t ) {
				return exmart_brand_type( $t->term_id ) === 'distributed';
			}
		),
		0,
		2
	);
	?>

	<footer class="em-site-footer">
		<div class="em-container em-footer-grid">
			<div>
				<p class="em-overline em-footer-col-title"><?php esc_html_e( 'Categories', 'exmart' ); ?></p>
				<?php foreach ( $product_cats as $cat ) : ?>
					<a class="em-footer-link" href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
				<?php endforeach; ?>
			</div>
			<div>
				<p class="em-overline em-footer-col-title"><?php esc_html_e( 'Brands', 'exmart' ); ?></p>
				<?php foreach ( $product_brands as $brand ) : ?>
					<a class="em-footer-link" href="<?php echo esc_url( get_term_link( $brand ) ); ?>"><?php echo esc_html( $brand->name ); ?></a>
				<?php endforeach; ?>
			</div>
			<div>
				<p class="em-overline em-footer-col-title"><?php esc_html_e( 'Service', 'exmart' ); ?></p>
				<a class="em-footer-link" href="<?php echo esc_url( home_url( '/shipping/' ) ); ?>"><?php esc_html_e( 'Shipping & Returns', 'exmart' ); ?></a>
				<a class="em-footer-link" href="<?php echo esc_url( home_url( '/payment/' ) ); ?>"><?php esc_html_e( 'Payment Methods', 'exmart' ); ?></a>
				<a class="em-footer-link" href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'exmart' ); ?></a>
				<a class="em-footer-link" href="<?php echo esc_url( function_exists( 'wc_get_endpoint_url' ) ? wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) ) : home_url( '/my-account/orders/' ) ); ?>"><?php esc_html_e( 'Track Order', 'exmart' ); ?></a>
				<a class="em-footer-link" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact Us', 'exmart' ); ?></a>
			</div>
			<div class="em-footer-about">
				<div>
					<p class="em-overline em-footer-col-title"><?php esc_html_e( 'About', 'exmart' ); ?></p>
					<a class="em-footer-link" href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'Our Story', 'exmart' ); ?></a>
					<a class="em-footer-link" href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>"><?php esc_html_e( 'Privacy & Terms', 'exmart' ); ?></a>
				</div>
				<div>
					<p class="em-overline" style="color:var(--ink-500);margin-bottom:var(--s3)"><?php esc_html_e( 'Newsletter', 'exmart' ); ?></p>
					<div id="em-newsletter-widget">
						<form class="em-newsletter-form" id="em-newsletter-form">
							<input type="email" class="em-input" placeholder="your@email.com" required aria-label="<?php esc_attr_e( 'Email address for newsletter', 'exmart' ); ?>" name="email" />
							<button type="submit" class="em-btn em-btn-primary em-btn-sm"><?php esc_html_e( 'Join', 'exmart' ); ?></button>
						</form>
						<p class="em-body-s" id="em-newsletter-done" style="color:var(--success);display:none;"></p>
					</div>
				</div>
			</div>
		</div>

		<div class="em-footer-strip">
			<div class="em-container em-footer-strip-row">
				<div class="em-footer-brand-block">
					<a class="em-footer-logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<img
							class="em-footer-logo"
							src="<?php echo esc_url( EXMART_URI . '/assets/images/logo.png' ); ?>"
							alt="exMart"
							width="140"
							height="40"
						/>
					</a>
					<p class="em-caption em-footer-address">
						Al Haram, First 6th of October, Giza Governorate, Egypt<br />
						<a href="<?php echo esc_url( 'tel:' . exmart_phone_tel() ); ?>"><?php echo esc_html( exmart_phone_display() ); ?></a>
						·
						<a href="<?php echo esc_url( 'mailto:' . exmart_email() ); ?>"><?php echo esc_html( exmart_email() ); ?></a>
					</p>
				</div>
				<div>
					<div class="em-footer-trust-row">
						<?php foreach ( $distributed as $b ) : ?>
							<span class="em-trust">
								<svg class="em-trust-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path stroke-linejoin="round" d="M8.5 12.5L10.5 14.5L15.5 9.5"/></g></svg>
								<span class="em-trust-text"><?php echo esc_html( $b->name ); ?> — <?php esc_html_e( 'Official & sole distributor in Egypt', 'exmart' ); ?></span>
							</span>
						<?php endforeach; ?>
					</div>
					<div class="em-footer-payments">
						<?php foreach ( exmart_payment_methods() as $method ) :
							$url  = ! empty( $method['file'] ) ? exmart_media_url_by_filename( $method['file'] ) : '';
							$icon = empty( $url ) ? ( $method['icon'] ?? '' ) : '';
							?>
							<span class="em-payment-icon<?php echo ( $url || $icon ) ? ' em-payment-icon--logo' : ''; ?>"<?php echo $icon ? ' aria-label="' . esc_attr( $method['label'] ) . '"' : ''; ?>>
								<?php if ( $url ) : ?>
									<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $method['label'] ); ?>" loading="lazy" decoding="async" />
								<?php elseif ( $icon ) : ?>
									<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup ?>
								<?php else : ?>
									<?php echo esc_html( $method['label'] ); ?>
								<?php endif; ?>
							</span>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<p class="em-footer-copyright">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> exMart Egypt. All rights reserved.</p>
		</div>
	</footer>

</div><!-- #page -->

<script type="application/json" id="exmart-cart-qty-map"><?php echo wp_json_encode( function_exists( 'exmart_get_cart_qty_map' ) ? exmart_get_cart_qty_map() : array() ); ?></script>

<?php wp_footer(); ?>
</body>
</html>
