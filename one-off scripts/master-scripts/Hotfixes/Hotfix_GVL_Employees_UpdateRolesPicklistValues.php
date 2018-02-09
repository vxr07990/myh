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


// OT 3150 - Updating the primary and secondary roles picklists for Employees. Using the same values for both picklists.
//Picklist dependencies set up to change roles available based on Employee type

$moduleName = 'Employees';
$blockName = 'LBL_EMPLOYEES_DETAILINFO';
$module = Vtiger_Module::getInstance($moduleName);
$addedField = false;

$rolesPickList = [
        'Installer ', 'Lead Installer',
        'Mover',        'Packer',
        'Supervisor',       'Project Manager',
        'Computer Technician',      'Unpacker',
        'Warehouse Local',      'Forklift Operator',
        'Warehouse Supervisor',         'Driver - A',
        'Driver - B',       'Driver - Non CDL',
        'Inventory Technician',     'AutoCAD Operator',
        'Project Coordinator',      'Senior Project Manager',
        'Space Planner',        'Supervisor - Inventory',
        'Systems Administrator - Inventory',        'Local Dispatcher',
        'Operations Manager',       'Administrative Assistant',
        'Contractor Relations Manager',         'Operations Manager - Assistant',
        'OA/DA Coordinator',        'Surveyor',
        'Consumer Sales',       'Consumer Customer Service Coordinator',
        'Commercial Customer Service Coordinator',      'Branch Administrator',
        'General Manager',      'Assistant General Manager',
        'Customer Service Coordinator',         'Customer Service Coordinator - Assistant',
        'Customer Service Supervisor',      'Regional Client Service Manager',
        'Claims Adjuster',      'Claims Supervisor',
        'Claims Manager',       'Billing Support',
        'Accounting Specialist',    'Accounting Supervisor',
        'Accounting Manager',       'Director of Billing',
        'Claims Administration',        'Claims Adjusting',
        'Claims Management',        'Regional Planner ',
        'Planning Manager',         'Planning Coordinator',
        'ATS Manager',      'ATS Coordinator',
        'CRM Manager'
];



echo "<br>Starting UpdateRolesPicklistValues<br>\n";

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    $field0 = Vtiger_Field::getInstance('employee_prole', $module);
    if ($field0) {
        echo '<p>employee_prole field exists</p>';
        updatePicklistValuesURPV($field0, $rolesPickList);
    } else {
        $db = PearDatabase::getInstance();
        $sql = "TRUNCATE TABLE `vtiger_employee_prole`";
        $db->pquery($sql, array());
        $field0 = new Vtiger_Field();
        $field0->label = 'LBL_EMPLOYEE_PROLE';
        $field0->name = 'employee_prole';
        $field0->table = 'vtiger_employees';
        $field0->column = 'employee_prole';
        $field0->columntype = 'VARCHAR(100)';
        $field0->uitype = '16';
        $field0->typeofdata = 'V~O';
        $block->addField($field0);
        $field0->setPicklistValues($rolesPickList);
        echo '<p>Added Employee Primary Roles</p>';
    }

    $field1 = Vtiger_Field::getInstance('employee_srole', $module);
    if ($field1) {
        echo '<p>employee_srole field exists</p>';
        updatePicklistValuesURPV($field1, $rolesPickList);
    } else {
        $db = PearDatabase::getInstance();
        $sql = "TRUNCATE TABLE `vtiger_employee_srole`";
        $db->pquery($sql, array());
        $field1 = new Vtiger_Field();
        $field1->label = 'LBL_EMPLOYEE_SROLE';
        $field1->name = 'employee_srole';
        $field1->table = 'vtiger_employees';
        $field1->column = 'employee_srole';
        $field1->columntype = 'VARCHAR(100)';
        $field1->uitype = '16';
        $field1->typeofdata = 'V~O';
        $block->addField($field1);
        $field1->setPicklistValues($rolesPickList);
        echo '<p>Added Employees Secondary Roles</p>';
    }

    $field2 = Vtiger_Field::getInstance('contractor_prole', $module);
    if ($field2) {
        echo '<p>contractor_prole field exists</p>';
        updatePicklistValuesURPV($field2, $rolesPickList);
    } else {
        $db = PearDatabase::getInstance();
        $sql = "TRUNCATE TABLE `vtiger_contractor_prole`";
        $db->pquery($sql, array());
        $field2 = new Vtiger_Field();
        $field2->label = 'LBL_CONTRACTORS_PROLE';
        $field2->name = 'contractor_prole';
        $field2->table = 'vtiger_employees';
        $field2->column = 'contractor_prole';
        $field2->columntype = 'VARCHAR(100)';
        $field2->uitype = '16';
        $field2->typeofdata = 'V~O';
        $block->addField($field2);
        $field2->setPicklistValues($rolesPickList);
        echo '<p>Added Contractor Primary Roles</p>';
    }
} else {
    echo "<br>Fields not added. $blockName not found.<br/>";
}


function updatePicklistValuesURPV($field, $pickList)
{
    $fieldName = $field->name;
    $tableName = 'vtiger_'.$fieldName;
//    $keyField = $fieldName.'id';
    $db = PearDatabase::getInstance();
    $sql = "TRUNCATE TABLE `$tableName`";
    $db->pquery($sql, array());
    $field->setPicklistValues($pickList);
//    $id = 0;
//    $presenceValue = 1;
//    foreach ($picklist as $index => $value) {
//        $insertSql = 'INSERT INTO `'.$tableName.'` SET
//                `presence` = ?,
//                `'.$keyField.'` = ?,
//                `'.$fieldName.'` = ?,
//                `sortorderid` = ?';
//        $db->pquery($insertSql, array($presenceValue, $id, $value, $id));
//        $id++;
//        }
    echo "<p>Updated $fieldName picklist.</p>";
}



echo "<br>Finished NewStatusItemsAndRelatedPicklists<br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";