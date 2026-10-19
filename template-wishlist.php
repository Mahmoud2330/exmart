<?php
/**
 * Template Name: exMart — Wishlist
 *
 * Product IDs live in the visitor's browser (localStorage), same as the
 * original app — no account required. This page fetches the matching
 * product cards via AJAX on load. See assets/js/site.js.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<div class="em-container" style="padding-block: var(--s8);">
	<?php exmart_breadcrumb( array( array( 'label' => __( 'Home', 'exmart' ), 'href' => home_url( '/' ) ), array( 'label' => __( 'Wishlist', 'exmart' ) ) ) ); ?>
	<h1 class="em-h2" style="margin-bottom:var(--s8);">
		<?php esc_html_e( 'Wishlist', 'exmart' ); ?>
		<span id="em-wishlist-count" style="font-size:1rem;color:var(--ink-400);font-weight:400;"></span>
	</h1>

	<div id="em-wishlist-loading" class="em-body" style="color:var(--ink-500);"><?php esc_html_e( 'Loading…', 'exmart' ); ?></div>

	<div id="em-wishlist-empty" class="em-empty-state" style="display:none;">
		<div class="em-empty-icon">
			<svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true" style="color:var(--ink-400)"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path d="M3.74181 20.5545C3.74181 20.5545 3.74181 20.5545 3.74181 20.5545C4.94143 22 7.17414 22 11.6395 22H12.3607C16.8261 22 19.0589 22 20.2585 20.5545M3.74181 20.5545C2.54219 19.1091 2.95365 16.9146 3.77657 12.5257C4.36179 9.40452 4.65441 7.84393 5.7653 6.92196M20.2585 20.5545C20.2585 20.5545 20.2585 20.5545 20.2585 20.5545C21.4581 19.1091 21.0466 16.9146 20.2237 12.5257C19.6385 9.40452 19.3459 7.84393 18.235 6.92196M18.235 6.92196C18.235 6.92196 18.235 6.92196 18.235 6.92196C17.1241 6 15.5363 6 12.3607 6H11.6395C8.46398 6 6.8762 6 5.7653 6.92196C5.7653 6.92196 5.7653 6.92196 5.7653 6.92196"/><path d="M9 6V5C9 3.34315 10.3431 2 12 2C13.6569 2 15 3.34315 15 5V6"/></g></svg>
		</div>
		<h2 class="em-h3" style="color:var(--ink-800);"><?php esc_html_e( 'Your wishlist is empty', 'exmart' ); ?></h2>
		<p class="em-body-s" style="color:var(--ink-500);max-width:40ch;"><?php esc_html_e( 'Save products you love by tapping the heart icon on any product card.', 'exmart' ); ?></p>
		<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>" class="em-btn em-btn-primary"><?php esc_html_e( 'Discover products', 'exmart' ); ?></a>
	</div>

	<ul class="products em-grid" id="em-wishlist-grid" style="display:none;"></ul>
</div>
<?php get_footer(); ?>
