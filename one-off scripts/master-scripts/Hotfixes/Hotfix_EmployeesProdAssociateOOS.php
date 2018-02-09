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



$employees = Vtiger_Module::getInstance('Employees'); // The module1 your blocks and fields will be in.

if ($employees) {
    $block = Vtiger_Block::getInstance('LBL_EMPLOYEES_PRODASSOCIATEOOS', $employees);
    if ($block) {
        echo "<h3>The LBL_EMPLOYEES_PRODASSOCIATEOOS block already exists</h3><br> \n";
    } else {
        $block        = new Vtiger_Block();
        $block->label = 'LBL_EMPLOYEES_PRODASSOCIATEOOS';
        $employees->addBlock($block);
    }
    $field = Vtiger_Field::getInstance('date_oos', $employees);
    if ($field) {
        echo "The date_oos field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_EMPLOYEES_DATE_OOS';
        $field->name       = 'date_oos';
        $field->table      = 'vtiger_employees';
        $field->column     = 'date_oos';
        $field->columntype = 'DATE';
        $field->uitype     = 5;
        $field->typeofdata = 'D~O';
        $block->addField($field);
    }
    $field = Vtiger_Field::getInstance('date_reinstated', $employees);
    if ($field) {
        echo "The date_reinstated field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_EMPLOYEES_DATE_REINSTATED';
        $field->name       = 'date_reinstated';
        $field->table      = 'vtiger_employees';
        $field->column     = 'date_reinstated';
        $field->columntype = 'DATE';
        $field->uitype     = 5;
        $field->typeofdata = 'D~O';
        $block->addField($field);
    }
    $field = Vtiger_Field::getInstance('oos_reason', $employees);
    if ($field) {
        echo "The oos_reason field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_EMPLOYEES_OOS_REASON';
        $field->name       = 'oos_reason';
        $field->table      = 'vtiger_employees';
        $field->column     = 'oos_reason';
        $field->columntype = 'VARCHAR(255)';
        $field->uitype     = 1;
        $field->typeofdata = 'V~O';
        $block->addField($field);
    }
    $field = Vtiger_Field::getInstance('oos_comments', $employees);
    if ($field) {
        echo "The oos_comments field already exists<br>\n";
    } else {
        $field             = new Vtiger_Field();
        $field->label      = 'LBL_EMPLOYEES_OOS_COMMENTS';
        $field->name       = 'oos_comments';
        $field->table      = 'vtiger_employees';
        $field->column     = 'oos_comments';
        $field->columntype = 'TEXT';
        $field->uitype     = 19;
        $field->typeofdata = 'V~O';
        $block->addField($field);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";