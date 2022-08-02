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
define( 'DB_NAME', 'veevek' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '{!e:Im+1HTp@)h7`|0{X:egl!u1 C^*IPu,$>8WH@pX:<5Z-!o iALPV5$MP8M3c' );
define( 'SECURE_AUTH_KEY',  'zLi<n:>REl4UdJeY^c)LATE!eyW]DiLf(]:O3A0a|<Dx$%#4)>gt*8h^vIToJ5~5' );
define( 'LOGGED_IN_KEY',    'QIWhOK#)a-/Y/FNULz>X3UN@0Me<uzp7pT( sE.1@jZv5W,r<fC%;ICyH0[%QkfG' );
define( 'NONCE_KEY',        'tamnDLyw0(nsEEr>,|{;YkX.D>f;o;FAfP2^2BSVZY~I!Ze)4|,f#)hM.Cm+5,7<' );
define( 'AUTH_SALT',        '(`v6t8]pOL3Y+mjR)n@{Dqi~jX eGI0o`]>`K3(c-%6JII#{OxY%gv6X,e23@dli' );
define( 'SECURE_AUTH_SALT', '.*RB2RSc1>D-77WzpSu +n>,7(U!1K<4apF Jg&_o9adD}hLzJr%)Fv83ucR$o!|' );
define( 'LOGGED_IN_SALT',   'J|^lp?HK~:oO00d[O}w*yho8~2jFy$Mv5p#-.>4cuuGH`?dT#b>-E3_NB^@UL~4R' );
define( 'NONCE_SALT',       '@tBYES0i6I%m~VXogHoT+})b3!Lq[kNIYZRs=7dWgu;udDt5zVFUC*OHi;V)WArU' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'vm_';

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
