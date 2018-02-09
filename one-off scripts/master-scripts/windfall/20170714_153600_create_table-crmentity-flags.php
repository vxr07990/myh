<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 7/14/2017
 * Time: 3:41 PM
 */

print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$Vtiger_Utils_Log = true;
$flagsTable = 'vtiger_crmentity_flags';
$db = PearDatabase::getInstance();
$result = $db->pquery('SHOW TABLES LIKE ?', [$flagsTable]);
if ($db->num_rows($result) > 0){
    return;
}
$createSQL = "CREATE TABLE `$flagsTable` (
          `crmid` int(11) NOT NULL PRIMARY KEY,
          `in_use` tinyint(1),
          `prevent_edit` tinyint(1),
          `prevent_delete` tinyint(1) 
        );";

$db->pquery($createSQL, array());


