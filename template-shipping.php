<?php
/**
 * Template Name: exMart — Shipping & Returns
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<div class="em-container em-page-narrow" style="padding-block: var(--s12);">
	<?php exmart_breadcrumb( array( array( 'label' => __( 'Home', 'exmart' ), 'href' => home_url( '/' ) ), array( 'label' => __( 'Shipping & Returns', 'exmart' ) ) ) ); ?>
	<h1 class="em-h1" style="margin-bottom:var(--s8);"><?php esc_html_e( 'Shipping & Returns', 'exmart' ); ?></h1>

	<section style="margin-bottom:var(--s8);">
		<h2 class="em-h3" style="margin-bottom:var(--s4);"><?php esc_html_e( 'Delivery', 'exmart' ); ?></h2>
		<table class="em-info-table">
			<thead><tr><th><?php esc_html_e( 'Zone', 'exmart' ); ?></th><th><?php esc_html_e( 'Estimated delivery', 'exmart' ); ?></th><th><?php esc_html_e( 'Cost', 'exmart' ); ?></th></tr></thead>
			<tbody>
				<?php
				$rows = array(
					array( 'Greater Cairo & Giza', '1–2 working days', 'EGP 25 (free over EGP 300)' ),
					array( 'Alexandria & Delta', '2–3 working days', 'EGP 35 (free over EGP 300)' ),
					array( "Upper Egypt & Sa'id", '3–5 working days', 'EGP 45 (free over EGP 500)' ),
					array( 'Sinai, Red Sea & Frontier', '4–6 working days', 'EGP 55' ),
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
				__( 'We accept returns within 14 days of delivery for unused items in original, sealed packaging.', 'exmart' ),
				__( 'To initiate a return, email hello@exmart.eg with your order number and reason. We will send a prepaid return label for orders within Greater Cairo.', 'exmart' ),
				__( 'Refunds are processed within 3–5 working days after we receive and inspect the item.', 'exmart' ),
				__( 'Personalised, perishable, or intimate hygiene products (opened) are excluded from the return policy for health and safety reasons.', 'exmart' ),
				__( 'If you receive a damaged or incorrect product, contact us within 48 hours with a photo and we will arrange a replacement at no cost.', 'exmart' ),
			);
			foreach ( $items as $t ) : ?>
				<div class="em-info-list-item">
					<svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="flex-shrink:0;margin-top:2px" aria-hidden="true"><circle cx="8" cy="8" r="7" stroke="var(--ink-700)" stroke-width="1.5"/><path d="M5 8l2 2 4-4" stroke="var(--ink-700)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
					<p class="em-body-s" style="color:var(--ink-700);"><?php echo esc_html( $t ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
</div>
<?php get_footer(); ?>
