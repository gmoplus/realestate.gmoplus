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

/* load system config */

use Flynax\Utils\Valid;

require_once 'includes' . DIRECTORY_SEPARATOR . 'config.inc.php';

/* system controller */
require_once RL_INC . 'control.inc.php';

// ========== GMO PLUS REFRESH SYSTEM - GLOBAL HANDLER ==========
// Handle refresh AJAX calls before any page processing
if ($_POST['mode'] == 'refreshListing' && $_POST['listing_id']) {
    header('Content-Type: application/json');
    
    try {
        // Start session safely
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $listing_id = (int)$_POST['listing_id'];
        $listing_type = $_POST['listing_type'] ?: 'konut';
        $current_account_id = isset($_SESSION['account']['ID']) ? $_SESSION['account']['ID'] : null;
        
        error_log("GMO Plus Global Refresh Handler:");
        error_log("- Listing ID: " . $listing_id);
        error_log("- Listing Type: " . $listing_type);
        error_log("- Account ID: " . $current_account_id);
        
        if (!$current_account_id) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Giriş yapmanız gerekiyor'
            ]);
            exit;
        }
        
        // Simple database connection (avoid framework dependencies)
        $pdo = new PDO("mysql:host=" . RL_DBHOST . ";dbname=" . RL_DBNAME, RL_DBUSER, RL_DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Verify listing ownership
        $sql = "SELECT `ID`, `Account_ID`, `Category_ID`, `Status` 
                FROM `" . RL_DBPREFIX . "listings` 
                WHERE `ID` = ? AND `Account_ID` = ? 
                LIMIT 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$listing_id, $current_account_id]);
        $listing = $stmt->fetch(PDO::FETCH_ASSOC);
        
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
        
        // Check refresh rules
        $sql = "SELECT * FROM fl_listing_refresh_rules WHERE Listing_Type = ? AND Status = 'active' LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$listing_type]);
        $rule = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$rule) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Bu ilan türü için yenileme kuralı bulunamadı'
            ]);
            exit;
        }
        
        // Check recent refresh history
        $sql = "SELECT COUNT(*) FROM fl_listing_refresh_history 
                WHERE Listing_ID = ? AND Listing_Type = ? 
                AND Refresh_Date > DATE_SUB(NOW(), INTERVAL ? DAY)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$listing_id, $listing_type, $rule['Period_Days']]);
        $recent_count = $stmt->fetchColumn();
        
        if ($recent_count >= $rule['Max_Refreshes']) {
            echo json_encode([
                'status' => 'error',
                'message' => "Bu dönemde maksimum {$rule['Max_Refreshes']} yenileme hakkınız var. Sonraki yenileme: " . 
                           $rule['Period_Days'] . " gün sonra"
            ]);
            exit;
        }
        
        // Perform refresh - Update listing date
        $sql = "UPDATE `" . RL_DBPREFIX . "listings` SET Date = NOW() WHERE ID = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$listing_id]);
        
        if ($result) {
            // Log refresh action
            $sql = "INSERT INTO fl_listing_refresh_history 
                    (Listing_ID, Account_ID, Listing_Type, Category_ID, Refresh_Date, IP, User_Agent, Status) 
                    VALUES (?, ?, ?, ?, NOW(), ?, ?, 'success')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $listing_id, 
                $current_account_id, 
                $listing_type, 
                $listing['Category_ID'],
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['HTTP_USER_AGENT']
            ]);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'İlan başarıyla yenilendi',
                'newDate' => date('d.m.Y H:i', time())
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'İlan yenileme işlemi başarısız'
            ]);
        }
        
    } catch (Exception $e) {
        error_log("Refresh Error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Sistem hatası: ' . $e->getMessage()
        ]);
    }
    
    exit;
}
// ========== GMO PLUS REFRESH SYSTEM END ==========

$reefless->baseUrlRedirect();

/* define is agent */
define('IS_BOT', $reefless->isBot());

$rlHook->load('init');

/* load cache control */
$reefless->loadClass('Cache');

