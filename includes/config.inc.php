<?php

/******************************************************************************
 * COOLIFY/DOCKER SSL & SESSION FIX
 * Bu kod Traefik reverse proxy arkasında HTTPS algılaması için gereklidir
 ******************************************************************************/

// Session dizinini /tmp olarak ayarla (container içinde yazılabilir)
ini_set('session.save_path', '/tmp');

// Traefik/Cloudflare proxy arkasında HTTPS algılama
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}

// Alternatif header kontrolü
if (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') {
    $_SERVER['HTTPS'] = 'on';
}

/******************************************************************************
 *
 *  PROJECT: Flynax Classifieds Software
 *  VERSION: 4.9.3
 *  LICENSE: FL30UFXTM56M - https://www.flynax.com/flynax-software-eula.html
 *  PRODUCT: Real Estate Classifieds
 *  DOMAIN: realestate.gmoplus.com
 *
 ******************************************************************************/

/* define system variables */

define('RL_DS', DIRECTORY_SEPARATOR);

//debug manager, set true to enable, false to disable
define('RL_DEBUG', filter_var(getenv('DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN));
define('RL_DB_DEBUG', filter_var(getenv('DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN));
define('RL_MEMORY_DEBUG', false);
define('RL_AJAX_DEBUG', false);

// mysql credentials
define('RL_DBPORT', getenv('DB_PORT') ?: '3306');
define('RL_DBHOST', getenv('DB_HOST') ?: 'localhost');
define('RL_DBUSER', getenv('DB_USER') ?: 'gmoplus_realestateuser');
define('RL_DBPASS', getenv('DB_PASSWORD') ?: getenv('DB_PASS') ?: 'gmoplus_realestateuser1234');
define('RL_DBNAME', getenv('DB_NAME') ?: 'gmoplus_realestate');
define('RL_DBPREFIX', getenv('DB_PREFIX') ?: 'fl_');

// system paths
define('RL_DIR', '');
define('RL_ROOT', dirname(dirname(__FILE__)) . RL_DS . RL_DIR);

define('RL_INC', RL_ROOT . 'includes' . RL_DS);
define('RL_CLASSES', RL_INC . 'classes' . RL_DS);
define('RL_CONTROL', RL_INC . 'controllers' . RL_DS);
define('RL_LIBS', RL_ROOT . 'libs' . RL_DS);
define('RL_TMP', RL_ROOT . 'tmp' . RL_DS);
define('RL_UPLOAD', RL_TMP . 'upload' . RL_DS);
define('RL_FILES', RL_ROOT . 'files' . RL_DS);
define('RL_PLUGINS', RL_ROOT . 'plugins' . RL_DS);
define('RL_CACHE', RL_TMP . 'cache_1893581862' . RL_DS);

// system URLs - trailing slash garantisi
$app_url = getenv('APP_URL') ?: getenv('SITE_URL') ?: 'https://realestate.gmoplus.com';
$app_url = rtrim($app_url, '/') . '/'; // Her zaman trailing slash olsun
define('RL_URL_HOME', $app_url);
define('RL_FILES_URL', RL_URL_HOME . 'files/');
define('RL_LIBS_URL', RL_URL_HOME . 'libs/');
define('RL_PLUGINS_URL', RL_URL_HOME . 'plugins/');

//system admin paths
define('ADMIN', 'admin');
define('ADMIN_DIR', ADMIN . RL_DS);
define('RL_ADMIN', RL_ROOT . ADMIN . RL_DS);
define('RL_ADMIN_CONTROL', RL_ADMIN . 'controllers' . RL_DS);

// Redis / Memcache settings (from user env)
define('RL_REDIS_HOST', getenv('REDIS_HOST') ?: '127.0.0.1');
define('RL_REDIS_PORT', getenv('REDIS_PORT') ?: 6379);
define('RL_REDIS_USER', getenv('REDIS_USER') ?: '');
define('RL_REDIS_PASS', getenv('REDIS_PASSWORD') ?: '');

/* YOU ARE NOT PERMITTED TO MODIFY THE CODE BELOW */
define('RL_SETUP', 'JGxpY2Vuc2VfZG9tYWluID0gInJlYWxlc3RhdGUuZ21vcGx1cy5jb20iOyRsaWNlbnNlX251bWJlciA9ICJGTDMwVUZYVE01Nk0iOw==');
/* END CODE */

/* define system variables end */
