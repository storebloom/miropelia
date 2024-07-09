<?php
/**
 * Meta Box Template
 *
 * The template wrapper for post/page meta box.
 *
 * @package ShareThisShareButtons
 */

$post_type = get_post_type();
?>
<div id="explore-meta-box">
    <p>
        Top<br>
        <input class="top" type="number" name="explore-top" id="explore-top" value="<?php echo intval($top); ?>">
    </p>
    <p>
        Left<br>
        <input class="top" type="number" name="explore-left" id="explore-left" value="<?php echo intval($left); ?>">
    </p>
    <p>
        Height<br>
        <input class="top" type="number" name="explore-height" id="explore-height" value="<?php echo intval($height); ?>">
    </p>
    <p>
        Width<br>
        <input class="top" type="number" name="explore-width" id="explore-width" value="<?php echo intval($width); ?>">
    </p>
    <?php if ('explore-area' === $post_type) :  ?>
        <p>
            Starting Top<br>
            <input class="top" type="number" name="explore-start-top" id="explore-start-top" value="<?php echo intval($start_top); ?>">
        </p>
        <p>
            Starting Left<br>
            <input class="top" type="number" name="explore-start-left" id="explore-start-left" value="<?php echo intval($start_left); ?>">
        </p>
        <p>
            Music<br>
            <input class="music" type="text" name="explore-music" id="explore-music" value="<?php echo esc_html($music); ?>">
        </p>
        <p>
            Map SVG<br>
            <input class="map-svg" type="text" name="explore-map-svg" id="explore-map-svg" value="<?php echo esc_html($map); ?>">
        </p>
    <?php endif; ?>
    <p>
        Value<br>
        <input class="top" type="number" name="value" id="value" value="<?php echo intval($value); ?>">
    </p>

    <p>
        Unlock Level<br>
        <input class="top" type="number" name="explore-unlock-level" id="explore-unlock-level" value="<?php echo intval($unlock_level); ?>">
    </p>

    <?php if (false === in_array($post_type, ['explore-character', 'explore-area', 'explore-mission', 'explore-cutscene', 'explore-magic'], true )) :  ?>
        <p>
            Collectable<br>
            <input class="top" type="radio" name="explore-interaction-type" id="explore-collectable" value="collectable" <?php checked('collectable', $interaction_type, true) ?>>
        </p>
        <p>
            Breakable<br>
            <input class="top" type="radio" name="explore-interaction-type" id="explore-breakable" value="breakable" <?php checked('breakable', $interaction_type, true) ?>>
        </p>
        <p>
            Draggable<br>
            <input class="top" type="radio" name="explore-interaction-type" id="explore-draggable" value="draggable" <?php checked('draggable', $interaction_type, true) ?>>
        </p>

        <p>
            None<br>
            <input class="top" type="radio" name="explore-interaction-type" id="explore-draggable" value="" <?php checked('', $interaction_type, true) ?>>
        </p>
        <h2>Draggable Destination</h2>
        <p>
            Top<br>
            <input class="top" type="number" name="explore-drag-dest[top]" id="explore-drag-dest[top]" value="<?php echo intval($drag_dest['top']); ?>">
        </p>
        <p>
            Left<br>
            <input class="top" type="number" name="explore-drag-dest[left]" id="explore-drag-dest[left]" value="<?php echo intval($drag_dest['left']); ?>">
        </p>
        <p>
            Height<br>
            <input class="top" type="number" name="explore-drag-dest[height]" id="explore-drag-dest[height]" value="<?php echo intval($drag_dest['height']); ?>">
        </p>
        <p>
            Width<br>
            <input class="top" type="number" name="explore-drag-dest[width]" id="explore-drag-dest[width]" value="<?php echo intval($drag_dest['width']); ?>">
        </p>
        <p>
            Dest Image<br>
            <input class="top" type="text" name="explore-drag-dest[image]" id="explore-drag-dest[image]" value="<?php echo esc_html($drag_dest['image']); ?>">
        </p>
        <p>
            Mission Complete<br>
            <input class="top" type="text" name="explore-drag-dest[mission]" id="explore-drag-dest[mission]" value="<?php echo esc_html($drag_dest['mission']); ?>">
        </p>
    <?php endif; ?>
    <?php if (true === in_array($post_type, ['explore-character', 'explore-enemy'], true )) :  ?>
    <div class="repeater-container">
        <h2>Walking Path</h2>
        <p>Speed<br>
            <input class="speed" type="number" name="explore-speed" id="explore-speed" value="<?php echo intval($walking_speed); ?>">
        </p>
        <p>
            Repeat<br>
            yes
            <input class="repeat" type="radio" name="explore-repeat" id="explore-repeat" value="yes" <?php checked('yes', $repeat, true); ?>
            <br>
            no
            <input class="repeat" type="radio" name="explore-repeat" id="explore-repeat" value="no" <?php checked('no', $repeat, true); ?>>
        </p>
        <div class="field-container-wrap">
            <?php foreach($walking_paths as $index => $walking_point) : ?>
                <div class="field-container">
                    <span class="container-index"><?php echo esc_html($index); ?></span>
                    <p>
                        Top
                        <br>
                        <input class="top" type="number" data-index="<?php echo esc_attr($index); ?>" name="explore-path[<?php echo esc_attr($index); ?>][top]" id="explore-path[<?php echo esc_attr($index); ?>][top]" value="<?php echo intval($walking_point['top']); ?>">
                    </p>
                    <p>
                        Left
                        <br>
                        <input class="left" type="number" data-index="<?php echo esc_attr($index); ?>" name="explore-path[<?php echo esc_attr($index); ?>][left]" id="explore-path[<?php echo esc_attr($index); ?>][left]" value="<?php echo intval($walking_point['left']); ?>">
                    </p>
                    <div class="remove-field">-</div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="add-field">+</div>

        <h2>Walking Trigger Fields</h2>
        <p>
            Top<br>
            <input class="top" type="number" name="explore-path-trigger[top]" id="explore-path-trigger[top]" value="<?php echo intval($path_trigger['top']); ?>">
        </p>
        <p>
            Left<br>
            <input class="top" type="number" name="explore-path-trigger[left]" id="explore-path-trigger[left]" value="<?php echo intval($path_trigger['left']); ?>">
        </p>
        <p>
            Height<br>
            <input class="top" type="number" name="explore-path-trigger[height]" id="explore-path-trigger[height]" value="<?php echo intval($path_trigger['height']); ?>">
        </p>
        <p>
            Width<br>
            <input class="top" type="number" name="explore-path-trigger[width]" id="explore-path-trigger[width]" value="<?php echo intval($path_trigger['width']); ?>">
        </p>
        <p>
            Point<br>
            <input class="top" type="text" name="explore-path-trigger[point]" id="explore-path-trigger[point]" value="<?php echo esc_html($path_trigger['point']); ?>">
        </p>
        <p>
            Cutscene<br>
            <input class="top" type="text" name="explore-path-trigger[cutscene]" id="explore-path-trigger[cutscene]" value="<?php echo esc_html($path_trigger['cutscene']); ?>">
        </p>
    </div>
    <?php endif; ?>
    <?php if ('explore-cutscene' === $post_type) : ?>
        <h2>Cutscene Trigger Fields</h2>
        <p>
            Top<br>
            <input class="top" type="number" name="explore-cutscene-trigger[top]" id="explore-cutscene-trigger[top]" value="<?php echo intval($cutscene_trigger['top']); ?>">
        </p>
        <p>
            Left<br>
            <input class="top" type="number" name="explore-cutscene-trigger[left]" id="explore-cutscene-trigger[left]" value="<?php echo intval($cutscene_trigger['left']); ?>">
        </p>
        <p>
            Height<br>
            <input class="top" type="number" name="explore-cutscene-trigger[height]" id="explore-cutscene-trigger[height]" value="<?php echo intval($cutscene_trigger['height']); ?>">
        </p>
        <p>
            Width<br>
            <input class="top" type="number" name="explore-cutscene-trigger[width]" id="explore-cutscene-trigger[width]" value="<?php echo intval($cutscene_trigger['width']); ?>">
        </p>

        <h2>Character Position</h2>
        <p>
            Top<br>
            <input class="top" type="number" name="explore-cutscene-character-position[top]" id="explore-cutscene-character-position[top]" value="<?php echo intval($cutscene_character_position['top']); ?>">
        </p>
        <p>
            Left<br>
            <input class="top" type="number" name="explore-cutscene-character-position[left]" id="explore-cutscene-character-position[left]" value="<?php echo intval($cutscene_character_position['left']); ?>">
        </p>
        <p>
            Trigger Point<br>
            Before
            <input class="repeat" type="radio" name="explore-cutscene-character-position[trigger]" id="explore-cutscene-character-position[trigger]" value="before" <?php checked('before', $cutscene_character_position['trigger'], true); ?>
            <br>
            After
            <input class="repeat" type="radio" name="explore-cutscene-character-position[trigger]" id="explore-cutscene-character-position[trigger]" value="after" <?php checked('after', $cutscene_character_position['trigger'], true); ?>>
        </p>

        <h2>Mission</h2>
    <p>
        Mission Name<br>
        <input class="top" type="text" name="explore-mission-cutscene" id="explore-mission-cutscene" value="<?php echo esc_html($mission_cutscene); ?>">
    </p>
        <p>
            Mission Complete Name<br>
            <input class="top" type="text" name="explore-mission-complete-cutscene" id="explore-mission-complete-cutscene" value="<?php echo esc_html($mission_complete_cutscene); ?>">
        </p>
    <?php endif; ?>
    <?php if ('explore-mission' === $post_type) : ?>
        <h2 class="handle" style="padding-left: 0;">Mission Properties</h2>
    <p>
        Next Mission
        <br>

        <input class="top" type="text" name="explore-next-mission" id="explore-next-mission" value="<?php echo esc_html($next_mission); ?>">
    </p>
        <h2>Complete Mission Trigger</h2>
        <p>
            Top<br>
            <input class="top" type="number" name="explore-mission-trigger[top]" id="explore-mission-trigger[top]" value="<?php echo intval($mission_trigger['top']); ?>">
        </p>
        <p>
            Left<br>
            <input class="top" type="number" name="explore-mission-trigger[left]" id="explore-mission-trigger[left]" value="<?php echo intval($mission_trigger['left']); ?>">
        </p>
        <p>
            Height<br>
            <input class="top" type="number" name="explore-mission-trigger[height]" id="explore-mission-trigger[height]" value="<?php echo intval($mission_trigger['height']); ?>">
        </p>
        <p>
            Width<br>
            <input class="top" type="number" name="explore-mission-trigger[width]" id="explore-mission-trigger[width]" value="<?php echo intval($mission_trigger['width']); ?>">
        </p>
    <?php endif; ?>
</div>
