<?php
/**
 * Explore
 *
 * @package Miropelia
 */

namespace Miropelia;

/**
 * Explore Class
 *
 * @package Miropelia
 */
class Explore
{

	/**
	 * Theme instance.
	 *
	 * @var object
	 */
	public $theme;

	/**
	 * Class constructor.
	 *
	 * @param object $plugin Plugin class.
	 */
	public function __construct($theme)
    {
		$this->theme = $theme;
	}

    /**
     * Register API field.
     *
     * @action rest_api_init
     */
    public function create_api_posts_meta_field()
    {
        $namespace = 'orbemorder/v1';

        // Register route for getting event by location.
        register_rest_route($namespace, '/add-explore-points/(?P<user>\d+)/(?P<position>[a-zA-Z0-9-]+)/(?P<point>\d+)/(?P<type>[a-zA-Z0-9-]+)', array(
            'methods'  => 'GET',
            'callback' => [$this, 'addCharacterPoints'],
        ));

        // Register route for saving storage item.
        register_rest_route($namespace, '/save-storage-item/(?P<user>\d+)/(?P<id>\d+)/(?P<name>[a-zA-Z0-9-]+)/(?P<type>[a-zA-Z0-9-]+)/(?P<value>\d+)/(?P<remove>[a-zA-Z0-9-]+)', array(
            'methods'  => 'GET',
            'callback' => [$this, 'saveStorageItem'],
        ));

        // Register route for getting event by location.
        register_rest_route($namespace, '/area/(?P<position>[a-zA-Z0-9-]+)/(?P<userid>\d+)', array(
            'methods'  => 'GET',
            'callback' => [$this, 'getOrbemArea'],
        ));

        // Register route for getting item description.
        register_rest_route($namespace, '/get-item-description/(?P<id>\d+)/(?P<userid>\d+)', array(
            'methods'  => 'GET',
            'callback' => [$this, 'getItemDescription'],
        ));

        // Register route for getting item description.
        register_rest_route($namespace, '/get-item-description/(?P<id>\d+)', array(
            'methods'  => 'GET',
            'callback' => [$this, 'getItemDescription'],
        ));

        // Register route for getting event by location.
        register_rest_route($namespace, '/coordinates/(?P<left>\d+)/(?P<top>\d+)/(?P<userid>\d+)', array(
            'methods'  => 'GET',
            'callback' => [$this, 'saveCoordinates'],
        ));

        // Register route for getting event by location.
        register_rest_route($namespace, '/resetexplore/(?P<userid>\d+)', array(
            'methods'  => 'GET',
            'callback' => [$this, 'resetExplore'],
        ));

        // Register route for getting event by location.
        register_rest_route($namespace, '/addspell/(?P<userid>\d+)/(?P<spellid>\d+)', array(
            'methods'  => 'GET',
            'callback' => [$this, 'addSpell'],
        ));

        // Register route for saving settings.
        register_rest_route($namespace, '/save-settings/(?P<userid>\d+)/(?P<music>\d+)/(?P<sfx>\d+)', array(
            'methods'  => 'GET',
            'callback' => [$this, 'saveSettings'],
        ));

        // Register route for saving enemy info.
        register_rest_route($namespace, '/enemy/(?P<position>[a-zA-Z0-9-]+)/(?P<health>\d+)/(?P<userid>\d+)', array(
            'methods'  => 'GET',
            'callback' => [$this, 'saveEnemy'],
        ));

        // Register route for equiping new item.
        register_rest_route($namespace, '/equip-explore-item/(?P<type>[a-zA-Z0-9-]+)/(?P<itemid>\d+)/(?P<amount>\d+)/(?P<userid>\d+)/(?P<unequip>[a-zA-Z0-9-]+)', array(
            'methods'  => 'GET',
            'callback' => [$this, 'equipNewItem'],
        ));

        // Register route for saving completed missions.
        register_rest_route($namespace, '/mission/(?P<mission>[a-zA-Z0-9-]+)/(?P<userid>\d+)', array(
            'methods'  => 'GET',
            'callback' => [$this, 'saveMission'],
        ));
    }

    /**
     * Call back function for rest route that adds spell to the explore_magic user meta
     * @param object $return The arg values from rest route.
     */
    public function addSpell(object $return)
    {
        $user = isset($return['userid']) ? intval($return['userid']) : '';
        $spell_id = isset($return['spellid']) ? intval($return['spellid']) : '';

        if (!in_array('', [$user, $spell_id], true)) {
            $explore_magic = get_user_meta($user, 'explore_magic', true);
            $explore_magic = false === empty($explore_magic) ? $explore_magic : ['defense' => [], 'offense' => []];
            $spell_type = get_the_terms($spell_id, 'magic-type');
            $the_spell_type = '';

            if (true === is_array($spell_type)) {
                foreach( $spell_type as $type) {
                    if ( true === in_array($type->slug, ['defense', 'offense'], true)) {
                        $the_spell_type = $type->slug;
                    }
                }
            }

            if ( '' !== $the_spell_type ) {
                $explore_magic[$the_spell_type][] = $spell_id;

                update_user_meta($user, 'explore_magic', $explore_magic);
                wp_send_json_success('SPELL ADDED');
            } else {
                wp_send_json_error('no type selected');
            }
        }
    }

