<?php
/**
 * Template Name: exMart — Contact
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$status = isset( $_GET['exmart_contact'] ) ? sanitize_text_field( wp_unslash( $_GET['exmart_contact'] ) ) : '';
$subjects = array( 'Order issue', 'Product inquiry', 'Authenticity / warranty', 'Partnership', 'Complaint', 'Other' );
?>
<div class="em-container" style="padding-block: var(--s12);">
	<?php exmart_breadcrumb( array( array( 'label' => __( 'Home', 'exmart' ), 'href' => home_url( '/' ) ), array( 'label' => __( 'Contact', 'exmart' ) ) ) ); ?>
	<h1 class="em-h1" style="margin-bottom:var(--s10);"><?php esc_html_e( 'Contact Us', 'exmart' ); ?></h1>

	<div class="em-contact-grid">
		<div class="em-contact-info">
			<div>
				<p class="em-overline" style="color:var(--ink-500);margin-bottom:var(--s3);"><?php esc_html_e( 'Address', 'exmart' ); ?></p>
				<p class="em-body-s" style="color:var(--ink-700);">12 El-Nozha Street, Heliopolis<br />Cairo, Egypt 11361</p>
			</div>
			<div>
				<p class="em-overline" style="color:var(--ink-500);margin-bottom:var(--s3);"><?php esc_html_e( 'Phone & WhatsApp', 'exmart' ); ?></p>
				<a href="<?php echo esc_url( 'tel:' . exmart_phone_tel() ); ?>" style="display:block;color:var(--ink-800);text-decoration:none;font-weight:600;"><?php echo esc_html( exmart_phone_display() ); ?></a>
				<a href="<?php echo esc_url( exmart_whatsapp_url() ); ?>" class="em-whatsapp-link" style="margin-top:var(--s2);" target="_blank" rel="noopener noreferrer">
					<?php exmart_whatsapp_icon( 18 ); ?>
					<?php esc_html_e( 'Chat on WhatsApp', 'exmart' ); ?>
				</a>
			</div>
			<div>
				<p class="em-overline" style="color:var(--ink-500);margin-bottom:var(--s3);"><?php esc_html_e( 'Email', 'exmart' ); ?></p>
				<a href="<?php echo esc_url( 'mailto:' . exmart_email() ); ?>" style="color:var(--ink-800);text-decoration:none;font-weight:600;"><?php echo esc_html( exmart_email() ); ?></a>
			</div>
			<div>
				<p class="em-overline" style="color:var(--ink-500);margin-bottom:var(--s3);"><?php esc_html_e( 'Hours', 'exmart' ); ?></p>
				<p class="em-body-s" style="color:var(--ink-700);">Sunday – Thursday: 9:00 am – 6:00 pm<br />Friday – Saturday: 10:00 am – 3:00 pm</p>
			</div>
			<div class="em-contact-map">
				<div class="em-contact-map-inner">
					<svg width="32" height="32" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="var(--ink-400)" stroke-width="1.5"/><circle cx="12" cy="9" r="2.5" stroke="var(--ink-400)" stroke-width="1.5"/></svg>
					<p class="em-caption" style="color:var(--ink-400);">12 El-Nozha Street, Heliopolis, Cairo</p>
				</div>
			</div>
		</div>

		<div>
			<?php if ( 'sent' === $status ) : ?>
				<div style="display:flex;flex-direction:column;align-items:flex-start;gap:var(--s4);padding-top:var(--s8);">
					<div style="width:56px;height:56px;border-radius:50%;background:var(--success-bg);display:flex;align-items:center;justify-content:center;">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12l4 4 10-10" stroke="var(--success)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</div>
					<h2 class="em-h3"><?php esc_html_e( 'Message sent!', 'exmart' ); ?></h2>
					<p class="em-body" style="color:var(--ink-500);"><?php esc_html_e( 'Thank you for reaching out. We will reply within one business day.', 'exmart' ); ?></p>
					<a href="<?php echo esc_url( get_permalink() ); ?>" class="em-btn em-btn-secondary"><?php esc_html_e( 'Send another message', 'exmart' ); ?></a>
				</div>
			<?php else : ?>
				<?php if ( 'error' === $status ) : ?>
					<div class="woocommerce-error" style="margin-bottom:var(--s4);"><?php esc_html_e( 'Something went wrong — please check the form and try again.', 'exmart' ); ?></div>
				<?php endif; ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:flex;flex-direction:column;gap:var(--s4);">
					<input type="hidden" name="action" value="exmart_contact_submit" />
					<?php wp_nonce_field( 'exmart_contact', 'exmart_contact_nonce' ); ?>
					<input type="text" name="exmart_website" value="" style="position:absolute;left:-9999px;" tabindex="-1" autocomplete="off" aria-hidden="true" />

					<div class="em-form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:var(--s4);">
						<div class="em-input-wrap">
							<label class="em-input-label" for="c-name"><?php esc_html_e( 'Full name', 'exmart' ); ?></label>
							<input class="em-input" type="text" id="c-name" name="name" required />
						</div>
						<div class="em-input-wrap">
							<label class="em-input-label" for="c-email"><?php esc_html_e( 'Email address', 'exmart' ); ?></label>
							<input class="em-input" type="email" id="c-email" name="email" required />
						</div>
					</div>
					<div class="em-input-wrap">
						<label class="em-input-label" for="c-phone"><?php esc_html_e( 'Phone (optional)', 'exmart' ); ?></label>
						<input class="em-input" type="tel" id="c-phone" name="phone" />
					</div>
					<div class="em-input-wrap">
						<label class="em-input-label" for="c-subject"><?php esc_html_e( 'Subject', 'exmart' ); ?></label>
						<select class="em-input" id="c-subject" name="subject">
							<?php foreach ( $subjects as $s ) : ?><option value="<?php echo esc_attr( $s ); ?>"><?php echo esc_html( $s ); ?></option><?php endforeach; ?>
						</select>
					</div>
					<div class="em-input-wrap">
						<label class="em-input-label" for="c-message"><?php esc_html_e( 'Message', 'exmart' ); ?></label>
						<textarea class="em-input" id="c-message" name="message" rows="6" style="resize:vertical;min-height:120px;" required placeholder="<?php esc_attr_e( 'Describe your enquiry in detail…', 'exmart' ); ?>"></textarea>
					</div>
					<button type="submit" class="em-btn em-btn-primary em-btn-lg" style="align-self:flex-start;min-width:180px;"><?php esc_html_e( 'Send message', 'exmart' ); ?></button>
				</form>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>
