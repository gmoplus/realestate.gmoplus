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
$ids = $rlValid->xSql($_REQUEST['ids']);
$action = $_REQUEST['action'] ? $_REQUEST['action'] : 'fetch';
$listing_id = intval($_REQUEST['lid']);
$account_id = intval($account_info['ID']);
$response = array();

$_COOKIE['favorites'] = $ids;

$reefless->loadClass('Listings');
$reefless->loadClass('Actions');

switch ($action) {
    case 'fetch':
        $response = array(
            'listings' => array(),
            'calc' => 0,
        );
        $listings = $rlListings->getMyFavorite('ID', 'ASC', $stack, $config['iflynax_grid_listings_number']);

        if (!empty($listings)) {
            foreach($listings as $key => $entry) {
                $response['listings'][] = $iOSHandler->adaptShortFormWithData($entry);
            }
            $response['calc'] = intval($rlListings->calc);

            // clear memory
            unset($listings);
        }
        break;

    case 'add':
        $iOSHandler->addToFavorites($listing_id, $account_id);
        break;

    case 'remove':
        $iOSHandler->removeFromFavorites($listing_id, $account_id);
        break;
}

// send response to iOS device
$iOSHandler->send($response);
