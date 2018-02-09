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

// OT4868 - Personnel Roles  Setup "Classification" picklist dependency on "Classification Type" picklist value

echo "Start: Adding Personnel Roles Picklist Dependency<br>";

$db = PearDatabase::getInstance();
$module = "EmployeeRoles";
$tabId = getTabid($module);
$sourceField = 'emprole_class_type';
$targetField = 'emprole_class';
$dependenciesArray = [//sourcevalue => targetvalues
    'Operations' => '["Driver","Owner Operator","Lead","Lease Driver","Helper","Packer","Supervisor","Installer","Warehouse"]',
    'Office' => '["Salesperson","Coordinator","Surveyor","Claims Adjuster","Biller","Collector","General Office"]'
];

foreach ($dependenciesArray as $sourceValue => $targetValues) {
    $sqlSelect = "SELECT id FROM vtiger_picklist_dependency WHERE tabid = ? AND sourcefield = ? AND targetfield = ? AND sourcevalue = ?";
    $select = $db->pquery($sqlSelect,array($tabId,$sourceField,$targetField,$sourceValue));
    if ( $db->num_rows($select) > 0 ){
        echo "Dependency already exist for module $module, sourcefield $sourceField, targetfield $targetField, sourcevalue $sourceValue<br>";
    } else {
        $sqlInsert = "INSERT INTO vtiger_picklist_dependency (id,tabid,sourcefield,targetfield,sourcevalue,targetvalues) VALUES (?,?,?,?,?,?)";
        $insert = $db->pquery($sqlInsert, array($db->getUniqueID('vtiger_picklist_dependency'),$tabId,$sourceField,$targetField,$sourceValue,$targetValues));
    }
}

echo "Finish: Adding Personnel Roles Picklist Dependency<br>";