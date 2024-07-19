<?php
/**
 * Template Name: Explore
 * Register form template.
 */

use Miropelia\Explore;

$userid = get_current_user_id();
$points = get_user_meta($userid, 'explore_points', true);
$weapon_haul = get_user_meta($userid, 'explore_weapons', true);
$equipped_weapon = Explore::getCurrentWeapon($weapon_haul['equipped'] ?? 'fists');
$location = get_user_meta($userid, 'current_location', true);
$location = false === empty($location) ? $location : 'foresight';
$coordinates = get_user_meta($userid, 'current_coordinates', true);
$back = false === empty($coordinates) ? ' Back' : '';
$explore_area = get_posts(['post_type' => 'explore-area', 'name' => $location]);
$is_area_cutscene = get_post_meta($explore_area[0]->ID, 'explore-is-cutscene', true);
$explore_area_map = get_the_post_thumbnail_url($explore_area[0]->ID);
$explore_points = Explore::getExplorePoints($location);
$explore_cutscenes = Explore::getExploreCutscenes($location);
$explore_abilities = Explore::getExploreAbilities();
$rst = 'true' === filter_input( INPUT_GET, 'rst', FILTER_UNSAFE_RAW) ? ' reset' :'';
$health = true === isset($points['health']['points']) ? $points['health']['points'] : 100;
$mana = true === isset($points['mana']['points']) ? $points['mana']['points'] : 100;
$point = true === isset($points['point']['points']) ? $points['point']['points'] : 0;
$point_widths = Explore::getCurrentPointWidth();
$current_level = Explore::getCurrentLevel();
$max_points = Explore::getLevelMap();

wp_enqueue_script('explore');

