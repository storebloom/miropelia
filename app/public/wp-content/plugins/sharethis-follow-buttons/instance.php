<?php
/**
 * Instantiates the Sharethis Follow Buttons plugin
 *
 * @package SharethisFollowButtons
 */

namespace SharethisFollowButtons;

global $sharethis_follow_buttons_plugin;

require_once __DIR__ . '/php/class-plugin-base.php';
require_once __DIR__ . '/php/class-plugin.php';

$sharethis_follow_buttons_plugin = new Plugin();

/**
 * Sharethis Follow Buttons Plugin Instance
 *
 * @return Plugin
 */
function get_plugin_instance() {
	global $sharethis_follow_buttons_plugin;
	return $sharethis_follow_buttons_plugin;
}
