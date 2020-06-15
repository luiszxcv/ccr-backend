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
define( 'DB_NAME', 'yNUFSe0FYVujwH' );

/** MySQL database username */
define( 'DB_USER', 'yNUFSe0FYVujwH' );

/** MySQL database password */
define( 'DB_PASSWORD', 'cLeKv4Dwmsstij' );

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
define( 'AUTH_KEY',          'qhat;CPHx{eJ%4gXS2OX#uehD1#k][ @Ky5~*TR}gG<l5ct==bE_s}7.NOjp_:03' );
define( 'SECURE_AUTH_KEY',   '9{.tpd>E<]*3P!tn~X%9P]Vf_786zc(XT#Wn$m3*(wBfVK;$X+q.[&~E4#xRy;C!' );
define( 'LOGGED_IN_KEY',     'oqiP@]t<l,Y HUbDGG)+})=?z%d@jQJQ,2yOm2`wZ:tqN3>gyfK<llmNJN#zUhdb' );
define( 'NONCE_KEY',         '8!yILO]Xu2<xX }8&NY$%<-frA(McR*FTfKtQswy[jKp]!UT50PI s(Lb;F[OyYK' );
define( 'AUTH_SALT',         'G Bnz]IEcHqJ]&J(tzIP3{-_gK<_*X1/35cWQyG64tE0]J9_1sbLl|4|=h(F,TrS' );
define( 'SECURE_AUTH_SALT',  'N(r_}n 2S*pA_1!q]IG9tdR>ZkPx7nAG6N?eu|;/ j-h8(Rg7K7XO;.iHl`sl+fK' );
define( 'LOGGED_IN_SALT',    'E&CGGSr8mccGvOlH~}_t]P$-o[H4SZVrJ Dlv2`0NW <F}<nP.RtLFWKl>^=rDc[' );
define( 'NONCE_SALT',        '{|jNJ=GOo.)nq8xwi+m[:!y10^28M@TUDp}.F^aDK]/ubQO1F9<}=xP@kPs&n#h:' );
define( 'WP_CACHE_KEY_SALT', '9?Zt gJyf@)?[ ~~is)]yq:_px[:T-r!,<GiRt?XM|I%{]#*V(()x{/+q~28],!^' );

define('JWT_AUTH_SECRET_KEY', 'm,1xawdawdadghedrhgfth$wI<9,va4wd65a65GR1?');
define('JWT_AUTH_CORS_ENABLE', true);
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
