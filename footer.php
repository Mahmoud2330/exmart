<?php
/**
 * Footer: four-column link grid, brand/payment strip, copyright, floating mobile pill.
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
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
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
						12 El-Nozha St, Heliopolis, Cairo, Egypt<br />
						<a href="<?php echo esc_url( 'tel:' . exmart_phone_tel() ); ?>"><?php echo esc_html( exmart_phone_display() ); ?></a>
						·
						<a href="<?php echo esc_url( 'mailto:' . exmart_email() ); ?>"><?php echo esc_html( exmart_email() ); ?></a>
					</p>
				</div>
				<div>
					<div class="em-footer-trust-row">
						<?php foreach ( $distributed as $b ) : ?>
							<span class="em-trust">
								<svg class="em-trust-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M5 8l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
								<span class="em-trust-text"><?php echo esc_html( $b->name ); ?> — <?php esc_html_e( 'Official & sole distributor in Egypt', 'exmart' ); ?></span>
							</span>
						<?php endforeach; ?>
					</div>
					<div class="em-footer-payments">
						<?php foreach ( array( 'COD', 'Fawry', 'Meeza', 'Visa', 'MC', 'Instapay' ) as $p ) : ?>
							<span class="em-payment-icon"><?php echo esc_html( $p ); ?></span>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<p class="em-footer-copyright">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> exMart Egypt. All rights reserved.</p>
		</div>
	</footer>

	<!-- Floating mobile pill -->
	<div class="em-floating-pill" id="em-floating-pill">
		<button id="em-pill-search" aria-label="<?php esc_attr_e( 'Search', 'exmart' ); ?>">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
		</button>
		<span class="em-floating-pill-sep"></span>
		<a href="<?php echo esc_url( $shop_url ); ?>" id="em-pill-shop" aria-label="<?php esc_attr_e( 'Browse all products', 'exmart' ); ?>" style="display:flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:var(--r-full);color:#fff;text-decoration:none;">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
		</a>
		<span class="em-floating-pill-sep"></span>
		<button id="em-pill-cart" aria-label="<?php esc_attr_e( 'Cart', 'exmart' ); ?>" class="em-icon-badge">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 3h2l2.4 12.4a2 2 0 002 1.6h8.2a2 2 0 002-1.6L21 8H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="21" r="1" fill="currentColor"/><circle cx="18" cy="21" r="1" fill="currentColor"/></svg>
		</button>
	</div>

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
