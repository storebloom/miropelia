<?php
/**
 * Settings panel for game.
 */
$settings = get_user_meta($userid, 'explore_settings', true);
$music = false === empty($settings['music']) ? $settings['music'] : 5;
$sfx = false === empty($settings['sfx']) ? $settings['sfx'] : 5;
?>
<div class="settings-form">
    <span class="close-settings">X</span>
    <h2>Game Settings</h2>
    <label for="music-volume">
        Music Volume
        <input id="music-volume" type="range" min="0" max="10" value="<?php echo esc_attr($music); ?>"/>
    </label>
    <label for="sfx-volume">
        SFX Volume
        <input id="sfx-volume" type="range" min="0" max="10" value="<?php echo esc_attr($sfx); ?>"/>
    </label>
    <button id="update-settings">Save</button>

    <a href="/explore">Leave Game</a>
</div>