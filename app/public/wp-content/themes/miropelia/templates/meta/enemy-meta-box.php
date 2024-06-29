<?php
/**
 * Meta Box Template
 *
 * The template wrapper for post/page meta box.
 *
 * @package ShareThisShareButtons
 */

?>
<div id="explore-meta-box">
    <p>
        Shooter<br>
        <input class="top" type="radio" value="shooter" name="explore-enemy-type" id="explore-enemy-type" <?php echo checked(true, 'shooter' === $enemy_type); ?>>
        <br>
        Runner<br>
        <input class="top" type="radio" value="runner" name="explore-enemy-type" id="explore-enemy-type" <?php echo checked(true, 'runner' === $enemy_type); ?>>
    </p>
    <p>
        Health<br>
        <input class="top" type="number" name="explore-health" id="explore-health" value="<?php echo intval($health); ?>">
    </p>
    <p>
        Speed<br>
        <input class="top" type="number" name="explore-speed" id="explore-speed" value="<?php echo intval($speed); ?>">
    </p>
    <p>
        Projectile IMG<br>
        <input class="top" type="text" name="explore-projectile[url]" id="explore-projective[url]" value="<?php echo esc_url($proj_url); ?>">
    </p>
    <p>
        Projectile Width<br>
        <input class="top" type="number" name="explore-projectile[width]" id="explore-projectile[width]" value="<?php echo intval($proj_width); ?>">
    </p>
    <p>
        Projectile Height<br>
        <input class="top" type="number" name="explore-projectile[height]" id="explore-projectile[height]" value="<?php echo intval($proj_height); ?>">
    </p>
    <h2>Trigger Fields</h2>
    <p>
        Top<br>
        <input class="top" type="number" name="explore-trigger-top" id="explore-trigger-top" value="<?php echo intval($trigger_top); ?>">
    </p>
    <p>
        Left<br>
        <input class="top" type="number" name="explore-trigger-left" id="explore-trigger-left" value="<?php echo intval($trigger_left); ?>">
    </p>
    <p>
        Height<br>
        <input class="top" type="number" name="explore-trigger-height" id="explore-trigger-height" value="<?php echo intval($trigger_height); ?>">
    </p>
    <p>
        Width<br>
        <input class="top" type="number" name="explore-trigger-width" id="explore-trigger-width" value="<?php echo intval($trigger_width); ?>">
    </p>
</div>
