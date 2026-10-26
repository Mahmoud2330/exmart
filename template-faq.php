<?php
/**
 * Template Name: exMart — FAQ
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$faqs = array(
	array( 'q' => __( 'Are the products authentic?', 'exmart' ), 'a' => __( 'Yes — all products are sourced directly from manufacturers or produced under our own quality protocols. We are the official sole distributor in Egypt for Diversey, Grace, Oview, and SureCheck.', 'exmart' ) ),
	array( 'q' => __( 'Do you offer cash on delivery?', 'exmart' ), 'a' => __( 'Yes. Cash on delivery is available across all Egyptian governorates at no extra charge.', 'exmart' ) ),
	array( 'q' => __( 'How long does delivery take?', 'exmart' ), 'a' => __( 'Delivery takes 3–5 working days across Egypt.', 'exmart' ) ),
	array( 'q' => __( 'Can I return a product?', 'exmart' ), 'a' => __( 'Returns and exchanges are reviewed case by case, so please contact us as soon as possible after delivery: email info@exmartegypt.com or message us through the WhatsApp button on the left side of the screen. Intimate hygiene and opened products cannot be returned for health and safety reasons.', 'exmart' ) ),
	array( 'q' => __( 'What is your warranty policy?', 'exmart' ), 'a' => __( 'Electronic devices (Oview, SureCheck) carry a 12-month warranty from purchase date. Contact us with your order number and a description of the fault.', 'exmart' ) ),
	array( 'q' => __( 'Are your house brands safe for sensitive skin?', 'exmart' ), 'a' => __( 'All Qualita, Vodlia, Eliv, and Verve products are developed with dermatologist review and are formulated to minimise irritation. However, as with any personal care product, we recommend performing a patch test before first use if you have known skin sensitivities.', 'exmart' ) ),
	array( 'q' => __( 'Do you ship outside Egypt?', 'exmart' ), 'a' => __( 'Not currently. We ship within Egypt only.', 'exmart' ) ),
	array( 'q' => __( 'How do I track my order?', 'exmart' ), 'a' => __( 'After dispatch you will receive an SMS with your tracking number. You can also track your order from the Account section on our website.', 'exmart' ) ),
);
?>
<div class="em-container" style="padding-block: var(--s12);">
	<?php exmart_breadcrumb( array( array( 'label' => __( 'Home', 'exmart' ), 'href' => home_url( '/' ) ), array( 'label' => __( 'FAQ', 'exmart' ) ) ) ); ?>
	<h1 class="em-h1" style="margin-bottom:var(--s8);"><?php esc_html_e( 'Frequently Asked Questions', 'exmart' ); ?></h1>
	<div>
		<?php foreach ( $faqs as $i => $f ) : ?>
			<div class="em-faq-item">
				<button class="em-faq-q" aria-expanded="false">
					<span><?php echo esc_html( $f['q'] ); ?></span>
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path fill="none" stroke="var(--ink-500)" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9L12 15L5 9"/></svg>
				</button>
				<p class="em-body-s em-faq-a"><?php echo esc_html( $f['a'] ); ?></p>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<?php get_footer(); ?>
