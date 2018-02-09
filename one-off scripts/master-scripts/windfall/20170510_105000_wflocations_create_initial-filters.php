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
error_reporting(E_ERROR);

require_once('vtlib/Vtiger/Menu.php');
require_once('vtlib/Vtiger/Module.php');
require_once('includes/main/WebUI.php');
require_once('includes/runtime/LanguageHandler.php');
global $adb;

$locationInstance = Vtiger_Module::getInstance('WFLocations');
if ($locationInstance) {
    $field1 = Vtiger_Field::getInstance('wflocation_type', $locationInstance);
    $field2 = Vtiger_Field::getInstance('tag', $locationInstance);
    $field3 = Vtiger_Field::getInstance('wfslot_configuration', $locationInstance);
    $field4 = Vtiger_Field::getInstance('wflocation_base', $locationInstance);
    $field5 = Vtiger_Field::getInstance('squarefeet', $locationInstance);
    $field6 = Vtiger_Field::getInstance('offsite', $locationInstance);
    $field7 = Vtiger_Field::getInstance('reserved', $locationInstance);
    $field8 = Vtiger_Field::getInstance('percentused', $locationInstance);
    $activeField = Vtiger_Field::getInstance('active', $locationInstance);

    echo "<br> Add All Active Locations Filter For Locations Module";
    $filter1 = Vtiger_Filter::getInstance('All Active Locations', $locationInstance);
    if ($filter1) {
        echo "<br> Filter exists";
    } else {
        $filter1 = new Vtiger_Filter();
        $filter1->name = "All Active Locations";
        $filter1->isdefault = false;
        $locationInstance->addFilter($filter1);
        $filter1->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3)->addField($field5, 4)->addField($field6, 5)->addField($field7, 6)->addField($field8, 7);
        $filter1->addRule($activeField, 'EQUALS', 1, '1');
        Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1,$filter1->id, 'and', '0 and 1')");
        Vtiger_Utils::ExecuteQuery("UPDATE vtiger_customview SET status=0 WHERE cvid=$filter1->id");
    }

    echo "<br> Add All Inactive Locations Filter For Locations Module";
    $filter2 = Vtiger_Filter::getInstance('All Inactive Locations', $locationInstance);
    if ($filter2) {
        echo "<br> Filter exists";
    } else {
        $filter2 = new Vtiger_Filter();
        $filter2->name = "All Inactive Locations";
        $filter2->isdefault = false;
        $locationInstance->addFilter($filter2);
        $filter2->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3)->addField($field5, 4)->addField($field6, 5)->addField($field7, 6)->addField($field8, 7);
        $filter2->addRule($activeField, 'EQUALS', 0, '1');
        Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_cvadvfilter_grouping` (`groupid`, `cvid`, `group_condition`, `condition_expression`) VALUES ( 1,$filter2->id, 'and', '0 and 1')");
        Vtiger_Utils::ExecuteQuery("UPDATE vtiger_customview SET status=0 WHERE cvid=$filter2->id");
    }

    echo "<br> Add All Locations Filter For Locations Module";
    $filter3 = Vtiger_Filter::getInstance('All', $locationInstance);
    if ($filter3) $filter3->delete();
    $filter3 = new Vtiger_Filter();
    $filter3->name = "All";
    $filter3->isdefault = true;
    $locationInstance->addFilter($filter3);
    $filter3->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3)->addField($field5, 4)->addField($field6, 5)->addField($field7, 6)->addField($field8, 7);
}
