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

/* template settings */
$tpl_settings = array(
    'type' => 'responsive_42', // DO NOT CHANGE THIS SETTING
    'version' => 1.1,
    'name' => 'realty_nova_wide',
    'inventory_menu' => false,
    'category_menu' => false,
    'right_block' => false,
    'long_top_block' => false,
    'featured_price_tag' => true,
    'ffb_list' => false, //field bound boxes plugins list
    'fbb_custom_tpl' => true,
    'header_banner' => true,
    'header_banner_size_hint' => '728x90',
    'home_page_gallery' => false,
    'home_page_load_more_button' => true,
    'autocomplete_tags' => true,
    'category_banner' => false,
    'shopping_cart_use_sidebar' => true,
    'listing_details_anchor_tabs' => true,
    'search_on_map_page' => true,
    'home_page_map_search' => true,
    'browse_add_listing_icon' => false,
    'listing_grid_except_fields' => array('title', 'bedrooms', 'bathrooms', 'square_feet', 'time_frame', 'phone', 'pay_period'),
    'category_dropdown_search' => true,
    'sidebar_sticky_pages' => array('listing_details'),
    'sidebar_restricted_pages' => array('search_on_map'),
    'svg_icon_fill' => true,
    'default_listing_grid_mode' => 'list',
    'listing_grid_mode_only' => false,
    'qtip' => array(
        'background' => '1473cc',
        'b_color'    => '1473cc',
    ),
);

if ( is_object($rlSmarty) ) {
    $rlSmarty -> assign_by_ref('tpl_settings', $tpl_settings);
}

// Insert configs and hooks
if (!isset($config['home_map_search'])) {
    // set phrases
    $reefless->loadClass('Lang');
    $languages = $rlLang->getLanguagesList();
    $tpl_phrases = array(
        array('frontEnd', 'home_page_h3', 'YourDomain.com is known for providing access to fine international estates and property listings.'),
        array('frontEnd', 'pages+h1+home', 'The best way to find your home'),
        array('admin', 'config+name+home_map_search', 'Map search on home page'),
    );

    // insert template phrases
    foreach ($languages as $language) {
        foreach ($tpl_phrases as $tpl_phrase) {
            if (!$rlDb -> getOne('ID', "`Code` = '{$language['Code']}' AND `Key` = '{$tpl_phrase[1]}'", 'lang_keys')) {
                $sql = "INSERT IGNORE INTO `". RL_DBPREFIX ."lang_keys` (`Code`, `Module`, `Key`, `Value`, `Plugin`) VALUES ";
                $sql .= "('{$language['Code']}', '{$tpl_phrase[0]}', '{$tpl_phrase[1]}', '". $rlValid->xSql($tpl_phrase[2])."', 'nova_template');";
                $rlDb -> query($sql);
            }
        }
    }

    // Insert configs
    $insert_setting = array(
        array(
            'Group_ID' => 14,
            'Position' => 10,
            'Key' => 'home_map_search',
            'Default' => '0',
            'Type' => 'bool',
            'Plugin' => 'nova_template'
        )
    );
    $rlDb->insert($insert_setting, 'config');

    // Enable home page h1
    $rlDb->query("UPDATE `{db_prefix}config` SET `Default` = '1' WHERE `Key` = 'home_page_h1' LIMIT 1");
   
    // insert hooks
    $db_prefix = RL_DBPREFIX;
    $sql = <<< MYSQL
INSERT INTO `{db_prefix}hooks` (`Name`, `Plugin`, `Class`, `Code`, `Status`) VALUES
('apTplContentBottom', 'nova_template', '', 'global \$controller, \$config;\r\n\r\nif (\$controller != ''settings'') return;\r\n\r\n\$out = <<< JAVASCRIPT\r\n<script>\r\n(function(){\r\n    var option = function(){\r\n        var row = \$(''#home_map_search_1'').closest(''tr'');\r\n        var name = \$(''select[name=\"post_config[template][value]\"]'').val();\r\n\r\n        if (name == ''realty_nova'') {\r\n            row.show();\r\n        } else {\r\n            row.hide();\r\n        }\r\n    }\r\n\r\n    option();\r\n\r\n    \$(''select[name=\"post_config[template][value]\"]'').change(function(){\r\n        option();\r\n    });\r\n})();\r\n</script>\r\nJAVASCRIPT;\r\n\r\necho \$out;', 'active');
MYSQL;
    $rlDb->query($sql);

    // update page for fetch new hooks in system
    if (defined('REALM') && REALM == 'admin') {
        $reefless->referer();
    }
}
