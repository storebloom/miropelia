<?php
/**
 * Template Name: Explore
 * Register form template.
 */
get_header();

$char_choice = get_user_meta(get_current_user_id(), 'chosen-map-charater');
?>
<main id="primary" class="site-main">
	<div class="explore-overlay">
		<button type="button" id="engage-explore">Start Exploring!</button>
	</div>
	<div class="container">
		<a id="leave-map" href="/explore">Leave Map</a>
		<div class="touch-buttons">
			<span class="top-left">
			</span>
			<span class="top-middle">
			</span>
			<span class="top-right">
			</span>
			<span class="middle-left">
			</span>
			<span class="middle-middle">
			</span>
			<span class="middle-right">
			</span>
			<span class="bottom-left">
			</span>
			<span class="bottom-middle">
			</span>
			<span class="bottom-right">
			</span>
		</div>
		<div style="top: 1400px; left: 2000px" id="map-character">
			<img src="<?php echo get_template_directory_uri() . '/assets/src/images/temp-char.png'; ?>" />
		</div>
		<?php the_content(); ?>
	</div>
</main>
<?php
get_footer();
