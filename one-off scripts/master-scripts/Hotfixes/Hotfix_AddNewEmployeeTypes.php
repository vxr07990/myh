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



//OT 2561 - New Employee Types

echo 'Deletting vtiger_employee_type Values</br>';
//first: delete the existing picklist values
$sqlquery = 'DELETE FROM vtiger_employee_type WHERE 1';
Vtiger_Utils::ExecuteQuery($sqlquery);
echo 'OK</br>';

//last: add the new picklist values
$newTypes = array(
    'IC' => 'IC Transportation Contractor',
    'IC/CODRVR' => 'I/C Co-driver',
    'TSC EMP' => 'TSC Employee',
    'TSC' => 'Terminal Service Contractor',
    'IT' => 'I/C and TSC',
    'CAS LAB' => 'Casual Laborer',
    'EMP AGT' => 'Employee Agent',
    'EMP FULL' => 'Employee - Full Time',
    'EMP PART' => 'Employee - Part Time',
    'CON SURV' => 'Contractor Surveyor',
    'TEMPADMIN' => 'Temp Agency Employee - Admin',
    'TEMPPROD' => 'Temp Agency Employee - Prod',
    'IC SHUTTLE' => 'I/C Shuttle',
    'IC LABOR' => 'IC Labor',
    'ADMIN' => 'Administrative',
);

$pickListName = 'employee_type';
$moduleName = 'Employees';

$employeesInstance = Vtiger_Module::getInstance('Employees');
if (!$employeesInstance) {
    echo 'Module Employees not present<br>';
} else {
    echo 'Adding new values</br>';
    $field = Vtiger_Field::getInstance($pickListName, $employeesInstance);
    if (!$field) {
        echo 'Field employees_type not found';
    } else {
        $field->setPicklistValues($newTypes);
    }
}
echo '</br>';
echo 'OK</br>';

// OT2683, OT2681, OT2551


if (!$employeesInstance) {
    echo 'Module Employees not present<br>';
} else {
    $safetyBlock = Vtiger_Block::getInstance('LBL_EMPLOYEES_SAFETYDETAILS', $employeesInstance);
    if (!$safetyBlock) {
        echo 'Block LBL_EMPLOYEES_SAFETYDETAILS not present';
    } else {
        $field_r = Vtiger_Field::getInstance('employees_report_id');
        if (!$field_r) {
            $field1 = new Vtiger_Field();
            $field1->label = 'LBL_EMPLOYEES_REPORT_ID';
            $field1->name = 'employees_report_id';
            $field1->table = 'vtiger_employees';
            $field1->column = $field1->name;
            $field1->columntype = 'varchar(100)';
            $field1->uitype = 1;
            $field1->typeofdata = 'V~O~LE~100';
            $safetyBlock->addField($field1);
        }
        echo 'OK</br>';
    }
    $licenseBlock = Vtiger_Block::getInstance('LBL_EMPLOYEES_LICENSEINFO', $employeesInstance);
    if (!$licenseBlock) {
        echo 'Block LBL_EMPLOYEES_LICENSEINFO not present';
    } else {
        $field_i = Vtiger_Field::getInstance('employees_issue_date');
        if (!$field_i) {
            $field2 = new Vtiger_Field();
            $field2->label = 'LBL_EMPLOYEES_ISSUE_DATE';
            $field2->name = 'employees_issue_date';
            $field2->table = 'vtiger_employees';
            $field2->column = $field2->name;
            $field2->columntype = 'date';
            $field2->uitype = 5;
            $field2->typeofdata = 'D~O';
            $licenseBlock->addField($field2);
        }
        echo 'OK</br>';
    }
    $driverBlock = Vtiger_Block::getInstance('LBL_DRIVER_INFORMATION', $employeesInstance);
    if (!$driverBlock) {
        echo 'Block LBL_DRIVER_INFORMATION not present';
    } else {
        $field_d = Vtiger_Field::getInstance('employees_co_driver');
        if (!$field_d) {
            $field3 = new Vtiger_Field();
            $field3->label = 'LBL_EMPLOYEES_CO_DRIVER';
            $field3->name = 'employees_co_driver';
            $field3->table = 'vtiger_employees';
            $field3->column = $field3->name;
            $field3->columntype = 'INT(11)';
            $field3->uitype = 10;
            $field3->typeofdata = 'I~O';

            $driverBlock->addField($field3);

            $field3->setRelatedModules(array('Employees'));
        }
        $field_f = Vtiger_Field::getInstance('employees_fleet_manager');
        if (!$field_f) {
            $field4 = new Vtiger_Field();
            $field4->label = 'LBL_EMPLOYEES_FLEET_MANAGER';
            $field4->name = 'employees_fleet_manager';
            $field4->table = 'vtiger_employees';
            $field4->column = $field4->name;
            $field4->columntype = 'INT(11)';
            $field4->uitype = 10;
            $field4->typeofdata = 'I~O';

            $driverBlock->addField($field4);
            
            $field4->setRelatedModules(array('Employees'));
        }
        echo 'OK</br>';
    }
    
   
    $field01 = Vtiger_Field::getInstance('employees_committedstatus', $employeesInstance);
    if (!$field01) {
        echo "Creating Field committedstatus in Employees</br>";
        $field1  = new Vtiger_Field();
        $field1->name = 'employees_committedstatus';
        $field1->label= 'Committed Status';
        $field1->uitype= 15;
        $field1->table = 'vtiger_employees';
        $field1->column = $field1->name;
        $field1->columntype = 'VARCHAR(255)';
        $field1->typeofdata = 'V~O';
        $field1->setPicklistValues(array('Committed', 'Uncommitted'));
        $driverBlock->addField($field1);
        echo "OK adding Committed Status Field in Employees</br>";
    } else {
        echo "Field committedstatus already exists in Employees</br>";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";