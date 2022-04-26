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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'idfood_net_helpdesk_undb' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'Dig@311' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         'vE++;`zQ<K1`cw_l)icrMtlvk`ekwL`<~$%Havd{Dl>|q<V7Aai^?mKxy69!8~<V' );
define( 'SECURE_AUTH_KEY',  'FfZ3Ify;>~^DHej_UTe:(`7q>a:zxULy6E7*P&r7ctckeXs? z{JSfIaunR^uE03' );
define( 'LOGGED_IN_KEY',    'T7aey(J<ZwA$,|x-M2Bm~[$n_6OISS^__8=^0]a1~hgf.xcIFrVHHXJ?3I*;A6P,' );
define( 'NONCE_KEY',        'TVz8N+60M_k}-vtWV+_0w>4Djx,22i;_a(DXYk{OZj9}#$4{rK7F! kZyb&$ee/K' );
define( 'AUTH_SALT',        '<!xREqo}kNOi&PVH>01,bt;rM//zVRp*d7sW5(5`MT&u:%-U7B0# Profsq +[;m' );
define( 'SECURE_AUTH_SALT', ')y_CS%Kvslw,2!E#l@w&72w|ZR/n71]b2v81qW1!r{7NaQMs>r!$*fllSz4}?Yh1' );
define( 'LOGGED_IN_SALT',   '!/LFuhNmUM|dX[`EpHA$P3><}EE$vstU]/wbdK#WqnAi!wT0;;yxOg$w,3E8iz4z' );
define( 'NONCE_SALT',       'qpV,7%l>Mhs+|1Civ1y8RR3PU#6GddkH;g.EKKs 548*H;]8RLE9s)Nm _.q7V`Y' );
define('JWT_AUTH_SECRET_KEY', 'kiA7)gWC_]~b0/CsXfdw:1PrwD(JYz3Og#8> EGBhNf?.R|]-NB-{j;QBtaj^J^');
define('API_BEARER_JWT_SECRET', 'kiA7)gWC_]~b0/CsXfdw:1PrwD(JYz3Og#8> EGBhNf?.R|]-NB-{j;QBtaj^J^');

define('JWT_AUTH_CORS_ENABLE', true);

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'undb_';

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
