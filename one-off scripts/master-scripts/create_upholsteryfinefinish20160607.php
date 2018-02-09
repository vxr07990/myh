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

echo "<br>begin create module script for UpholsteryFineFinish<br>";

$moduleInstance = Vtiger_Module::getInstance('UpholsteryFineFinish');
$new_module = false;

if (!$moduleInstance) {
    echo "module doesn't exist";
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'UpholsteryFineFinish';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $new_module = true;
}

echo "<br>creating blocks...";

$block1 = Vtiger_Block::getInstance('LBL_UPHOLSTERYFINEFINISH_INFORMATION', $moduleInstance);
if (!$block1) {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_UPHOLSTERYFINEFINISH_INFORMATION';
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
$field3 = Vtiger_Field::getInstance('uff_description', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_UPHOLSTERYFINEFINISH_DESCRIPTION';
    $field3->name = 'uff_description';
    $field3->table = 'vtiger_upholsteryfinefinish';
    $field3->column = 'uff_description';
    $field3->columntype = 'VARCHAR(255)';
    $field3->uitype = 1;
    $field3->typeofdata = 'V~O';

    $block1->addField($field3);
    $moduleInstance->setEntityIdentifier($field3);
}
$field4 = Vtiger_Field::getInstance('uff_numpieces', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_UPHOLSTERYFINEFINISH_NUMPIECES';
    $field4->name = 'uff_numpieces';
    $field4->table = 'vtiger_upholsteryfinefinish';
    $field4->column = 'uff_numpieces';
    $field4->columntype = 'INT(10)';
    $field4->uitype = 7;
    $field4->typeofdata = 'I~O';
    $block1->addField($field4);
}
$field5 = Vtiger_Field::getInstance('uff_upholstery', $moduleInstance);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_UPHOLSTERYFINEFINISH_UPHOLSTERY';
    $field5->name = 'uff_upholstery';
    $field5->table = 'vtiger_upholsteryfinefinish';
    $field5->column = 'uff_upholstery';
    $field5->columntype = 'VARCHAR(3)';
    $field5->uitype = 56;
    $field5->typeofdata = 'V~O';
    $block1->addField($field5);
}
$field6 = Vtiger_Field::getInstance('uff_finefinish', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_UPHOLSTERYFINEFINISH_FINEFINISH';
    $field6->name = 'uff_finefinish';
    $field6->table = 'vtiger_upholsteryfinefinish';
    $field6->column = 'uff_finefinish';
    $field6->columntype = 'VARCHAR(3)';
    $field6->uitype = 56;
    $field6->typeofdata = 'V~O';
    $block1->addField($field6);
}
$field7 = Vtiger_Field::getInstance('uff_overtime', $moduleInstance);
if (!$field7) {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_UPHOLSTERYFINEFINISH_OVERTIME';
    $field7->name = 'uff_overtime';
    $field7->table = 'vtiger_upholsteryfinefinish';
    $field7->column = 'uff_overtime';
    $field7->columntype = 'VARCHAR(3)';
    $field7->uitype = 56;
    $field7->typeofdata = 'V~O';
    $block1->addField($field7);
}
$field8 = Vtiger_Field::getInstance('uff_relcrmid', $moduleInstance);
if (!$field8) {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_UPHOLSTERYFINEFINISH_RELCRMID';
    $field8->name = 'uff_relcrmid';
    $field8->table = 'vtiger_upholsteryfinefinish';
    $field8->column = 'uff_relcrmid';
    $field8->columntype = 'INT(10)';
    $field8->uitype = 10;
    $field8->typeofdata = 'V~O';
    $block1->addField($field8);
    $field8->setRelatedModules(array('Estimates'));
}
$field7 = Vtiger_Field::getInstance('uff_overtime', $moduleInstance);
if (!$field7) {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_UPHOLSTERYFINEFINISH_UNWRAPHOURS';
    $field7->name = 'uff_unwraphours';
    $field7->table = 'vtiger_upholsteryfinefinish';
    $field7->column = 'uff_unwraphours';
    $field7->columntype = 'INT(10)';
    $field7->uitype = 7;
    $field7->typeofdata = 'V~O';
    $block1->addField($field7);
}

$block1->save($moduleInstance);

if ($new_module) {
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
    $estInstance = Vtiger_Module::getInstance('Estimates');
    $estInstance->setGuestBlocks('UpholsteryFineFinish', ['LBL_UPHOLSTERYFINEFINISH_INFORMATION']);
}

echo "done!<br> module creation script complete";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";