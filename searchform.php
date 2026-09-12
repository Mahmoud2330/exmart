<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<form role="search" method="get" class="em-search-wrap" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<svg class="em-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
	<input type="hidden" name="post_type" value="product" />
	<input class="em-input em-search-input" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search products, brands, categories…', 'exmart' ); ?>" aria-label="<?php esc_attr_e( 'Search', 'exmart' ); ?>" />
</form>
