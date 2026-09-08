<?php
/**
 * Contact page form handler — a real submission via wp_mail(), no
 * plugin dependency. Posts to admin-post.php and redirects back to
 * the Contact page with ?exmart_contact=sent or =error.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function exmart_handle_contact_submit() {
	if ( ! isset( $_POST['exmart_contact_nonce'] ) || ! wp_verify_nonce( $_POST['exmart_contact_nonce'], 'exmart_contact' ) ) {
		wp_safe_redirect( add_query_arg( 'exmart_contact', 'error', wp_get_referer() ?: home_url( '/' ) ) );
		exit;
	}

	// Honeypot — bots fill hidden fields, humans don't.
	if ( ! empty( $_POST['exmart_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'exmart_contact', 'sent', wp_get_referer() ?: home_url( '/' ) ) );
		exit;
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : 'General enquiry';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	$redirect_base = wp_get_referer() ?: home_url( '/' );

	if ( ! $name || ! is_email( $email ) || ! $message ) {
		wp_safe_redirect( add_query_arg( 'exmart_contact', 'error', $redirect_base ) );
		exit;
	}

	$to      = get_option( 'admin_email' );
	$subject_line = sprintf( '[%s] %s — %s', get_bloginfo( 'name' ), $subject, $name );
	$body    = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\nSubject: {$subject}\n\nMessage:\n{$message}";
	$headers = array( 'Content-Type: text/plain; charset=UTF-8', "Reply-To: {$name} <{$email}>" );

	$sent = wp_mail( $to, $subject_line, $body, $headers );

	wp_safe_redirect( add_query_arg( 'exmart_contact', $sent ? 'sent' : 'error', $redirect_base ) );
	exit;
}
add_action( 'admin_post_exmart_contact_submit', 'exmart_handle_contact_submit' );
add_action( 'admin_post_nopriv_exmart_contact_submit', 'exmart_handle_contact_submit' );

/**
 * Newsletter sign-up — stores the email as a lightweight custom post
 * (type "exmart_subscriber") so admins have a real list to export,
 * without requiring a mailing-list plugin. Swap for Mailchimp/etc. later.
 */
function exmart_register_subscriber_cpt() {
	register_post_type( 'exmart_subscriber', array(
		'label'        => __( 'Newsletter Subscribers', 'exmart' ),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => 'tools.php',
		'supports'     => array( 'title' ),
		'labels'       => array(
			'name'      => __( 'Newsletter Subscribers', 'exmart' ),
			'menu_name' => __( 'Newsletter', 'exmart' ),
		),
	) );
}
add_action( 'init', 'exmart_register_subscriber_cpt' );

function exmart_ajax_newsletter_subscribe() {
	check_ajax_referer( 'exmart_ajax', 'nonce' );
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'exmart' ) ) );
	}

	$existing = get_posts( array(
		'post_type'      => 'exmart_subscriber',
		'title'          => $email,
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( empty( $existing ) ) {
		wp_insert_post( array(
			'post_type'   => 'exmart_subscriber',
			'post_title'  => $email,
			'post_status' => 'publish',
		) );
	}

	wp_send_json_success( array( 'message' => __( "You're subscribed — thank you!", 'exmart' ) ) );
}
add_action( 'wp_ajax_exmart_newsletter_subscribe', 'exmart_ajax_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_exmart_newsletter_subscribe', 'exmart_ajax_newsletter_subscribe' );
