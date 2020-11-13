<?php
/**
 * Check Field template
 *
 * The template wrapper for the check field type settings.
 *
 * @package ShareThisReviews
 */

$option_value = get_option( 'sharethisreviews_' . $section . '_' . $name, true );

$label = 'approval' === $name ? 'Permissions:' : '';
$label = 'total' === $name ? 'Total:' : $label;
$label = 'negative' === $name ? 'Remove Negative Symbols:' : $label;
?>
<?php if ( '' !== $label ) : ?>
<label class="st-menu-desc">
	<?php echo esc_html( $label ); ?>
</label>
<?php endif; ?>

<div class="<?php echo esc_attr( 'sharethisreviews_' . $section . '_' . $name ); ?>">
	<input type="checkbox" id="sharethisreviews_<?php echo esc_attr( $section . '_' . $name ); ?>" name="sharethisreviews_<?php echo esc_attr( $section . '_' . $name ); ?>" <?php echo esc_attr( checked( 'on', $option_value, false ) ); ?>>
	<label for="sharethisreviews_<?php echo esc_attr( $section . '_' . $name ); ?>" class="st-label st-check-field">
		<?php echo wp_kses_post( $description ); ?>
	</label>
</div>
