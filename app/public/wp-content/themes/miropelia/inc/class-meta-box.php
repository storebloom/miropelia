<?php
/**
 * Minute Control.
 *
 * @package Miropelia
 */

namespace Miropelia;

/**
 * Minute Control Class
 *
 * @package Miropelia
 */
class Meta_Box {


	/**
	 * Plugin instance.
	 *
	 * @var object
	 */
	public $theme;

	/**
	 * Class constructor.
	 *
	 * @param object $theme Plugin class.
	 */
	public function __construct( $theme ) {
		$this->theme = $theme;
	}

	/**
	 * Register the new share buttons metabox.
	 *
	 * @action add_meta_boxes
	 */
	public function explore_metabox() {
		// Get all post types available.
		$post_types = ['explore-point', 'explore-area', 'explore-character', 'explore-enemy', 'explore-weapon', 'explore-magic', 'explore-cutscene', 'explore-mission', 'explore-sign'];

		// Add the Explore Point meta box to editor pages.
		add_meta_box( 'explore-point', esc_html__( 'Explore Point Position', 'miropelia' ), [$this, 'explore_point_box'], $post_types, 'side', 'high' );

        // Add the Share Buttons meta box to editor pages.
        add_meta_box( 'explore-enemy', esc_html__( 'Explore Enemy Stuff', 'miropelia' ), [$this, 'explore_enemy_box'], ['explore-enemy'], 'side', 'high' );
	}

	/**
	 * Call back function for the share buttons metabox.
	 */
	public function explore_point_box() {
        global $post;

		// Get all needed options for meta boxes.
		$top = get_post_meta($post->ID, 'explore-top', true);
		$left = get_post_meta($post->ID, 'explore-left', true);
		$height = get_post_meta($post->ID, 'explore-height', true);
		$width = get_post_meta($post->ID, 'explore-width', true);
        $start_top = get_post_meta($post->ID, 'explore-start-top', true);
        $start_left = get_post_meta($post->ID, 'explore-start-left', true);
        $music = get_post_meta($post->ID, 'explore-music', true);
        $map = get_post_meta($post->ID, 'explore-map-svg', true);
        $value = get_post_meta($post->ID, 'value', true);
        $unlock_level = get_post_meta($post->ID, 'explore-unlock-level', true);
        $interaction_type = get_post_meta($post->ID, 'explore-interaction-type', true);
        $drag_dest = get_post_meta($post->ID, 'explore-drag-dest', true);
        var_dump($drag_dest);
        $drag_dest = false === empty($drag_dest['explore-drag-dest']) ? $drag_dest['explore-drag-dest'] : [
            'top' => 0,
            'left' => 0,
            'height' => 0,
            'width' => 0,
            'image' => '',
            'mission' => '',
        ];
        $walking_paths = get_post_meta($post->ID, 'explore-path', true);
        $walking_paths = false === empty($walking_paths['explore-path']) ? $walking_paths['explore-path'] : [['top' => 0, 'left' => 0]];
        $walking_speed = get_post_meta($post->ID, 'explore-speed', true);
        $repeat = get_post_meta($post->ID, 'explore-repeat', true);
        $path_trigger = get_post_meta($post->ID, 'explore-path-trigger', true);
        $path_trigger = false === empty($path_trigger['explore-path-trigger']) ? $path_trigger['explore-path-trigger'] : [
            'top' => 0,
            'left' => 0,
            'height' => 0,
            'width' => 0,
            'point' => '',
            'cutscene' => ''
        ];
        $cutscene_trigger = get_post_meta($post->ID, 'explore-cutscene-trigger', true);
        $cutscene_trigger = false === empty($cutscene_trigger['explore-cutscene-trigger']) ? $cutscene_trigger['explore-cutscene-trigger'] : [
            'top' => 0,
            'left' => 0,
            'height' => 0,
            'width' => 0,
            'point' => '',
            'cutscene' => ''
        ];
        $mission_trigger = get_post_meta($post->ID, 'explore-mission-trigger', true);
        $mission_trigger = false === empty($mission_trigger['explore-mission-trigger']) ? $mission_trigger['explore-mission-trigger'] : [
            'top' => 0,
            'left' => 0,
            'height' => 0,
            'width' => 0,
        ];
        $mission_cutscene = get_post_meta($post->ID, 'explore-mission-cutscene', true);
        $mission_complete_cutscene = get_post_meta($post->ID, 'explore-mission-complete-cutscene', true);
        $cutscene_character_position = get_post_meta($post->ID, 'explore-cutscene-character-position', true);
        $cutscene_character_position = false === empty($cutscene_character_position['explore-cutscene-character-position']) ? $cutscene_character_position['explore-cutscene-character-position'] : [
            'top' => 0,
            'left' => 0,
            'trigger' => 'before'
        ];

        if ('explore-mission' === $post->post_type) {
            $next_mission = get_post_meta($post->ID, 'explore-next-mission', true);
        }

		// Include the meta box template.
		include "{$this->theme->dir_path}/../templates/meta/meta-box.php";
	}

