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


$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


$module = Vtiger_Module::getInstance('Quotes');

$block1 = Vtiger_Block::getInstance('Accessorial Details', $module);

$field1 = new Vtiger_Field();
$field1->label = 'Regular Hours';
$field1->name = 'acc_exlabor_origin_hours';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_exlabor_origin_hours';
$field1->columntype = 'DECIMAL(4,2)';
$field1->uitype = 7;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Regular Hours';
$field1->name = 'acc_exlabor_dest_hours';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_exlabor_dest_hours';
$field1->columntype = 'DECIMAL(4,2)';
$field1->uitype = 7;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'OT Hours';
$field1->name = 'acc_exlabor_ot_origin_hours';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_exlabor_ot_origin_hours';
$field1->columntype = 'DECIMAL(4,2)';
$field1->uitype = 7;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'OT Hours';
$field1->name = 'acc_exlabor_ot_dest_hours';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_exlabor_ot_dest_hours';
$field1->columntype = 'DECIMAL(4,2)';
$field1->uitype = 7;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Regular Hours';
$field1->name = 'acc_wait_origin_hours';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_wait_origin_hours';
$field1->columntype = 'DECIMAL(4,2)';
$field1->uitype = 7;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Regular Hours';
$field1->name = 'acc_wait_dest_hours';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_wait_dest_hours';
$field1->columntype = 'DECIMAL(4,2)';
$field1->uitype = 7;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'OT Hours';
$field1->name = 'acc_wait_ot_origin_hours';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_wait_ot_origin_hours';
$field1->columntype = 'DECIMAL(4,2)';
$field1->uitype = 7;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'OT Hours';
$field1->name = 'acc_wait_ot_dest_hours';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_wait_ot_dest_hours';
$field1->columntype = 'DECIMAL(4,2)';
$field1->uitype = 7;
$field1->typeofdata = 'N~O';

$block1->addField($field1);



$block1->save($module);
