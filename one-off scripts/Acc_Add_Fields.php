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

 
// Turn on debugging level
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


$module = Vtiger_Module::getInstance('Quotes');

$block1 = new Vtiger_Block();
$block1->label = 'Accessorial Details';
$module->addBlock($block1);

$field1 = new Vtiger_Field();
$field1->label = 'Weight';
$field1->name = 'acc_shuttle_origin_weight';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_shuttle_origin_weight';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Weight';
$field1->name = 'acc_shuttle_dest_weight';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_shuttle_dest_weight';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Service Applied';
$field1->name = 'acc_shuttle_origin_applied';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_shuttle_origin_applied';
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56;
$field1->typeofdata = 'C~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Service Applied';
$field1->name = 'acc_shuttle_dest_applied';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_shuttle_dest_applied';
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56;
$field1->typeofdata = 'C~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'OT';
$field1->name = 'acc_shuttle_origin_ot';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_shuttle_origin_ot';
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56;
$field1->typeofdata = 'C~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'OT';
$field1->name = 'acc_shuttle_dest_ot';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_shuttle_dest_ot';
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56;
$field1->typeofdata = 'C~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Over 25 Miles';
$field1->name = 'acc_shuttle_origin_over25';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_shuttle_origin_over25';
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56;
$field1->typeofdata = 'C~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Over 25 Miles';
$field1->name = 'acc_shuttle_dest_over25';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_shuttle_dest_over25';
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56;
$field1->typeofdata = 'C~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Mileage';
$field1->name = 'acc_shuttle_origin_miles';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_shuttle_origin_miles';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Mileage';
$field1->name = 'acc_shuttle_dest_miles';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_shuttle_dest_miles';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Weight';
$field1->name = 'acc_ot_origin_weight';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_ot_origin_weight';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Weight';
$field1->name = 'acc_ot_dest_weight';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_ot_dest_weight';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Applied';
$field1->name = 'acc_ot_origin_applied';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_ot_origin_applied';
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56;
$field1->typeofdata = 'C~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Applied';
$field1->name = 'acc_ot_dest_applied';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_ot_dest_applied';
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56;
$field1->typeofdata = 'C~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Weight';
$field1->name = 'acc_selfstg_origin_weight';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_selfstg_origin_weight';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Weight';
$field1->name = 'acc_selfstg_dest_weight';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_selfstg_dest_weight';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Applied';
$field1->name = 'acc_selfstg_origin_applied';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_selfstg_origin_applied';
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56;
$field1->typeofdata = 'C~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Applied';
$field1->name = 'acc_selfstg_dest_applied';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_selfstg_dest_applied';
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56;
$field1->typeofdata = 'C~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'OT';
$field1->name = 'acc_selfstg_origin_ot';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_selfstg_origin_ot';
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56;
$field1->typeofdata = 'C~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'OT';
$field1->name = 'acc_selfstg_dest_ot';
$field1->table = 'vtiger_quotes';
$field1->column = 'acc_selfstg_dest_ot';
$field1->columntype = 'VARCHAR(3)';
$field1->uitype = 56;
$field1->typeofdata = 'C~O';

$block1->addField($field1);



$block1->save($module);