    /**
     * Call back function for rest route that adds points to user's explore game.
     * @param object $return The arg values from rest route.
     */
    public function addCharacterPoints(object $return)
    {
        $user = isset($return['user']) ? intval($return['user']) : '';
        $points = isset($return['point']) ? intval($return['point']) : '';
        $type = isset($return['type']) ? sanitize_text_field(wp_unslash($return['type'])) : '';
        $position = isset($return['position']) ? sanitize_text_field(wp_unslash($return['position'])) : '';

        if (false === in_array('', [$points, $type, $user, $position], true)) {
            $current_explore_points = get_user_meta($user, 'explore_points', true);
            $explore_points         = !empty($current_explore_points) && is_array($current_explore_points) ? $current_explore_points : [
                'health' => ['points' => 100, 'positions' => []],
                'mana' => ['points' => 100, 'positions' => []],
                'point' => ['points' => 0, 'positions' => []],
                'gear' => ['positions' => []],
                'weapons' => ['positions' => []]
            ];
            $explore_points[$type]['points'] = $points;

            // Add position to list of positions received points on.
            if (false === isset($explore_points[$type]['positions'])) {
                $explore_points[$type]['positions'] = [$position];
            } elseif (false === in_array($position, $explore_points[$type]['positions'], true)) {
                $explore_points[$type]['positions'][] = $position;
            }

            update_user_meta($user, 'explore_points', $explore_points);
        }
    }

    /**
     * Call back function for rest route that adds points to user's explore game.
     * @param object $return The arg values from rest route.
     */
    public function saveEnemy(object $return)
    {
        $user = isset($return['userid']) ? intval($return['userid']) : '';
        $health = isset($return['health']) ? intval($return['health']) : '';
        $position = isset($return['position']) ? sanitize_text_field(wp_unslash($return['position'])) : '';

        if (false === in_array('', [$health, $user, $position], true) && 0 === $health) {
            $explore_enemies = get_user_meta($user, 'explore_enemies', true);

            if (false === empty($explore_enemies)) {
                $explore_enemies[] = $position;
            } else {
                $explore_enemies = [$position];
            }

            update_user_meta($user, 'explore_enemies', $explore_enemies);
        }
    }

    /**
     * Call back function for rest route that saves completed missions.
     * @param object $return The arg values from rest route.
     */
    public function saveMission(object $return)
    {
        $user = isset($return['userid']) ? intval($return['userid']) : '';
        $mission = isset($return['mission']) ? sanitize_text_field(wp_unslash($return['mission'])) : '';

        if (false === in_array('', [$user, $mission], true)) {
            $explore_missions = get_user_meta($user, 'explore_missions', true);

            if (false === empty($explore_missions)) {
                $explore_missions[] = $mission;
            } else {
                $explore_missions = [$mission];
            }

            update_user_meta($user, 'explore_missions', $explore_missions);
        }
    }

    /**
     * Call back function for rest route that equips a new item on the player.
     * @param object $return The arg values from rest route.
     */
    public function equipNewItem(object $return)
    {
        $user = isset($return['userid']) ? intval($return['userid']) : '';
        $item_id = isset($return['itemid']) ? intval($return['itemid']) : '';
        $amount = isset($return['amount']) ? intval($return['amount']) : '';
        $type = isset($return['type']) ? sanitize_text_field(wp_unslash($return['type'])) : '';
        $unequip = isset($return['unequip']) ? 'true' === sanitize_text_field(wp_unslash($return['unequip'])) : '';
        $current_equipped = get_user_meta($user, 'explore_current_' . $type, true);
        $current_equipped = false === empty($current_equipped) ? $current_equipped : [];
        $effect_types = get_the_terms($item_id, 'value-type');
        $the_effect_type = '';

        if (true === is_array($effect_types)) {
            foreach( $effect_types as $effect_type) {
                if ( true === in_array($effect_type->slug, ['mana', 'health', 'power'], true)) {
                    $the_effect_type = $effect_type->slug;
                }
            }
        }

        if (false === $unequip && false === empty($current_equipped[$the_effect_type])) {
            if (true === is_array($current_equipped[$the_effect_type])) {
                foreach ($current_equipped[$the_effect_type] as $current_array) {
                    if (false === in_array(intval($item_id), array_keys($current_array), true)) {
                        $current_equipped[$the_effect_type][] = [$item_id => $amount];
                    }
                }
            }
        } elseif (true === $unequip && false === empty($current_equipped[$the_effect_type])) {
            $equip_position = array_search($item_id, $current_equipped[$the_effect_type]);
            unset($current_equipped[$the_effect_type][$equip_position]);
        }

        update_user_meta(
            $user,
            'explore_current_' . $type,
            $current_equipped
        );
    }

