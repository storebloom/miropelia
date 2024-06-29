<?php
/**
 * Settings panel for game.
 */
$completed_missions = get_user_meta($userid, 'explore_missions', true);
$completed_missions = false === empty($completed_missions) && true === is_array($completed_missions) ? $completed_missions : [];
$current_location = $position ?? get_user_meta($userid, 'current_location', true);
$current_location = false === empty($current_location) ? $current_location : 'foresight';
$linked_mission = [];
$missions_from_cutscenes = [];

$cutscene_missions = get_posts(
    [
        'post_type' => 'explore-cutscene',
        'numberposts' => 100,
        'post_status' => 'publish',
        'meta_query' => [
            [
                'meta_key' => 'explore-mission-cutscene',
                'meta_value' => '',
                'compare' => '!='
            ]
        ]
    ]
);

// Get all the missions from the cutscenes.
foreach($cutscene_missions as $cutscene_mission) {
    $mission_cutscene = get_post_meta($cutscene_mission->ID, 'explore-mission-cutscene', true);

    if (false === empty($mission_cutscene)) {
        $missions_from_cutscenes[] = $mission_cutscene;
    }
}
$missions = get_posts(
    [
        'post_type' => 'explore-mission',
        'numberposts' => 100,
        'post_status' => 'publish',
        'tax_query' => [
            [
                'taxonomy' => 'explore-area-point',
                'field' => 'slug',
                'terms' => $current_location,
            ],
        ],
        'pagename' => $missions_from_cutscenes
    ]
);

// Get all linked missions.
foreach ($missions as $mission)  {
    $linked_mission[$mission->post_name] = get_post_meta($mission->ID, 'explore-next-mission', true);
}
?>
<div class="mission-list">
    <?php foreach($missions as $mission) : ?>
        <?php if (false === in_array($mission->post_name, $completed_missions, true)) :
            $is_next = true === in_array($mission->post_name, array_values($linked_mission), true);
            $parent_mission = array_search($mission->post_name, $linked_mission, true);
            $parent_mission = false === $parent_mission ? '' : $parent_mission;
            $mission_points = get_post_meta($mission->ID, 'value', true);
            $is_cutscene_mission = false !== array_search($mission->post_name, $missions_from_cutscenes, true);
            $mission_blockade = [];
            $mission_blockade['top'] = get_post_meta($mission->ID, 'explore-top', true);
            $mission_blockade['left'] = get_post_meta($mission->ID, 'explore-left', true);
            $mission_blockade['height'] = get_post_meta($mission->ID, 'explore-height', true);
            $mission_blockade['width'] = get_post_meta($mission->ID, 'explore-width', true);
            $mission_blockade = false === in_array('', $mission_blockade, true) ? $mission_blockade : '';
            ?>
            <div
                    class="<?php echo true === in_array($parent_mission, $completed_missions, true) ? 'engage ' : ''; ?><?php echo true === $is_next || true === $is_cutscene_mission ? 'next-mission ' : ''; ?>mission-item <?php echo esc_attr($mission->post_name); ?>-mission-item"
                    data-nextmission="<?php echo $linked_mission[$mission->post_name] ?? esc_attr($linked_mission[$mission->post_name]); ?>"
                    data-points="<?php echo esc_attr($mission_points); ?>"
                    data-blockade="<?php echo false === empty($mission_blockade) ? esc_attr(wp_json_encode($mission_blockade)) : ''; ?>"
            >
            <?php echo esc_html($mission->post_title); ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>