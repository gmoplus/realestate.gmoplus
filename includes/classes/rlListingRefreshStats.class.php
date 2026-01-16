<?php

/******************************************************************************
 *  
 *  PROJECT: Flynax Classifieds Software
 *  VERSION: 4.10.0
 *  LICENSE: RU70XA4A4YQ2 - https://www.flynax.ru/license-agreement.html
 *  PRODUCT: Real Estate Classifieds
 *  DOMAIN: realestate.gmoplus.com
 *  FILE: RLLISTINGREFRESHSTATS.CLASS.PHP
 *  
 *  The software is a commercial product delivered under single, non-exclusive,
 *  non-transferable license for one domain or IP address. Therefore distribution,
 *  sale or transfer of the file in whole or in part without permission of Flynax
 *  respective owners is prohibited.
 *  
 *  CONTACT: www.flynax.com
 *
 ******************************************************************************/

/* listing refresh stats class */
class rlListingRefreshStats
{
    /**
     * class constructor
     **/
    function __construct()
    {
        // empty
    }

    /**
     * Get refresh statistics for a specific account
     *
     * @param int $account_id - Account ID
     * @return array - Refresh statistics
     */
    public function getAccountRefreshStats($account_id)
    {
        global $rlDb;
        
        if (!$account_id) {
            return array(
                'total_refreshes' => 0,
                'this_month_refreshes' => 0,
                'this_week_refreshes' => 0,
                'today_refreshes' => 0,
                'last_refresh_date' => null
            );
        }
        
        $account_id = (int)$account_id;
        
        // Total refreshes
        $sql = "SELECT COUNT(*) FROM `" . RL_DBPREFIX . "listing_refresh_history` 
                WHERE `Account_ID` = {$account_id} AND `Status` = 'success'";
        $total_refreshes = $rlDb->getOne($sql);
        
        // Debug log - Check actual table structure and data
        $tables_exist = $rlDb->getOne("SHOW TABLES LIKE '" . RL_DBPREFIX . "listing_refresh_history'");
        error_log("Refresh Stats Debug - Account ID: {$account_id}, Total: {$total_refreshes}, Table exists: " . ($tables_exist ? 'YES' : 'NO'));
        
        // This month refreshes
        $sql = "SELECT COUNT(*) FROM `" . RL_DBPREFIX . "listing_refresh_history` 
                WHERE `Account_ID` = {$account_id} AND `Status` = 'success' 
                AND MONTH(`Refresh_Date`) = MONTH(NOW()) 
                AND YEAR(`Refresh_Date`) = YEAR(NOW())";
        $this_month_refreshes = $rlDb->getOne($sql);
        
        // This week refreshes
        $sql = "SELECT COUNT(*) FROM `" . RL_DBPREFIX . "listing_refresh_history` 
                WHERE `Account_ID` = {$account_id} AND `Status` = 'success' 
                AND WEEK(`Refresh_Date`) = WEEK(NOW()) 
                AND YEAR(`Refresh_Date`) = YEAR(NOW())";
        $this_week_refreshes = $rlDb->getOne($sql);
        
        // Today refreshes
        $sql = "SELECT COUNT(*) FROM `" . RL_DBPREFIX . "listing_refresh_history` 
                WHERE `Account_ID` = {$account_id} AND `Status` = 'success' 
                AND DATE(`Refresh_Date`) = DATE(NOW())";
        $today_refreshes = $rlDb->getOne($sql);
        
        // Last refresh date
        $sql = "SELECT `Refresh_Date` FROM `" . RL_DBPREFIX . "listing_refresh_history` 
                WHERE `Account_ID` = {$account_id} AND `Status` = 'success' 
                ORDER BY `Refresh_Date` DESC LIMIT 1";
        $last_refresh_date = $rlDb->getOne($sql);
        
        return array(
            'total_refreshes' => (int)$total_refreshes,
            'this_month_refreshes' => (int)$this_month_refreshes,
            'this_week_refreshes' => (int)$this_week_refreshes,
            'today_refreshes' => (int)$today_refreshes,
            'last_refresh_date' => $last_refresh_date
        );
    }
    
    /**
     * Get refresh statistics for a specific listing
     *
     * @param int $listing_id - Listing ID
     * @return array - Listing refresh statistics
     */
    public function getListingRefreshStats($listing_id)
    {
        global $rlDb;
        
        if (!$listing_id) {
            return array(
                'total_refreshes' => 0,
                'last_refresh_date' => null,
                'can_refresh_again' => false,
                'next_refresh_date' => null
            );
        }
        
        $listing_id = (int)$listing_id;
        
        // Total refreshes for this listing
        $sql = "SELECT COUNT(*) FROM `" . RL_DBPREFIX . "listing_refresh_history` 
                WHERE `Listing_ID` = {$listing_id} AND `Status` = 'success'";
        $total_refreshes = $rlDb->getOne($sql);
        
        // Last refresh date
        $sql = "SELECT `Refresh_Date` FROM `" . RL_DBPREFIX . "listing_refresh_history` 
                WHERE `Listing_ID` = {$listing_id} AND `Status` = 'success' 
                ORDER BY `Refresh_Date` DESC LIMIT 1";
        $last_refresh_date = $rlDb->getOne($sql);
        
        // Check if can refresh again (get refresh rules)
        $can_refresh_again = false;
        $next_refresh_date = null;
        
        if ($last_refresh_date) {
            // Get listing type and rules
            $sql = "SELECT l.`Category_ID`, r.`Period_Days`, r.`Max_Refreshes` 
                    FROM `" . RL_DBPREFIX . "listings` l
                    LEFT JOIN `" . RL_DBPREFIX . "listing_refresh_rules` r ON r.`Listing_Type` = 'konut'
                    WHERE l.`ID` = {$listing_id} AND r.`Status` = 'active'
                    LIMIT 1";
            $listing_rule = $rlDb->getRow($sql);
            
            if ($listing_rule && $listing_rule['Period_Days']) {
                // Check recent refreshes within period
                $sql = "SELECT COUNT(*) FROM `" . RL_DBPREFIX . "listing_refresh_history` 
                        WHERE `Listing_ID` = {$listing_id} AND `Status` = 'success' 
                        AND `Refresh_Date` > DATE_SUB(NOW(), INTERVAL {$listing_rule['Period_Days']} DAY)";
                $recent_refreshes = $rlDb->getOne($sql);
                
                $can_refresh_again = $recent_refreshes < $listing_rule['Max_Refreshes'];
                
                if (!$can_refresh_again) {
                    // Calculate next refresh date
                    $sql = "SELECT DATE_ADD('{$last_refresh_date}', INTERVAL {$listing_rule['Period_Days']} DAY) as next_date";
                    $next_refresh_date = $rlDb->getOne($sql);
                }
            }
        } else {
            $can_refresh_again = true; // Never refreshed before
        }
        
        return array(
            'total_refreshes' => (int)$total_refreshes,
            'last_refresh_date' => $last_refresh_date,
            'can_refresh_again' => $can_refresh_again,
            'next_refresh_date' => $next_refresh_date
        );
    }
    
    /**
     * Get refresh rules summary
     *
     * @return array - Active refresh rules
     */
    public function getRefreshRules()
    {
        global $rlDb;
        
        $sql = "SELECT * FROM `" . RL_DBPREFIX . "listing_refresh_rules` 
                WHERE `Status` = 'active' 
                ORDER BY `Listing_Type`";
        
        return $rlDb->getAll($sql);
    }
}

/* EOF */ 