    /**
     * Call back function for rest route that storages items.
     * @param object $return The arg values from rest route.
     */
    public function saveStorageItem(object $return)
    {
        $user = isset($return['user']) ? intval($return['user']) : '';
        $id = isset($return['id']) ? intval($return['id']) : '';
        $value = isset($return['value']) ? intval($return['value']) : '';
        $type = isset($return['type']) ? sanitize_text_field(wp_unslash($return['type'])) : '';
        $name = isset($return['name']) ? sanitize_text_field(wp_unslash($return['name'])) : '';
        $remove = isset($return['remove']) && 'true' === sanitize_text_field(wp_unslash($return['remove']));
        $menu_map = $this->getMenuType($type);

        if (false === in_array('', [$id, $type, $user, $name, $value], true)) {
            $current_storage_items = get_user_meta($user, 'explore_storage', true);
            $item_subtypes = get_the_terms($id, 'value-type');
            $subtype = '';

            foreach($item_subtypes as $item_subtype) {
                if ($type !== $item_subtype->slug) {
                    $subtype = $item_subtype->slug;
                }
            }

            // If remove is true then remove the provided item.
            if (true === $remove) {
                foreach($current_storage_items[$menu_map] as $index => $storage_item) {
                    if ($name === $storage_item['name']) {
                        if (false === empty($storage_item['count']) && 1 < $storage_item['count']) {
                            $current_storage_items[$menu_map][$index]['count'] = $storage_item['count'] - 1;
                        } else {
                            unset($current_storage_items[$menu_map][$index]);
                        }
                    }
                }
            } else {
                $new_item = [
                    'id'    => $id,
                    'name'  => $name,
                    'type'  => $type,
                    'value' => $value,
                ];

                if (false === empty($subtype)) {
                    $new_item['subtype'] = $subtype;
                }

                $has_dupe = false;

                if (true === empty($current_storage_items)) {
                    $current_storage_items = ['items' => [], 'weapons' => [], 'gear' => []];
                } else {
                    foreach ($current_storage_items[$menu_map] as $index => $item) {
                        if ($name === $item['name']) {
                            $count                                             = $item['count'] ?? 1;
                            $current_storage_items[$menu_map][$index]['count'] = $count + 1;
                            $has_dupe                                          = true;
                        }
                    }
                }

                if (false === $has_dupe) {
                    $current_storage_items[$menu_map][] = $new_item;
                }
            }

            update_user_meta($user, 'explore_storage', $current_storage_items);
        }
    }

    /**
     * Map for menu item versus item type.
     *
     * @param string $menu_type
     */
    public function getMenuType(string $menu_type )
    {
        $menu_map = [
            'health' => 'items',
            'mana' => 'items',
            'gear' => 'gear',
            'weapons' => 'weapons'
        ];

        return $menu_map[$menu_type];
    }

    /**
     * Call back function for rest route that saves game settings.
     * @param object $return The arg values from rest route.
     */
    public function saveSettings(object $return)
    {
        $music = isset($return['music']) ? intval($return['music']) : '';
        $sfx = isset($return['sfx']) ? intval($return['sfx']) : '';
        $userid = isset($return['userid']) ? intval($return['userid']) : '';

        if (false === in_array('', [$music, $userid, $sfx], true)) {
            update_user_meta($userid, 'explore_settings', ['music' => $music, 'sfx' => $sfx]);
        }
    }

    /**
     * Call back function for rest route that adds points to user's explore game.
     * @param object $return The arg values from rest route.
     */
    public function getOrbemArea(object $return)
    {
        $position = isset($return['position']) ? sanitize_text_field(wp_unslash($return['position'])) : '';

        // Get content from the new explore-area post type.
        $area = get_posts(['post_type' => 'explore-area', 'name' => $position, 'posts_per_page' => 1]);
        $userid = $return['userid'] ?? get_current_user_id();
        $explore_points = self::getExplorePoints($position);
        $explore_cutscenes = self::getExploreCutscenes($position);
        $map_items = self::getMapItemHTML($explore_points, $userid);
        $map_cutscenes = self::getMapCutsceneHTML($explore_cutscenes);
        $map_abilities = self::getMapAbilitiesHTML($explore_cutscenes);

        ob_start();
        include_once $this->theme->dir_path . '/../templates/style-scripts.php';
        $area_item_styles_scripts = ob_get_clean();

        ob_start();
        include_once $this->theme->dir_path . '/../page-templates/components/explore-missions.php';
        $map_missions = ob_get_clean();

        update_user_meta($userid, 'current_location', $position);

        if (is_wp_error($area) || !isset($area[0])) {
            return;
        }

        wp_send_json_success(
            wp_json_encode(
                [
                    'map-items' => $map_items,
                    'map-cutscenes' => $map_cutscenes,
                    'map-missions' => $map_missions,
                    'map-abilities' => $map_abilities,
                    'map-item-styles-scripts' => $area_item_styles_scripts,
                    'start-top' => get_post_meta($area[0]->ID, 'explore-start-top', true),
                    'start-left' => get_post_meta($area[0]->ID, 'explore-start-left', true),
                    'map-svg' => self::getMapSVG($area[0])
                ]
            )
        );
    }

