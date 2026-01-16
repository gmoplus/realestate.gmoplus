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

$stack = intval($_REQUEST['stack']);
$tablet = intval($_REQUEST['tablet']);
$type = $rlValid->xSql($_REQUEST['type']);
$action = $_REQUEST['cmd'];

$response = array(
    'listings' => array(),
    'calc' => 0
);

switch ($action) {
    case 'fetch':
        $reefless->loadClass('Listings');
        $listings = $rlListings->getMyListings($type, 'Date', 'DESC', $stack, $config['iflynax_grid_listings_number']);

        foreach ($listings as $key => $entry) {
            $response['listings'][] = $iOSHandler->adaptShortFormWithData($entry, true);
        }
        unset($listings);

        $response['calc'] = intval($rlListings->calc);
        break;

    case 'remove':
        
        break;
}

$iOSHandler->send($response);
