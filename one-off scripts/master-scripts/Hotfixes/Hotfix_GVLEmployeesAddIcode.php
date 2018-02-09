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

echo "<br>start add ICode to employees<br>";

$employeesInstance = Vtiger_Module::getInstance('Employees');
$employeesBlock = Vtiger_Block::getInstance('LBL_CONTRACTORS_DETAILINFO', $employeesInstance);

$field01 = Vtiger_Field::getInstance('employees_icode', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'LBL_EMPLOYEES_ICODE';
    $field01->name = 'employees_icode';
    $field01->table = 'vtiger_employees';
    $field01->column = 'employees_icode';
    $field01->columntype = 'VARCHAR(255)';
    $field01->uitype = 1;
    $field01->typeofdata = 'V~0';

    $employeesBlock->addField($field01);
}

echo "<br>end add ICode to employees";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";