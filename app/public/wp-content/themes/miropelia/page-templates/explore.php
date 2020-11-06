<?php
/**
 * Template Name: Explore
 * Register form template.
 */

$characters = [
	'Sionnah' => 'sionnah',
	'Graeme' => 'graeme',
	'Corinder' => 'corinder',
	'Koromere' => 'koromere'
];

get_header();
?>
<main id="primary" class="site-main">
	<div class="explore-overlay">
		<div class="greeting-message">
			<h1>
				<?php esc_html_e('Welcome to the Orbem explore page.', 'miropelia'); ?>
			</h1>
			<p>
				<?php echo esc_html__('Go ahead and choose your Orbem avatar.', 'miropelia'); ?>
			</p>
			<div class="character-choice">
				<?php foreach ($characters as $name => $character) : ?>
					<div class="character-item">
						<img src="<?php echo get_template_directory_uri() . '/assets/src/images/' . $character . '-avatar.png'; ?>" />
						<span><?php echo esc_html($name); ?></span>
					</div>
				<?php endforeach; ?>
			</div>

			<button type="button" id="engage-explore">
				<?php esc_html_e('Start Exploring!', 'miropelia'); ?>
			</button>
		</div>
	</div>
	<div class="container">
		<a id="leave-map" href="/explore"><?php esc_html_e('Leave Map', 'miropelia'); ?></a>
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
		<span id="key-guide" href="/explore">
			<img src="<?php echo get_template_directory_uri() . '/assets/src/images/keys.png'; ?>" />
		</span>
		<div style="top: 1400px; left: 2000px" id="map-character">
			<span id="character-bubble"></span>
			<img id="map-character-icon" src="" />
		</div>
		<?php the_content(); ?>
	</div>
</main>
<?php
get_footer();
