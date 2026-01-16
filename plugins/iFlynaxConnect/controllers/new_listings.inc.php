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

$response = array(
    'listings' => array(),
    'calc' => 0
);

// simulate rlListingTypes class
$rlListingTypes = new stdClass;
$rlListingTypes->types = & $iOSHandler->listing_types;

$reefless->loadClass('Listings');
$listings = $rlListings->getRecentlyAdded($stack, $config['iflynax_grid_listings_number'], $type);

if (empty($listings)) {
	$iOSHandler->send($response);
}

$sections_diff = array();
$_sections = array();

foreach ($listings as $key => $entry) {
	// build section
	$date_diff = intval($entry['Date_diff']);
	if (!array_key_exists($date_diff, $sections_diff)) {
		$section_index = count($_sections);
		$sections_diff[$date_diff] = $section_index;

		// init the section
		$_sections[$section_index] = array(
			'title' => $iOSHandler->buildSectionTitleWithDateDiff($date_diff, $entry['Post_date']),
			'rows' => array()
		);
	}

	$section = &$_sections[$sections_diff[$date_diff]];
	$listing = $iOSHandler->adaptShortFormWithData($entry);

	// put row to section
	$section['rows'][] = $listing;
	unset($listing);
}
$response['listings'] = $_sections;
$response['calc'] = intval($rlListings->calc);

// clear memory
unset($listings, $sections_diff, $_sections);

$iOSHandler->send($response);
