<?php
/**
 * Template Name: exMart — Privacy & Terms
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$sections = array(
	array( 'title' => __( 'Data we collect', 'exmart' ), 'body' => __( 'We collect your name, email, phone number, and delivery address solely to process and deliver your orders. We do not sell or share your data with third parties for marketing purposes.', 'exmart' ) ),
	array( 'title' => __( 'How we use your data', 'exmart' ), 'body' => __( 'Your data is used to process orders, send order status updates via SMS or email, respond to customer service requests, and (with your consent) send newsletters. You can unsubscribe at any time.', 'exmart' ) ),
	array( 'title' => __( 'Data security', 'exmart' ), 'body' => __( 'All data is stored on encrypted servers. Card payments are processed by our payment gateway and we do not store or have access to card numbers.', 'exmart' ) ),
	array( 'title' => __( 'Cookies', 'exmart' ), 'body' => __( 'We use essential cookies to maintain your cart and session. We use analytics cookies (anonymised) to improve the site. You can disable non-essential cookies in your browser settings.', 'exmart' ) ),
	array( 'title' => __( 'Terms of use', 'exmart' ), 'body' => __( 'Products sold on exMart.eg are intended for personal, household, or professional use by competent adults. Diagnostic devices are for informational purposes only and do not substitute for clinical medical advice. Health claims on our own-brand products comply with Egyptian regulatory standards and are not intended to diagnose, treat, or cure any condition.', 'exmart' ) ),
	array( 'title' => __( 'Governing law', 'exmart' ), 'body' => __( 'These terms are governed by Egyptian law. Any disputes shall be subject to the jurisdiction of Egyptian courts.', 'exmart' ) ),
);
?>
<div class="em-container em-page-narrow" style="padding-block: var(--s12);">
	<?php exmart_breadcrumb( array( array( 'label' => __( 'Home', 'exmart' ), 'href' => home_url( '/' ) ), array( 'label' => __( 'Privacy & Terms', 'exmart' ) ) ) ); ?>
	<h1 class="em-h1" style="margin-bottom:var(--s8);"><?php esc_html_e( 'Privacy Policy & Terms of Use', 'exmart' ); ?></h1>
	<?php foreach ( $sections as $s ) : ?>
		<section style="margin-bottom:var(--s6);">
			<h2 class="em-h4" style="margin-bottom:var(--s3);"><?php echo esc_html( $s['title'] ); ?></h2>
			<p class="em-body-s" style="color:var(--ink-700);line-height:1.6;"><?php echo esc_html( $s['body'] ); ?></p>
		</section>
	<?php endforeach; ?>
	<p class="em-body-s" style="color:var(--ink-400);"><?php esc_html_e( 'Note for the store owner: review this copy with counsel and align it with your actual data-handling practices (hosting provider, analytics tools, payment gateway) before launch.', 'exmart' ); ?></p>
</div>
<?php get_footer(); ?>
