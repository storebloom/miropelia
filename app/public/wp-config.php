<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'uOeizzE/90N8GP6yyp/QmXHJASN8t53lKuCLiqfF+0eAMU+ME4/PbqH1ZFFq/W/my9LcQRllWZ+SRk96kIH0PQ==');
define('SECURE_AUTH_KEY',  'EBSZo+KKXjbRXOjDubZkCmajmOaR+Nntl+zhvyH7VbHbL9htRmoiGG3cpIzh2DJrjc0KUrzVh0r4dSmqnxXYLw==');
define('LOGGED_IN_KEY',    'L6xbUmAY+kLE413rvGbNJ2+6ObGsIbjVnML5TgDO1SYplpTFXHxqdY8G/Lk9IYC5v6rR0YZImqO3T1qaSC3UJQ==');
define('NONCE_KEY',        'Uk+jcqq6yNYicftPaeT3Y18ZhIrKenM3Ppb9bfqV0S5ArI47IJZTxCRh9bmpIlKAfmjWDurP9N+hzz3XlNLpHw==');
define('AUTH_SALT',        'ebQslGjUT5ZqoegYPLANudEUWq37zTnhNj2RAuZheT5wlzmbmDBBah5Pdt0tU88cZDt/oX2QqZjTE7KEcPF+Ow==');
define('SECURE_AUTH_SALT', 'OGaV9Pe1RAM+zXTsdOx5hhlmE0/FyIp9KxCKqbx/BwxkZSFalN0qumFhYePM1yrWY7Zfzzr7hFCcisOXyXL1xQ==');
define('LOGGED_IN_SALT',   'GxHowloeo05ZIJxbX7pElwkYlxQpXhnXAz+a3aGOqRVPFQXd6cKzp2BX6VVAvCHopbfOcBfcwqRVp5FC7rZjyw==');
define('NONCE_SALT',       'UnXqLt7YZqN4KS66KsPKm1Y1AFk8CrTzGpnI+KZQViCEUB70UZdNBD0gWwEDeNl4eZZylj6T9s3JRcOxD+RJ5A==');
define('REST_API_KEY', 'aG9tb25pYW46QnVyYmFuazQ1MjQzIQ==');
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_oqiyhatj7i_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
