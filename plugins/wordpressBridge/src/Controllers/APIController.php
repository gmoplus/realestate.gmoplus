<?php

/******************************************************************************
 *  
 *  PROJECT: Flynax Classifieds Software
 *  VERSION: 4.9.3
 *  LICENSE: FL30UFXTM56M - https://www.flynax.com/flynax-software-eula.html
 *  PRODUCT: General Classifieds
 *  DOMAIN: gmowin.online
 *  FILE: REQUESTS.PHP
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

namespace Flynax\Plugin\WordPressBridge\Controllers;

use Flynax\Plugin\WordPressBridge\Controller;
use Flynax\Plugin\WordPressBridge\Request;
use Flynax\Plugin\WordPressBridge\Response;
use Flynax\Plugin\WordPressBridge\WordPressAPI\API;
use Flynax\Plugin\WordPressBridge\WordPressAPI\Token;

/**
 * Class APIController
 *
 * @since 2.0.0
 *
 * @package Flynax\Plugin\WordPressBridge\Controllers
 */
class APIController extends Controller
{
    private $wordPressApi = null;

    /**
     * @var \rlActions
     */
    private $rlActions;

    /**
     * APIController constructor.
     */
    public function __construct()
    {
        $this->wordPressApi = new API();
        $this->rlActions = wbMake('rlActions');
    }


    /**
     * Delete tokens of the bridges from Flynax
     */
    public function deleteTokens()
    {
        $flToken = (string) $_REQUEST['fl_token'];
        if (!$this->isTokenValid($flToken)) {
            Response::error('Invalid token', 500);
            return;
        }

        $tokenManager = new Token();
        $tokenManager->clearAllTokens();

        /** @var \rlConfig $rlConfig */
        $rlConfig = wbMake('rlConfig');

        $rlConfig->setConfig('fl_wp_root', '');
        $rlConfig->setConfig('wp_path', '');
    }

    /**
     * Get listings types and return it as JSON
     */
    public function getListingTypes()
    {
        /** @var \rlListingTypes $rlListingsTypes */
        $rlListingsTypes = wbMake('rlListingTypes');

        $listingTypes = array();

        foreach ($rlListingsTypes->types as $key => $type) {
            $listingTypes[] = array(
                'name' => $type['name'],
                'key' => $type['Key'],
            );
        }

        Response::success(array(
            'listing_types' => $listingTypes,
        ), 200);
    }
}
