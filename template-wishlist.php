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
			<svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 7H4a1 1 0 00-1 1v10a1 1 0 001 1h16a1 1 0 001-1V8a1 1 0 00-1-1z" stroke="var(--ink-400)" stroke-width="1.5"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" stroke="var(--ink-400)" stroke-width="1.5"/></svg>
		</div>
		<h2 class="em-h3" style="color:var(--ink-800);"><?php esc_html_e( 'Your wishlist is empty', 'exmart' ); ?></h2>
		<p class="em-body-s" style="color:var(--ink-500);max-width:40ch;"><?php esc_html_e( 'Save products you love by tapping the heart icon on any product card.', 'exmart' ); ?></p>
		<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>" class="em-btn em-btn-primary"><?php esc_html_e( 'Discover products', 'exmart' ); ?></a>
	</div>

	<ul class="products em-grid" id="em-wishlist-grid" style="display:none;"></ul>
</div>
<?php get_footer(); ?>
