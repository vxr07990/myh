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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;

$module = Vtiger_Module::getInstance('TariffServices');
if (!$module) {
    return;
}
$block = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_INFORMATION', $module);
if ($block) {
    echo "<br> The LBL_TARIFFSERVICES_INFORMATION block already exists in TariffServices <br>";
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_TARIFFSERVICES_INFORMATION';
    $module->addBlock($block);
}

//Add Assign to Module Field
$field = Vtiger_Field::getInstance('tariffservices_assigntomodule', $module);
if ($field) {
    echo "<br> The tariffservices_assigntomodule field already exists in TariffServices <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_ASSIGN_TO_MODULE';
    $field->name = 'tariffservices_assigntomodule';
    $field->table = 'vtiger_tariffservices';
    $field->column = 'tariffservices_assigntomodule';
    $field->columntype = 'varchar(100)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->quickcreate = 0;
    $field->summaryfield = 0;

    $block->addField($field);
    $field->setPicklistValues(array('Containers', 'Equipment'));
}

//Add Assign to Record Field
$field = Vtiger_Field::getInstance('tariffservices_assigntorecord', $module);
if ($field) {
    echo "<br> The tariffservices_assigntorecord field already exists in TariffServices <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_ASSIGN_TO_RECORD';
    $field->name = 'tariffservices_assigntorecord';
    $field->table = 'vtiger_tariffservices';
    $field->column = 'tariffservices_assigntorecord';
    $field->columntype = 'INT(11)';
    $field->uitype = 10;
    $field->typeofdata = 'V~O';
    $field->quickcreate = 0;
    $field->summaryfield = 0;

    $block->addField($field);
    $field->setRelatedModules(array('Containers', 'Equipment'));
}

//Add Related To Item Code Field
$field = Vtiger_Field::getInstance('related_itemcodes', $module);
if ($field) {
    echo "<br> The related_itemcodes field already exists in TariffServices <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_TARIFFSERVICES_RELATED_ITEMCODES';
    $field->name = 'related_itemcodes';
    $field->table = 'vtiger_tariffservices';
    $field->column = 'related_itemcodes';
    $field->columntype = 'INT(11)';
    $field->uitype = 10;
    $field->typeofdata = 'V~O';
    $field->quickcreate = 0;
    $field->summaryfield = 0;

    $block->addField($field);
    $field->setRelatedModules(array('ItemCodes'));
}

$data = array(
    1 => "service_name",
    2 => "tariff_section",
    3 => "rate_type",
    4 => "applicability",
    5 => "tariffservices_assigntomodule",
    6 => "tariffservices_assigntorecord",
    7 => "is_required",
    8 => "assigned_user_id",
    9 => "invoiceable",
    10 => "distributable",
    11 => "effective_date",
    12 => "related_tariff"
);

echo "<br> Reorder fields <br>";
foreach ($data as $key => $val) {
    $query = $adb->pquery("UPDATE `vtiger_field`  SET `sequence` =? WHERE `fieldname` =? AND `block`=? AND tabid=?", array("$key", "$val", $block->id, $module->id));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";