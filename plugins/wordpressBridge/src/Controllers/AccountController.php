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

/**
 * Class AccountController
 *
 * @since 2.1.0
 *
 * @package Flynax\Plugin\WordPressBridge\Controllers
 */
class AccountController
{
    /**
     * Register new account on Flynax
     */
    public function register()
    {
        if (!$_REQUEST['username'] || !$_REQUEST['mail']) {
            return;
        }

        $rlValid = wbMake('rlValid');
        $rlAccount = wbMake('rlAccount');
        $rlDb = wbMake('rlDb');

        $data = [
            'Username' => $rlValid->xSql($_REQUEST['username']),
            'Password' => wpCryptPassword($_REQUEST['password']),
            'Password_tmp' => $_REQUEST['password'],
            'Mail' => $rlValid->xSql($_REQUEST['mail']),
            'First_name' => $rlValid->xSql($_REQUEST['first_name']),
            'Last_name' => $rlValid->xSql($_REQUEST['last_name']),
            'wp_user_id' => (int) $_REQUEST['wp_user_id'],
            'Date' => 'NOW()',
            'Status' => 'active',
            'Lang' => RL_LANG_CODE,
            'Type' => $GLOBALS['config']['wp_account_type'],
        ];

        $rlDb->insertOne($data, 'accounts');
    }

    /**
     * Delete account on Flynax
     */
    public function delete()
    {
        global $config;

        if (!$config['wp_delete_flynax_account']) {
            return;
        }

        $rlDb = wbMake('rlDb');
        $rlAccount = wbMake('rlAccount');
        $rlAdmin = wbMake('rlAdmin');

        $id = (int) $_REQUEST['wp_user_id'];
        $accountID = $rlDb->getOne('ID', "`wp_user_id`='" . $id . "'", 'accounts');
        $rlAdmin->deleteAccountDetails($accountID, null, true);
    }

    /**
     * Update account information on Flynax
     */
    public function update()
    {
        $rlValid = wbMake('rlValid');
        $rlDb = wbMake('rlDb');
        $id = (int) $_REQUEST['wp_user_id'];

        $update = [
            'fields' => [
                'Mail' => $rlValid->xSql($_REQUEST['user_email']),
                'First_name' => $rlValid->xSql($_REQUEST['first_name']),
                'Last_name' => $rlValid->xSql($_REQUEST['last_name']),
            ],
            'where' => [
                'wp_user_id' => $id,
            ],
        ];

        if ($_REQUEST['password']) {
            $update['fields']['Password'] = wpCryptPassword($_REQUEST['password']);
            $update['fields']['Password_tmp'] = $rlValid->xSql($_REQUEST['password']);
        }

        $rlDb->updateOne($update, 'accounts');
    }

    /**
     * Validate account information on Flynax
     */
    public function validate()
    {
        $rlValid = wbMake('rlValid');
        $rlDb = wbMake('rlDb');
        $id = (int) $_REQUEST['id'];
        $addWhere = '';

        if ($id) {
            $accountInfo = $rlDb->fetch('*', array('wp_user_id' => $id, 'Status' => 'active'), null, null, 'accounts', 'row');
            if ($accountInfo) {
                $addWhere = "AND `Mail` <> '{$accountInfo['Mail']}'";
            }
        }

        $emailExist = $rlDb->fetch(
            array('Mail', 'Status'),
            array('Mail' => $rlValid->xSql($_REQUEST['email'])),
            $addWhere,
            null,
            'accounts',
            'row'
        );

        print(json_encode(['exist' => (bool) $emailExist]));
    }

    /**
     * Change password in account on Flynax
     */
    public function changePassword()
    {
        $rlDb = wbMake('rlDb');
        $id = (int) $_REQUEST['wp_user_id'];

        $update = [
            'fields' => [
                'Password' => wpCryptPassword($_REQUEST['password']),
                'Password_tmp' => $_REQUEST['password'],
            ],
            'where' => [
                'wp_user_id' => $id,
            ],
        ];

        $rlDb->updateOne($update, 'accounts');
    }
}
