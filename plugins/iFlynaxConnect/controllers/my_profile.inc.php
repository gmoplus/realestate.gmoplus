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

$account_id = intval($_REQUEST['id']);
$type       = $rlValid->xSql($_REQUEST['type']);
$tablet     = intval($_REQUEST['tablet']);
$action     = $_REQUEST['action'];
$response   = array();

switch ($action) {
    case 'change_password':
        $old_pass = $_REQUEST['old_pass'];
        $new_pass = $_REQUEST['new_pass'];
        $response = $iOSHandler->changeAccountPassword($account_id, $old_pass, $new_pass);
        break;

    case 'upload_image':
        $account_id = intval($account_info['ID']);

        $response = $iOSHandler->uploadProfileImage($account_id);
        break;

    case 'profile_info':
        $response = $iOSHandler->fetchUserShortInfo($account_id);
        break;

    case 'profileForm':
        $response = $iOSHandler->getProfileForm($type, $account_id);
        break;

    case 'updateProfile':
        $form_data = $_REQUEST['f'];

        $response = $iOSHandler->updateMyProfile($form_data);
        break;

    case 'updateProfileEmail':
        $account_id = intval($account_info['ID']);
        $new_email = $rlValid->xSql($_REQUEST['email']);

        $response = $iOSHandler->updateProfileEmail($account_id, $new_email);
        break;

    case 'deleteAccount':
        $account_id = (int) $account_info['ID'];
        $password = \Flynax\Utils\Valid::escape($_REQUEST['password']);

        $response = $iOSHandler->deleteAccount($account_id, $password);
        break;
}

// send response to iOS device
$iOSHandler->send($response);
