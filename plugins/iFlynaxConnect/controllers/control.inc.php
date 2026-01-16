<?php

/******************************************************************************
 *  
 *  PROJECT: Flynax Classifieds Software
 *  VERSION: 4.9.3
 *  LICENSE: FL30UFXTM56M - https://www.flynax.com/flynax-software-eula.html
 *  PRODUCT: General Classifieds
 *  DOMAIN: gmowin.online
 *  FILE: LISTINGPICTUREUPLOADADAPTER.PHP
 *  
 *  The software is a commercial product delivered under single, non-exclusive,
 *  non-transferable license for one domain or IP address. Therefore distribution,
 *  sale or transfer of the file in whole or in part without permission of Flynax
 *  respective owners is considered to be illegal and breach of Flynax License End
 *  User Agreement.
 *  
 *  You are not allowed to remove this information from the file without permission
 *  of Flynax respective owners.
 *  
 *  Flynax Classifieds Software 2024 | All copyrights reserved.
 *  
 *  https://www.flynax.com
 ******************************************************************************/

require_once RL_CLASSES . 'rlDb.class.php';

// connect to database
$rlDb = new rlDb;
$rlDb->connect(RL_DBHOST, RL_DBPORT, RL_DBUSER, RL_DBPASS, RL_DBNAME);

// The plugin installed and active; let's go!
session_start();

// init website main class
require_once RL_CLASSES . 'reefless.class.php';
$reefless = new reefless;

/* Emulate smarty class */
class Smarty
{
    public function __call($name, $arguments)
    {}
}
require_once RL_CLASSES . 'rlSmarty.class.php';
class FakeSmarty extends rlSmarty {
    public function __construct()
    {}
}
$rlSmarty = new FakeSmarty;
/* Emulate smarty class END */

// load helper classes
$reefless->loadClass('Debug');
$reefless->loadClass('Config');
$reefless->loadClass('Common');
$reefless->loadClass('Lang');
$reefless->loadClass('Valid');
$reefless->loadClass('Hook');
$reefless->loadClass('Account');
$reefless->loadClass('Cache');

// init app main class
require_once RL_PLUGINS . 'iFlynaxConnect' . RL_DS . 'rlIFlynaxConnect.class.php';
$iOSHandler = new rlIFlynaxConnect;

// utf8 library functions if necessary
if (function_exists('loadUTF8functions') === false) {
    function loadUTF8functions()
    {
        $names = func_get_args();
        if (empty($names)) {
            return false;
        }

        foreach ($names as $name) {
            if (file_exists(RL_LIBS . 'utf8' . RL_DS . 'utils' . RL_DS . $name . '.php')) {
                require_once RL_LIBS . 'utf8' . RL_DS . 'utils' . RL_DS . $name . '.php';
            }
        }
    }
}
