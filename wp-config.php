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
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
$url = parse_url(getenv("DATABASE_URL"));

$host = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$database = substr($url["path"], 1);

/** The name of the database for WordPress */
define( 'DB_NAME', $database );

/** MySQL database username */
define( 'DB_USER', $username );

/** MySQL database password */
define( 'DB_PASSWORD', $password );

/** MySQL hostname */
define( 'DB_HOST', $host );

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
define('AUTH_KEY',         'N6@D2`K`Mbt3BoX++$;s`MrI,h#_kHn6f3VA: )^BO)Q~Tw6e$`}n]X9|z^@6-h9');
define('SECURE_AUTH_KEY',  'x,dqiQ>PDSPofI,qg%uM%[$G%&gdm-2s>O{P3<2ff>|gl~{5]DA+:vkiLW_B,Kr]');
define('LOGGED_IN_KEY',    '^HHQbPMi(;.gE81^cQkSZgp^s.kErbRiE|[+z-?<;gRuR||d]P*GtwF;+CxMU;J+');
define('NONCE_KEY',        'Tts=A3C.s6-K#!U)1w9$dls@z@*=9)Abf=XL[3)hY{]S2u.9/uBh&3>/1pza$4Hr');
define('AUTH_SALT',        '^;C*C}zG.,S-_Ro>5*t<L+p(Q*Ypm3T{xW2liHq5{q2|4unlBw2*)Q7b:+gk5*$i');
define('SECURE_AUTH_SALT', '^yve+0`{7z.+ePAOH!peecq,6UCCGRA(L:gA2of}UxO*z#PSA*si<DL(!~q|%HU%');
define('LOGGED_IN_SALT',   '7(l@eO~dX-Y8SZlckFH=t`N|>]dy7qXqbq)N[-C Z>,?<`()Y+8U$;4%|r `~=-8');
define('NONCE_SALT',       'yW^F?H2n@nj#=_gD! :4~|z<rE!5U4PuESjqTYqG-(Zng5=N&^i)ZlmC_Yu6P[[L');

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */
/** Ensure ssl is detected and responded to appropriately */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    $_SERVER['HTTPS'] = 'on';
}


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
