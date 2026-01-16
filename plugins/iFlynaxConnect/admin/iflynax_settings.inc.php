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

if ($_POST['submit']) {
    $post_config = isset($_POST['post_config']) ? $_POST['post_config'] : $_POST['config'];
    $update = array();

    foreach ($post_config as $key => $entry) {
        if ($entry['d_type'] == 'int') {
            $entry['value'] = (int) $entry['value'];
        }
        $rlValid->sql($entry['value']);

        $row['where']['Key'] = $key;
        $row['fields']['Default'] = $entry['value'];
        $update[] = $row;
    }

    $reefless->loadClass('Actions');

    if ($rlActions->update($update, 'config')) {
        $reefless->loadClass('Notice');

        $aUrl = array('controller' => $controller);
        $rlNotice->saveNotice($lang['config_saved']);
        $reefless->redirect($aUrl);
    }
}

$group_id = (int) $rlDb->getOne('ID', "`Key` = 'iFlynaxConnect'", 'config_groups');

// Get missing config phrases
if (method_exists($rlLang, 'preparePhrases')) {
    $config_phrases = (array) $rlLang->preparePhrases(
        "WHERE `Plugin` = 'iFlynaxConnect' AND `Target_key` = 'settings' AND `Code` = '" . RL_LANG_CODE . "'"
    );
    $lang = array_merge($lang, $config_phrases);
}

// Get all configs
$configsLsit = $rlDb->fetch('*', array('Group_ID' => $group_id), "ORDER BY `Position`", null, 'config');
$configsLsit = $rlLang->replaceLangKeys($configsLsit, 'config', array('name', 'des'), RL_LANG_CODE, 'admin');
$rlAdmin->mixSpecialConfigs($configsLsit);

$rlSmarty->assign_by_ref('configs', $configsLsit);
