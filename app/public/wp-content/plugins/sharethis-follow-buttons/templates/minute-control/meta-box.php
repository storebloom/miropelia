<?php
/**
 * Meta Box Template
 *
 * The template wrapper for post/page meta box.
 *
 * @package ShareThisFollowButtons
 */

?>
<div id="sharethis-follow-meta-box">
	<div id="inline-follow" class="button-setting-wrap">
		<div class="button-check-wrap">
			<input class="top" type="checkbox" id="sharethis-follow-top-post" <?php echo checked( 'true', $this->is_box_checked( '_top' ) ); ?>>

			<label for="sharethis-top-post">
				<?php
				// translators: The post type.
				printf( esc_html__( 'Include at top of %1$s content', 'sharethis-follow-buttons' ), esc_html( $post_type ) );
				?>
			</label>
		</div>
		<div class="button-check-wrap">
			<input class="bottom" type="checkbox" id="sharethis-follow-bottom-post" <?php echo checked( 'true', $this->is_box_checked( '_bottom' ) ); ?>>

			<label for="sharethis-bottom-post">
				<?php
				// translators: The post type.
				printf( esc_html__( 'Include at bottom of %1$s content', 'sharethis-follow-buttons' ), esc_html( $post_type ) );
				?>
			</label>
		</div>
		<input type="text" class="sharethis-shortcode" readonly value="[sharethis-follow-buttons]">

		<span class="under-message"><?php esc_html_e( 'Follow button shortcode.', 'sharethis-follow-buttons' ); ?></span>
	</div>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=sharethis-share-buttons' ) ); ?>">
		<?php esc_html_e( 'Update your default settings', 'sharethis-follow-buttons' ); ?>
	</a>
</div>
