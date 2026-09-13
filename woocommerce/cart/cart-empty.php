<?php
/**
 * Empty cart — Figma empty state.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package exMart
 * @version 1.0.72
 */

defined( 'ABSPATH' ) || exit;

/*
 * Default WC message suppressed — we render the Figma empty state.
 * @see exmart_render_cart_empty_state()
 */
remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );

do_action( 'woocommerce_cart_is_empty' );
