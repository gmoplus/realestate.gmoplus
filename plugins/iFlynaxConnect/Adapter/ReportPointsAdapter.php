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

/**
 * Class adapter to get Report Listing points
 *
 * @since 3.4.0
 */
class ReportPointsAdapter
{
    /**
     * @var \rlDb
     */
    protected $rlDb;

    /**
     * @var string
     */
    protected $lang;

    /**
     * @var string
     */
    protected $db_table;

    /**
     * @override
     */
    public function __construct($lang = RL_LANG_CODE)
    {
        $this->lang = $lang;
        $this->rlDb = &$GLOBALS['rlDb'];
        $this->db_table = RL_DBPREFIX . 'report_broken_listing_points';
    }

    /**
     * Return all active ordered points
     *
     * @return array|false
     */
    public static function getAllActivePoints()
    {
        $self = new self;
        $point_prefix = 'report_broken_point_';

        $entries = $self->rlDb->getAll("
            SELECT REPLACE(`Key`, '{$point_prefix}', '') AS `Key` FROM `{$self->db_table}` 
            WHERE `Status` = 'active' ORDER BY `Position`
        ");

        $response = array(
            'point_prefix' => $point_prefix,
            'points' => array(),
        );

        $not_available = $GLOBALS['lang']['not_available'] ?: 'N/A';

        foreach ($entries as $entry) {
            $key  = $point_prefix . $entry['Key'];
            $name = $GLOBALS['lang'][$key] ?: $GLOBALS['rlLang']->getPhrase($key, $GLOBALS['config']['lang'], null, true);
            $name = $name ?: $not_available;

            $response['points'][] = array($entry['Key'] => $name);
        }

        return $response;
    }
}
