<?php
/**
 * Template Name: exMart — Shipping & Returns
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<div class="em-container" style="padding-block: var(--s12);">
	<?php exmart_breadcrumb( array( array( 'label' => __( 'Home', 'exmart' ), 'href' => home_url( '/' ) ), array( 'label' => __( 'Shipping & Returns', 'exmart' ) ) ) ); ?>
	<h1 class="em-h1" style="margin-bottom:var(--s8);"><?php esc_html_e( 'Shipping & Returns', 'exmart' ); ?></h1>

	<section style="margin-bottom:var(--s8);">
		<h2 class="em-h3" style="margin-bottom:var(--s4);"><?php esc_html_e( 'Delivery', 'exmart' ); ?></h2>
		<table class="em-info-table">
			<thead><tr><th><?php esc_html_e( 'Zone', 'exmart' ); ?></th><th><?php esc_html_e( 'Estimated delivery', 'exmart' ); ?></th><th><?php esc_html_e( 'Cost', 'exmart' ); ?></th></tr></thead>
			<tbody>
				<?php
				$rows = array(
					array( 'Greater Cairo & Giza', '3–5 working days', 'EGP 90' ),
					array( 'Alexandria', '3–5 working days', 'EGP 90' ),
					array( 'Delta', '3–5 working days', 'EGP 100' ),
					array( "Upper Egypt & Sa'id", '3–5 working days', 'EGP 120–150' ),
					array( 'Sinai, Red Sea & Frontier', '3–5 working days', 'EGP 120–150' ),
				);
				foreach ( $rows as $r ) : ?>
					<tr><td><?php echo esc_html( $r[0] ); ?></td><td><?php echo esc_html( $r[1] ); ?></td><td><?php echo esc_html( $r[2] ); ?></td></tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</section>

	<section>
		<h2 class="em-h3" style="margin-bottom:var(--s4);"><?php esc_html_e( 'Returns & Refunds', 'exmart' ); ?></h2>
		<div class="em-info-list">
			<?php
			$items = array(
				__( 'Returns and exchanges are reviewed case by case. Please contact us as soon as possible after your order arrives and we will help.', 'exmart' ),
				__( 'To start a request, email info@exmartegypt.com or message us through the WhatsApp button on the left side of the screen, with your order number and the reason.', 'exmart' ),
				__( 'If a return is approved, we will confirm the next steps and any refund details with you directly.', 'exmart' ),
				__( 'Personalised, perishable, or intimate hygiene products (opened) cannot be returned, for health and safety reasons.', 'exmart' ),
				__( 'If you receive a damaged or incorrect product, contact us right away with a photo and we will make it right.', 'exmart' ),
			);
			foreach ( $items as $t ) : ?>
				<div class="em-info-list-item">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="flex-shrink:0;margin-top:2px;color:var(--ink-700)" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path stroke-linejoin="round" d="M8.5 12.5L10.5 14.5L15.5 9.5"/></g></svg>
					<p class="em-body-s" style="color:var(--ink-700);"><?php echo esc_html( $t ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
</div>
<?php get_footer(); ?>
