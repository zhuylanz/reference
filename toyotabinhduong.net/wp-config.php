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
define('DB_NAME', 'toyotabd');

/** MySQL database username */
define('DB_USER', 'toyotabd');

/** MySQL database password */
define('DB_PASSWORD', 'toyotabd@2018');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '.&z]}51|Kd/Z(f$QBL-~N`WyP=I*z(4pP O I`6!;gLI==sn>#1zFM[}Km$%a4r:');
define('SECURE_AUTH_KEY',  'XLRn.6r^?wSxycd9WzQM~c9{J*LJ ;n%1dv;Y@UB(C+y&>!)$?@P(J7Tq667Sh!Z');
define('LOGGED_IN_KEY',    '2(BAa3:K{g:jMe8(IlK6#@n iJ&<8b q:cr.4*|Ge`^GTRMeAI`VW,chV(R=jX0m');
define('NONCE_KEY',        'c1WZd4EP(DZ%iP$Z:0}LbrqZL,~JImId:~frX_cP%-mVS1+y PIy#E?X~1){[6rZ');
define('AUTH_SALT',        '_}>2X927Pq%BuANWzn8pL#WLujV2GjF26eJ~^9(@1GN79!.IK_%O>mvX~?5P33X?');
define('SECURE_AUTH_SALT', 'UZ><p!?j{>E>_n,~.&`2NR>[R;O6S{`_V^]3K1(R&G k2<L2t?,lIlA|)LLpYyus');
define('LOGGED_IN_SALT',   'WB2SFTnV?@1NUGD Y$eCK6;]Ur$Rh2R48IG+;rAo/[LmQwtHerD=jBdRgs@jE9EU');
define('NONCE_SALT',       'V+~zCFLke38<0}*@..e~c,gXA~}!oSf-7wUKBd7R8pj=EsEs|9%;`TBn43hN1$j7');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
