<?php

/******************************************************************************
 *  
 *  PROJECT: Flynax Classifieds Software
 *  VERSION: 4.9.3
 *  LICENSE: FL30UFXTM56M - https://www.flynax.com/flynax-software-eula.html
 *  PRODUCT: Real Estate Classifieds
 *  DOMAIN: gmowin.online
 *  FILE: INDEX.PHP
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

if (defined('IS_LOGIN')) {
    // AJAX Refresh System Handler - EARLY PROCESSING
    if ($_POST['mode'] == 'refreshListing' && $_POST['listing_id']) {
        // Load required classes first
        $reefless->loadClass('Listings');
        $reefless->loadClass('ListingRefresh', 'rlListingRefresh');
        
        header('Content-Type: application/json');
        
        $listing_id = (int)$_POST['listing_id'];
        $listing_type = $_POST['listing_type'] ?: 'konut';
        
        // Get account info from session if not available
        if (!isset($account_info) && isset($_SESSION['account'])) {
            $account_info = $_SESSION['account'];
        }
        
        // Debug log
        error_log("GMO Plus Realestate Refresh DEBUG:");
        error_log("- Listing ID: " . $listing_id);
        error_log("- Listing Type: " . $listing_type);
        error_log("- IS_LOGIN defined: " . (defined('IS_LOGIN') ? 'YES' : 'NO'));
        error_log("- Account info exists: " . (isset($account_info) ? 'YES' : 'NO'));
        error_log("- Session account exists: " . (isset($_SESSION['account']) ? 'YES' : 'NO'));
        
        if (isset($account_info['ID'])) {
            error_log("- Account ID: " . $account_info['ID']);
        } else {
            error_log("- No Account ID found!");
        }
        
        // Get account ID (try from account_info first, then session)
        $current_account_id = isset($account_info['ID']) ? $account_info['ID'] : 
                            (isset($_SESSION['account']['ID']) ? $_SESSION['account']['ID'] : null);
        
        if (!$current_account_id) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Giriş yapmanız gerekiyor'
            ]);
            exit;
        }
        
        error_log("- Using Account ID: " . $current_account_id);
        
        // Get listing details to verify ownership
        $sql = "SELECT `ID`, `Account_ID`, `Listing_type`, `Category_ID`, `Status` 
                FROM `" . RL_DBPREFIX . "listings` 
                WHERE `ID` = {$listing_id} AND `Account_ID` = {$current_account_id} 
                LIMIT 1";
        
        $listing = $rlDb->getRow($sql);
        
        if (!$listing) {
            echo json_encode([
                'status' => 'error',
                'message' => 'İlan bulunamadı veya size ait değil'
            ]);
            exit;
        }
        
        if ($listing['Status'] != 'active') {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Sadece aktif ilanlar yenilenebilir'
            ]);
            exit;
        }
        
        try {
            $refreshSystem = new rlListingRefresh();
            error_log("- rlListingRefresh class loaded successfully");
            
            $refreshResult = $refreshSystem->refreshListing($listing_id, $listing_type);
            error_log("- Refresh result: " . print_r($refreshResult, true));
            
            if ($refreshResult['success']) {
                echo json_encode([
                    'status' => 'success',
                    'message' => $refreshResult['message'],
                    'newDate' => date($config['date_format'], time())
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => $refreshResult['message']
                ]);
            }
        } catch (Exception $e) {
            error_log("- Exception caught: " . $e->getMessage());
            error_log("- Exception trace: " . $e->getTraceAsString());
            echo json_encode([
                'status' => 'error',
                'message' => 'Sistem hatası: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }

    $reefless->loadClass('Listings');
    $reefless->loadClass('Actions');
    $reefless->loadClass('Search');
    $reefless->loadClass('ListingRefresh', 'rlListingRefresh');

    /* register ajax methods */
    $rlXajax->registerFunction(array('deleteListing', $rlListings, 'ajaxDeleteListing'));

    /* define listings type */
    $l_type_key = substr($page_info['Key'], 3);
    $listings_type = $rlListingTypes->types[$l_type_key];

    if ($listings_type) {
        $rlSmarty->assign_by_ref('listings_type', $listings_type);
        $rlSmarty->assign('page_key', 'lt_' . $listings_type['Key']);
    }

    if ($config['one_my_listings_page']) {
        $search_forms = array();

        // get search forms
        foreach ($rlListingTypes->types as $lt_key => $ltype) {
            if ($ltype['Myads_search']) {
                if ($search_form = $rlSearch->buildSearch($lt_key . '_myads')) {
                    $search_forms[$lt_key] = $search_form;
                }

                unset($search_form);
            }
        }

        // define all available listing types & search forms
        $rlSmarty->assign_by_ref('listing_types', $rlListingTypes->types);
        $rlSmarty->assign_by_ref('search_forms', $search_forms);

        // save selected listing type in search
        if ($_POST['search_type'] || $_SESSION['search_type']) {
            if ($_POST['search_type']) {
                $_SESSION['search_type'] = $search_type = $_POST['search_type'];
            } else if ($_SESSION['search_type']
                && (isset($_GET[$search_results_url]) || $_GET['nvar_1'] == $search_results_url)
            ) {
                $_POST['search_type'] = $search_type = $_SESSION['search_type'];
            } else if ($_SESSION['search_type']) {
                // Clear previous search criteria data
                unset(
                    $_SESSION[$_SESSION['search_type'] . '_post'],
                    $_SESSION[$_SESSION['search_type'] . '_pageNum'],
                    $_SESSION['search_type'],
                    $_SESSION['post_form_key']
                );
            }

            if ($_POST['post_form_key']) {
                $_SESSION['post_form_key'] = $_POST['post_form_key'];
            }

            $rlSmarty->assign_by_ref('selected_search_type', $search_type);
            $rlSmarty->assign('refine_search_form', true);
        }
    }

    $add_listing_href = $config['mod_rewrite'] ? SEO_BASE . $pages['add_listing'] . '.html' : RL_URL_HOME . 'index.php?page=' . $pages['add_listing'];
    $rlSmarty->assign_by_ref('add_listing_href', $add_listing_href);

    /* paging info */
    $pInfo['current'] = (int) $_GET['pg'];

    /* fields for sorting */
    $sorting = array(
        'date'        => array(
            'name'  => $lang['date'],
            'field' => "date",
            'Type'  => 'date',
        ),
        'category'    => array(
            'name'  => $lang['category'],
            'field' => 'Category_ID',
        ),
        'status'      => array(
            'name'  => $lang['status'],
            'field' => 'Status',
        ),
        'expire_date' => array(
            'name'  => $lang['expire_date'],
            'field' => 'Plan_expire',
        ),
    );
    $rlSmarty->assign_by_ref('sorting', $sorting);

    /* define sort field */
    $sort_by = empty($_GET['sort_by']) ? $_SESSION['ml_sort_by'] : $_GET['sort_by'];
    $sort_by = $sort_by ? $sort_by : 'date';
    if (!empty($sorting[$sort_by])) {
        $order_field = $sorting[$sort_by]['field'];
    }
    $_SESSION['ml_sort_by'] = $sort_by;
    $rlSmarty->assign_by_ref('sort_by', $sort_by);

    /* define sort type */
    $sort_type = empty($_GET['sort_type']) ? $_SESSION['ml_sort_type'] : $_GET['sort_type'];
    $sort_type = !$sort_type && $sort_by == 'date' ? 'desc' : $sort_type;
    $sort_type = in_array($sort_type, array('asc', 'desc')) ? $sort_type : false;
    $_SESSION['ml_sort_type'] = $sort_type;
    $rlSmarty->assign_by_ref('sort_type', $sort_type);

    $rlHook->load('myListingsPreSelect');

    if ($pInfo['current'] > 1) {
        $bc_page = str_replace('{page}', $pInfo['current'], $lang['title_page_part']);

        // add bread crumbs item
        $bread_crumbs[1]['title'] .= $bc_page;
    }

    $reefless->loadClass('Plan');
    $available_plans = $rlPlan->getPlanByCategory(0, $account_info['Type'], true);
    $rlSmarty->assign_by_ref('available_plans', $available_plans);

    if ($listings_type) {
        $listing_type_key = $listings_type['Key'];
    } else if ($l_type_key == 'all_ads') {
        $listing_type_key = 'all_ads';
    }

    // build search form
    if ($config['one_my_listings_page']
        && ($_POST['search_type']
            || ($_SESSION['search_type']
                && (isset($_GET[$search_results_url]) || $_GET['nvar_1'] == $search_results_url)))
    ) {
        $listing_type_key = $_POST['search_type'] ?: $_SESSION['search_type'];

        if ($_POST['post_form_key'] || $_SESSION['post_form_key']) {
            $form_key = $_POST['post_form_key'] ?: $_SESSION['post_form_key'];
        }
    } else {
        $form_key = $listing_type_key . '_myads';
    }

    $form = false;
    if (($block_keys && array_key_exists('ltma_' . $listing_type_key, $block_keys))
        || $config['one_my_listings_page']
    ) {
        if ($form = $rlSearch->buildSearch($form_key)) {
            if ($listings_type) {
                $rlSmarty->assign('listing_type', $listings_type);
            }

            $rlSmarty->assign('refine_search_form', $form);
        }

        $rlCommon->buildActiveTillPhrases();
    }

    /* search results mode */
    if ($_GET['nvar_1'] == $search_results_url ||
        $_GET['nvar_2'] == $search_results_url ||
        isset($_GET[$search_results_url])
    ) {
        if ($_SESSION[$listing_type_key . '_post'] && $_REQUEST['action'] != 'search') {
            $_POST = $_SESSION[$listing_type_key . '_post'];
        }

        // redirect to My ads page to reset search criteria when type wasn't selected
        if ($config['one_my_listings_page'] && $_POST['action'] == 'search' && !$_POST['search_type']) {
            $reefless->redirect(null, $reefless->getPageUrl('my_all_ads'));
        }

        $rlSmarty->assign('search_results_mode', true);

        $data = $_SESSION[$listing_type_key . '_post'] = $_REQUEST['f']
        ? $_REQUEST['f']
        : $_SESSION[$listing_type_key . '_post'];

        // re-assign POST for refine search block
        if ($_POST['f']) {
            $_POST = $_POST['f'];
        }

        $pInfo['current'] = (int) $_GET['pg'];
        $data['myads_controller'] = true;

        // get current search form
        $rlSearch->getFields($form_key, $listing_type_key);

        // load fields from "quick_" form if "my_" form is empty
        if (!$rlSearch->fields && $config['one_my_listings_page'] && $search_type) {
            $rlSearch->fields = true;
        }

        // get listings
        $listings = $rlSearch->search($data, $listing_type_key, $pInfo['current'], $config['listings_per_page']);
        $rlSmarty->assign_by_ref('listings', $listings);

        $pInfo['calc'] = $rlSearch->calc;
        $rlSmarty->assign('pInfo', $pInfo);

        if ($listings) {
            $page_info['name'] = str_replace('{number}', $pInfo['calc'], $lang['listings_found']);
        } elseif ($_GET['pg']) {
            Flynax\Utils\Util::redirect($reefless->getPageUrl($page_info['Key']));
        }

        $rlHook->load('phpMyAdsSearchMiddle');

        // add bread crumbs item
        $page_info['title'] = $sort_by
        ? str_replace('{field}', $sorting[$sort_by]['name'], $lang['search_results_sorting_mode'])
        : $lang['search_results'];

        if ($pInfo['current']) {
            $page_info['title'] .= str_replace('{page}', $pInfo['current'], $lang['title_page_part']);
        }

        $bread_crumbs[] = array(
            'title' => $page_info['title'],
            'name'  => $lang['search_results'],
        );
    }
    /* browse mode */
    else {
        unset($_SESSION[$listing_type_key . '_post']);

        // get my listings
        $listings = $rlListings->getMyListings($listing_type_key, $order_field, $sort_type, $pInfo['current'], $config['listings_per_page']);
        $rlSmarty->assign('listings', $listings);

        /* redirect to the first page if no listings found */
        if (!$listings && $_GET['pg']) {
            if ($config['mod_rewrite']) {
                $url = SEO_BASE . $page_info['Path'] . ".html";
            } else {
                $url = SEO_BASE . "?page=" . $page_info['Path'];
            }

            header('Location: ' . $url, true, 301);
            exit;
        }
        /* redirect to the first page end */

        $pInfo['calc'] = $rlListings->calc;
        $rlSmarty->assign('pInfo', $pInfo);

        // remove box if necessary
        if (!$form || empty($listings)) {
            $rlCommon->removeSearchInMyAdsBox($listing_type_key);

            // remove all search boxes if access is denied for this user
            if ($listing_type_key == 'all_ads'
                && (isset($account_info['Type'])
                    && in_array($account_info['Type_ID'], explode(',', $page_info['Deny']))
                )
            ) {
                $rlCommon->removeAllSearchInMyAdsBoxes();
            }
        }
    }

    // Save current page number
    if ($_GET['pg']) {
        $_SESSION[$listing_type_key . '_pageNum'] = (int) $_GET['pg'];
    } else {
        unset($_SESSION[$listing_type_key . '_pageNum']);
    }

    // Add refresh capability check to listings
    if (!empty($listings)) {
        $refreshSystem = new rlListingRefresh();
        
        foreach ($listings as $key => $listing) {
            $canRefresh = $refreshSystem->canRefresh($listing['ID'], $listing['Listing_type']);
            $listings[$key]['canRefresh'] = $canRefresh;
        }
        
        $rlSmarty->assign('listings', $listings);
    }

    // GMO Plus: Get refresh statistics for current user
    if (isset($account_info['ID'])) {
        $account_id = $account_info['ID'];
        
        // Total refresh count (use direct table name)
        $sql_total = "SELECT COUNT(*) as total_refreshes 
                      FROM `fl_listing_refresh_history` 
                      WHERE `Account_ID` = {$account_id} AND `Status` = 'success'";
        $total_refreshes = $rlDb->getOne($sql_total);
        
        // This month refresh count
        $sql_month = "SELECT COUNT(*) as month_refreshes 
                      FROM `fl_listing_refresh_history` 
                      WHERE `Account_ID` = {$account_id} AND `Status` = 'success' 
                      AND MONTH(`Refresh_Date`) = MONTH(NOW()) 
                      AND YEAR(`Refresh_Date`) = YEAR(NOW())";
        $month_refreshes = $rlDb->getOne($sql_month);
        
        // This week refresh count
        $sql_week = "SELECT COUNT(*) as week_refreshes 
                     FROM `fl_listing_refresh_history` 
                     WHERE `Account_ID` = {$account_id} AND `Status` = 'success' 
                     AND YEARWEEK(`Refresh_Date`, 1) = YEARWEEK(NOW(), 1)";
        $week_refreshes = $rlDb->getOne($sql_week);
        
        // Today refresh count
        $sql_today = "SELECT COUNT(*) as today_refreshes 
                      FROM `fl_listing_refresh_history` 
                      WHERE `Account_ID` = {$account_id} AND `Status` = 'success' 
                      AND DATE(`Refresh_Date`) = CURDATE()";
        $today_refreshes = $rlDb->getOne($sql_today);
        
        // Assign refresh statistics to template
        $refresh_stats = array(
            'total' => (int)$total_refreshes,
            'month' => (int)$month_refreshes,
            'week' => (int)$week_refreshes,
            'today' => (int)$today_refreshes
        );
        
        $rlSmarty->assign('refresh_stats', $refresh_stats);
    }
} else {
    $rlCommon->removeAllSearchInMyAdsBoxes();
}
