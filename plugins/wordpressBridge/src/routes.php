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
/**
 * @since 2.0.0
 */
$route->addGroup('/api/v1', function (\FastRoute\RouteCollector $route) {
    $route->get('/listings', 'ListingsController@index');
    $route->get('/listings/recent', 'ListingsController@getRecent');
    $route->get('/listings/featured', 'ListingsController@getFeatured');
    $route->get('/post/update-cache', 'BlocksController@updateBlocksCache');
    $route->get('/flynax-bridge-uninstall', 'APIController@deleteTokens');
    $route->get('/listing-types', 'APIController@getListingTypes');
    $route->get('/account/register', 'AccountController@register');
    $route->get('/account/delete', 'AccountController@delete');
    $route->get('/account/update', 'AccountController@update');
    $route->get('/account/validate', 'AccountController@validate');
    $route->get('/account/change-password', 'AccountController@changePassword');
});