/* load template settings */
$ts_path = RL_ROOT . 'templates' . RL_DS . $config['template'] . RL_DS . 'settings.tpl.php';
if (is_readable($ts_path)) {
    require_once $ts_path;
}

// select all languages
$languages = $rlLang->getLanguagesList();
$rlSmarty->assign_by_ref('languages', $languages);

/* rewrite GET method variables */
$reefless->loadClass('Navigator');
$rlNavigator->transformLinks();
$rlNavigator->rewriteGet($_GET['rlVareables'], $_GET['page'], $_GET['language']);

/* define site languages */
$rlLang->defineLanguage($rlNavigator->cLang);
$rlLang->modifyLanguagesList($languages);
$rlLang->preferredLanguageRedirect($languages);

if ($_GET['page'] == $config['lang']) {
    $sError = true;
}

// Load main types classes
$reefless->loadClass('ListingTypes', null, false, true);
$reefless->loadClass('AccountTypes', null, false, true);

// Define system page
$page_info = $rlNavigator->definePage();

$lang = [];

// Get blocks
$blocks = $rlCommon->getBlocks();
$rlSmarty->assign_by_ref('blocks', $blocks);
$block_keys = $rlCommon->block_keys;

// Get frontEnd phrases
$js_keys = [];
$controller = $page_info['Controller_alt'] ?: $page_info['Controller'];
$lang = array_merge(
    $lang,
    $rlLang->getPhrases(RL_LANG_CODE, $controller, $block_keys ? array_keys($block_keys) : [], $js_keys)
);
$rlSmarty->assign_by_ref('js_keys', $js_keys);
$rlSmarty->assign_by_ref('lang', $lang);

$rlCommon->setNames();

require_once RL_LIBS . 'system.lib.php';

$reefless->setTimeZone();
$reefless->setLocalization();

$rlHook->load('phpBeforeLoginValidation'); // required version >= 4.2

/* check user login */
$reefless->loadClass('Account');

if ($rlAccount->isLogin()) {
    $isLogin = $_SESSION['account']['Full_name'];
    Valid::escapeQuotes($isLogin);
    $rlSmarty->assign('isLogin', $isLogin);
    define('IS_LOGIN', true);

    $account_info = $_SESSION['account'];
    $rlSmarty->assign_by_ref('account_info', $account_info);
}
else {
    $reefless->loginAttempt();
}

/**
 * @since 4.9.0 - Moved from $rlCommon->getBlocks(); method
 */
$rlCommon->defineBlocksExist($blocks);

/* account abilities handler */
$deny_pages = array();
if ($config['one_my_listings_page'] && !$account_info['Abilities']) {
    $deny_pages[] = 'my_all_ads';
}
foreach ($rlListingTypes->types as $listingType) {
    if ($account_info && !in_array($listingType['Key'], $account_info['Abilities'])) {
        array_push($deny_pages, "my_{$listingType['Key']}");
    }

    /* count admin only types */
    $admin_only_types += $listingType['Admin_only'] ? 1 : 0;
}
unset($listingType);

$rlSmarty->assign_by_ref('admin_only_types', $admin_only_types);

if (empty($account_info['Abilities']) || empty($rlListingTypes->types) || $admin_only_types == count($rlListingTypes->types)) {
    array_push($deny_pages, 'add_listing');
    array_push($deny_pages, 'payment_history');
    array_push($deny_pages, 'my_packages');
}

/* assign base path */
$bPath = RL_URL_HOME;
if ($config['lang'] != RL_LANG_CODE && $config['mod_rewrite']) {
    $bPath .= RL_LANG_CODE . '/';
}
if (!$config['mod_rewrite']) {
    $bPath .= 'index.php';
}

$rlHook->load('seoBase');

define('SEO_BASE', $bPath);

$rlSmarty->assign('rlBase', $bPath);
define('RL_TPL_BASE', RL_URL_HOME . 'templates/' . $config['template'] . '/');
$rlSmarty->assign('rlTplBase', RL_TPL_BASE);

// Get all pages keys/paths
$pages = $rlNavigator->getAllPages();
$rlSmarty->assign_by_ref('pages', $pages);

// add system static files
$rlStatic->addSystemFiles();

