<?php
/**
 * Multi Check template
 *
 * The template wrapper for the multi check field type settings.
 *
 * @package ShareThisReviews
 */

?>
<label class="st-menu-desc">
<?php echo wp_kses_post( $description ); ?>
</label>

<?php
foreach ( $checks as $check ) :
	$option_value = get_option( 'sharethisreviews_' . $section . '_posttype', true );
	$option_value = ! empty( $option_value[ $check ] ) ? $option_value[ $check ] : '';
	?>
<div class="multi-check-item" id="<?php echo esc_attr( 'sharethisreviews_' . $section . '_posttype_' . $check ); ?>">
	<input type="checkbox" id="sharethisreviews_<?php echo esc_attr( $section . '_' . $check ); ?>" name="sharethisreviews_<?php echo esc_attr( $section . '_posttype[' . $check . ']' ); ?>" <?php echo esc_attr( checked( 'on', $option_value, false ) ); ?>>
	<label for="sharethisreviews_<?php echo esc_attr( $section . '_' . $check ); ?>" class="st-label st-multi-check-field">
		<?php echo esc_html( ucfirst( $check ) ); ?>
	</label>
</div>
<?php endforeach; ?>
