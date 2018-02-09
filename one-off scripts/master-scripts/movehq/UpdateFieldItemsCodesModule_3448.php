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
//3448: Item Codes - Add 2 Fields
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;
global $adb;

$moduleInstance = Vtiger_Module::getInstance('ItemCodes');
if (!$moduleInstance) {
    return;
}
$blockInstance = Vtiger_Block::getInstance('LBL_ITEMCODES_DETAILS', $moduleInstance);
if ($blockInstance) {
    $field1 = Vtiger_Field::getInstance('itemcodes_default_revenue_agent', $moduleInstance);
    if ($field1) {
        echo "<br> The itemcodes_default_revenue_agent field already exists in ItemCodes <br>";
        if ($field1->getBlockId() != $blockInstance->id) {
            $adb->pquery("update `vtiger_field` set `block`=? where `fieldid`=? ;", array($blockInstance->id, $field1->id));
        }
    } else {
        $field1 = new Vtiger_Field();
        $field1->label = 'LBL_DEFAULT_REVENUE_AGENT';
        $field1->name = 'itemcodes_default_revenue_agent';
        $field1->table = 'vtiger_itemcodes';
        $field1->column = 'itemcodes_default_revenue_agent';
        $field1->columntype = 'varchar(20)';
        $field1->uitype = 16;
        $field1->typeofdata = 'V~O';
        $field1->quickcreate = 0;
        $field1->summaryfield = 1;

        $blockInstance->addField($field1);
        $field1->setPicklistValues(['Booking Agent', 'Origin Agent', 'Destination Agent', 'Hauling Agent', 'General Office']);
    }


    $field2 = Vtiger_Field::getInstance('itemcodes_appear_on_invoice', $moduleInstance);
    if ($field2) {
        echo "<br> The itemcodes_appear_on_invoice field already exists in ItemCodes <br>";
        if ($field2->getBlockId() != $blockInstance->id) {
            $adb->pquery("update `vtiger_field` set `block`=? where `fieldid`=? ;", array($blockInstance->id, $field2->id));
        }
    } else {
        $field2 = new Vtiger_Field();
        $field2->label = 'LBL_APPEAR_ON_INVOICE';
        $field2->name = 'itemcodes_appear_on_invoice';
        $field2->table = 'vtiger_itemcodes';
        $field2->column = 'itemcodes_appear_on_invoice';
        $field2->columntype = 'varchar(6)';
        $field2->uitype = 16;
        $field2->typeofdata = 'V~O';
        $field2->defaultvalue = 'Yes';
        $field2->quickcreate = 0;
        $field2->summaryfield = 1;

        $blockInstance->addField($field2);
        $field2->setPicklistValues(['Yes', 'No']);
    }

    $data = array(
        1 => "itemcodes_number",
        2 => "itemcodes_description",
        3 => "itemcodes_status",
        4 => "agentid",
        5 => "itemcodes_revenuegroup",
        6 => "itemcodes_default_revenue_agent",
        7 => "itemcodes_igctariff_servicecode",
        8 => "itemcodes_vanlinecode",
        9 => "itemcodes_appear_on_invoice");

    foreach ($data as $key => $val) {
        $query = $adb->pquery("UPDATE `vtiger_field`  SET `sequence` =? WHERE `fieldname` =? AND `block`=? AND tabid=?", array("$key", "$val", $blockInstance->id, $moduleInstance->id));
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";