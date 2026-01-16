<?php

/******************************************************************************
 *  
 *  PROJECT: Flynax Classifieds Software
 *  VERSION: 4.9.3
 *  LICENSE: FL30UFXTM56M - https://www.flynax.com/flynax-software-eula.html
 *  PRODUCT: General Classifieds
 *  DOMAIN: gmowin.online
 *  FILE: RLUNDERCONSTRUCTION.CLASS.PHP
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

class rlUnderConstruction
{
    /**
     * Plugin installer
     * @since 3.1.0
     */
    public function install()
    {
        $sql = "
            UPDATE `" . RL_DBPREFIX . "config` 
            SET `Default` = DATE(DATE_ADD(NOW(), INTERVAL 1 MONTH)) 
            WHERE `Key` = 'under_constructions_date' LIMIT 1
        ";
        $GLOBALS['rlDb']->query($sql);
    }

    /**
     * @hook boot
     *
     * @since 3.1.0
     */
    public function hookBoot()
    {
        global $config, $reefless, $rlSmarty, $rlDb;

        $ips = explode(';', $config['under_constructions_ip']);
        $ip = $reefless->getClientIpAddress();

        $file = $config['under_constructions_file'];
        $date = strtotime($config['under_constructions_date']);

        $rlSmarty->assign('date', $date);

        if (!in_array($ip, $ips) && time() <= $date) {
            // Massmailer support
            $mm_version = $GLOBALS['plugins']['massmailer_newsletter'];
            $legacy_version = version_compare($mm_version, '3.0.0') < 0 ? true : false;

            $rlSmarty->assign('legacy_version', $legacy_version);

            if ($mm_version && $legacy_version) {
                $reefless->loadClass('MassmailerNewsletter', null, 'massmailer_newsletter');
                $GLOBALS['rlXajax']->registerFunction(
                    array('subscribe', $GLOBALS['rlMassmailerNewsletter'], 'ajaxSubscribe')
                );
            }

            // Show under construction interface
            if (!empty($file) && file_exists(RL_ROOT . $file)) {
                echo file_get_contents(RL_ROOT . $file);
            } else {
                $rlSmarty->display(RL_PLUGINS . 'underConstructions' . RL_DS .'content.tpl');
            }
            exit;
        }
    }
}
