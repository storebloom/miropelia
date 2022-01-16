<?php
/**
 * Plugin Name: ShareThis Reviews
 * Description: Simple and functional reviews plugin including ratings and impressions.
 * Version: 1.2.0
 * Author: ShareThis
 * Author URI: https://sharethis.com/
 * Text Domain: sharethis-reviews
 * Domain Path: /languages
 * License:     GPL v2 or later

Copyright 2019 ShareThis

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

 * @package ShareThisReviews
 */

namespace ShareThisReviews;

if ( version_compare( phpversion(), '5.3', '>=' ) ) {
	require_once __DIR__ . '/instance.php';
} else {
	if ( defined( 'WP_CLI' ) ) {
		WP_CLI::warning( sharethis_reviews_php_version_text() );
	} else {
		add_action( 'admin_notices', 'sharethis_reviews_php_version_error' );
	}
}

/**
 * Admin notice for incompatible versions of PHP.
 */
function sharethis_reviews_php_version_error() {
	printf( '<div class="error"><p>%s</p></div>', esc_html( sharethis_reviews_php_version_text() ) );
}

/**
 * String describing the minimum PHP version.
 *
 * @return string
 */
function sharethis_reviews_php_version_text() {
	return __( 'ShareThis Reviews plugin error: Your version of PHP is too old to run this plugin. You must be running PHP 5.3 or higher.', 'sharethis-reviews' );
}
