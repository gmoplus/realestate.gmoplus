<?php


/******************************************************************************
 *  
 *  PROJECT: Flynax Classifieds Software
 *  VERSION: 4.9.3
 *  LICENSE: FL30UFXTM56M - https://www.flynax.com/flynax-software-eula.html
 *  PRODUCT: General Classifieds
 *  DOMAIN: gmowin.online
 *  FILE: RLIMPORTEXPORTCATEGORIES.CLASS.PHP
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

namespace Flynax\Plugins\ImportExportCategories;

use Flynax\Utils\Valid;

/**
 * Class DatabaseHandler
 * @since 3.0.0
 */
class DatabaseHandler
{
    /**
     * Insert several rows with one request into the Database
     *
     * @param object $db
     * @param array  $data
     * @param string $table
     *
     * @return bool
     */
    public static function insert(object $db, array $data, string $table): bool
    {
        $sql = "INSERT INTO `{db_prefix}{$table}` (";
        foreach ($data[0] as $field => $value) {
            $sql .= "`{$field}`, ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= ') VALUES ' . PHP_EOL;

        foreach ($data as $insert) {
            $sql .= '(';
            foreach ($insert as $value) {
                Valid::escape($value, true);
                $sql .= "'{$value}', ";
            }
            $sql = substr($sql, 0, -2) . '),' . PHP_EOL;
        }
        $sql = substr($sql, 0, -2) . ';';

        return $db->query($sql);
    }
}
