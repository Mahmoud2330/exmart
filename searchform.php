<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<form role="search" method="get" class="em-search-wrap" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<svg class="em-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><circle cx="11.5" cy="11.5" r="9.5"/><path d="M18.2173 18.2178L21.9999 22.0004"/></g></svg>
	<input type="hidden" name="post_type" value="product" />
	<input class="em-input em-search-input" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search products, brands, categories…', 'exmart' ); ?>" aria-label="<?php esc_attr_e( 'Search', 'exmart' ); ?>" />
</form>
