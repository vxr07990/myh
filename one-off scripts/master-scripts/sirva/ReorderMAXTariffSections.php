<?php
/**
 * Created by PhpStorm.
 * User: DBOlin
 * Date: 3/7/2017
 * Time: 8:50 AM
 */

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = &PearDatabase::getInstance();

$res = $db->pquery('SELECT tariffsectionsid,section_name,tariff_name FROM vtiger_tariffsections
                    INNER JOIN vtiger_tariffs ON (tariffsid=related_tariff)
                    INNER JOIN vtiger_crmentity ON (crmid=tariffsid)
                    WHERE deleted=0 AND tariff_name LIKE ? AND admin_access=?',
                    ['MAX%',1]);
while($res && $row = $res->fetchRow())
{
    $order = 0;
    if($row['section_name'] == 'Packing')
    {
        $order = 1;
    } else if($row['section_name'] == 'Accessorial')
    {
        $order = 2;
    } else if($row['section_name'] == 'Valuation')
    {
        $order = 3;
    }
    $db->pquery('UPDATE vtiger_tariffsections SET tariffsection_sortorder=? WHERE tariffsectionsid=?',
                [$order, $row['tariffsectionsid']]);
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";