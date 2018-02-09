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

//HOTFIX Create AddressSegments Module

//$Vtiger_Utils_Log = true;
require_once('vtlib/Vtiger/Menu.php');
require_once('vtlib/Vtiger/Module.php');
require_once('includes/main/WebUI.php');
require_once('includes/runtime/LanguageHandler.php');

//needs these
require_once('include/Webservices/Create.php');
require_once('modules/Vtiger/uitypes/Date.php');

echo "<br>BEGINNING Create AddressSegments Module<br>";

echo "<br>BEGINNING Creating Module<br>";

$tableConversion = false;
$oldStops = [];
$db = PearDatabase::getInstance();

$moduleInstance = Vtiger_Module::getInstance('AddressSegments');
if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'AddressSegments';
    $moduleInstance->save();
    $moduleInstance->initTables();
}

$blockInstance = Vtiger_Block::getInstance('LBL_ADDRESSSEGMENTS_INFORMATION', $moduleInstance);
if (!$blockInstance) {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_ADDRESSSEGMENTS_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}

$blockInstance2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);
if (!$blockInstance2) {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_CUSTOM_INFORMATION';
    $moduleInstance->addBlock($blockInstance2);
}

$field1 = Vtiger_Field::getInstance('addresssegments_no', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ADDRESSSEGMENTS_NO';
    $field1->name = 'addresssegments_no';
    $field1->table = 'vtiger_addresssegments';
    $field1->column = 'addresssegments_no';
    $field1->columntype = 'VARCHAR(75)';
    $field1->uitype = 4;
    $field1->typeofdata = 'V~M';
    $field1->summaryfield = 0;

    $blockInstance->addField($field1);
    $moduleInstance->setEntityIdentifier($field1);
}

$field2 = Vtiger_Field::getInstance('addresssegments_sequence', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_ADDRESSSEGMENTS_SEQUENCE';
    $field2->name = 'addresssegments_sequence';
    $field2->table = 'vtiger_addresssegments';
    $field2->column = 'addresssegments_sequence';
    $field2->columntype = 'VARCHAR(40)';
    $field2->uitype = 16;
    $field2->typeofdata = 'V~O';
    $field2->summaryfield = 1;

    $blockInstance->addField($field2);
    $field2->setPicklistValues(array(2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16));
}

$field3 = Vtiger_Field::getInstance('addresssegments_origin', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_ADDRESSSEGMENTS_ORIGIN';
    $field3->name = 'addresssegments_origin';
    $field3->table = 'vtiger_addresssegments';
    $field3->column = 'addresssegments_origin';
    $field3->columntype = 'VARCHAR(40)';
    $field3->uitype = 16;
    $field3->typeofdata = 'V~O';

    $blockInstance->addField($field3);
    $field3->setPicklistValues(array('Extra Pickup 1', 'Extra Pickup 2', 'Extra Pickup 3', 'Extra Pickup 4', 'Extra Pickup 5', 'Extra Delivery 1', 'Extra Delivery 2', 'Extra Delivery 3', 'Extra Delivery 4', 'Extra Delivery 5', 'O - SIT', 'D - SIT', 'Self Stg PU', 'Perm Dlv', 'Perm PU', 'Self Stg Dlv', 'Original', 'Destination'));
}

$field4 = Vtiger_Field::getInstance('addresssegments_destination', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_ADDRESSSEGMENTS_DESTINATION';
    $field4->name = 'addresssegments_destination';
    $field4->table = 'vtiger_addresssegments';
    $field4->column = 'addresssegments_destination';
    $field4->columntype = 'VARCHAR(40)';
    $field4->uitype = 16;
    $field4->typeofdata = 'V~O';

    $blockInstance->addField($field4);
    $field4->setPicklistValues(array('Extra Pickup 1', 'Extra Pickup 2', 'Extra Pickup 3', 'Extra Pickup 4', 'Extra Pickup 5', 'Extra Delivery 1', 'Extra Delivery 2', 'Extra Delivery 3', 'Extra Delivery 4', 'Extra Delivery 5', 'O - SIT', 'D - SIT', 'Self Stg PU', 'Perm Dlv', 'Perm PU', 'Self Stg Dlv', 'Original', 'Destination'));
}

