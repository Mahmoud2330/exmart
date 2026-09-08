<?php
/**
 * exMart theme functions and definitions.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'EXMART_VERSION', '1.0.2' );
define( 'EXMART_DIR', get_template_directory() );
define( 'EXMART_URI', get_template_directory_uri() );

require EXMART_DIR . '/inc/theme-setup.php';
require EXMART_DIR . '/inc/custom-taxonomies.php';
require EXMART_DIR . '/inc/woocommerce-hooks.php';
require EXMART_DIR . '/inc/wishlist.php';
require EXMART_DIR . '/inc/contact-form.php';
require EXMART_DIR . '/inc/theme-activation.php';
require EXMART_DIR . '/inc/template-tags.php';