    /**
     * Call back function for rest route that returns item description.
     * @param object $return The arg values from rest route.
     */
    public function getItemDescription(object $return)
    {
        $item = isset($return['id']) ? intval($return['id']) : false;
        $userid = isset($return['userid']) ? intval($return['userid']) : false;

        if ( false !== $item ) {
            // Get content from the new explore-area post type.
            $item_obj = get_post($item);

            // Check if equipped.
            $gear_equipped = get_user_meta( $userid, 'explore_current_gear', true);
            $weapons_equipped = get_user_meta( $userid, 'explore_current_weapons', true);
            $content = $item_obj->post_content;
            $types = get_the_terms($item_obj->ID, 'value-type');
            $item_type = '';

            foreach($types as $type) {
                if (true === in_array($type->slug, ['mana', 'health', 'power'], true)) {
                    $item_type = $type->slug;
                }
            }

            // Check equipped gear. IF so change button to unequip.
            if (false === empty($gear_equipped[$item_type]) && true === is_array($gear_equipped[$item_type])) {
                foreach ($gear_equipped[$item_type] as $current_array) {
                    if (true === in_array(intval($item), array_keys($current_array), true)) {
                        $content = str_replace(['Equip', 'equip', 'Ununequip'],
                            ['Unequip', 'unequip', 'Unequip'],
                            $content);
                    }
                }
            }
            
            // Return the post content for the supplied item.
            wp_send_json_success(wp_json_encode($content));
        } else {
            wp_send_json_error('Item id not provided');
        }
    }

    /**
     * Call back function for rest route that adds coordinates user's explore game.
     * @param object $return The arg values from rest route.
     */
    public function saveCoordinates(object $return)
    {
        $left = isset($return['left']) ? intval($return['left']) : '';
        $top = isset($return['top']) ? intval($return['top']) : '';
        $current_user = isset($return['userid']) ? intval($return['userid']) : '';

        update_user_meta($current_user, 'current_coordinates', ['left'=>$left,'top'=>$top]);
    }

    /**
     * Call back function to reset explore game.
     * @param object $return The arg values from rest route.
     */
    public function resetExplore(object $return)
    {
        $current_user = isset($return['userid']) ? intval($return['userid']) : '';

        delete_user_meta($current_user, 'current_coordinates');
        delete_user_meta($current_user, 'current_location');
        delete_user_meta($current_user, 'explore_points');
        delete_user_meta($current_user, 'explore_enemies');
        delete_user_meta($current_user, 'explore_missions');
        delete_user_meta($current_user, 'explore_storage');
        delete_user_meta($current_user, 'explore_magic');
        delete_user_meta($current_user, 'explore_current_gear');
        delete_user_meta($current_user, 'explore_current_weapons');
        delete_user_meta($current_user, 'explore_missions');
    }

    /**
     * get Map svg content.
     */
    public static function getMapSVG($explore_area) {
        // Create a stream context with SSL options
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        // Fetch the image data using the created context
        $imageData = file_get_contents(get_post_meta($explore_area->ID, 'explore-map-svg', true), false, $context);
        $find_string   = '<svg';
        $position = strpos($imageData, $find_string);
        return substr($imageData, $position);
    }

    /**
     * Grab all the points you can collide with.
     * @return int[]|\WP_Post[]
     */
    public static function getExplorePoints($position)
    {
        $args = [
            'numberposts' => -1,
            'post_type' => ['explore-area', 'explore-point', 'explore-character', 'explore-enemy'],
            'tax_query' => [
                [
                    'taxonomy' => 'explore-area-point',
                    'field' => 'slug',
                    'terms' => [false === empty($position) ? $position : 'foresight'],
                    'operator' => 'IN',
                ]
            ]
        ];

        return get_posts($args);
    }

    /**
     * Return post object of currently equipped weapon.
     * @param string $weapon_name
     *
     * @return int[]|\WP_Post
     */
    public static function getCurrentWeapon($weapon_name)
    {
        $args = [
            'post_type' => ['explore-weapon'],
            'post_name' => $weapon_name,
            'numberpost' => 1
        ];

        return get_posts($args)[0];
    }

    /**
     * Grab all the points you can collide with.
     * @return int[]|\WP_Post[]
     */
    public static function getExploreCutscenes($position)
    {
        $args = [
            'post_type' => ['explore-cutscene'],
            'tax_query' => [
                [
                    'taxonomy' => 'explore-area-point',
                    'field' => 'slug',
                    'terms' => [false === empty($position) ? $position : 'foresight'],
                    'operator' => 'IN',
                ]
            ]
        ];

        return get_posts($args);
    }

