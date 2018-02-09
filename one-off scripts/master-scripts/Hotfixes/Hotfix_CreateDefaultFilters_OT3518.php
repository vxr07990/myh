<?php

if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$db = PearDatabase::getInstance();

$moduleInstance = Vtiger_Module::getInstance('OrdersTask');
$moduleInstanceEmployees = Vtiger_Module::getInstance('Employees');
$field1 = Vtiger_Field::getInstance('name', $moduleInstanceEmployees);
$field2 = Vtiger_Field::getInstance('employee_lastname', $moduleInstanceEmployees);
$field3 = Vtiger_Field::getInstance('employee_type', $moduleInstanceEmployees);
$field4 = Vtiger_Field::getInstance('agentid', $moduleInstanceEmployees);

if ($moduleInstance) {
    
    $db->pquery("DELETE FROM vtiger_customview WHERE view = 'NewLocalDispatchCrew' AND viewname='Crew Default Filter'",array());
    
    
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'Crew Default Filter'; 
    $filter1->status = 1;
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3);
		
    $db->pquery("UPDATE vtiger_customview SET view = 'NewLocalDispatchCrew' WHERE cvid = ?",array($filter1->id));
}

$moduleInstanceVehicles = Vtiger_Module::getInstance('Vehicles');
$field11 = Vtiger_Field::getInstance('vechiles_unit', $moduleInstanceVehicles);
$field22 = Vtiger_Field::getInstance('vehicle_type', $moduleInstanceVehicles);
$field33 = Vtiger_Field::getInstance('agentid', $moduleInstanceVehicles);
$field44 = Vtiger_Field::getInstance('vehicle_number', $moduleInstanceVehicles);

if ($moduleInstance) {
    
    $db->pquery("DELETE FROM vtiger_customview WHERE view = 'NewLocalDispatchEquipment' AND viewname='Equipment Default Filter'",array());

    
    $filter2 = new Vtiger_Filter();
    $filter2->name = 'Equipment Default Filter';
    $filter2->status = 1;
    $filter2->isdefault = true;
    $moduleInstance->addFilter($filter2);

    $filter2->addField($field11)->addField($field22, 1)->addField($field33, 2)->addField($field44, 3);
		
    $db->pquery("UPDATE vtiger_customview SET view = 'NewLocalDispatchEquipment' WHERE cvid = ?",array($filter2->id));
}

$moduleInstanceVendors = Vtiger_Module::getInstance('Vendors');
$field111 = Vtiger_Field::getInstance('vendorname', $moduleInstanceVendors);
$field222 = Vtiger_Field::getInstance('vendor_no', $moduleInstanceVendors);
$field333 = Vtiger_Field::getInstance('agentid', $moduleInstanceVendors);

if ($moduleInstance) {
    
    
    $db->pquery("DELETE FROM vtiger_customview WHERE view = 'NewLocalDispatchVendors' AND viewname='Vendors Default Filter'",array());

    
    $filter3 = new Vtiger_Filter();
    $filter3->name = 'Vendors Default Filter';
    $filter3->status = 1;
    $filter3->isdefault = true;
    $moduleInstance->addFilter($filter3);

    $filter3->addField($field111)->addField($field222, 1)->addField($field333, 2);
		
    $db->pquery("UPDATE vtiger_customview SET view = 'NewLocalDispatchVendors' WHERE cvid = ?",array($filter3->id));
}