<?php
/**
 * Dev/staging utilities for previewing the review UI with placeholder
 * reviews before the real ~100k-review spreadsheet import lands. Every
 * review these tools create is a normal, fully-approved WooCommerce
 * review — it shows up everywhere on the live site exactly like a real
 * one (product cards, PDP, rating averages). The only difference is an
 * invisible `_exmart_seed_review` comment meta tag, used purely so the
 * cleanup tool can remove exactly what it created and nothing else —
 * real/imported reviews never carry that tag and are never touched.
 *
 * Two ways to run this:
 * - wp-admin: Tools → Seed Reviews (admins only, no server/SSH access needed).
 * - WP-CLI, if you ever get shell access: `wp exmart seed-reviews --count=200`
 *   and `wp exmart remove-seeded-reviews`.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

const EXMART_SEED_REVIEW_META_KEY = '_exmart_seed_review';

function exmart_seed_review_first_names() {
	return array(
		'Mona', 'Youssef', 'Sara', 'Ahmed', 'Nour', 'Omar', 'Laila', 'Khaled',
		'Hana', 'Mostafa', 'Dina', 'Tarek', 'Rania', 'Hassan', 'Mariam', 'Amr',
		'Salma', 'Ziad', 'Yasmin', 'Karim',
	);
}

function exmart_seed_review_last_initials() {
	return array( 'A.', 'M.', 'S.', 'K.', 'H.', 'R.', 'T.', 'N.', 'E.', 'F.' );
}

function exmart_seed_review_snippets() {
	return array(
		5 => array(
			'Exactly what I needed, great quality.',
			'Fast delivery and the product works perfectly.',
			'Really happy with this purchase, will order again.',
			'Excellent value for the price.',
			'Exceeded my expectations, highly recommend.',
			'Packaging was great and it arrived quickly.',
			'Love it, exactly as described.',
			'Top quality, no complaints at all.',
			'Great product, true to the description.',
			'Very satisfied, this is my second order.',
		),
		4 => array(
			'Good product overall, does the job well.',
			'Solid quality, delivery took a bit longer than expected.',
			'Works well, would buy again.',
			'Pretty good, minor packaging issue but the product is fine.',
			'Does what it says, happy with it.',
			'Nice quality for the price point.',
			'Good experience, would recommend.',
		),
		3 => array(
			"It's okay, nothing special but it gets the job done.",
			'Average product, expected a bit more.',
			"Decent but delivery was slower than I'd like.",
			'Does the job but quality could be better.',
		),
		2 => array(
			'Not quite what I expected from the photos.',
			'Packaging could be better, product is just okay.',
			'A bit disappointed, but customer service was helpful.',
		),
		1 => array(
			'Did not meet my expectations, considering a return.',
			'Had issues with this order, hoping for a resolution.',
		),
	);
}

/**
 * Pick a star rating >= $min_rating, weighted toward higher ratings
 * (like a real store's distribution).
 */
function exmart_seed_review_weighted_rating( $min_rating ) {
	$weights = array( 5 => 50, 4 => 25, 3 => 15, 2 => 7, 1 => 3 );
	$pool    = array();
	foreach ( $weights as $r => $w ) {
		if ( $r < $min_rating ) {
			continue;
		}
		$pool[ $r ] = $w;
	}
	if ( empty( $pool ) ) {
		return $min_rating;
	}
	$total = array_sum( $pool );
	$rand  = wp_rand( 1, $total );
	$acc   = 0;
	foreach ( $pool as $r => $w ) {
		$acc += $w;
		if ( $rand <= $acc ) {
			return $r;
		}
	}
	return array_key_first( $pool );
}

/**
 * Insert $count placeholder reviews as normal, approved WooCommerce
 * reviews across $product_ids (or every published product if empty).
 *
 * @param int        $count
 * @param int        $min_rating 1-5.
 * @param int[]      $product_ids
 * @return array{inserted:int,products:int,error:string}
 */
