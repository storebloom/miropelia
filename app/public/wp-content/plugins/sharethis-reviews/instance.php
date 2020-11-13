<?php
/**
 * Instantiates the ShareThis Reviews plugin
 *
 * @package ShareThisReviews
 */

namespace ShareThisReviews;

global $sharethis_reviews_plugin;

require_once __DIR__ . '/php/class-plugin-base.php';
require_once __DIR__ . '/php/class-plugin.php';

$sharethis_reviews_plugin = new Plugin();

/**
 * ShareThis Reviews Plugin Instance
 *
 * @return Plugin
 */
function get_plugin_instance() {
	global $sharethis_reviews_plugin;
	return $sharethis_reviews_plugin;
}
