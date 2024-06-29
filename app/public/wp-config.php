<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_oqiyhatj7i_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}


define('AUTH_KEY',         '9ZcZarhFXcJ+LIQyKX7LGdn1lLq5hafByAA8BR7pYnr4iwhy+SoBZKYL/tzt1ckHtRdxQP/kbmeXI9DKlzea6Q==');
define('SECURE_AUTH_KEY',  'hd8XlLg5+AbHWDZuBzRCW/S5gKNNVm5L0ufnf+Xfrbn2XtKFyg98wIXMy8k/+6yoYRaups00/Ff3l0jPxPwR4A==');
define('LOGGED_IN_KEY',    'fQWHcwuQ3+mi/Ajily8DK1rOvoI7eCVX1JlJDuXKlS3mFvTduvyE7vCKOPulrJH3mGVhp0ZjmtieL7EPmKSUpQ==');
define('NONCE_KEY',        'D0a4RDPIGRHWG8VlHrx4K3mNnKdNx8D2yceJd3h6U9vbOrsqtz+JgUKTgd79DuaYAzZYJ5WG0C7OUkEzknHOPA==');
define('AUTH_SALT',        '/I0zbvVEhY3D7BulmNgs+4gcytFsspAIv2Pih+ZijF07Lpdnw/EvdHk3cI4Gq8wiTf38tgxSGUzXC+hM8gqh5w==');
define('SECURE_AUTH_SALT', 'PS3+OATwui25VEGD4Gk8EQDQF2d+4EtX7pqbL6P0sK/hs8bqXj75e3BLWnNfuZ2cyQ+QyiASc4e1tykOKGCDuQ==');
define('LOGGED_IN_SALT',   'olJw6c9ZFdmMqh2gGyBdEV2IP5ABBqZ0Wh8bTTwX4T3Kz91Hdh7+2rzdfD/BOYDliERO9N7/AbcZG70sW67vhg==');
define('NONCE_SALT',       'ZGbtOw1xiEAOJOpWoHqUFNNMIwx2AD3T+oV67wh9IvkWjvYFcJ+8NvyBOeGacsf/VRj1b2dsKTI3UjkoSsic1Q==');
define( 'WP_ENVIRONMENT_TYPE', 'local' );
define('REST_API_KEY', 'aG9tb25pYW46QnVyYmFuazQ1MjQzIQ==');

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