    /**
     * Grab all the abilities you can unlock.
     * @return int[]|\WP_Post[]
     */
    public static function getExploreAbilities()
    {
        $args = [
            'post_type' => ['explore-magic'],
            'posts_per_page' => -1,
        ];

        return get_posts($args);
    }

    /**
     * Add map item styles.
     *
     * @action wp_head
     */
    public function inlineExploreStyles()
    {
        if (is_page('explore')) {
            $position = get_user_meta(get_current_user_id(), 'current_location', true);
            $explore_points = \Miropelia\Explore::getExplorePoints($position);
            $explore_areas = get_posts(['post_type' => 'explore-area', 'numberpost' => -1]);
            $music_names = '';


            ?>
            <style id="map-item-styles">
                <?php foreach($explore_points as $explore_point) :
                    $top = get_post_meta($explore_point->ID, 'explore-top', true) . 'px';
                    $left = get_post_meta($explore_point->ID, 'explore-left', true) . 'px';
                    $height = get_post_meta($explore_point->ID, 'explore-height', true) . 'px';
                    $width = get_post_meta($explore_point->ID, 'explore-width', true) . 'px';
                    $map_url = get_the_post_thumbnail_url($explore_point->ID);
                    $background_url = true === in_array($explore_point->post_type, ['explore-point', 'explore-character', 'explore-enemy'], true) ? "background: url(" . $map_url . ") no-repeat;" : '';
                    $point_type = 'explore-enemy' === $explore_point->post_type ? '.enemy-item' : '.map-item';
                    ?>

                    .page-template-explore .container .default-map <?php echo esc_html($point_type); ?>.<?php echo esc_html($explore_point->post_name); ?>-map-item {
                    <?php echo esc_html($background_url); ?>
                        background-size: cover;
                        top: <?php echo esc_html($top); ?>;
                        left: <?php echo esc_html($left); ?>;
                        height: <?php echo esc_html($height); ?>;
                        width: <?php echo esc_html($width); ?>;
                    }
                <?php endforeach; ?>
            </style>
            <?php

            foreach($explore_areas as $explore_area):
                if ('explore-area' === $explore_area->post_type) {
                    $music = get_post_meta($explore_area->ID, 'explore-music', true);
                    $music_names .= '"' . $explore_area->post_name . '":"' . $music . '",';
                }
            endforeach;?>
            <script id="enterable-maps">
				const musicNames = {<?php echo $music_names; ?>}
            </script>
            <?php
        }
    }

