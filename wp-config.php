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
define( 'DB_NAME', 'ms_gym_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

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
define( 'AUTH_KEY',         'XBLeeT0.N;ZkY)hrFv{]^.NAH.*28h:0%HI2DO;T1n[bGPDgHxul+6?sq W;z[/I' );
define( 'SECURE_AUTH_KEY',  '4@o]I @;ZM|LbY~5TGyL>?.V|DP}tk8P;Y1JF4>bH,a=&N&z0v!4tfnZ>qOX!6G/' );
define( 'LOGGED_IN_KEY',    'p|0QqrfB5i]MfTGp7K1bP_}4C,1ez#Md`xP@Xz}<5|iOyGwP}jGAVfO#>*u`np~&' );
define( 'NONCE_KEY',        '6xdc)]e^6.=M~x&^0o2b~H<Wu1hLH+vH+_)f/#^~ow+ v egmKft{V.)Js&u/Ok:' );
define( 'AUTH_SALT',        'D!k@&hNK^:hhu9o19&>Gl,8+67ZgWl%)D.%ubQs.y5J9ALp[/ef*`B%C7,P@/%h_' );
define( 'SECURE_AUTH_SALT', '{1Wx[q.M9|)cayy;Af&aKe_RK,i 0wxKoY c0JWCRMrXtE3Ocf;m3_obfHl-=heT' );
define( 'LOGGED_IN_SALT',   '][L+-tx5R?y6`j]?9t###H=f)o]-jfg|8Px0/}Fh}pxr#/8!R$7$`t[f/nG~}q{!' );
define( 'NONCE_SALT',       'h}AM|#/xpZrC3@ZQCd@wDS]{ZH3clvYTT,`?57JA*NVPtYn-bAh]Ur]M]j##~j&?' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';


define( 'FS_METHOD', 'direct' );