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

use Flynax\Plugin\WordPressBridge\Response;

/**
 * Class ListingsController
 *
 * @since 2.0.0
 *
 * @package Flynax\Plugin\WordPressBridge\Controllers
 */
class ListingsController
{
    /**
     * Getting all recent listings
     */
    public function getRecent()
    {
        /** @var \rlValid $rlValid */
        $rlValid = wbMake('rlValid');

        $limit = (int) $_REQUEST['limit'];
        $listingType = $rlValid->xSql($_REQUEST['l_type']);

        $result = $GLOBALS['rlListings']->getRecentlyAdded(0, $limit, $listingType);
        $listings = $this->adaptListingsToWordPressFormat($result);

        Response::json($listings);
    }

    /**
     * Getting featured listings
     */
    public function getFeatured()
    {
        /** @var \rlListings $rlListings */
        $rlListings = wbMake('rlListings');
        /** @var \rlValid $rlValid */
        $rlValid = wbMake('rlValid');

        $limit = (int) $_REQUEST['limit'];
        $listingType = $rlValid->xSql($_REQUEST['l_type']);

        $listings = $this->adaptListingsToWordPressFormat(
            $rlListings->getFeatured($listingType, $limit)
        );

        Response::json($listings);
    }

    /**
     * Adapt Flynax generated listings into Flynax-bridge readable format
     *
     * @param array $listings
     *
     * @return array
     */
    private function adaptListingsToWordPressFormat($listings)
    {
        if (!$listings) {
            return array();
        }

        $adaptedListings = array();
        foreach ($listings as $listing) {
            $fields = implode(', ', array_filter(array_column($listing['fields'], 'value')));

            $adaptedListings[$listing['ID']] = array(
                'title' => $listing['listing_title'],
                'url' => $listing['url'],
                'fields' => $fields,
                'img' => $listing['Main_photo'] ? RL_FILES_URL . $listing['Main_photo'] : null,
                'img_x2' => $listing['Main_photo_x2'] ? RL_FILES_URL . $listing['Main_photo_x2'] : null,
            );
        }

        return $adaptedListings;
    }
}
