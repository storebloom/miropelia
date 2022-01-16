<?php
/**
 * Enable Button Template
 *
 * The template wrapper for the enable button settings.
 *
 * @package ShareThisFollowButtons
 */

$option_value = get_option( 'sharethis_inline-follow', true );
?>
<div id="inline-follow" class="enable-buttons">
	<label class="share-on">
		<input type="radio" id="sharethis_inline-follow_on" name="sharethis_inline-follow" value="true" <?php echo esc_attr( checked( 'true', $option_value, false ) ); ?>>
		<div class="label-text"><?php esc_html_e( 'On', 'sharethis-follow-buttons' ); ?></div>
	</label>
	<label class="share-off">
		<input type="radio" id="sharethis_inline-follow_off" name="sharethis_inline-follow" value="false" <?php echo esc_attr( checked( 'false', $option_value, false ) ); ?>>
		<div class="label-text"><?php esc_html_e( 'Off', 'sharethis-follow-buttons' ); ?></div>
	</label>
</div>
