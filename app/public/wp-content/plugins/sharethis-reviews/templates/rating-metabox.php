<?php
/**
 * Rating metabox template.
 */

?>
<div class="rating-metabox">
	<div class="hide-reviews">
		<label>
			<?php echo esc_html__( 'Hide review/rating/impression section on this post?', 'sharethis-reviews' ); ?>
			<input type="checkbox" value="on" name="sharethis-hide-review-section" <?php echo checked( 'on', $hide ); ?>>
		</label>
	</div>
	<?php if ( is_array( $current_ratings ) && [] !== $current_ratings ) : ?>
		<h4><?php echo esc_html__( 'Ratings', 'sharethis-reviews' ); ?></h4>
		<?php
		foreach ( $current_ratings as $num => $rating ) :
			$name         = isset( $rating['name'] ) ? $rating['name'] : '';
			$rating_score = isset( $rating['rating'] ) ? $this->get_rating_icons( $rating['rating'], true ) : '';
			$date         = isset( $rating['date'] ) ? $rating['date'] : '';
			?>
			<div class="rating-item">
				<div class="review-name review-info">
					<?php echo esc_html( $name ); ?>
				</div>

				<div class="review-rating review-info">
					<?php echo $rating_score; // WPCS: XSS ok. ?>
				</div>

				<div class="review-date review-info">
					<?php echo esc_html( $date ); ?>
				</div>

				<button data-id="<?php echo esc_attr( get_the_ID() ); ?>" data-pos="<?php echo esc_attr( $num ); ?>" class="remove-rating">
					<?php echo esc_html__( 'X', 'sharethis-reviews' ); ?>
				</button>
			</div>
			<?php
		endforeach;
	endif;
?>
</div>
