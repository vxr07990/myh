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


//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<br>begin testhotfix<br>";

$moduleInstance = Vtiger_Module::getInstance('VehicleTransportation');
$new_module = false;

if (!$moduleInstance) {
    echo "module doesn't exist";
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'VehicleTransportation';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $new_module = true;
}

echo "<br>creating blocks...";

$block1 = Vtiger_Block::getInstance('LBL_VEHICLETRANSPORTATION_INFORMATION', $moduleInstance);
if (!$block1) {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_VEHICLETRANSPORTATION_INFORMATION';
    $moduleInstance->addBlock($block1);
}

$block2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);
if (!$block2) {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_CUSTOM_INFORMATION';
    $moduleInstance->addBlock($block2);
}

echo "done!<br> creating fields...";

$field1 = Vtiger_Field::getInstance('agentid', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'Owner';
    $field1->name = 'agentid';
    $field1->table = 'vtiger_crmentity';
    $field1->column = 'agentid';
    $field1->uitype = 1002;
    $field1->typeofdata = 'I~M';

    $block1->addField($field1);
}
$field2 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'Assigned To';
    $field2->name = 'assigned_user_id';
    $field2->table = 'vtiger_crmentity';
    $field2->column = 'smownerid';
    $field2->uitype = 53;
    $field2->typeofdata = 'V~M';

    $block1->addField($field2);
}
$field3 = Vtiger_Field::getInstance('vehicletrans_description', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_VEHICLETRANSPORTATION_DESCRIPTION';
    $field3->name = 'vehicletrans_description';
    $field3->table = 'vtiger_vehicletransportation';
    $field3->column = 'vehicletrans_description';
    $field3->columntype = 'VARCHAR(255)';
    $field3->uitype = 1;
    $field3->typeofdata = 'V~O';

    $block1->addField($field3);
    $moduleInstance->setEntityIdentifier($field3);
}
$field4 = Vtiger_Field::getInstance('vehicletrans_miles', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_VEHICLETRANSPORTATION_MILES';
    $field4->name = 'vehicletrans_miles';
    $field4->table = 'vtiger_vehicletransportation';
    $field4->column = 'vehicletrans_miles';
    $field4->columntype = 'INT(10)';
    $field4->uitype = 7;
    $field4->typeofdata = 'I~O';

    $block1->addField($field4);
}
$field4 = Vtiger_Field::getInstance('vehicletrans_ot', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_VEHICLETRANSPORTATION_OT';
    $field4->name = 'vehicletrans_ot';
    $field4->table = 'vtiger_vehicletransportation';
    $field4->column = 'vehicletrans_ot';
    $field4->columntype = 'VARCHAR(3)';
    $field4->uitype = 56;
    $field4->typeofdata = 'V~O';

    $block1->addField($field4);
}
$field4 = Vtiger_Field::getInstance('vehicletrans_diversions', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_VEHICLETRANSPORTATION_DIVERSIONS';
    $field4->name = 'vehicletrans_diversions';
    $field4->table = 'vtiger_vehicletransportation';
    $field4->column = 'vehicletrans_diversions';
    $field4->columntype = 'INT(10)';
    $field4->uitype = 7;
    $field4->typeofdata = 'I~O';

    $block1->addField($field4);
}
$field4 = Vtiger_Field::getInstance('vehicletrans_oversized', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_VEHICLETRANSPORTATION_OVERSIZED';
    $field4->name = 'vehicletrans_oversized';
    $field4->table = 'vtiger_vehicletransportation';
    $field4->column = 'vehicletrans_oversized';
    $field4->columntype = 'VARCHAR(3)';
    $field4->uitype = 56;
    $field4->typeofdata = 'V~O';

    $block1->addField($field4);
}
$field4 = Vtiger_Field::getInstance('vehicletrans_inoperable', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_VEHICLETRANSPORTATION_INOPERABLE';
    $field4->name = 'vehicletrans_inoperable';
    $field4->table = 'vtiger_vehicletransportation';
    $field4->column = 'vehicletrans_inoperable';
    $field4->columntype = 'VARCHAR(3)';
    $field4->uitype = 56;
    $field4->typeofdata = 'V~O';

    $block1->addField($field4);
}
$field4 = Vtiger_Field::getInstance('vehicletrans_groundclearance', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_VEHICLETRANSPORTATION_GROUNDCLEARANCE';
    $field4->name = 'vehicletrans_groundclearance';
    $field4->table = 'vtiger_vehicletransportation';
    $field4->column = 'vehicletrans_groundclearance';
    $field4->columntype = 'VARCHAR(3)';
    $field4->uitype = 56;
    $field4->typeofdata = 'V~O';

    $block1->addField($field4);
}
$field4 = Vtiger_Field::getInstance('vehicletrans_sitdays', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_VEHICLETRANSPORTATION_SITDAYS';
    $field4->name = 'vehicletrans_sitdays';
    $field4->table = 'vtiger_vehicletransportation';
    $field4->column = 'vehicletrans_sitdays';
    $field4->columntype = 'INT(10)';
    $field4->uitype = 7;
    $field4->typeofdata = 'I~O';

    $block1->addField($field4);
}
$field4 = Vtiger_Field::getInstance('vehicletrans_sitmiles', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_VEHICLETRANSPORTATION_SITMILES';
    $field4->name = 'vehicletrans_sitmiles';
    $field4->table = 'vtiger_vehicletransportation';
    $field4->column = 'vehicletrans_sitmiles';
    $field4->columntype = 'INT(10)';
    $field4->uitype = 7;
    $field4->typeofdata = 'I~O';

    $block1->addField($field4);
}
$field4 = Vtiger_Field::getInstance('vehicletrans_valamount', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_VEHICLETRANSPORTATION_VALAMOUNT';
    $field4->name = 'vehicletrans_valamount';
    $field4->table = 'vtiger_vehicletransportation';
    $field4->column = 'vehicletrans_valamount';
    $field4->columntype = 'INT(10)';
    $field4->uitype = 7;
    $field4->typeofdata = 'I~O';

    $block1->addField($field4);
}
$field8 = Vtiger_Field::getInstance('vehicletrans_relcrmid', $moduleInstance);
if (!$field8) {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_VEHICLETRANSPORTATION_RELCRMID';
    $field8->name = 'vehicletrans_relcrmid';
    $field8->table = 'vtiger_vehicletransportation';
    $field8->column = 'vehicletrans_relcrmid';
    $field8->columntype = 'INT(10)';
    $field8->uitype = 10;
    $field8->typeofdata = 'V~O';
    $block1->addField($field8);
    $field8->setRelatedModules(array('Estimates'));
}

$block1->save($moduleInstance);

if ($new_module) {
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
    $estInstance = Vtiger_Module::getInstance('Estimates');
    $estInstance->setGuestBlocks('VehicleTransportation', ['LBL_VEHICLETRANSPORTATION_INFORMATION']);
}

echo "<br>end testhotfix";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";