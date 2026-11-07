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
define( 'AUTH_KEY',          '(5Q7_]u?,2tbR{,<,n4$&As95I Z?.1dKSi:7C6UUn%V1?4pg^!BZcPmTwZiEDGc' );
define( 'SECURE_AUTH_KEY',   '[%oS{7YOOBAU2d9&ndW.WbxSbl||,woKVO}K*nlkvVteI&uw|$JMh0$~bPUJPk==' );
define( 'LOGGED_IN_KEY',     'xJJ>X5H>OJZ;s#5*(v<<h7Ua%w8~-b[J#KGC!wPnI|[cC@obx|1%W&oCLC^Zw&sF' );
define( 'NONCE_KEY',         '}:tBE(qR>jwv_KO&g/}0,H&W{[w+rd5]ARN7h.V+qSvKBo6fU]H;pTPDKp0K,Zf:' );
define( 'AUTH_SALT',         '?,*j.098Q?,i3it0Z<:/tMw+0x2LF] *AOL8~<oR+(bC@%9AjI#t+hEcpD$TVc!e' );
define( 'SECURE_AUTH_SALT',  ':1zZ[2^D|VUWXIx8Q[H)=1u~Qta~b+ScH XY&NMdre97tA9sQ_!Cksw0;P~Y^T~X' );
define( 'LOGGED_IN_SALT',    'QOOpYhdlb;]&~a[qe!@JHb>;OGx6&Ui,?JdT[~uwzI`wA?AQ>nX598H{5r[ockor' );
define( 'NONCE_SALT',        '1G^^0b|Iz0f$FU;E^2F6#eNNWBzsCdlaQ,7??m udlV!9Zf|,KG){vK:;r#6sNEQ' );
define( 'WP_CACHE_KEY_SALT', 'rcY1NZpcU%TyeSP+U/&]3lVC<@nI(cfrde4uk?R_%Iv&sjt744kNCej/ $1-5cI5' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


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

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
