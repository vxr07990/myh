<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

//*/
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
//*/

$moduleEquipment = Vtiger_Module::getInstance('Equipment');

$block254 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleEquipment);
$block391 = Vtiger_Block::getInstance('LBL_EQUIPMENT_FIELDS_TO_REMOVE', $moduleEquipment);
$field1583 = Vtiger_Field::getInstance('createdtime', $moduleEquipment);
$field2689 = Vtiger_Field::getInstance('createdby', $moduleEquipment);
$field1584 = Vtiger_Field::getInstance('modifiedtime', $moduleEquipment);
$field1582 = Vtiger_Field::getInstance('assigned_user_id', $moduleEquipment);
$field1586 = Vtiger_Field::getInstance('date_out', $moduleEquipment);
$field1585 = Vtiger_Field::getInstance('quantity', $moduleEquipment);
$field1589 = Vtiger_Field::getInstance('time_in', $moduleEquipment);
$field1588 = Vtiger_Field::getInstance('date_in', $moduleEquipment);
$field1587 = Vtiger_Field::getInstance('time_out', $moduleEquipment);

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field1583->id." THEN 1 WHEN fieldid=".$field2689->id." THEN 3 WHEN fieldid=".$field1584->id." THEN 2 WHEN fieldid=".$field1582->id." THEN 4 WHEN fieldid=".$field1586->id." THEN 1 WHEN fieldid=".$field1585->id." THEN 3 WHEN fieldid=".$field1589->id." THEN 5 WHEN fieldid=".$field1588->id." THEN 2 WHEN fieldid=".$field1587->id." THEN 4 END, block=CASE WHEN fieldid=".$field1583->id." THEN ".$block254->id." WHEN fieldid=".$field2689->id." THEN ".$block254->id." WHEN fieldid=".$field1584->id." THEN ".$block254->id." WHEN fieldid=".$field1582->id." THEN ".$block254->id." WHEN fieldid=".$field1586->id." THEN ".$block391->id." WHEN fieldid=".$field1585->id." THEN ".$block391->id." WHEN fieldid=".$field1589->id." THEN ".$block391->id." WHEN fieldid=".$field1588->id." THEN ".$block391->id." WHEN fieldid=".$field1587->id." THEN ".$block391->id." END WHERE fieldid IN (".$field1583->id.",".$field2689->id.",".$field1584->id.",".$field1582->id.",".$field1586->id.",".$field1585->id.",".$field1589->id.",".$field1588->id.",".$field1587->id.")");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";