    /**
     * Save meta
     *
     * @action save_post, 1
     */
    public function save_meta($post_id) {
        // Check if the request came from the WordPress save post process
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $post_type = get_post_type();

        if (true === in_array($post_type, ['explore-point', 'explore-area', 'explore-character', 'explore-weapon', 'explore-magic', 'explore-cutscene', 'explore-mission', 'explore-sign'], true)) {
            $top    = filter_input(INPUT_POST, 'explore-top', FILTER_SANITIZE_NUMBER_INT);
            $left   = filter_input(INPUT_POST, 'explore-left', FILTER_SANITIZE_NUMBER_INT);
            $height = filter_input(INPUT_POST, 'explore-height', FILTER_SANITIZE_NUMBER_INT);
            $width  = filter_input(INPUT_POST, 'explore-width', FILTER_SANITIZE_NUMBER_INT);
            $start_top  = filter_input(INPUT_POST, 'explore-start-top', FILTER_SANITIZE_NUMBER_INT);
            $start_left  = filter_input(INPUT_POST, 'explore-start-left', FILTER_SANITIZE_NUMBER_INT);
            $music  = filter_input(INPUT_POST, 'explore-music', FILTER_UNSAFE_RAW);
            $map  = filter_input(INPUT_POST, 'explore-map-svg', FILTER_UNSAFE_RAW);
            $value  = filter_input(INPUT_POST, 'value', FILTER_SANITIZE_NUMBER_INT);
            $unlock_level  = filter_input(INPUT_POST, 'explore-unlock-level', FILTER_SANITIZE_NUMBER_INT);
            $interaction_type  = filter_input(INPUT_POST, 'explore-interaction-type', FILTER_UNSAFE_RAW);
            $drag_dest  = filter_input_array(
                INPUT_POST, ['explore-drag-dest' => ['filter' => FILTER_UNSAFE_RAW, 'flags' => FILTER_REQUIRE_ARRAY]]
            );
            $repeat  = filter_input(INPUT_POST, 'explore-repeat', FILTER_UNSAFE_RAW);
            $walking_path = filter_input_array(
                INPUT_POST, ['explore-path' => ['filter' => FILTER_UNSAFE_RAW, 'flags' => FILTER_REQUIRE_ARRAY]]
            );
            $path_trigger = filter_input_array(
                INPUT_POST, ['explore-path-trigger' => ['filter' => FILTER_UNSAFE_RAW, 'flags' => FILTER_REQUIRE_ARRAY]]
            );
            $cutscene_trigger = filter_input_array(
                INPUT_POST, ['explore-cutscene-trigger' => ['filter' => FILTER_UNSAFE_RAW, 'flags' => FILTER_REQUIRE_ARRAY]]
            );
            $mission_trigger = filter_input_array(
                INPUT_POST, ['explore-mission-trigger' => ['filter' => FILTER_UNSAFE_RAW, 'flags' => FILTER_REQUIRE_ARRAY]]
            );
            $walking_speed = filter_input(INPUT_POST, 'explore-speed', FILTER_SANITIZE_NUMBER_INT);
            $cutscene_character_position = filter_input_array(
                INPUT_POST, ['explore-cutscene-character-position' => ['filter' => FILTER_UNSAFE_RAW, 'flags' => FILTER_REQUIRE_ARRAY]]
            );
            $mission_cutscene = filter_input(INPUT_POST, 'explore-mission-cutscene', FILTER_UNSAFE_RAW);
            $mission_complete_cutscene = filter_input(INPUT_POST, 'explore-mission-complete-cutscene', FILTER_UNSAFE_RAW);

            $inputs = [
                'explore-top'              => $top,
                'explore-left'             => $left,
                'explore-height'           => $height,
                'explore-width'            => $width,
                'value'                    => $value,
                'explore-unlock-level'     => $unlock_level,
                'explore-interaction-type' => $interaction_type,
                'explore-drag-dest'        => $drag_dest,
                'explore-path'             => $walking_path,
                'explore-speed'            => $walking_speed,
                'explore-repeat'           => $repeat,
                'explore-path-trigger'     => $path_trigger,
                'explore-mission-trigger'  => $mission_trigger,
                'explore-cutscene-trigger' => $cutscene_trigger,
                'explore-cutscene-character-position' => $cutscene_character_position,
                'explore-mission-cutscene' => sanitize_text_field(wp_unslash($mission_cutscene)),
                'explore-mission-complete-cutscene' => sanitize_text_field(wp_unslash($mission_complete_cutscene)),
            ];

            if ($start_top && $start_left) {
                $inputs['explore-start-top'] = $start_top;
                $inputs['explore-start-left'] = $start_left;
                $inputs['explore-music'] = sanitize_text_field(wp_unslash($music));
                $inputs['explore-map-svg'] = sanitize_text_field(wp_unslash($map));
            }

            // Missions.
            if ('explore-mission' === $post_type) {
                $next_mission  = filter_input(INPUT_POST, 'explore-next-mission', FILTER_UNSAFE_RAW);
                $inputs['explore-next-mission'] = $next_mission ?? '';
            }

            foreach ($inputs as $name => $output) {
                update_post_meta($post_id, $name, $output);
            }
        }
    }

