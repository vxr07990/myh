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

include_once('vtlib/Vtiger/Module.php');

if (!$db) {
    $db = PearDatabase::getInstance();
}

$opportunitiesModule = Vtiger_Module::getInstance('Opportunities');

$block = Vtiger_Block::getInstance('LBL_OPPORTUNITIES_BLOCK_LEADDETAILS', $opportunitiesModule);

$field1 = Vtiger_Field::getInstance('lmp_lead_id', $opportunitiesModule);

if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_OPPORTUNITIES_LMPID';
    $field1->name = 'lmp_lead_id';
    $field1->table = 'vtiger_potential';
    $field1->column = 'lmp_lead_id';
    $field1->columntype = 'VARCHAR(50)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;

    $block->addField($field1);
}

$field2 = Vtiger_Field::getInstance('source_name', $opportunitiesModule);

if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label             = 'LBL_OPPORTUNITIES_SOURCENAME';
    $field2->name              = 'source_name';
    $field2->table             = 'vtiger_potential';
    $field2->column            = 'source_name';
    $field2->columntype        = 'VARCHAR(50)';
    $field2->uitype            = 10;
    $field2->typeofdata        = 'V~O';
    $field2->summaryfield      = 0;

    $block->addField($field2);
    $field2->setRelatedModules(['LeadSourceManager']);
}

$field3 = Vtiger_Field::getInstance('program_name', $opportunitiesModule);

if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_OPPORTUNITIES_PROGRAMNAME';
    $field3->name = 'program_name';
    $field3->table = 'vtiger_potential';
    $field3->column = 'program_name';
    $field3->columntype = 'VARCHAR(100)';
    $field3->uitype = 1;
    $field3->typeofdata = 'V~O';
    $field3->displaytype = 1;

    $block->addField($field3);
}

$field4 = Vtiger_Field::getInstance('non_conforming', $opportunitiesModule);

if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_OPPORTUNITIES_NONCONFORMING';
    $field4->name = 'non_conforming';
    $field4->table = 'vtiger_potential';
    $field4->column = 'non_conforming';
    $field4->columntype = 'VARCHAR(3)';
    $field4->uitype = 56;
    $field4->typeofdata = 'V~O';
    $field4->displaytype = 1;

    $block->addField($field4);
}

$field5 = Vtiger_Field::getInstance('non_conforming_params', $opportunitiesModule);

if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_OPPORTUNITIES_NONCONFORMINGPARAMS';
    $field5->name = 'non_conforming_params';
    $field5->table = 'vtiger_potential';
    $field5->column = 'non_conforming_params';
    $field5->columntype = 'VARCHAR(255)';
    $field5->uitype = 1;
    $field5->typeofdata = 'V~O';
    $field5->displaytype = 1;

    $block->addField($field5);
}

$field6 = Vtiger_Field::getInstance('warm_transfer', $opportunitiesModule);

if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_OPPORTUNITIES_WARMTRANSFER';
    $field6->name = 'warm_transfer';
    $field6->table = 'vtiger_potential';
    $field6->column = 'warm_transfer';
    $field6->columntype = 'VARCHAR(3)';
    $field6->uitype = 56;
    $field6->typeofdata = 'V~O';
    $field6->displaytype = 1;

    $block->addField($field6);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";