    /**
     * Build html for map items.
     * @param $explore_points
     *
     * @return string
     */
    public static function getMapItemHTML($explore_points, $userid) {
        $html = '';

        $userid = $userid ?? get_current_user_id();
        $dead_ones = get_user_meta($userid, 'explore_enemies', true);
        $dead_ones = false === empty($dead_ones) ? $dead_ones : [];
        $current_location = get_user_meta($userid, 'current_location', true);
        $current_location = false === empty($current_location) ? $current_location : 'foresight';

        foreach( $explore_points as $explore_point ) {
            if ('explore-enemy' === $explore_point->post_type) {
                $health = get_post_meta($explore_point->ID, 'explore-health', true);
                $explore_enemy_type = get_post_meta($explore_point->ID, 'explore-enemy-type', true);

                if (true === isset($dead_ones[$explore_point->post_name])) {
                    continue;
                }
            }

            $value = get_post_meta($explore_point->ID, 'value', true);
            $type = get_the_terms($explore_point->ID, 'value-type');
            $breakable = get_post_meta($explore_point->ID, 'explore-breakable', true);
            $collectable = get_post_meta($explore_point->ID, 'explore-collectable', true);
            $top = get_post_meta($explore_point->ID, 'explore-top', true) . 'px';
            $left = get_post_meta($explore_point->ID, 'explore-left', true) . 'px';
            $type = false === empty($type[0]->slug) ? $type[0]->slug : '';
            $walking_path = get_post_meta($explore_point->ID, 'explore-path', true);
            $walking_speed = get_post_meta($explore_point->ID, 'explore-speed', true);
            $repeat = get_post_meta($explore_point->ID, 'explore-repeat', true);
            $path_trigger = get_post_meta($explore_point->ID, 'explore-path-trigger', true);
            $path_trigger = false === empty($path_trigger['explore-path-trigger']) ? $path_trigger['explore-path-trigger'] : '';
            $path_trigger_left = false === empty($path_trigger['left']) ? $path_trigger['left'] : '';
            $path_trigger_top = false === empty($path_trigger['top']) ? $path_trigger['top'] : '';
            $path_trigger_height = false === empty($path_trigger['height']) ? $path_trigger['height'] : '';
            $path_trigger_width = false === empty($path_trigger['width']) ? $path_trigger['width'] : '';
            $path_trigger_cutscene = false === empty($path_trigger['cutscene']) ? $path_trigger['cutscene'] : '';
            $missions = get_posts(
                [
                    'post_type' => 'explore-mission',
                    'numberposts' => 1,
                    'post_status' => 'publish',
                    'tax_query' => [
                        [
                            'taxonomy' => 'explore-area-point',
                            'field' => 'slug',
                            'terms' => $current_location,
                        ],
                        [
                            'taxonomy' => 'explore-point-tax',
                            'field' => 'slug',
                            'terms' => $explore_point->post_name,
                        ],
                    ],
                ]
            );


             // Create onload class:
             $path_onload = true === empty($path_trigger['left']) && true === empty($path_trigger['cutscene']) ? ' path-onload' : '';
             $classes = $path_onload;

            // If it's an enemy and they have health show or if not an enemy show.
            if (('explore-enemy' === $explore_point->post_type && false === in_array($explore_point->post_name, $dead_ones,
                        true)) || 'explore-enemy' !== $explore_point->post_type ) {
                $html .= '<div style="left:' . intval($left) . 'px; top:' . intval($top) . 'px;" id="' . $explore_point->ID . '" data-genre="' . $explore_point->post_type . '" data-type="' . esc_attr($type) . '" data-value="' . intval($value) . '"';
                $html .= 'data-image=' . get_the_post_thumbnail_url($explore_point->ID) . '" ';
                if ('explore-area' === $explore_point->post_type) {
                    $map_url = get_the_post_thumbnail_url($explore_point->ID);

                    $html .= ' data-map-url="' . $map_url . '" ';
                }

                // Is item breakable.
                if ($breakable) {
                    $html .= ' data-breakable="true" ';
                }

                // Is item attached to a mission.
                if (false === empty($missions)) {
                    $html .= ' data-mission="' . $missions[0]->post_name . '"';
                }

                $explore_path = false === empty($walking_path['explore-path']) ? wp_json_encode($walking_path["explore-path"]) : '[{"top":"0","left":"0"}]';

                if ('[{"top":"0","left":"0"}]' !== $explore_path) {
                    $html .= ' data-path=\'' . $explore_path . '\' ';
                    $html .= ' data-speed="' . $walking_speed . '" ';

                    if ('yes' === $repeat) {
                        $html .= ' data-repeat="true" ';
                    }

                    if (false === empty($path_trigger_cutscene)) {
                        $html .= ' data-trigger-cutscene="' . $path_trigger_cutscene . '"';
                    }
                }

                if ($collectable) {
                    $html .= ' data-collectable="true" ';
                }

                // Eneemy specific data-points.
                if ('explore-enemy' === $explore_point->post_type) {
                    $speed = get_post_meta($explore_point->ID, 'explore-speed', true);

                    $html .= 'data-health="' . intval($health) . '" data-speed="' . intval($speed) . '" data-enemy-type="' . esc_attr($explore_enemy_type) . '"';
                    $html .= 'class="wp-block-group enemy-item ' . $explore_point->post_name . '-map-item is-layout-flow wp-block-group-is-layout-flow' . $classes. '"';
                } else {
                    $html .= 'class="wp-block-group map-item ' . $explore_point->post_name . '-map-item is-layout-flow wp-block-group-is-layout-flow' . $classes. '"';
                }

                $html .= '>';

                $html .= 'explore-character' === $explore_point->post_type ? $explore_point->post_content : '';

                // Projectile html for enemy.
                if ('explore-enemy' === $explore_point->post_type && 'shooter' === $explore_enemy_type) {
                    $projectile = get_post_meta($explore_point->ID, 'explore-projectile', true);
                    $projectile = $projectile['explore-projectile'] ?? false;

                    if (false !== $projectile) {
                        $html .= '<div class="projectile" data-value="' . intval($value) . '"><img alt="projectile" style="width:' . $projectile['width'] . 'px; height: ' . $projectile['height'] . 'px;" src="' . $projectile['url'] . '" /></div>';
                    }
                }

                $html .= '</div>';

                // Trigger HTML.
                $trigger_top = get_post_meta($explore_point->ID, 'explore-trigger-top', true);

                if ('explore-enemy' === $explore_point->post_type && false === empty($trigger_top)) {
                    $trigger_left   = get_post_meta($explore_point->ID, 'explore-trigger-left', true);
                    $trigger_height = get_post_meta($explore_point->ID, 'explore-trigger-height', true);
                    $trigger_width  = get_post_meta($explore_point->ID, 'explore-trigger-width', true);

                    $html .= '<div class="wp-block-group map-item ' . $explore_point->post_name . '-trigger-map-item is-layout-flow wp-block-group-is-layout-flow"';
                    $html .= 'style="left:' . $trigger_left . 'px;top:' . $trigger_top . 'px;height:' . $trigger_height . 'px; width:' . $trigger_width . 'px;"';
                    $html .= 'data-trigger="true" data-triggee="' . $explore_point->post_name . '-map-item"';
                    $html .= '></div>';
                }

                // Trigger Walking Path.
                if (true === in_array($explore_point->post_type, ['explore-enemy', 'explore-character'], true) && false === in_array( '', [$path_trigger_width, $path_trigger_height], true)) {
                    $html .= '<div class="path-trigger wp-block-group map-item ' . $explore_point->post_name . '-trigger-map-item is-layout-flow wp-block-group-is-layout-flow"';
                    $html .= 'style="left:' . $path_trigger_left . 'px;top:' . $path_trigger_top . 'px;height:' . $path_trigger_height . 'px; width:' . $path_trigger_width . 'px;"';
                    $html .= 'data-trigger="true" data-triggee="' . $explore_point->post_name . '-map-item"';
                    $html .= '></div>';
                }
            }
         }

         return $html;
    }