    /**
     * Call back function for the explore enemy meta box.
     */
    public function explore_enemy_box() {
        global $post;

        // Get all needed options for meta boxes.
        $health = get_post_meta($post->ID, 'explore-health', true);
        $speed = get_post_meta($post->ID, 'explore-speed', true);
        $projectile = get_post_meta($post->ID, 'explore-projectile', true);
        $proj_url = false === empty($projectile['explore-projectile']['url']) ? $projectile['explore-projectile']['url'] : '';
        $proj_width = false === empty($projectile['explore-projectile']['width']) ? $projectile['explore-projectile']['width'] : 0;
        $proj_height = false === empty($projectile['explore-projectile']['height']) ? $projectile['explore-projectile']['height'] : 0;
        $enemy_type = get_post_meta($post->ID, 'explore-enemy-type', true);
        $trigger_top = get_post_meta($post->ID, 'explore-trigger-top', true);
        $trigger_left = get_post_meta($post->ID, 'explore-trigger-left', true);
        $trigger_height = get_post_meta($post->ID, 'explore-trigger-height', true);
        $trigger_width = get_post_meta($post->ID, 'explore-trigger-width', true);

        // Include the meta box template.
        include "{$this->theme->dir_path}/../templates/meta/enemy-meta-box.php";
    }

    /**
     * Save meta
     *
     * @action save_post, 1
     */
    public function save_enemy_meta($post_id) {
        // Check if the request came from the WordPress save post process.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if ('explore-enemy' === get_post_type()) {
            $health  = filter_input(INPUT_POST, 'explore-health', FILTER_SANITIZE_NUMBER_INT);
            $speed  = filter_input(INPUT_POST, 'explore-speed', FILTER_SANITIZE_NUMBER_INT);
            $enemy_type = filter_input(INPUT_POST, 'explore-enemy-type', FILTER_UNSAFE_RAW);
            $enemy_trigger_top = filter_input(INPUT_POST, 'explore-trigger-top', FILTER_SANITIZE_NUMBER_INT);
            $enemy_trigger_left = filter_input(INPUT_POST, 'explore-trigger-left', FILTER_SANITIZE_NUMBER_INT);
            $enemy_trigger_height = filter_input(INPUT_POST, 'explore-trigger-height', FILTER_SANITIZE_NUMBER_INT);
            $enemy_trigger_width = filter_input(INPUT_POST, 'explore-trigger-width', FILTER_SANITIZE_NUMBER_INT);
            $projectile  = filter_input_array(
                INPUT_POST, ['explore-projectile' => ['filter' => FILTER_UNSAFE_RAW, 'flags' => FILTER_REQUIRE_ARRAY]]
            );

            $inputs['explore-health'] = $health;
            $inputs['explore-speed'] = $speed;
            $inputs['explore-projectile'] = $projectile;
            $inputs['explore-enemy-type'] = $enemy_type;
            $inputs['explore-trigger-top'] = $enemy_trigger_top;
            $inputs['explore-trigger-left'] = $enemy_trigger_left;
            $inputs['explore-trigger-height'] = $enemy_trigger_height;
            $inputs['explore-trigger-width'] = $enemy_trigger_width;

            foreach ($inputs as $name => $output) {
                if (false === empty($output)) {
                    update_post_meta($post_id, $name, $output);
                }
            }
        }
    }
}
