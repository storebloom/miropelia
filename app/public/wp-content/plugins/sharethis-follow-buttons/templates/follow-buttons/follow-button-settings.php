<?php
/**
 * Follow Button Settings Template
 *
 * The template wrapper for the follow buttons settings page.
 *
 * @package ShareThisFollowButtons
 */

?>
<hr class="wp-header-end" style="display:none;">
<div class="wrap sharethis-wrap">
	<?php echo wp_kses_post( $description ); ?>

	<form action="options.php" method="post">
		<?php
		settings_fields( $this->menu_slug . '-follow-buttons' );
		do_settings_sections( $this->menu_slug . '-follow-buttons' );
		submit_button( esc_html__( 'Update', 'sharethis-follow-buttons' ) );
		?>
	</form>
</div>