function exmart_seed_reviews_run( $count, $min_rating = 1, $product_ids = array() ) {
	$count      = max( 1, (int) $count );
	$min_rating = max( 1, min( 5, (int) $min_rating ) );

	if ( empty( $product_ids ) ) {
		$product_ids = get_posts( array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		) );
	}
	$product_ids = array_values( array_filter( array_map( 'absint', $product_ids ) ) );

	if ( empty( $product_ids ) ) {
		return array(
			'inserted' => 0,
			'products' => 0,
			'error'    => __( 'No products found to attach reviews to.', 'exmart' ),
		);
	}

	$first_names = exmart_seed_review_first_names();
	$last_inits  = exmart_seed_review_last_initials();
	$snippets    = exmart_seed_review_snippets();

	$inserted = 0;
	$touched  = array();

	for ( $i = 0; $i < $count; $i++ ) {
		$product_id = $product_ids[ array_rand( $product_ids ) ];
		$rating     = exmart_seed_review_weighted_rating( $min_rating );
		$pool       = isset( $snippets[ $rating ] ) ? $snippets[ $rating ] : $snippets[3];
		$content    = $pool[ array_rand( $pool ) ];
		$name       = $first_names[ array_rand( $first_names ) ] . ' ' . $last_inits[ array_rand( $last_inits ) ];
		$days_ago   = wp_rand( 0, 180 );
		$date_gmt   = gmdate( 'Y-m-d H:i:s', strtotime( "-{$days_ago} days" ) );

		$comment_id = wp_insert_comment( array(
			'comment_post_ID'      => $product_id,
			'comment_author'       => $name,
			'comment_author_email' => sanitize_title( $name ) . '+seed@example.com',
			'comment_content'      => $content,
			'comment_type'         => 'review',
			'comment_approved'     => 1,
			'comment_date'         => get_date_from_gmt( $date_gmt ),
			'comment_date_gmt'     => $date_gmt,
		) );

		if ( ! $comment_id ) {
			continue;
		}

		update_comment_meta( $comment_id, 'rating', $rating );
		update_comment_meta( $comment_id, EXMART_SEED_REVIEW_META_KEY, 1 );

		$touched[ $product_id ] = true;
		$inserted++;
	}

	foreach ( array_keys( $touched ) as $pid ) {
		if ( class_exists( 'WC_Comments' ) ) {
			WC_Comments::clear_transients( $pid );
		}
	}

	return array(
		'inserted' => $inserted,
		'products' => count( $touched ),
		'error'    => '',
	);
}

/**
 * Delete every review exmart_seed_reviews_run() created — and only those.
 *
 * @return array{removed:int,products:int}
 */
function exmart_remove_seeded_reviews_run() {
	$comments = get_comments( array(
		'meta_key'   => EXMART_SEED_REVIEW_META_KEY,
		'meta_value' => 1,
		'type'       => 'review',
		'number'     => 0,
	) );

	$touched = array();
	foreach ( $comments as $comment ) {
		$touched[ $comment->comment_post_ID ] = true;
		wp_delete_comment( $comment->comment_ID, true );
	}

	foreach ( array_keys( $touched ) as $pid ) {
		if ( class_exists( 'WC_Comments' ) ) {
			WC_Comments::clear_transients( $pid );
		}
	}

	return array(
		'removed'  => count( $comments ),
		'products' => count( $touched ),
	);
}

/* ── wp-admin: Tools → Seed Reviews ───────────────────────
   No SSH/WP-CLI access needed — click-to-run for anyone with the
   manage_options capability (site admins). */

function exmart_seed_reviews_admin_menu() {
	add_management_page(
		__( 'Seed Reviews', 'exmart' ),
		__( 'Seed Reviews', 'exmart' ),
		'manage_options',
		'exmart-seed-reviews',
		'exmart_seed_reviews_admin_page'
	);
}
add_action( 'admin_menu', 'exmart_seed_reviews_admin_menu' );

