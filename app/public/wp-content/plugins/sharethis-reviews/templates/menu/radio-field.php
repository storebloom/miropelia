<?php
/**
 * Radio template
 *
 * The template wrapper for the radio field type settings.
 *
 * @package ShareThisReviews
 */

?>
<label class="st-menu-desc">
	<?php echo esc_html( ucfirst( $radio_name ) ) . ':'; ?>
</label>

<div class="sub-label">
	<?php echo esc_html__( 'Choose which type of symbol you want to use:', 'sharethis-reviews' ); ?>
</div>
<div class="symbol-radio-wrap">
	<?php
	foreach ( $radios as $num => $radio ) :
		$symbols      = $this->get_symbol_svg( $section, $radio );
		$option_value = get_option( 'sharethisreviews_' . $section . '_' . $radio_name, true );
		$symbols      = isset( $symbols[0] ) && 'impression_section' !== $section ? end( $symbols ) : $symbols[0];
		?>

		<div class="symbol-radio-item">
			<input type="radio" id="sharethisreviews_<?php echo esc_attr( $section . '_' . $num . '_' . $radio_name ); ?>" name="sharethisreviews_<?php echo esc_attr( $section . '_' . $radio_name ); ?>" value="<?php echo esc_attr( $radio ); ?>" <?php echo esc_attr( checked( $radio, $option_value, false ) ); ?>>
			<label for="sharethisreviews_<?php echo esc_attr( $section . '_' . $num . '_' . $radio_name ); ?>">
				<div class="symbol-icon-wrap">
					<?php include $this->plugin->dir_path . 'assets/' . $symbols; ?>
				</div>
			</label>
		</div>

	<?php endforeach; ?>
</div>