    /**
     * Build html for map items.
     * @param $explore_cutscenes
     *
     * @return string
     */
    public static function getMapCutsceneHTML($explore_cutscenes) {
        $html = '';

        foreach( $explore_cutscenes as $explore_cutscene ) {
            $cut_char = get_the_post_thumbnail_url($explore_cutscene->ID);
            $character = get_the_terms( $explore_cutscene->ID, 'explore-character-point' );
            $next_area = get_the_terms( $explore_cutscene->ID, 'explore-next-area' );
            $cutscene_trigger = get_post_meta($explore_cutscene->ID, 'explore-cutscene-trigger', true);
            $character_position = get_post_meta($explore_cutscene->ID, 'explore-cutscene-character-position', true);
            $character_position_left = $character_position['explore-cutscene-character-position']['left'] ?? '';
            $character_position_top = $character_position['explore-cutscene-character-position']['top'] ?? '';
            $character_position_trigger = $character_position['explore-cutscene-character-position']['trigger'] ?? '';
            $mission_cutscene = get_post_meta($explore_cutscene->ID, 'explore-mission-cutscene', true);
            $mission_complete_cutscene = get_post_meta($explore_cutscene->ID, 'explore-mission-complete-cutscene', true);

            $next_area = false === empty($next_area[0]) ? ' data-nextarea="' . $next_area[0]->slug . '"' : '';

            $html .= '<div class="wp-block-group map-cutscene ' . $character[0]->slug . '-map-cutscene is-layout-flow wp-block-group-is-layout-flow"';

            if (false === empty($mission_cutscene)) {
                $html .= 'data-mission="' . $mission_cutscene . '" ';
            }

            // Add data point for the mission that is complete by having this cutscene.
            if (false === empty($mission_cutscene)) {
                $html .= 'data-missioncomplete="' . $mission_complete_cutscene . '" ';
            }

            // Add character position point if selected.
            if (false === empty($character_position_top)) {
                $html .= 'data-character-position=[{"left":"' . $character_position_left . '","top":"' . $character_position_top . '","trigger":"' . $character_position_trigger . '"}]';
            }

            if (false === empty($next_area)) {
                $area_obj = get_posts(['post_name' => $next_area, 'post_type' => 'explore-area', 'post_status' => 'publish']);

                $html .= $next_area;
                $html .= ' data-mapurl="' . get_the_post_thumbnail_url($area_obj[0]->ID) . '"';
            }

            $html .= '>';
            $html .= '<div class="cut-character"><img src="/wp-content/themes/miropelia/assets/src/images/graeme-avatar.png"/></div>';
            $html .= 'explore-area' !== $explore_cutscene->post_type ? $explore_cutscene->post_content : '';
            $html .= '<div class="cut-character"><img src="' . $cut_char . '"/></div>';
            $html .= '</div>';

            $path_trigger_left = false === empty($cutscene_trigger['explore-cutscene-trigger']['left']) && 0 !== $cutscene_trigger['explore-cutscene-trigger']['left'] ? $cutscene_trigger['explore-cutscene-trigger']['left'] : '';
            $path_trigger_top = false === empty($cutscene_trigger['explore-cutscene-trigger']['top']) && 0 !== $cutscene_trigger['explore-cutscene-trigger']['top'] ? $cutscene_trigger['explore-cutscene-trigger']['top'] : '';
            $path_trigger_height = false === empty($cutscene_trigger['explore-cutscene-trigger']['height']) && 0 !== $cutscene_trigger['explore-cutscene-trigger']['height'] ? $cutscene_trigger['explore-cutscene-trigger']['height'] : '';
            $path_trigger_width = false === empty($cutscene_trigger['explore-cutscene-trigger']['width']) && 0 !== $cutscene_trigger['explore-cutscene-trigger']['width'] ? $cutscene_trigger['explore-cutscene-trigger']['width'] : '';

            // Trigger Cutscene.
            if (false === in_array( '', [$path_trigger_width, $path_trigger_height], true)) {
                $html .= '<div class="cutscene-trigger wp-block-group map-item ' . $explore_cutscene->post_name . '-cutscene-trigger-map-item is-layout-flow wp-block-group-is-layout-flow"';
                $html .= 'style="left:' . $path_trigger_left . 'px;top:' . $path_trigger_top . 'px;height:' . $path_trigger_height . 'px; width:' . $path_trigger_width . 'px;"';
                $html .= 'data-trigger="true" data-triggee="' . $character[0]->slug . '-map-item"';
                $html .= '></div>';
            }
        }

        return $html;
    }

