<?php
/**
 * ShareThis Reviews.
 *
 * @package ShareThisReviews
 */

namespace ShareThisReviews;

/**
 * Register Class
 *
 * @package ShareThisReviews
 */
class Reviews {

	/**
	 * Plugin instance.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Register instance.
	 *
	 * @var object
	 */
	public $register;

	/**
	 * Class constructor.
	 *
	 * @param object $plugin Plugin class.
	 * @param object $button_widget Button Widget class.
	 */
	public function __construct( $plugin, $register ) {
		$this->plugin          = $plugin;
		$this->current_reviews = $register->get_reviews();
		$this->hide_reviews    = ( 'on' !== get_post_meta( get_the_ID(), 'sharethis-hide-review-section', true ) );
		$this->register        = $register;
		$this->review_approval = $register->review_approval;
	}

	/**
	 * Display Ratings section at bottom of content.
	 *
	 * @filter the_content
	 *
	 * @param string $content The content.
	 */
	public function display_impression_section( $content ) {
		global $post;

		if ( ! isset( $post ) ) {
			return $content;
		}

		$impression_section  = $content;
		$impression_section .= $this->get_impression_display();

		return $impression_section;
	}

	/**
	 * Display Reviews section at bottom of content.
	 *
	 * @filter the_content
	 *
	 * @param string $content The content.
	 */
	public function display_review_section( $content ) {
		global $post;

		if ( ! isset( $post ) ) {
			return $content;
		}

		$review_section  = $content;
		$review_section .= $this->get_review_display();

		return $review_section;
	}

	/**
	 * Display Ratings section at bottom of content.
	 *
	 * @filter the_content
	 *
	 * @param string $content The content.
	 */
	public function display_rating_section( $content ) {
		global $post;

		if ( ! isset( $post ) ) {
			return $content;
		}

		$rating_section  = $content;
		$rating_section .= $this->get_rating_display();

		return $rating_section;
	}

	/**
	 * Helper function to pull specific type of review options.
	 *
	 * @param string $type The type of review to get option for.
	 * @param string $subtype The sub type of review option.
	 */
	private function get_info( $type, $subtype ) {
		$option = get_option( 'sharethisreviews_' . $type . '_section_' . $subtype );

		return $option;
	}