get_header();
?>
<main id="primary" class="site-main<?php echo esc_attr($rst); ?>">
	<div class="explore-overlay engage" style="background: white;height: 100svh;left: 0;position: fixed;top: 0;width: 100%; z-index: 2;">
		<div class="greeting-message engage">
			<h1>
				<?php echo 'Welcome' . esc_html($back) . ' to Orbem Explore!'; ?>
			</h1>
			<?php if (is_user_logged_in()) : ?>
				<div class="character-choice">
                    <div class="character-item">
                        <img src="<?php echo get_template_directory_uri() . '/assets/src/images/graeme-avatar.gif'; ?>" />
                        <br>
                        <small>
                            <?php echo esc_html__('Current lvl: ', 'miropelia') . esc_attr($current_level); ?>
                        </small>
                    </div>
				</div>

                <div class="greeting-buttons">
                    <button type="button" class="engage" id="engage-explore">
                        <?php echo false === empty($coordinates) ? esc_html__('Continue', 'miropelia') : esc_html__('Start Game', 'miropelia'); ?>
                    </button>
                    <?php if ( false === empty($coordinates) ) : ?>
                        <button type="button" class="engage" id="new-explore">
                            <?php esc_html_e('New Game', 'miropelia'); ?>
                        </button>
                    <?php endif; ?>
                </div>
			<?php else : ?>
				<p>
					<?php
					echo esc_html__('Please login to gain the full experience. Enjoy ', 'miropelia') .
					esc_html__('collecting achievements and points as well as saving your progress.', 'miropelia');
					?>
					<br>
                    <div class="login-form">
                        <?php echo wp_login_form(); ?>
                    </div>
                    <div class="register-form" style="display: none;">
                        <?php echo do_shortcode('[register-form explore="true"]'); ?>
                    </div>
				</p>
				<p id="create-account">
					<?php esc_html_e('Create Account', 'miropelia'); ?>
				</p>
                <p id="login-account" style="display: none;">
                    <?php esc_html_e('Already have an account', 'miropelia'); ?>
                </p>
			<?php endif; ?>
		</div>
	</div>
	<div class="container <?php echo esc_attr($location); ?>" style="background: url(<?php echo esc_url($explore_area_map); ?>) no-repeat left top; background-size: cover;">
		<div id="explore-points">
            <div class="health-amount point-bar" data-type="health" data-amount="<?php echo esc_attr($health + ($point_widths['health'] - 100)); ?>" style="width: <?php echo isset($point_widths['health']) ? esc_attr($point_widths['health']) : 100; ?>px;"><div class="gauge"></div></div>
            <div class="mana-amount point-bar" data-type="mana" data-amount="<?php echo esc_attr($mana + ($point_widths['mana'] - 100)); ?>" style="width: <?php echo isset($point_widths['mana']) ? esc_attr($point_widths['mana']) : 100; ?>px;"><div class="gauge"></div></div>
            <div class="power-amount point-bar" data-type="power" data-amount="100" style="width: <?php echo isset($point_widths['power']) ? esc_attr($point_widths['power']) : 100; ?>px;"><div class="gauge"></div></div>
            <div class="point-amount point-bar" data-type="point" data-amount="<?php echo esc_attr($point); ?>" style="width: <?php echo isset($point_widths['point']) ? esc_attr($point_widths['point']) : 100; ?>px;">
                <div class="gauge"></div>
            </div>
            <div class="point-info-wrap">
                <span class="current-level">lvl. <?php echo esc_html($current_level); ?></span>
                <span class="current-points">
                    <span class="my-points"><?php echo esc_html($point);?></span>/<span class="next-level-points"><?php echo esc_html($max_points[$current_level]); ?></span>
            </div>
		</div>
        <?php if ( is_user_logged_in() ) : ?>
            <div id="settings">
                <div class="setting-content">
                    <?php include get_template_directory() . '/page-templates/components/explore-settings.php'; ?>
                </div>
            </div>
            <div id="missions">
                <div class="missions-content">
                    <?php include get_template_directory() . '/page-templates/components/explore-missions.php'; ?>
                </div>
            </div>
            <div id="storage">
                <div class="storage-content">
                    <?php include get_template_directory() . '/page-templates/components/explore-storage.php'; ?>
                </div>
            </div>
            <div id="weapon">
                <div class="weapon-content">
                    <img src="<?php echo esc_url(get_the_post_thumbnail_url($equipped_weapon->ID)); ?>" width="60px" height="60px" />
                </div>
            </div>
            <div id="magic">
                <div class="magic-content">
                    <?php include get_template_directory() . '/page-templates/components/explore-magic.php'; ?>
                </div>
            </div>
        <?php endif; ?>
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
        <div style="top: <?php echo false === empty($coordinates['top']) ? esc_attr($coordinates['top']) : 3518; ?>px; left: <?php echo false === empty($coordinates['left']) ? esc_attr($coordinates['left']) : 1942; ?>px" class="down-dir" id="map-character">
			<img id="map-character-icon" src="<?php echo get_template_directory_uri() . '/assets/src/images/graeme-avatar.gif'; ?>" />
		</div>
        <div style="top: <?php echo false === empty($coordinates['top']) ? esc_attr( intval($coordinates['top']) + 500) : 4018; ?>px; left: <?php echo false === empty($coordinates['left']) ? esc_attr(intval($coordinates['left'] + 500)) : 2442; ?>px" class="map-weapon" data-direction="down" data-strength="<?php echo intval(get_post_meta($equipped_weapon->ID, 'value', true)); ?>">
            <img src="<?php echo get_the_post_thumbnail_url($equipped_weapon); ?>"
                 width="<?php echo intval(get_post_meta($equipped_weapon->ID, 'explore-width', true)); ?>px"
                 height="<?php echo intval(get_post_meta($equipped_weapon->ID, 'explore-height', true)); ?>px"
            />
        </div>
		<div class="default-map" data-iscutscene="<?php echo esc_attr($is_area_cutscene); ?>">
            <?php echo Explore::getMapSVG($explore_area[0]); ?>
			<?php echo html_entity_decode(Explore::getMapItemHTML($explore_points, get_current_user_id())); ?>
            <?php echo html_entity_decode(Explore::getMapCutsceneHTML($explore_cutscenes, $explore_area[0]->post_name)); ?>
            <?php echo html_entity_decode(Explore::getMapAbilitiesHTML($explore_abilities)); ?>
		</div>
	</div>
	<?php include get_template_directory() . '/page-templates/components/sounds.php'; ?>
    <div class="loading-screen">
        LOADING...
    </div>
</main>
<?php
get_footer();