$field5 = Vtiger_Field::getInstance('addresssegments_transportation', $moduleInstance);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_ADDRESSSEGMENTS_TRANSPORTATION';
    $field5->name = 'addresssegments_transportation';
    $field5->table = 'vtiger_addresssegments';
    $field5->column = 'addresssegments_transportation';
    $field5->columntype = 'VARCHAR(40)';
    $field5->uitype = 16;
    $field5->typeofdata = 'V~O';

    $blockInstance->addField($field5);
    $field5->setPicklistValues(array('Road', 'Air', 'Perm', 'Sea'));
}

$field6 = Vtiger_Field::getInstance('addresssegments_cube', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_ADDRESSSEGMENTS_CUBE';
    $field6->name = 'addresssegments_cube';
    $field6->table = 'vtiger_addresssegments';
    $field6->column = 'addresssegments_cube';
    $field6->columntype = 'decimal(23,2)';
    $field6->uitype = 7;
    $field6->typeofdata = 'NN~O';

    $blockInstance->addField($field6);
}

$field6 = Vtiger_Field::getInstance('addresssegments_weight', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_ADDRESSSEGMENTS_WEIGHT';
    $field6->name = 'addresssegments_weight';
    $field6->table = 'vtiger_addresssegments';
    $field6->column = 'addresssegments_weight';
    $field6->columntype = 'decimal(23,2)';
    $field6->uitype = 7;
    $field6->typeofdata = 'NN~O';

    $blockInstance->addField($field6);
}

$field6 = Vtiger_Field::getInstance('addresssegments_weightoverride', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_ADDRESSSEGMENTS_WEIGHTOVERRIDE';
    $field6->name = 'addresssegments_weightoverride';
    $field6->table = 'vtiger_addresssegments';
    $field6->column = 'addresssegments_weightoverride';
    $field6->columntype = 'decimal(23,2)';
    $field6->uitype = 7;
    $field6->typeofdata = 'NN~O';

    $blockInstance->addField($field6);
}


$field6 = Vtiger_Field::getInstance('addresssegments_cubeoverride', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_ADDRESSSEGMENTS_CUBEOVERRIDE';
    $field6->name = 'addresssegments_cubeoverride';
    $field6->table = 'vtiger_addresssegments';
    $field6->column = 'addresssegments_cubeoverride';
    $field6->columntype = 'decimal(23,2)';
    $field6->uitype = 7;
    $field6->typeofdata = 'NN~O';

    $blockInstance->addField($field6);
}

$field6 = Vtiger_Field::getInstance('addresssegments_relcrmid', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_ADDRESSSEGMENTS_RELCRMID';
    $field6->name = 'addresssegments_relcrmid';
    $field6->table = 'vtiger_addresssegments';
    $field6->column = 'addresssegments_relcrmid';
    $field6->columntype = 'INT(11)';
    $field6->uitype = 10;
    $field6->typeofdata = 'V~O';

    $blockInstance->addField($field6);
    $field6->setRelatedModules(array('Estimates'));
}

$field6 = Vtiger_Field::getInstance('addresssegments_fromcube', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_ADDRESSSEGMENTS_FROMCUBE';
    $field6->name = 'addresssegments_fromcube';
    $field6->table = 'vtiger_addresssegments';
    $field6->column = 'addresssegments_fromcube';
    $field6->columntype = 'int(3)';
    $field6->uitype = 56;
    $field6->typeofdata = 'C~O';

    $blockInstance->addField($field6);
}

$db->pquery("update `vtiger_tab` set `parent`='' where `tabid`=?", array(getTabid("AddressSegments")));
$db->pquery("update `vtiger_tab` set `parent`='' where `tabid`=?", array(getTabid("AddressSegments")));
// Check entity module
$rs=$adb->query("SELECT * FROM `vtiger_ws_entity` WHERE `name`='AddressSegments'");
if ($adb->num_rows($rs) == 0) {
    $adb->query("INSERT INTO `vtiger_ws_entity` (`name`, `handler_path`, `handler_class`, `ismodule`)
                VALUES ('AddressSegments', 'include/Webservices/VtigerModuleOperation.php', 'VtigerModuleOperation', '1');");
}
echo "<br>COMPLETED: Create AddressSegments Module<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";