function exmart_seed_reviews_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$notice = '';

	if ( ! empty( $_POST['exmart_seed_action'] ) && check_admin_referer( 'exmart_seed_reviews' ) ) {
		$action = sanitize_text_field( wp_unslash( $_POST['exmart_seed_action'] ) );

		if ( 'seed' === $action ) {
			$count      = isset( $_POST['exmart_seed_count'] ) ? absint( $_POST['exmart_seed_count'] ) : 50;
			$count      = min( 300, max( 1, $count ) ); // capped per click — safe on any host, click again for more.
			$min_rating = isset( $_POST['exmart_seed_min_rating'] ) ? absint( $_POST['exmart_seed_min_rating'] ) : 1;
			$result     = exmart_seed_reviews_run( $count, $min_rating );

			if ( $result['error'] ) {
				$notice = '<div class="notice notice-error"><p>' . esc_html( $result['error'] ) . '</p></div>';
			} else {
				$notice = '<div class="notice notice-success"><p>'
					. sprintf(
						/* translators: 1: reviews inserted, 2: products touched */
						esc_html__( 'Inserted %1$d seeded reviews across %2$d products. They are live on the site now, same as any real review.', 'exmart' ),
						(int) $result['inserted'],
						(int) $result['products']
					)
					. '</p></div>';
			}
		} elseif ( 'remove' === $action ) {
			$result = exmart_remove_seeded_reviews_run();
			$notice = '<div class="notice notice-success"><p>'
				. sprintf(
					/* translators: 1: reviews removed, 2: products affected */
					esc_html__( 'Removed %1$d seeded reviews from %2$d products.', 'exmart' ),
					(int) $result['removed'],
					(int) $result['products']
				)
				. '</p></div>';
		}
	}

	$seeded_count = get_comments( array(
		'meta_key'   => EXMART_SEED_REVIEW_META_KEY,
		'meta_value' => 1,
		'type'       => 'review',
		'count'      => true,
	) );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Seed Reviews', 'exmart' ); ?></h1>
		<p>
			<?php esc_html_e( 'Generates placeholder product reviews so you can see how the review UI looks on the live site before the real review import. These are normal, fully-approved reviews — remove them with the button below right before importing the real ones.', 'exmart' ); ?>
		</p>
		<p><strong><?php echo esc_html( sprintf(
			/* translators: %d: number of currently seeded reviews */
			__( 'Currently seeded: %d review(s).', 'exmart' ),
			(int) $seeded_count
		) ); ?></strong></p>

		<?php echo $notice; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — built from escaped pieces above. ?>

		<h2><?php esc_html_e( 'Add seeded reviews', 'exmart' ); ?></h2>
		<form method="post">
			<?php wp_nonce_field( 'exmart_seed_reviews' ); ?>
			<input type="hidden" name="exmart_seed_action" value="seed" />
			<table class="form-table">
				<tr>
					<th scope="row"><label for="exmart_seed_count"><?php esc_html_e( 'How many', 'exmart' ); ?></label></th>
					<td><input type="number" min="1" max="300" step="1" name="exmart_seed_count" id="exmart_seed_count" value="50" class="small-text" />
						<p class="description"><?php esc_html_e( 'Up to 300 per click — click Generate again to add more.', 'exmart' ); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><label for="exmart_seed_min_rating"><?php esc_html_e( 'Lowest star rating', 'exmart' ); ?></label></th>
					<td>
						<select name="exmart_seed_min_rating" id="exmart_seed_min_rating">
							<option value="1" selected><?php esc_html_e( '1 — full realistic spread', 'exmart' ); ?></option>
							<option value="3"><?php esc_html_e( '3 — only 3★ and up', 'exmart' ); ?></option>
							<option value="4"><?php esc_html_e( '4 — only 4★ and up', 'exmart' ); ?></option>
						</select>
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'Generate seeded reviews', 'exmart' ) ); ?>
		</form>

		<h2><?php esc_html_e( 'Remove all seeded reviews', 'exmart' ); ?></h2>
		<p><?php esc_html_e( 'Deletes only the reviews this tool created — real/imported reviews are never touched. Run this right before the real review import.', 'exmart' ); ?></p>
		<form method="post">
			<?php wp_nonce_field( 'exmart_seed_reviews' ); ?>
			<input type="hidden" name="exmart_seed_action" value="remove" />
			<?php submit_button( __( 'Remove seeded reviews', 'exmart' ), 'secondary' ); ?>
		</form>
	</div>
	<?php
}

/* ── WP-CLI, for later if/when server access is available ── */

if ( defined( 'WP_CLI' ) && WP_CLI ) {

	class Exmart_Dev_CLI {

		/**
		 * Generate dummy product reviews for UI/staging testing.
		 *
		 * ## OPTIONS
		 *
		 * [--count=<number>]
		 * : How many reviews to generate. Default 50.
		 *
		 * [--products=<ids>]
		 * : Comma-separated product IDs to attach reviews to. Default: every
		 * published product.
		 *
		 * [--min-rating=<n>]
		 * : Lowest star rating to generate, 1-5. Default 1.
		 *
		 * ## EXAMPLES
		 *
		 *     wp exmart seed-reviews --count=200
		 *
		 * @when after_wp_load
		 */
		public function seed_reviews( $args, $assoc_args ) {
			$count      = isset( $assoc_args['count'] ) ? (int) $assoc_args['count'] : 50;
			$min_rating = isset( $assoc_args['min-rating'] ) ? (int) $assoc_args['min-rating'] : 1;
			$product_ids = ! empty( $assoc_args['products'] )
				? array_map( 'absint', explode( ',', $assoc_args['products'] ) )
				: array();

			$result = exmart_seed_reviews_run( $count, $min_rating, $product_ids );

			if ( $result['error'] ) {
				WP_CLI::error( $result['error'] );
			}

			WP_CLI::success( sprintf(
				'Inserted %d seeded reviews across %d products. Run `wp exmart remove-seeded-reviews` before the real import.',
				$result['inserted'],
				$result['products']
			) );
		}

		/**
		 * Delete every review `seed-reviews` generated — and only those.
		 *
		 * ## EXAMPLES
		 *
		 *     wp exmart remove-seeded-reviews
		 *
		 * @when after_wp_load
		 */
		public function remove_seeded_reviews( $args, $assoc_args ) {
			$result = exmart_remove_seeded_reviews_run();
			WP_CLI::success( sprintf( 'Removed %d seeded reviews from %d products.', $result['removed'], $result['products'] ) );
		}
	}

	WP_CLI::add_command( 'exmart', 'Exmart_Dev_CLI' );
}
