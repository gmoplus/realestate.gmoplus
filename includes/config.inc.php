<?php
/******************************************************************************
 *  DOMAIN: realestate.gmoplus.com
 ******************************************************************************/

/* define system variables */
define('RL_DS', DIRECTORY_SEPARATOR);

// Debug Modu
define('RL_DEBUG', filter_var(getenv('DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN));
define('RL_DB_DEBUG', filter_var(getenv('DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN));
define('RL_MEMORY_DEBUG', false);
define('RL_AJAX_DEBUG', false);

// Veritabaný Baðlantýsý (Sizin standartlarýnýza uygun)
define('RL_DBPORT', getenv('DB_PORT') ?: '3306');
define('RL_DBHOST', getenv('DB_HOST') ?: 'localhost');
define('RL_DBUSER', getenv('DB_USER') ?: 'gmoplus_realestateuser');
// Hem DB_PASSWORD hem DB_PASS desteklensin
define('RL_DBPASS', getenv('DB_PASSWORD') ?: getenv('DB_PASS'));
define('RL_DBNAME', getenv('DB_NAME') ?: 'gmoplus_realestate');
define('RL_DBPREFIX', getenv('DB_PREFIX') ?: 'fl_');

// Sistem Yollarý
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

// Site URL (APP_URL veya SITE_URL destekler)
define('RL_URL_HOME', getenv('APP_URL') ?: getenv('SITE_URL') ?: 'https://realestate.gmoplus.com/');
define('RL_FILES_URL', RL_URL_HOME . 'files/');
define('RL_LIBS_URL', RL_URL_HOME . 'libs/');
define('RL_PLUGINS_URL', RL_URL_HOME . 'plugins/');
