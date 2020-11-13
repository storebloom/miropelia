<?php
/**
 * Color Picker template
 *
 * The template wrapper for the color picker type settings.
 *
 * @package ShareThisReviews
 */

$option_value = get_option( 'sharethisreviews_' . $section . '_' . $name );
$option_value = ! empty( $option_value ) ? $option_value : '';
?>
<div class="<?php echo esc_attr( 'sharethisreviews_' . $section . '_' . $name ); ?>">
	<label class="st-text-field">
		<?php echo wp_kses_post( $description ); ?>
	</label>
	<input type="text" id="sharethisreviews_<?php echo esc_attr( $section . '_' . $name ); ?>" name="sharethisreviews_<?php echo esc_attr( $section . '_' . $name ); ?>" value="<?php echo esc_attr( $option_value ); ?>" >
</div>
