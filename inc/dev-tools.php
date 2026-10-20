<?php
/**
 * WP-CLI dev/staging utilities. Only defines anything when running under
 * WP-CLI, so this file has zero effect on normal site requests.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * `wp exmart seed-reviews` / `wp exmart remove-seeded-reviews`.
 *
 * Generates placeholder product reviews for previewing the review UI on
 * staging before the real review import (spreadsheet of existing social
 * reviews) lands. Every review this creates is tagged with the
 * `_exmart_seed_review` comment meta key so the cleanup command only ever
 * removes reviews it created itself — real/imported reviews are never
 * touched.
 */
class Exmart_Dev_CLI {

	const SEED_META_KEY = '_exmart_seed_review';

	private static $first_names = array(
		'Mona', 'Youssef', 'Sara', 'Ahmed', 'Nour', 'Omar', 'Laila', 'Khaled',
		'Hana', 'Mostafa', 'Dina', 'Tarek', 'Rania', 'Hassan', 'Mariam', 'Amr',
		'Salma', 'Ziad', 'Yasmin', 'Karim',
	);

	private static $last_initials = array( 'A.', 'M.', 'S.', 'K.', 'H.', 'R.', 'T.', 'N.', 'E.', 'F.' );

	private static $snippets = array(
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
	 * : Lowest star rating to generate, 1-5. Default 1 (full realistic spread,
	 * weighted toward positive like a real store).
	 *
	 * ## EXAMPLES
	 *
	 *     wp exmart seed-reviews --count=200
	 *     wp exmart seed-reviews --count=20 --products=123,456
	 *     wp exmart seed-reviews --count=50 --min-rating=3
	 *
	 * @when after_wp_load
	 */
	public function seed_reviews( $args, $assoc_args ) {
		$count      = isset( $assoc_args['count'] ) ? max( 1, (int) $assoc_args['count'] ) : 50;
		$min_rating = isset( $assoc_args['min-rating'] ) ? max( 1, min( 5, (int) $assoc_args['min-rating'] ) ) : 1;

		if ( ! empty( $assoc_args['products'] ) ) {
			$product_ids = array_filter( array_map( 'absint', explode( ',', $assoc_args['products'] ) ) );
		} else {
			$product_ids = get_posts( array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			) );
		}

		if ( empty( $product_ids ) ) {
			WP_CLI::error( 'No products found to attach reviews to.' );
		}

		$weights = array( 5 => 50, 4 => 25, 3 => 15, 2 => 7, 1 => 3 );
		$product_ids = array_values( $product_ids );

		$inserted = 0;
		$touched  = array();
		$progress = \WP_CLI\Utils\make_progress_bar( 'Seeding reviews', $count );

		for ( $i = 0; $i < $count; $i++ ) {
			$product_id = $product_ids[ array_rand( $product_ids ) ];
			$rating     = self::weighted_rating( $weights, $min_rating );
			$snippets   = isset( self::$snippets[ $rating ] ) ? self::$snippets[ $rating ] : self::$snippets[3];
			$content    = $snippets[ array_rand( $snippets ) ];
			$name       = self::$first_names[ array_rand( self::$first_names ) ] . ' ' . self::$last_initials[ array_rand( self::$last_initials ) ];
			$days_ago   = wp_rand( 0, 180 );
			$date       = gmdate( 'Y-m-d H:i:s', strtotime( "-{$days_ago} days" ) );

			$comment_id = wp_insert_comment( array(
				'comment_post_ID'      => $product_id,
				'comment_author'       => $name,
				'comment_author_email' => sanitize_title( $name ) . '+seed@example.com',
				'comment_content'      => $content,
				'comment_type'         => 'review',
				'comment_approved'     => 1,
				'comment_date'         => get_date_from_gmt( $date ),
				'comment_date_gmt'     => $date,
			) );

			if ( ! $comment_id ) {
				continue;
			}

			update_comment_meta( $comment_id, 'rating', $rating );
			update_comment_meta( $comment_id, self::SEED_META_KEY, 1 );

			$touched[ $product_id ] = true;
			$inserted++;
			$progress->tick();
		}
		$progress->finish();

		foreach ( array_keys( $touched ) as $pid ) {
			if ( class_exists( 'WC_Comments' ) ) {
				WC_Comments::clear_transients( $pid );
			}
		}

		WP_CLI::success( sprintf(
			'Inserted %d seeded reviews across %d products. Run `wp exmart remove-seeded-reviews` before the real import.',
			$inserted,
			count( $touched )
		) );
	}

	/**
	 * Delete every review `seed-reviews` generated — and only those; real
	 * or imported reviews have no `_exmart_seed_review` meta so they are
	 * never touched.
	 *
	 * ## EXAMPLES
	 *
	 *     wp exmart remove-seeded-reviews
	 *
	 * @when after_wp_load
	 */
	public function remove_seeded_reviews( $args, $assoc_args ) {
		$comments = get_comments( array(
			'meta_key'   => self::SEED_META_KEY,
			'meta_value' => 1,
			'type'       => 'review',
			'number'     => 0,
		) );

		if ( empty( $comments ) ) {
			WP_CLI::success( 'No seeded reviews found — nothing to remove.' );
			return;
		}

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

		WP_CLI::success( sprintf( 'Removed %d seeded reviews from %d products.', count( $comments ), count( $touched ) ) );
	}

	/**
	 * Pick a star rating >= $min_rating, weighted toward higher ratings.
	 */
	private static function weighted_rating( $weights, $min_rating ) {
		$pool = array();
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
}

WP_CLI::add_command( 'exmart', 'Exmart_Dev_CLI' );