/* save previous visited page key */
if ($page_info['Key'] != 404) {
    $page_info['prev'] = $_SESSION['page_info']['current'] ? $_SESSION['page_info']['current'] : false;
    $page_info['query_string'] = $_SERVER['QUERY_STRING'];
    $_SESSION['page_info']['current'] = $page_info['Key'];

    /* non .html redirect for single level URL */
    $request_uri = ltrim($_SERVER['REQUEST_URI'], '/');
    if (RL_LANG_CODE != $config['lang']) {
        $request_uri = ltrim($request_uri, RL_LANG_CODE . '/');
    }

    if ($config['mod_rewrite']
        && $page_info['Controller'] != '404'
        && $page_info['Key'] != 'home'
        && (bool) preg_match('/^[^\\/]+\\/$/', $request_uri)
        && (trim($request_uri, "/") == $page_info['Path'] . "/" || trim($request_uri, "/") == $page_info['Path'])
        && !$_GET['rlVareables']
    ) {
        $reefless->redirect(null, SEO_BASE . $page_info['Path'] . '.html');
        exit;
    }
}

$rlHook->load('pageinfoArea');

$rlSmarty->assign_by_ref('pageInfo', $page_info);

if (isset($_GET['wildcard'])) {
    $lang_url_home = str_replace($rlValid->getDomain(RL_URL_HOME), $_SERVER['HTTP_HOST'], RL_URL_HOME);
    $rlSmarty->assign('lang_url_home', $lang_url_home);
} else {
    /* redirect link handler */
    $currentPage = trim($_SERVER['REQUEST_URI'], '/');

    $dir = str_replace(RL_DS, '', RL_DIR);
    $currentPage = ltrim($currentPage, $dir);
    $currentPage = ltrim($currentPage, '/');

    if (!is_numeric(strpos($currentPage, 'index.php'))) {
        if ($config['lang'] != $rlNavigator->cLang) {
            $currentPage = substr($currentPage, 3, strlen($currentPage));
            $currentPage = !(bool) preg_match('/\.html($|\?)/', $currentPage) && $currentPage ? $currentPage . '/' : $currentPage;
        } elseif (strlen($currentPage) == 2 && in_array($currentPage, array_keys($languages))) {
            $currentPage = '';
        } else {
            $currentPage = !(bool) preg_match('/\.html($|\?)/', $currentPage) && $currentPage ? $currentPage . '/' : $currentPage;
        }
    }

    if (!$config['mod_rewrite']) {
        $currentPage = preg_replace('#(\?|&)language=[a-z]{2}#', '', $currentPage);
        $currentPage .= is_numeric(strpos($currentPage, '?')) ? '&' : '?';
    }

    $rlSmarty->assign_by_ref('pageLink', $currentPage);
}

$linkPage = $rlNavigator->cPage == 'index' ? '' : $rlNavigator->cPage;
$rlSmarty->assign_by_ref('page', $linkPage);

/* load common controller */
if ($page_info['Tpl']) {
    require_once RL_CONTROL . 'common.inc.php';
}

/* load page controller */
if ($page_info['Plugin']) {
    require_once RL_PLUGINS . $page_info['Plugin'] . RL_DS . $page_info['Controller'] . '.inc.php';
} else {
    require_once RL_CONTROL . $page_info['Controller'] . '.inc.php';
}

// build featured listing blocks
$rlListings->buildFeaturedBoxes($listing_type_key);

// prepare special content for the home page
if ($page_info['Controller'] == 'home') {
    $rlCommon->homePageSpecialContent();
}

// Get notice
if (isset($_SESSION['notice'])) {
    $reefless->loadClass('Notice');

    $pNotice = $_SESSION['notice'];

    switch ($_SESSION['notice_type']) {
        case 'notice':
            $pType = 'pNotice';
            break;

        case 'alert':
            $pType = 'pAlert';
            break;

        case 'error':
            $pType = 'errors';
            break;
    }
    $rlSmarty->assign_by_ref($pType, $pNotice);

    $rlNotice->resetNotice();
}

