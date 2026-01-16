<?php

/******************************************************************************
 *  
 *  PROJECT: Flynax Classifieds Software
 *  VERSION: 4.9.3
 *  LICENSE: FL30UFXTM56M - https://www.flynax.com/flynax-software-eula.html
 *  PRODUCT: General Classifieds
 *  DOMAIN: gmowin.online
 *  FILE: RLTESTIMONIALS.CLASS.PHP
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

/* ext js action */
if ($_GET['q'] == 'ext')
{
    /* system config */
    require_once( '../../../includes/config.inc.php' );
    require_once( RL_ADMIN_CONTROL . 'ext_header.inc.php' );
    require_once( RL_LIBS . 'system.lib.php' );

    /* date update */
    if ($_REQUEST['action'] == 'update') {
        $type  = $rlValid->xSql($_REQUEST['type']);
        $field  = $rlValid->xSql($_REQUEST['field']);
        $value = trim($_REQUEST['value'], PHP_EOL);
        $id    = $rlValid->xSql($_REQUEST['id']);
        $key   = $rlValid->xSql($_REQUEST['key']);

        $updateData = array(
            'fields' => array(
                $field => $value
            ),
            'where' => array(
                'ID' => $id
            )
        );

        $rlDb->updateOne($updateData, 'testimonials');

        $reefless->loadClass('Testimonials', null, 'testimonials');
        $rlTestimonials->updateBox();
        exit;
    }

    /* data read */
    $limit = (int)$_GET['limit'];
    $start = (int)$_GET['start'];

    $sql = "SELECT SQL_CALC_FOUND_ROWS `T1`.* ";
    $sql .= "FROM `". RL_DBPREFIX ."testimonials` AS `T1` ";
    $sql .= "ORDER BY `T1`.`Date` DESC ";
    $sql .= "LIMIT {$start}, {$limit}";
    $data = $rlDb -> getAll($sql);

    $count = $rlDb -> getRow("SELECT FOUND_ROWS() AS `testimonials`");

    foreach ($data as $key => $value)
    {
        $data[$key]['Status'] = $lang[$data[$key]['Status']];
    }

    $output['total'] = $count['testimonials'];
    $output['data'] = $data;

    echo json_encode($output);
}
/* ext js action end */
