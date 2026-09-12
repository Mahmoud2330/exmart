<?php
/**
 * View order / Track — Figma Account tracking pipeline.
 *
 * @package exmart
 */

defined( 'ABSPATH' ) || exit;

$notes = $order->get_customer_order_notes();
$status = $order->get_status();
$status_meta = exmart_account_order_status_meta( $status );

$pipeline = array( 'pending', 'on-hold', 'processing', 'completed' );
$pipeline_labels = array(
	'pending'    => __( 'Placed', 'exmart' ),
	'on-hold'    => __( 'Confirmed', 'exmart' ),
	'processing' => __( 'Shipped', 'exmart' ),
	'completed'  => __( 'Delivered', 'exmart' ),
);

$current_idx = array_search( $status, $pipeline, true );
if ( false === $current_idx ) {
	if ( in_array( $status, array( 'cancelled', 'failed', 'refunded' ), true ) ) {
		$current_idx = -1;
	} else {
		$current_idx = 0;
	}
}
?>
<div class="em-account-panel em-account-tracking">
	<p class="em-body-s" style="margin:0 0 var(--s4);">
		<a class="em-back-link" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">&larr; <?php esc_html_e( 'Back to orders', 'exmart' ); ?></a>
	</p>

	<h2 class="em-h4 em-account-panel-title">
		<?php
		printf(
			/* translators: %s: order number */
			esc_html__( 'Order #%s — Tracking', 'exmart' ),
			esc_html( $order->get_order_number() )
		);
		?>
	</h2>

	<?php if ( $current_idx >= 0 ) : ?>
		<div class="em-tracking-board">
			<div class="em-pipeline">
				<?php foreach ( $pipeline as $i => $slug ) :
					$done    = $i < $current_idx;
					$current = $i === $current_idx;
					$cls     = 'em-pipeline-step' . ( $done ? ' done' : '' );
					$dot     = 'em-pipeline-dot' . ( $done ? ' done' : ( $current ? ' current' : '' ) );
					$lab     = 'em-pipeline-label' . ( $done ? ' done' : ( $current ? ' current' : '' ) );
					?>
					<div class="<?php echo esc_attr( $cls ); ?>">
						<div class="<?php echo esc_attr( $dot ); ?>">
							<?php if ( $done ) : ?>
								<svg width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true"><path d="M2 5l2 2 4-4" stroke="var(--paper)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
							<?php endif; ?>
						</div>
						<span class="<?php echo esc_attr( $lab ); ?>"><?php echo esc_html( $pipeline_labels[ $slug ] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php else : ?>
		<p class="em-badge em-order-status" style="background: color-mix(in srgb, <?php echo esc_attr( $status_meta['color'] ); ?> 14%, transparent); color: <?php echo esc_attr( $status_meta['color'] ); ?>;">
			<?php echo esc_html( strtoupper( $status_meta['label'] ) ); ?>
		</p>
	<?php endif; ?>

	<p class="em-body-s" style="color:var(--ink-500);margin:var(--s6) 0;">
		<?php
		printf(
			/* translators: 1: order date 2: status */
			esc_html__( 'Placed on %1$s · Status: %2$s', 'exmart' ),
			esc_html( wc_format_datetime( $order->get_date_created() ) ),
			esc_html( $status_meta['label'] )
		);
		?>
	</p>

	<section class="em-account-order-details">
		<h3 class="em-h4" style="margin-bottom:var(--s4);"><?php esc_html_e( 'Items', 'exmart' ); ?></h3>
		<table class="woocommerce-table woocommerce-table--order-details shop_table order_details em-info-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Product', 'exmart' ); ?></th>
					<th><?php esc_html_e( 'Total', 'exmart' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $order->get_items() as $item_id => $item ) : ?>
					<tr>
						<td>
							<?php echo esc_html( $item->get_name() ); ?>
							&times; <?php echo esc_html( (string) $item->get_quantity() ); ?>
						</td>
						<td><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<?php foreach ( $order->get_order_item_totals() as $total ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
						<td><?php echo wp_kses_post( $total['value'] ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tfoot>
		</table>
	</section>

	<?php if ( $notes ) : ?>
		<section style="margin-top:var(--s8);">
			<h3 class="em-h4" style="margin-bottom:var(--s4);"><?php esc_html_e( 'Order updates', 'exmart' ); ?></h3>
			<ol class="em-account-notes">
				<?php foreach ( $notes as $note ) : ?>
					<li>
						<p class="em-caption" style="color:var(--ink-500);"><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $note->comment_date ) ) ); ?></p>
						<div class="em-body-s"><?php echo wp_kses_post( wpautop( wptexturize( $note->comment_content ) ) ); ?></div>
					</li>
				<?php endforeach; ?>
			</ol>
		</section>
	<?php endif; ?>
</div>
