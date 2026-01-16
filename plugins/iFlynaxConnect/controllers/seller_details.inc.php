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

$account_id = intval($_REQUEST['aid']);

// fetch seller info
$response['sellerInfo'] = $iOSHandler->fetchSellerInfo($account_id);

// fetch a first stack of seller ads
$response['sellerAds'] = $iOSHandler->getListingsByAccount($account_id);

// reassign location details
if (isset($response['sellerInfo']['location'])) {
    $response['location'] = $response['sellerInfo']['location'];
    unset($response['sellerInfo']['location']);
}

// send response to iOS device
$iOSHandler->send($response);
