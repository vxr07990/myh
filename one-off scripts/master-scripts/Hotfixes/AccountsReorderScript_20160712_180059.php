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

$moduleAccounts = Vtiger_Module::getInstance('Accounts');

$block9 = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $moduleAccounts);
$field1 = Vtiger_Field::getInstance('accountname', $moduleAccounts);
$field2 = Vtiger_Field::getInstance('account_no', $moduleAccounts);
$field3 = Vtiger_Field::getInstance('phone', $moduleAccounts);
$field5 = Vtiger_Field::getInstance('fax', $moduleAccounts);
$field7 = Vtiger_Field::getInstance('otherphone', $moduleAccounts);
$field9 = Vtiger_Field::getInstance('email1', $moduleAccounts);
$field12 = Vtiger_Field::getInstance('ownership', $moduleAccounts);
$field13 = Vtiger_Field::getInstance('rating', $moduleAccounts);
$field15 = Vtiger_Field::getInstance('siccode', $moduleAccounts);
$field17 = Vtiger_Field::getInstance('annual_revenue', $moduleAccounts);
$field19 = Vtiger_Field::getInstance('notify_owner', $moduleAccounts);
$field21 = Vtiger_Field::getInstance('createdtime', $moduleAccounts);
$field726 = Vtiger_Field::getInstance('created_user_id', $moduleAccounts);
$field2459 = Vtiger_Field::getInstance('secondary_phone_type', $moduleAccounts);
$field2464 = Vtiger_Field::getInstance('brand', $moduleAccounts);
$field2271 = Vtiger_Field::getInstance('apn', $moduleAccounts);
$field4 = Vtiger_Field::getInstance('website', $moduleAccounts);
$field6 = Vtiger_Field::getInstance('tickersymbol', $moduleAccounts);
$field8 = Vtiger_Field::getInstance('account_id', $moduleAccounts);
$field10 = Vtiger_Field::getInstance('employees', $moduleAccounts);
$field11 = Vtiger_Field::getInstance('email2', $moduleAccounts);
$field14 = Vtiger_Field::getInstance('industry', $moduleAccounts);
$field16 = Vtiger_Field::getInstance('accounttype', $moduleAccounts);
$field18 = Vtiger_Field::getInstance('emailoptout', $moduleAccounts);
$field20 = Vtiger_Field::getInstance('assigned_user_id', $moduleAccounts);
$field22 = Vtiger_Field::getInstance('modifiedtime', $moduleAccounts);
$field700 = Vtiger_Field::getInstance('isconvertedfromlead', $moduleAccounts);
$field2458 = Vtiger_Field::getInstance('primary_phone_type', $moduleAccounts);
$field1896 = Vtiger_Field::getInstance('agentid', $moduleAccounts);
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET sequence= CASE WHEN fieldid=".$field1->id." THEN 1 WHEN fieldid=".$field2->id." THEN 3 WHEN fieldid=".$field3->id." THEN 5 WHEN fieldid=".$field5->id." THEN 7 WHEN fieldid=".$field7->id." THEN 9 WHEN fieldid=".$field9->id." THEN 11 WHEN fieldid=".$field12->id." THEN 13 WHEN fieldid=".$field13->id." THEN 15 WHEN fieldid=".$field15->id." THEN 17 WHEN fieldid=".$field17->id." THEN 19 WHEN fieldid=".$field19->id." THEN 21 WHEN fieldid=".$field21->id." THEN 23 WHEN fieldid=".$field726->id." THEN 25 WHEN fieldid=".$field2459->id." THEN 27 WHEN fieldid=".$field2464->id." THEN 29 WHEN fieldid=".$field2271->id." THEN 2 WHEN fieldid=".$field4->id." THEN 4 WHEN fieldid=".$field6->id." THEN 6 WHEN fieldid=".$field8->id." THEN 8 WHEN fieldid=".$field10->id." THEN 10 WHEN fieldid=".$field11->id." THEN 12 WHEN fieldid=".$field14->id." THEN 14 WHEN fieldid=".$field16->id." THEN 16 WHEN fieldid=".$field18->id." THEN 18 WHEN fieldid=".$field20->id." THEN 20 WHEN fieldid=".$field22->id." THEN 22 WHEN fieldid=".$field700->id." THEN 24 WHEN fieldid=".$field2458->id." THEN 26 WHEN fieldid=".$field1896->id." THEN 28 END, block=CASE WHEN fieldid=".$field1->id." THEN ".$block9->id." WHEN fieldid=".$field2->id." THEN ".$block9->id." WHEN fieldid=".$field3->id." THEN ".$block9->id." WHEN fieldid=".$field5->id." THEN ".$block9->id." WHEN fieldid=".$field7->id." THEN ".$block9->id." WHEN fieldid=".$field9->id." THEN ".$block9->id." WHEN fieldid=".$field12->id." THEN ".$block9->id." WHEN fieldid=".$field13->id." THEN ".$block9->id." WHEN fieldid=".$field15->id." THEN ".$block9->id." WHEN fieldid=".$field17->id." THEN ".$block9->id." WHEN fieldid=".$field19->id." THEN ".$block9->id." WHEN fieldid=".$field21->id." THEN ".$block9->id." WHEN fieldid=".$field726->id." THEN ".$block9->id." WHEN fieldid=".$field2459->id." THEN ".$block9->id." WHEN fieldid=".$field2464->id." THEN ".$block9->id." WHEN fieldid=".$field2271->id." THEN ".$block9->id." WHEN fieldid=".$field4->id." THEN ".$block9->id." WHEN fieldid=".$field6->id." THEN ".$block9->id." WHEN fieldid=".$field8->id." THEN ".$block9->id." WHEN fieldid=".$field10->id." THEN ".$block9->id." WHEN fieldid=".$field11->id." THEN ".$block9->id." WHEN fieldid=".$field14->id." THEN ".$block9->id." WHEN fieldid=".$field16->id." THEN ".$block9->id." WHEN fieldid=".$field18->id." THEN ".$block9->id." WHEN fieldid=".$field20->id." THEN ".$block9->id." WHEN fieldid=".$field22->id." THEN ".$block9->id." WHEN fieldid=".$field700->id." THEN ".$block9->id." WHEN fieldid=".$field2458->id." THEN ".$block9->id." WHEN fieldid=".$field1896->id." THEN ".$block9->id." END WHERE fieldid IN (".$field1->id.",".$field2->id.",".$field3->id.",".$field5->id.",".$field7->id.",".$field9->id.",".$field12->id.",".$field13->id.",".$field15->id.",".$field17->id.",".$field19->id.",".$field21->id.",".$field726->id.",".$field2459->id.",".$field2464->id.",".$field2271->id.",".$field4->id.",".$field6->id.",".$field8->id.",".$field10->id.",".$field11->id.",".$field14->id.",".$field16->id.",".$field18->id.",".$field20->id.",".$field22->id.",".$field700->id.",".$field2458->id.",".$field1896->id.")");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";