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

$points = get_user_meta(get_current_user_id(), 'explore_points', true);

get_header();
?>
<main id="primary" class="site-main">
	<div class="explore-overlay">
		<div class="you-died-message">
			<h2><?php esc_html_e('Whoops. I don\'t have a boat. Guess I\'ll try again!'); ?></h2>
		</div>
		<div class="greeting-message">
			<h1>
				<?php esc_html_e('Welcome to the Orbem explore page.', 'miropelia'); ?>
			</h1>
			<?php if (is_user_logged_in()) : ?>
				<p>
					<?php esc_html_e('Go ahead and choose your Orbem avatar.', 'miropelia'); ?>
				</p>
				<div class="character-choice">
					<?php foreach ($characters as $name => $character) : ?>
						<div class="character-item">
							<img data-points="<?php echo isset($points[$character]['points']) ? esc_attr($points[$character]['points']) : ''; ?>" data-character="<?php echo esc_attr($character); ?>" src="<?php echo get_template_directory_uri() . '/assets/src/images/' . $character . '-avatar.png'; ?>" />
							<span><?php echo esc_html($name); ?></span>
							<small>
								<?php echo isset($points[$character]['points']) ?
                                    esc_html__('points: ', 'miropelia') .
                                    esc_attr($points[$character]['points']) :
									''; ?>
							</small>
						</div>
					<?php endforeach; ?>
				</div>

				<button type="button" id="engage-explore">
					<?php esc_html_e('Start Exploring!', 'miropelia'); ?>
				</button>
			<?php else : ?>
				<p>
					<?php
					echo esc_html__('Please login to gain the full experience. Enjoy ', 'miropelia') .
					esc_html__('collecting achievements and points for all the Orbem character', 'miropelia') .
					esc_html__(' choices and fully emersing in the universe of Orbem!', 'miropelia');
					?>
				</p>
			<?php endif; ?>
		</div>
	</div>
	<div class="container">
		<a id="leave-map" href="/explore"><?php esc_html_e('Leave Map', 'miropelia'); ?></a>
		<div id="explore-points">
			<span class="point-amount">0</span> / 10000
		</div>
		<div id="sound-control">
		</div>
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
		<div style="top: 3600px; left: 1842px" id="map-character">
			<img data-character="" id="map-character-icon" src="" />
		</div>
		<?php the_content(); ?>
	</div>
	<?php include get_template_directory() . '/page-templates/components/sounds.php'; ?>
</main>
<?php
get_footer();
