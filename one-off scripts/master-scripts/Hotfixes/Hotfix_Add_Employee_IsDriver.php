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



/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$employeesInstance = Vtiger_Module::getInstance('Employees');

if (!$employeesInstance) {
    echo 'Module Employees not present<br>';
} else {
    $Block = Vtiger_Block::getInstance('LBL_DRIVER_INFORMATION', $employeesInstance);
    if (!$Block) {
        echo 'Block LBL_DRIVER_INFORMATION not present';
    } else {
        $field1 = Vtiger_Field::getInstance('employees_isdriver');
        if (!$field1) {
            $field1 = new Vtiger_Field();
            $field1->label = 'LBL_EMPLOYEES_ISDRIVER';
            $field1->name = 'employees_isdriver';
            $field1->table = 'vtiger_employees';
            $field1->column = $field1->name;
            $field1->columntype = 'varchar(3)';
            $field1->uitype = 56;
            $field1->typeofdata = 'C~O';
            $Block->addField($field1);
        }
        echo 'OK</br>';
    
    //Updating Old records

    Vtiger_Utils::ExecuteQuery("UPDATE vtiger_employees SET employees_isdriver=1 WHERE employee_prole IN ('Driver - A','Driver - B', 'Driver - Non CDL') OR contractor_prole IN ('Driver - A','Driver - B', 'Driver - Non CDL')");
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";