	/**
	 * AJAX call back function to add review to post.
	 *
	 * @action wp_ajax_add_review
	 * @action wp_ajax_nopriv_add_review
	 */
	public function add_review() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['postid'], $_POST['review'], $_POST['title'] ) || '' === $_POST['review'] ) { // WPCS: input var ok.
			wp_send_json_error( 'Add Review Failed.' );
		}

		$postid  = intval( $_POST['postid'] ); // WPCS: input var ok.
		$title   = sanitize_text_field( wp_unslash( $_POST['title'] ) ); // WPCS: input var ok.
		$name    = sanitize_text_field( wp_unslash( $_POST['name'] ) ); // WPCS: input var ok.
		$message = sanitize_text_field( wp_unslash( $_POST['review'] ) ); // WPCS: input var ok.
		$rating  = '' !== $_POST['rating'] ? (string) intval( $_POST['rating'] ) : ''; // WPCS: input var ok.
		$date    = date( 'Y-m-d' );

		if ( '' !== $rating ) {
			$review = array(
				'title'    => $title,
				'message'  => $message,
				'date'     => $date,
				'postid'   => (string) $postid,
				'rating'   => $rating,
				'name'     => $name,
				'approved' => 'false',
			);
		} else {
			$review = array(
				'title'    => $title,
				'message'  => $message,
				'date'     => $date,
				'postid'   => (string) $postid,
				'name'     => $name,
				'approved' => 'false',
			);
		}

		$current_reviews = get_post_meta( $postid, 'sharethisreview_review', true );

		if ( is_array( $current_reviews ) ) {
			array_push( $current_reviews, $review );
		} else {
			$current_reviews = array( $review );
		}

		$current_postids = get_option( 'sharethisreview_posts', true );

		if ( is_array( $current_postids ) && ! in_array( $postid, $current_postids, true ) ) {
			array_push( $current_postids, $postid );
		} elseif ( is_array( $current_postids ) && in_array( $postid, $current_postids, true ) ) {
			$current_postids = $current_postids;
		} else {
			$current_postids = array( $postid );
		}

		update_post_meta( $postid, 'sharethisreview_review', $current_reviews );
		update_option( 'sharethisreview_posts', $current_postids );
	}

	/**
	 * AJAX call back function to add rating to post.
	 *
	 * @action wp_ajax_add_rating
	 * @action wp_ajax_nopriv_add_rating
	 */
	public function add_rating() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['postid'], $_POST['rating'] ) || '' === $_POST['rating'] ) { // WPCS: input var ok.
			wp_send_json_error( 'Add Review Failed.' );
		}

		$postid = intval( $_POST['postid'] ); // WPCS: input var ok.
		$rate   = intval( $_POST['rating'] ); // WPCS: input var ok.
		$name   = sanitize_text_field( wp_unslash( $_POST['name'] ) );
		$date   = date( 'Y-m-d' );
		$rating = array(
			'date'   => $date,
			'name'   => $name,
			'rating' => $rate,
			'postid' => (string) $postid,
		);

		$current_ratings = get_post_meta( $postid, 'sharethisreview_rating', true );

		if ( is_array( $current_ratings ) ) {
			array_push( $current_ratings, $rating );
		} else {
			$current_ratings = array( $rating );
		}

		update_post_meta( $postid, 'sharethisreview_rating', $current_ratings );
	}

	/**
	 * AJAX call back function to add impression to post.
	 *
	 * @action wp_ajax_add_impression
	 * @action wp_ajax_nopriv_add_impression
	 */
	public function add_impression() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['postid'], $_POST['impression'] ) ) { // WPCS: input var ok.
			wp_send_json_error( 'Add Impression Failed.' );
		}

		$postid     = (string) intval( $_POST['postid'] ); // WPCS: input var ok.
		$imp        = intval( $_POST['impression'] ); // WPCS: input var ok.
		$date       = date( 'Y-m-d' );
		$impression = array(
			'date'       => $date,
			'impression' => $imp,
			'postid'     => $postid,
		);

		$current_impressions = get_post_meta( $postid, 'sharethisreview_impression', true );

		if ( is_array( $current_impressions ) ) {
			array_push( $current_impressions, $impression );
		} else {
			$current_impressions = array( $impression );
		}

		update_post_meta( $postid, 'sharethisreview_impression', $current_impressions );
	}

	/**
	 * AJAX call back function to approve review to post.
	 *
	 * @action wp_ajax_approve_review
	 */
	public function approve_review() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['postid'], $_POST['pos'] ) || '' === $_POST['postid'] ) { // WPCS: input var ok.
			wp_send_json_error( 'Add Review Failed.' );
		}

		$postid = intval( $_POST['postid'] ); // WPCS: input var ok.
		$pos    = intval( $_POST['pos'] ); // WPCS: input var ok.

		$current_reviews                     = get_post_meta( $postid, 'sharethisreview_review', true );
		$current_reviews[ $pos ]['approved'] = 'true';

		update_post_meta( $postid, 'sharethisreview_review', $current_reviews );
	}

	/**
	 * AJAX call back function to retract review to post.
	 *
	 * @action wp_ajax_retract_review
	 */
	public function retract_review() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['postid'], $_POST['pos'] ) || '' === $_POST['postid'] ) { // WPCS: input var ok.
			wp_send_json_error( 'Retract Review Failed.' );
		}

		$postid = intval( $_POST['postid'] ); // WPCS: input var ok.
		$pos    = intval( $_POST['pos'] ); // WPCS: input var ok.

		$current_reviews                     = get_post_meta( $postid, 'sharethisreview_review', true );
		$current_reviews[ $pos ]['approved'] = 'false';

		update_post_meta( $postid, 'sharethisreview_review', $current_reviews );
	}

	/**
	 * AJAX call back function to remove review from post.
	 *
	 * @action wp_ajax_remove_review
	 */
	public function remove_review() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['postid'], $_POST['pos'] ) || '' === $_POST['postid'] ) { // WPCS: input var ok.
			wp_send_json_error( 'Remove Review Failed.' );
		}

		$postid = intval( $_POST['postid'] ); // WPCS: input var ok.
		$pos    = intval( $_POST['pos'] ); // WPCS: input var ok.

		$current_reviews = get_post_meta( $postid, 'sharethisreview_review', true );

		// Remove review from array.
		unset( $current_reviews[ $pos ] );

		update_post_meta( $postid, 'sharethisreview_review', $current_reviews );
	}

	/**
	 * AJAX call back function to remove review from post.
	 *
	 * @action wp_ajax_remove_rating
	 */
	public function remove_rating() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['postid'], $_POST['pos'] ) || '' === $_POST['postid'] ) { // WPCS: input var ok.
			wp_send_json_error( 'Remove Rating Failed.' );
		}

		$postid = intval( $_POST['postid'] ); // WPCS: input var ok.
		$pos    = intval( $_POST['pos'] ); // WPCS: input var ok.

		$current_reviews = get_post_meta( $postid, 'sharethisreview_rating', true );

		// Remove review from array.
		unset( $current_reviews[ $pos ] );

		update_post_meta( $postid, 'sharethisreview_rating', $current_reviews );
	}

	/**
	 * Helper function to get average rating for display.
	 *
	 * @param integer $post The post id.
	 * @param string  $type The type of review.
	 */
	private function get_average_rating( $post, $type ) {
		$current_ratings = get_post_meta( $post, 'sharethisreview_' . $type, true );
		$overall_rating  = 0;
		$count           = 0;
		$seo             = '';

		if ( is_array( $current_ratings ) && array() !== $current_ratings ) {
			foreach ( $current_ratings as $rating ) {
				if ( isset( $rating['rating'] ) ) {
					$overall_rating += intval( $rating['rating'] );

					$count++;
				}
			}

			$overall_rating = (int) ceil( $overall_rating / $count );

			// drop review seo data on page.
			$seo = $this->get_seo_markup( get_the_title( $post ), intval( $overall_rating ) + 1, $count );
		}

		return $this->register->get_rating_icons( $overall_rating ) . $seo;
	}

	/**
	 * Helper to enqueue scripts as needed.
	 */
	public function enqueue_front_scripts() {
		wp_enqueue_style( "{$this->plugin->assets_prefix}-review" );
		wp_enqueue_script( "{$this->plugin->assets_prefix}-review" );
		wp_add_inline_script( "{$this->plugin->assets_prefix}-review", sprintf( 'Review.boot( %s );',
			wp_json_encode( array(
				'postid'   => get_the_id(),
				'approval' => $this->register->review_approval,
				'nonce'    => wp_create_nonce( $this->plugin->meta_prefix ),
			) )
		) );
	}

	/**
	 * Helper function to get display for impressions.
	 *
	 * @param boolean $shortcode Is it being called by shortcode.
	 */
	public function get_impression_display( $shortcode = false ) {
		global $post;

		if ( ! isset( $post ) ) {
			return;
		}

		$posttypes            = $this->get_info( 'impression', 'posttype' );
		$current_impressions  = get_post_meta( $post->ID, 'sharethisreview_impression', true );
		$overall_0_impression = 0;
		$overall_1_impression = 0;
		$show_impression      = (
			isset( $posttypes[ $post->post_type ] ) &&
			'on' === $posttypes[ $post->post_type ] &&
			$this->hide_reviews
		);
		$impression_section   = '';

		if ( $show_impression || $shortcode ) {
			if ( is_array( $current_impressions ) && [] !== $current_impressions ) {
				foreach ( $current_impressions as $impression ) {
					if ( isset( $impression['impression'] ) && 0 === $impression['impression'] ) {
						$overall_0_impression ++;
					} else {
						$overall_1_impression ++;
					}
				}
			}

			$count = [ $overall_0_impression, $overall_1_impression ];

			$impression_section .= $this->register->get_impression_icons( $count );

			$this->enqueue_front_scripts();
		}

		return $impression_section;
	}

	/**
	 * Helper function for rating display.
	 *
	 * @param boolean $shortcode Is shortcode calling or not.
	 */
	public function get_review_display( $shortcode = false, $short_rating = false ) {
		global $post, $current_user;

		$ctacopy  = get_option( 'sharethisreviews_review_section_ctacopy' );
		$ctacopy  = ! empty( $ctacopy ) ? $ctacopy : 'Add Review / Rating';
		$ctacolor = get_option( 'sharethisreviews_review_section_ctacolor' );
		$ctacolor = ! empty( $ctacolor ) ? 'style="background-color:' . $ctacolor . ';"' : '';

		if ( ! isset( $post ) ) {
			return;
		}

		$title               = $this->get_info( 'review', 'title' );
		$title               = ! empty( $title ) ? $title : '';
		$posttypes           = $this->get_info( 'review', 'posttype' );
		$ratingposttypes     = $this->get_info( 'rating', 'posttype' );
		$symbol              = ' ' . $this->get_info( 'rating', 'symbols' );
		$total               = ( 'on' === $this->get_info( 'rating', 'total' ) );
		$logged_in           = is_user_logged_in();
		$current_reviews     = $this->get_accepted_reviews( get_post_meta( $post->ID, 'sharethisreview_review', true ) );
		$count               = ! empty( $current_reviews ) && 0 < count( $current_reviews ) ? count( $current_reviews ) : 0;
		$account             = ( 'on' === $this->get_info( 'review', 'account' ) );
		$name                = $logged_in ? $current_user->display_name : '';
		$show_reviews        = ( ( ( isset( $posttypes[ $post->post_type ] ) && 'on' === $posttypes[ $post->post_type ] ) || $shortcode ) && ( ( ! isset( $ratingposttypes[ $post->post_type ] ) || 'on' !== $ratingposttypes[ $post->post_type ] ) ) && ! $short_rating ) && $this->hide_reviews;
		$show_reviews_rating = ( ( ( isset( $posttypes[ $post->post_type ] ) && 'on' === $posttypes[ $post->post_type ] ) || $shortcode ) && ( ( isset( $ratingposttypes[ $post->post_type ] ) && 'on' === $ratingposttypes[ $post->post_type ] ) || $short_rating ) ) && $this->hide_reviews;
		$show_top            = ( 'on' === $this->get_info( 'rating', 'top' ) );
		$avg_rating          = $this->get_average_rating( $post->ID, 'review' );
		$avg_rating_html     = 0 !== $count ? esc_html( 'Average User Rating(' . $count . '): ', 'sharethis-reviews' ) . $avg_rating : esc_html__( 'Be The First To Review!', 'sharethis-reviews' );
		$review_section      = '';

		if ( $show_reviews ) {
			// Review section html.
			$review_section .= '<div class="review-section-wrap">';
			$review_section .= '<div class="review-cta">';
			$review_section .= '<button ' . $ctacolor . ' id="open-review-form" type="button">' . esc_html( $ctacopy ) . '</button>';
			$review_section .= '</div>';
			$review_section .= '<div class="review-hidden-wrap">';
			$review_section .= '' !== $title ? '<h3 class="review-section-title">' . esc_html( $title ) . '</h3>' : '';

			if ( ! $account || $logged_in ) {
				$review_section .= '<label>' . esc_html__( 'Name:', 'sharethis-reviews' ) . '</label>';
				$review_section .= '<input type="text" id="name" placeholder="Name..." value="' . $name . '">';
				$review_section .= '<label>' . esc_html__( 'Title:', 'sharethis-reviews' ) . '</label>';
				$review_section .= '<input type="text" id="title" placeholder="Add Title...">';
				$review_section .= '<label>' . esc_html__( 'Write a review:', 'sharethis-reviews' ) . '</label>';
				$review_section .= '<textarea rows="5" placeholder="Add Review"></textarea>';
				$review_section .= '<button id="submit-user-review">Submit</button>';
				$review_section .= '</div>';
				$review_section .= '</div>';
			} else {
				$review_section .= '<h4>';
				$review_section .= esc_html__( 'You must be logged in to leave a review.', 'sharethis-reviews' );
				$review_section .= '</h4>';
			}

			$this->enqueue_front_scripts();
		}

		// If ratings are enabled for the same post type.
		if ( $show_reviews_rating ) {
			if ( ! $account || $logged_in ) {
				// Review section html.
				$review_section .= '<div class="review-section-wrap">';

				if ( $total ) {
					$review_section .= '<h2 class="average-rating-title">';
					$review_section .= $avg_rating_html;
					$review_section .= '</h2>';
					$review_section .= '<hr>';
				}

				$review_section .= '<div class="review-cta">';
				$review_section .= '<button ' . $ctacolor . ' id="open-review-form" type="button">' . esc_html( $ctacopy ) . '</button>';
				$review_section .= '</div>';
				$review_section .= '<div class="review-hidden-wrap' . $symbol . '">';
				$review_section .= '' !== $title ? '<h3 class="review-section-title">' . esc_html( $title ) . '</h3>' : '';
				$review_section .= '<label>' . esc_html__( 'Name:', 'sharethis-reviews' ) . '</label>';
				$review_section .= '<input type="text" id="name" placeholder="Name..." value="' . $name . '">';
				$review_section .= '<label>' . esc_html__( 'Title:', 'sharethis-reviews' ) . '</label>';
				$review_section .= '<input type="text" id="title" placeholder="Add Title...">';
				$review_section .= '<label>' . esc_html__( 'Rate:', 'sharethis-reviews' ) . '</label>';
				$review_section .= $this->register->get_rating_icons( 4, true, true );
				$review_section .= '<label>' . esc_html__( 'Write a review:', 'sharethis-reviews' ) . '</label>';
				$review_section .= '<textarea rows="5" placeholder="Add Review"></textarea>';
				$review_section .= '<button id="submit-user-review">Submit</button>';
				$review_section .= '</div>';
				$review_section .= '</div>';
			} else {
				$review_section .= '<h4>';
				$review_section .= esc_html__( 'You must be logged in to leave a review.', 'sharethis-reviews' );
				$review_section .= '</h4>';
			}

			$this->enqueue_front_scripts();
		}

		if ( ( $show_reviews_rating || $show_reviews ) && is_array( $current_reviews ) && array() !== $current_reviews ) :
			$review_section .= '<div class="review-list">';

			if ( $show_top ) {
				usort( $current_reviews, [ $this, 'sort_by_rating' ] );
			}

			foreach ( $current_reviews as $num => $review ) :
				$title   = isset( $review['title'] ) ? $review['title'] : '';
				$message = isset( $review['message'] ) ? $review['message'] : '';
				$date    = isset( $review['date'] ) ? $review['date'] : '';
				$rating  = isset( $review['rating'] ) ? $review['rating'] : '';
				$rname   = ! empty( $review['name'] ) ? $review['name'] : 'Anonymous';

				$review_section .= '<div class="review-item">';

				$review_section .= '<h4 class="review-title">';
				$review_section .= esc_html( $title );
				$review_section .= '</h4>';

				$review_section .= '<div class="review-message">';
				$review_section .= esc_html( $message );
				$review_section .= '</div>';

				if ( '' !== $name ) {
					$review_section .= '<div class="review-name">';
					$review_section .= esc_html__( 'Reviewed by: ', 'sharethis-reviews' ) . $rname;
					$review_section .= '</div>';
				}

				if ( '' !== $rating && $show_reviews_rating ) {
					$review_section .= '<div class="review-rating">';
					$review_section .= $this->register->get_rating_icons( $rating );
					$review_section .= '</div>';
				}
				$review_section .= '<div class="review-date">';
				$review_section .= esc_html( $date );
				$review_section .= '</div>';
				$review_section .= '</div>';
			endforeach;

			$review_section .= '</div>';

			$this->enqueue_front_scripts();
		endif;

		return $review_section;
	}

	/**
	 * Helper function to display rating section.
	 *
	 * @param boolean $shortcode Is it being called by a shortcode.
	 */
	public function get_rating_display( $shortcode = false ) {
		global $post, $current_user;

		if ( ! isset( $post ) ) {
			return;
		}

		$title           = $this->get_info( 'rating', 'title' );
		$title           = ! empty( $title ) ? $title : '';
		$posttypes       = $this->get_info( 'rating', 'posttype' );
		$symbol          = $this->get_info( 'rating', 'symbols' );
		$total           = ( 'on' === $this->get_info( 'rating', 'total' ) );
		$reviewposttype  = $this->get_info( 'review', 'posttype' );
		$logged_in       = is_user_logged_in();
		$account         = ( 'on' === $this->get_info( 'review', 'account' ) );
		$name            = $logged_in ? $current_user->display_name : '';
		$overall_rating  = $this->get_average_rating( $post->ID, 'rating' );
		$current_reviews = $this->get_accepted_reviews( get_post_meta( $post->ID, 'sharethisreview_review', true ) );
		$count           = ! empty( $current_ratings ) ? count( $current_ratings ) : 0;
		$avg_rating      = $this->get_average_rating( $post->ID, 'rating' );
		$avg_rating_html = 0 !== $count ? esc_html( 'Average User Rating(' . $count . '): ', 'sharethis-reviews' ) . $avg_rating : esc_html__( 'Be The First To Review!', 'sharethis-reviews' );
		$show_top        = ( 'on' === $this->get_info( 'rating', 'top' ) );
		$show_rating     = (
			isset( $posttypes[ $post->post_type ] ) &&
			'on' === $posttypes[ $post->post_type ] &&
			( ! isset( $reviewposttype[ $post->post_type ] ) ||
			'on' !== $reviewposttype[ $post->post_type ] ) &&
			$this->hide_reviews
		);
		$rating_section  = '';

		// If ratings are enabled for the same post type.
		if ( $show_rating || $shortcode ) {
			if ( ! $account || $logged_in ) {
				// Review section html.
				$rating_section .= '<div class="review-section-wrap">';
				$rating_section .= '<div class="' . $symbol . '">';

				if ( $total ) {
					$rating_section .= '<h2 class="average-review-title">';
					$rating_section .= $avg_rating_html;
					$rating_section .= '</h2>';
					$rating_section .= '<hr>';
				}

				$rating_section .= '' !== $title ? '<h3 class="review-section-title">' . esc_html( $title ) . '</h3>' : '';
				$rating_section .= '<label>' . esc_html__( 'Name:', 'sharethis-reviews' ) . '</label>';
				$rating_section .= '<input type="text" id="name" placeholder="Name..." value="' . $name . '">';
				$rating_section .= '<label>' . esc_html__( 'Rate:', 'sharethis-reviews' ) . '</label>';
				$rating_section .= $this->register->get_rating_icons( 4, true, true );
				$rating_section .= '<button id="submit-user-rating">Submit</button>';
				$rating_section .= '</div>';
			} else {
				$rating_section .= '<h4>';
				$rating_section .= esc_html__( 'You must be logged in to leave a rating.', 'sharethis-reviews' );
				$rating_section .= '</h4>';
			}

			$this->enqueue_front_scripts();
		}

		if ( ( $show_rating ) && is_array( $current_ratings ) && array() !== $current_ratings ) :
			$rating_section .= '<div class="review-list">';

			if ( $show_top ) {
				usort( $current_ratings, [ $this, 'sort_by_rating' ] );
			}

			foreach ( $current_ratings as $num => $rating ) :
				$rating_num = isset( $rating['rating'] ) ? $rating['rating'] : '';
				$rname      = ! empty( $rating['name'] ) ? $rating['name'] : 'Anonymous';
				$date       = isset( $rating['date'] ) ? $rating['date'] : '';

				$rating_section .= '<div class="review-item rating-rating">';

				if ( '' !== $name ) {
					$rating_section .= '<div class="review-name">';
					$rating_section .= esc_html__( 'Rating by: ', 'sharethis-reviews' ) . $rname;
					$rating_section .= '</div>';
				}

				if ( '' !== $rating_num ) {
					$rating_section .= '<div class="review-rating">';
					$rating_section .= $this->register->get_rating_icons( $rating_num );
					$rating_section .= '</div>';
				}
				$rating_section .= '<div class="review-date">';
				$rating_section .= esc_html( $date );
				$rating_section .= '</div>';
				$rating_section .= '</div>';
			endforeach;

			$rating_section .= '</div>';
			$rating_section .= '</div>';

			$this->enqueue_front_scripts();
		endif;

		return $rating_section;
	}

	/**
	 * Shortcode for reviews section.
	 *
	 * @shortcode sharethis-reviews
	 *
	 * @param array $atts The shortcode attributes
	 */
	public function reviews_shortcode( $att ) {
		$ratings = isset( $att['ratings'] ) ? ( 'true' === $att['ratings'] ) : false;

		return $this->get_review_display( true, $ratings );
	}

	/**
	 * Shortcode for ratings section.
	 *
	 * @shortcode sharethis-ratings
	 */
	public function ratings_shortcode() {
		return $this->get_rating_display( true );
	}

	/**
	 * Shortcode for impressions section.
	 *
	 * @shortcode sharethis-impressions
	 */
	public function impressions_shortcode() {
		return $this->get_impression_display( true );
	}

	/**
	 * Helper function to echo seo markup on to page.
	 *
	 * @param string  $title The post title.
	 * @param integer $average The average rating.
	 * @param integer $count The amount of reviews.
	 */
	public function get_seo_markup( $title, $average, $count ) {
		global $post;

		if ( is_admin() ) {
			return;
		}

		$html  = '<div class="google-review-schema" itemscope itemtype="http://schema.org/Thing">';
		$html .= '<h2 itemprop="name"> ' . $title . ' </h2>';
		$html .= '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';
		$html .= '<div>Rating: ';
		$html .= '<span itemprop="ratingValue">' . $average . '</span> out of ';
		$html .= '<span itemprop="bestRating">5</span> with ';
		$html .= '<span itemprop="ratingCount">' . $count . '</span> ratings';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Helper function to sort by top rated.
	 *
	 * @param array $ratings The ratings to sort.
	 */
	public function sort_by_rating( $a, $b ) {
		return strcmp( $b['rating'], $a['rating'] );
	}

	/**
	 * Helper function to return only accepted results.
	 *
	 * @param array $reviews The current reviews.
	 */
	private function get_accepted_reviews( $reviews ) {
		$final_reviews = [];

		if ( is_array( $reviews ) ) {
			foreach ( $reviews as $review ) {
				$approved = isset( $review['approved'] ) && 'true' === $review['approved'] ? true : false;

				if ( ( $approved && $this->review_approval ) || false === $this->review_approval ) {
					$final_reviews[] = $review;
				}
			}

			// Remove identical reviews to avoid user multi reviews.
			$serialized    = array_map( 'serialize', $final_reviews );
			$unique        = array_unique( $serialized );
			$final_reviews = array_intersect_key( $final_reviews, $unique );
		}

		return $final_reviews;
	}
}
