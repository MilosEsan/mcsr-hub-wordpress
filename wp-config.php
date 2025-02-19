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
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'mcsr-hub1_wp426' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'qwer1234' );

/** Database hostname */
// define( 'DB_HOST', 'mcsr-hub-rs' ) -> proradilo kada je ova linija popravljena (port pronadjen u gui)
define( 'DB_HOST', '127.0.0.1:20544' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'eudmasxdw5ihb7kvfczc6v3mrgzlenv4yuvpb6z4nutqw1xystqqssldau03j81h' );
define( 'SECURE_AUTH_KEY',  'ztk9w0ws8mvixz1knbnfgtqsc6uttzoqn7a37htzfhyetxnf0q0b85fnyef6kamj' );
define( 'LOGGED_IN_KEY',    'vlakcos6otljibedci8wddegt80yjksz4sybs0haazsrdi60xhodfrzrax0orayf' );
define( 'NONCE_KEY',        'rm8z4kjesepaxrjq8t8inu3hotjpliqh9ct2c95bmik6md619azdq69bi3uqybns' );
define( 'AUTH_SALT',        'yqhjvv0zqn3wl00vsnpgmr5cw0lpsy7pfnvtlwvo18bpdaojrv7b6micppxcmeya' );
define( 'SECURE_AUTH_SALT', 'll7wpgbpx8auacjl9wx1kkgbezkq8k7tdam9u0g8qqt32vtiaexcnvrrcek4imi3' );
define( 'LOGGED_IN_SALT',   'uoe9gxjoqsri2srlskt4mfpbueaeeuilnlcdxpaxgiezeqd296y19saqouzigezn' );
define( 'NONCE_SALT',       'edn4jwnenrbpmjxtnat8is2ug2oa48denyfpbkuvnjvouzqwyvofbjporor2xj8x' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpxv_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
// define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */

define('WP_DEBUG', true); /*promjeni ovu liniju da iskljucis debug mode */
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';


// DEBUG LINES: