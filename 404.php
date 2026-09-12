<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<div class="em-container em-404">
	<p class="em-404-code">404</p>
	<h1 class="em-h2"><?php esc_html_e( 'Page not found', 'exmart' ); ?></h1>
	<p class="em-body" style="color:var(--ink-500);"><?php esc_html_e( 'The page you are looking for does not exist or has been moved.', 'exmart' ); ?></p>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="em-btn em-btn-primary"><?php esc_html_e( 'Go home', 'exmart' ); ?></a>
</div>
<?php get_footer(); ?>
