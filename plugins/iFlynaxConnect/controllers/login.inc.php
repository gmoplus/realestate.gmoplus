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

$response = array();
$action = $_REQUEST['action'];
$push_token = $_REQUEST['push_token'];

switch ($action) {
    case 'login':
    case 'fb_login':
        if (!$rlAccount->isLogin()) {
            $username = $_REQUEST['username'];
            $password = $_REQUEST['password'];
            $login_permitted = true;
            $direct = false;

            // prepare login details if the facebook login process
            if ($action == 'fb_login') {
                $facebook_id = intval($_REQUEST['fid']);
                $facebook_email = $_REQUEST['email'];
                $login_permitted = false;

                $mapping = array(
                    'fields' => array('Username', 'Mail', 'Password', 'facebook_ID'),
                    'where'  => array('facebook_ID' => $facebook_id)
                );
                $a_where = $_REQUEST['verified'] ? "OR `Mail` = '{$facebook_email}' " : '';
                $account = $rlDb->fetch($mapping['fields'], $mapping['where'], $a_where, null, 'accounts', 'row');

                // login
                if (!empty($account)) {
                    $match_field = $config['account_login_mode'] == 'email' ? 'Mail' : 'Username';
                    $username = $account[$match_field];
                    $password = $account['Password'];
                    $login_permitted = $direct = true;

                    if (!$account['facebook_ID']) {
                        $rlDb->query("
                            UPDATE `{db_prefix}accounts` SET `facebook_ID` = {$facebook_id} 
                            WHERE `{$match_field}` = '{$username}' LIMIT 1
                        ");
                    }
                }
                // quick registration
                else {
                    $username = trim($_REQUEST['first_name'] . ' ' . $_REQUEST['last_name']);

                    $response = $iOSHandler->registration(array(
                        'fid' => $_REQUEST['fid'],
                        'email' => $_REQUEST['email'],
                        'username' => !empty($username) ? $username : 'username',
                        'password' => $reefless->generateHash(8),
                        'fb_verified' => $_REQUEST['verified'],
                        'first_name' => $_REQUEST['first_name'],
                        'last_name' => $_REQUEST['last_name'],
                    ));
                }
            }

            if ($login_permitted) {
                $response = $iOSHandler->login($username, $password, $direct, $push_token);
            }
        }
        // resume session
        else {
            $response = array(
                'logged' => true,
                'token'  => $_SESSION['account'][$iOSHandler->aTokenField],
            );

            // get user short info
            $response += $iOSHandler->fetchUserShortInfo($account_info['ID']);
        }
        break;

    case 'logout':
        $clearSession = array('id', 'username', 'password', 'type', 'type_id', 'abilities', 'account');

        foreach ($clearSession as $key) {
            unset($_SESSION[$key]);
        }

        $reefless->eraseCookie('favorites');
        unset($_COOKIE['favorites']);

        if (isset($plugins['hybridAuthLogin']) && $config['remember_me'] && !empty($_COOKIE['rmc'])) {
            $selector = explode(':', $_COOKIE['rmc'])[0];
            $rlDb->query("DELETE FROM `{db_prefix}auth_tokens` WHERE `Selector` = '{$selector}'");
            $reefless->eraseCookie('rmc');
        }

        // unregister for remote notifications 
        $iOSHandler->unregisterForRemoteNotifications($push_token);

        $response['unlogged'] = true;
        $response['message'] = $lang['notice_logged_out'];
        break;

    case 'remind':

        break;
}

// send response to iOS device
$iOSHandler->send($response);
