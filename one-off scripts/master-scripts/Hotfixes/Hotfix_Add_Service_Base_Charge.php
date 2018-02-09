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



include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once 'includes/main/WebUI.php';

//Insert Service Base Charge option if it does't exist
Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_rate_type` (rate_typeid , rate_type, sortorderid, presence) SELECT id + 1, 'Service Base Charge', id + 1, 1 FROM `vtiger_rate_type_seq` WHERE NOT EXISTS (SELECT * FROM `vtiger_rate_type` WHERE rate_type = 'Service Base Charge')");

Vtiger_Utils::ExecuteQuery("INSERT IGNORE INTO `vtiger_ws_fieldtype` SET `uitype` = 1005, `fieldtype` = 'tariffservice'");

$module = Vtiger_Module::getInstance('TariffServices');

//Block
$block = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_SERVICECHARGE', $module);
if ($block) {
    echo "<h3>The LBL_TARIFFSERVICES_SERVICECHARGE block already exists</h3><br>\n";
} else {
    $block        = new Vtiger_Block();
    $block->label = 'LBL_TARIFFSERVICES_SERVICECHARGE';
    $module->addBlock($block);
}

$field = Vtiger_Field::getInstance('service_base_charge', $module);
if ($field) {
    echo "<br /> The service_base_charge field already exists in Quotes <br />";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_BASESERVICE';
    $field->name = 'service_base_charge';
    $field->table = 'vtiger_tariffservices';
    $field->column = 'service_base_charge';
    $field->columntype = 'DECIMAL(7,2)';
    $field->uitype = 9;
    $field->typeofdata = 'V~O';
    $field->displaytype = 1;
    $field->quickcreate = 0;
    $field->presence = 2;

    $block->addField($field);
}

$field = Vtiger_Field::getInstance('service_base_charge_applies', $module);
if ($field) {
    echo "<br /> The service_base_charge_applies field already exists in Quotes <br />";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_BASESERVICEAPPLIES';
    $field->name = 'service_base_charge_applies';
    $field->table = 'vtiger_tariffservices';
    $field->column = 'service_base_charge_applies';
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 1005;
    $field->typeofdata = 'V~O';
    $field->displaytype = 1;
    $field->quickcreate = 0;
    $field->presence = 2;

    $block->addField($field);
}

echo "<h1>Creating Service charge tabel</h1>";
echo "<ol>";
if (!Vtiger_Utils::CheckTable('vtiger_quotes_servicecharge')) {
    echo "<li>creating vtiger_quotes_servicecharge </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_servicecharge',
                              '(
							    estimateid INT(11),
							    serviceid INT(11),
								rate DECIMAL(12,3)
								)', true);
}
echo "</ol>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";