    /**
     * Build html for map abilities.
     * @param $explore_cutscenes
     *
     * @return string
     */
    public static function getMapAbilitiesHTML($explore_abilities) {
        $html = '';
        $magics = get_user_meta(get_current_user_id(), 'explore_magic', true);

        foreach( $explore_abilities as $explore_ability ) {
            if (false === is_array($magics) || false === in_array($explore_ability->ID, $magics, true)) {
                $unlockable = get_post_meta($explore_ability->ID, 'explore-unlock-level', true);

                $html .= '<div class="map-ability" ';
                $html .= 'id="' . $explore_ability->ID . '" ';
                $html .= 'data-genre="explore-magic" ';

                if (false === empty($unlockable)) {
                    $html .= 'data-unlockable="' . intval($unlockable) . '" ';
                }

                $html .= '></div>';
            }
        }

        return $html;
    }

    /**
     * Register post type for page components.
     *
     * @action init
     */
    public function registerPostType() {
        $post_types = [
            'explore-area' => 'Explore Area',
            'explore-point' => 'Explore Point',
            'explore-character' => 'Explore Character',
            'explore-cutscene' => 'Explore Cutscene',
            'explore-enemy' => 'Explore Enemy',
            'explore-weapon' => 'Explore Weapon',
            'explore-magic' => 'Explore Magic',
            'explore-mission' => 'Explore Mission'
        ];

        $taxo_types = [
            'explore-area-point' => [
                'name' => 'Explore Area',
                'post-types' => ['explore-area', 'explore-point', 'explore-character', 'explore-cutscene', 'explore-enemy', 'explore-mission']
            ],
            'explore-character-point' => [
                'name' => 'Explore Character',
                'post-types' => ['explore-cutscene']
            ],
            'explore-point-tax' => [
                'name' => 'Explore Point',
                'post-types' => ['explore-mission']
            ],
            'value-type' => [
                'name' => 'Value Type',
                'post-types' => ['explore-point', 'explore-character', 'explore-area', 'explore-enemy']
            ],
            'magic-type' => [
                'name' => 'Magic Type',
                'post-types' => ['explore-magic']
            ],
            'explore-next-area' => [
                'name' => 'Next Area',
                'post-types' => ['explore-cutscene']
            ]
        ];

        foreach($post_types as $slug => $name) {
            $args = array(
                'label'     => __( $name, 'sharethis-custom' ),
                'menu_icon' => 'dashicons-location-alt',
                'public'             => false,
                'publicly_queryable' => false,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'query_var'          => true,
                'rewrite'            => array( 'slug' => $slug ),
                'capability_type'    => 'page',
                'has_archive'        => false,
                'hierarchical'       => false,
                'menu_position'      => null,
                'show_in_rest'       => true,
                'supports'           => array( 'title', 'editor', 'author', 'thumbnail'),
            );

            register_post_type( $slug, $args );
        }

        foreach($taxo_types as $slug => $stuff) {
            // Add explore area sync with explore point taxo.
            $arg2s = [
                'label'             => __($stuff['name'], 'miropelia'),
                'hierarchical'      => true,
                'public'            => true,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => ['slug' => $slug],
                'show_in_rest'      => true,
            ];

            register_taxonomy($slug, $stuff['post-types'], $arg2s);
        }
    }

    /**
     * Get current point width based on equipped weapons and gear.
     * @return array
     */
    public static function getCurrentPointWidth() {
        $user = get_current_user_id();
        $gear = get_user_meta($user, 'explore_current_gear', true);
        $weapons = get_user_meta($user, 'explore_current_weapons', true);
        $types = ['health', 'mana', 'power'];
        $final_amounts = [
            'mana' => 100,
            'health' => 100
        ];

        foreach($types as $type) {
            if ( isset($gear[$type]) && is_array($gear[$type]) ) {
                foreach($gear[$type] as $gear_amount) {
                    $final_amounts[$type] += array_values($gear_amount)[0] ?? 0;
                }
            }
        }

        return $final_amounts;
    }

    /**
     * Map of levels.
     * @return int[]
     */
    public static function getLevelMap() {
        return [
            0,
            200,
            600,
            1200,
            2000,
            3000,
            4200,
            5600,
            7200,
        ];
    }

    /**
     * Get current level.
     */
    public static function getCurrentLevel() {
        $levels = self::getLevelMap();
        $points = get_user_meta(get_current_user_id(), 'explore_points', true);
        $points = true === isset($points['point']['points']) ? $points['point']['points'] : 0;

        if (false === empty($levels)) {
            foreach ($levels as $index => $level) {
                if (count($levels) === ($index - 1)) {
                    break;
                }

                if ($points > $level && $points < $levels[$index + 1] || $points === $level) {
                    return $index + 1;
                }
            }
        }

        return 1;
    }
}
