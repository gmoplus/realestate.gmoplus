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

// iphone router paths
define('RL_IPHONE_CONTROLLERS', RL_PLUGINS . 'iFlynaxConnect' . RL_DS . 'controllers' . RL_DS);
define('RL_IPHONE_GATEWAYS', RL_PLUGINS . 'iFlynaxConnect' . RL_DS . 'gateways' . RL_DS);

/**
 * iOS App trigger
 *
 * @since 3.0.0
 */
define('IOS_APP', true);

/**
 * @since 3.0.0 - deprecated
 */
define('APP_USE_GZIP', false);
define('APP_SHORT_FORM_FIELDS_LIMIT', 5);
define('APP_FEATUREDS_FIELDS_LIMIT', 3);

/**
 * @since 3.1.0 - deprecated
 */
define('RL_IPHONE_CLASSES', '');