/* assign errors */
if (!empty($errors) && !$pType && !$pNotice) {
    $errors = array_unique($errors);
    $rlSmarty->assign_by_ref('errors', $errors);
    $rlSmarty->assign('error_fields', $error_fields);
}

/* ajax process request / get javascripts */
$rlXajax->processRequest();

$ajax_javascripts = $rlXajax->getJavascript();

/* assign ajax javascripts */
$rlSmarty->assign_by_ref('ajaxJavascripts', $ajax_javascripts);

// Define sidebar exists
$rlCommon->defineSidebarExists();
$rlCommon->defineBreadCrumbsExists();

/* load boot hooks */
$rlHook->load('boot');

/* exit in ajax mode */
if ($_REQUEST['xjxfun']) {
    exit;
}

/* print total mysql queries execution time */
if (RL_DB_DEBUG) {
    echo '<br /><br />Total sql queries time: <b>' . $_SESSION['sql_debug_time'] . '</b>.<br />';
}

/* load templates */
if ($page_info['Tpl']) {
    // prepare bread crumbs and title data
    $rlSmarty->assign_by_ref('bread_crumbs', $bread_crumbs);

    $rlCommon->pageMetaTags();

    $page_info['Login'] = !empty($page_info['Deny']) ? 1 : $page_info['Login'];

    $rlSmarty->display('header.tpl');

    if ($page_info['Login'] && !defined('IS_LOGIN')) {
        $page_info['Controller'] = 'login';
        $page_info['Plugin'] = '';
        $page_info['Page_type'] = 'system';
        $rlSmarty->assign('request_page', $page_info['Path']);

        if (!empty($errors) && !$pType && !$pNotice) {
            $rlSmarty->assign('errors', $lang['notice_should_login']);
        }
    } elseif (
           (isset($account_info['Type']) && in_array($account_info['Type_ID'], explode(',', $page_info['Deny'])))
        || (isset($account_info['Abilities'][$page_info['Key']]) && $account_info['Abilities'][$page_info['Key']] === false)
    ) {
        $page_info['Page_type']  = 'static';

        $rlSmarty->assign('request_page', $page_info['Path']);
        $rlSmarty->assign('staticContent', $lang['notice_account_access_deny']);

        if (!$errors && !$pType && !$pNotice) {
            $rlSmarty->assign('errors', $lang['notice_account_access_deny']);
        }
    } elseif ($sError === true) {
        $sql = "SELECT * FROM `{db_prefix}pages` WHERE `Key` = '404' AND `Status` = 'active' LIMIT 1";
        $page_info = $rlDb->getRow($sql);

        require_once RL_CONTROL . $page_info['Controller'] . '.inc.php';
    }

    if ($page_info['Key'] == '404') {
        $rlSmarty->assign_by_ref('errors', $lang['error_404']);
    }

    if ($page_info['Plugin']) {
        $rlSmarty->assign('content', RL_PLUGINS . $page_info['Plugin'] . RL_DS . $page_info['Controller'] . '.tpl');
    } else {
        $rlSmarty->assign('content', 'controllers' . RL_DS . $page_info['Controller'] . '.tpl');
    }

    $rlSmarty->display('content.tpl');
    $rlSmarty->display('footer.tpl');
} else {
    if ($page_info['Login'] && !defined('IS_LOGIN')) {
        $page_info['Controller'] = 'login';
        $page_info['Page_type'] = 'system';

        $rlSmarty->assign('request_page', $page_info['Path']);
        $rlSmarty->assign('errors', $lang['notice_should_login']);
    }

    if ($page_info['Page_type'] == 'system') {
        if ($page_info['Plugin']) {
            $rlSmarty->display(RL_PLUGINS . $page_info['Plugin'] . RL_DS . $page_info['Controller'] . '.tpl');
        } else {
            $rlSmarty->display('controllers' . RL_DS . $page_info['Controller'] . '.tpl');
        }
    } else {
        require_once RL_CONTROL . $page_info['Controller'] . '.inc.php';
        echo $content['Value'];
    }
}

// clear memory (will release ~ 2-3 or more megabytes of memory!)
$rlSmarty->clear_all_assign();

// close the connection with a database
$rlDb->connectionClose();
