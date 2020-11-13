<?php
/**
 * Menu template.
 */

?>

<?php settings_errors(); ?>

<div id="review-menu-wrap">
	<h1><?php echo esc_html__( 'ShareThis Reviews Settings', 'sharethis-reviews' ); ?></h1>

	<ul class="str-tab-wrap">
		<li class="active-tab" data-tab="reviews-wrap"><?php echo esc_html__( 'Reviews', 'sharethis-reviews' ); ?></li>
		<li data-tab="ratings-wrap"><?php echo esc_html__( 'Ratings', 'sharethis-reviews' ); ?></li>
		<li data-tab="impressions-wrap"><?php echo esc_html__( 'Impressions', 'sharethis-reviews' ); ?></li>
	</ul>
	<form action="options.php" method="post">
		<?php
		settings_fields( $this->menu_slug . '-general' );
		do_settings_sections( $this->menu_slug . '-general' );
		submit_button( esc_html__( 'Update', 'sharethis-reviews' ) );
		?>
	</form>
</div>
