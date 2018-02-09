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




$moduleName = 'LocalDispatch';

$moduleInstance = Vtiger_Module::getInstance($moduleName);
if ($moduleInstance) {
    echo "Module already present - choose a different name.";
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = 'OPERATIONS_TAB' WHERE name = 'LocalDispatch'");
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET presence = 0 WHERE name = 'LocalDispatch'");
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $moduleName;
    $moduleInstance->parent = 'Tools';
    $moduleInstance->save();

    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = 'OPERATIONS_TAB' WHERE name = 'LocalDispatch'");
    echo "OK\n";
}

//Adding the new UI Types for Crew and Vehicles assigment to order

$newUIType = 1008;
$newUIFieldType = 'assignedemployee';

$db = PearDatabase::getInstance();
$chkStmt = 'SELECT * FROM `vtiger_ws_fieldtype` WHERE `uitype`=? and `fieldtype` = ? LIMIT 1';
$res = $db->pquery($chkStmt, [$newUIType, $newUIFieldType]);
$exists = false;

if (method_exists($res, 'fetchRow') && $row = $res->fetchRow()) {
    print "<li>UIType: $newUIType already exists</li>\n";
} else {
    $stmt = "INSERT INTO `vtiger_ws_fieldtype` (`uitype`, `fieldtype`) VALUES (?, ?)";
    print "<li>creating new UIType: $stmt; [$newUIType, $newUIFieldType] <br />\n";
    $db->pquery($stmt, [$newUIType, $newUIFieldType]);
}

$newUIType = 1009;
$newUIFieldType = 'assignedvehicles';

$chkStmt = 'SELECT * FROM `vtiger_ws_fieldtype` WHERE `uitype`=? and `fieldtype` = ? LIMIT 1';
$res = $db->pquery($chkStmt, [$newUIType, $newUIFieldType]);
$exists = false;

if (method_exists($res, 'fetchRow') && $row = $res->fetchRow()) {
    print "<li>UIType: $newUIType already exists</li>\n";
} else {
    $stmt = "INSERT INTO `vtiger_ws_fieldtype` (`uitype`, `fieldtype`) VALUES (?, ?)";
    print "<li>creating new UIType: $stmt; [$newUIType, $newUIFieldType] <br />\n";
    $db->pquery($stmt, [$newUIType, $newUIFieldType]);
}

$ordersTasksInstance = Vtiger_Module::getInstance("OrdersTask");
$block = Vtiger_Block::getInstance('LBL_DISPATCH_UPDATES', $ordersTasksInstance);
if ($block) {
    echo "<h3>The LBL_DISPATCH_UPDATES block already exists</h3><br> \n";
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_DISPATCH_UPDATES';
    $moduleInstance->addBlock($block);
}

print "<h2>START add assigned_employee to OrdersTask module. </h2>\n";

$field_e = Vtiger_Field::getInstance('assigned_employee', $ordersTasksInstance);
if (!$field_e) {
    $field0 = new Vtiger_Field();
    $field0->label        = 'Crew';
    $field0->name         = 'assigned_employee';
    $field0->table        = 'vtiger_orderstask';
    $field0->column       = 'assigned_employee';
    $field0->columntype   = 'VARCHAR(250)';
    $field0->uitype       = 1008;
    $field0->typeofdata   = 'V~O';
    $field0->summaryfield = 0;
    $field0->presence     = 2;
    $block->addField($field0);
}

print "<h2>END add assigned_employee to OrdersTask module. </h2>\n";

print "<h2>START add assigned_vehicles to OrdersTask module. </h2>\n";
    
$field_v = Vtiger_Field::getInstance('assigned_vehicles', $ordersTasksInstance);
if (!$field_v) {
    $field1 = new Vtiger_Field();
    $field1->label        = 'Assigned Vehicles';
    $field1->name         = 'assigned_vehicles';
    $field1->table        = 'vtiger_orderstask';
    $field1->column       = 'assigned_vehicles';
    $field1->columntype   = 'VARCHAR(250)';
    $field1->uitype       = 1009;
    $field1->typeofdata   = 'V~O';
    $field1->summaryfield = 0;
    $field1->presence    = 2;
    $block->addField($field1);
}

print "<h2>END add assigned_vehicles to OrdersTask module. </h2>\n";

print "<h2>START add disp_actualend to OrdersTask module. </h2>\n";

$field85 = Vtiger_Field::getInstance('disp_actualend', $ordersTasksInstance);
if (!$field85) {
    $field85 = new Vtiger_Field();
    $field85->label = 'Actual End Time';
    $field85->name = 'disp_actualend';
    $field85->table = 'vtiger_orderstask';
    $field85->column = 'disp_actualend';
    $field85->columntype = 'TIME';
    $field85->uitype = 14;
    $field85->typeofdata = 'T~O';

    $block->addField($field85);
};

print "<h2>END add disp_actualend to OrdersTask module. </h2>\n";

print "<h2>START add check_call to OrdersTask module. </h2>\n";

$field999 = Vtiger_Field::getInstance('check_call', $ordersTasksInstance);
if ($field999) {
    echo '<p> check_call Field already present</p>';
} else {
    $field999 = new Vtiger_Field();
    $field999->label = 'LBL_ORDERSTASK_CHECK_CALL';
    $field999->name = 'check_call';
    $field999->table = 'vtiger_orderstask';
    $field999->column = 'check_call';
    $field999->columntype = 'VARCHAR(150)';
    $field999->uitype = '16';
    $field999->typeofdata = 'V~O';
    $field999->setPicklistValues(array('Attempted - All Numbers, No Contact', 'Attempted - No Answer', 'Attempted - No Phone Nbr in P3', 'Attempted - Phone Busy', 'Attempted - Wrong Number', 'Contacted - Cancelled', 'Contacted - Confirmed', 'Contacted - Date Change', 'Contacted - Left Message with child', 'Contacted - Left Message with relative', 'Contacted - Left Message with secretary'));

    $block->addField($field999);
}

print "<h2>END add check_call to OrdersTask module. </h2>\n";

print "<h2>START add assigned_vendor to OrdersTask module. </h2>\n";

$fieldv0 = Vtiger_Field::getInstance('assigned_vendor', $ordersTasksInstance);
if (!$fieldv0) {
    $fieldv0 = new Vtiger_Field();
    $fieldv0->label        = 'Related Vendor';
    $fieldv0->name         = 'assigned_vendor';
    $fieldv0->table        = 'vtiger_orderstask';
    $fieldv0->column       = 'assigned_vendor';
    $fieldv0->columntype   = 'INT(19)';
    $fieldv0->uitype       = 10;
    $fieldv0->typeofdata   = 'V~O';
    $fieldv0->summaryfield = 0;
    $fieldv0->presence     = 2;
    $block->addField($fieldv0);
    
    $fieldv0->setRelatedModules(array('Vendors'));
}

print "<h2>END add assigned_vendor to OrdersTask module. </h2>\n";



print "<h2>ADDING Related Lists --> Employees & Vehicles</h2>\n";

$moduleInstance->setRelatedList(Vtiger_Module::getInstance('Employees'), 'Employees', array('SELECT'), 'get_related_list');
$moduleInstance->setRelatedList(Vtiger_Module::getInstance('Vehicles'), 'Vehicles', array('SELECT'), 'get_related_list');

print "<h2>ADDING Local Dispatch filter</h2>\n";

$ordersModuleInstance = Vtiger_Module::getInstance('Orders');


//
$field01 = Vtiger_Field::getInstance('dispatch_status', $ordersTasksInstance); // Task Name
$field02 = Vtiger_Field::getInstance('ordersid', $ordersTasksInstance); // Orders Number
$field03 = Vtiger_Field::getInstance('orders_contacts', $ordersModuleInstance); // Orders Customer Name
$field04 = Vtiger_Field::getInstance('operations_task', $ordersTasksInstance); // Task Type
$field05 = Vtiger_Field::getInstance('crew_number', $ordersTasksInstance); // # Crew
$field06 = Vtiger_Field::getInstance('est_vehicle_number', $ordersTasksInstance); // # Equipment
$field07 = Vtiger_Field::getInstance('estimated_hours', $ordersTasksInstance); // Estimated Hours
$field08 = Vtiger_Field::getInstance('assigned_employee', $ordersTasksInstance); // Assigned Crew (Custom UITYPE)
$field09 = Vtiger_Field::getInstance('assigned_vehicles', $ordersTasksInstance); // Assigned Equipment (Custom UITYPE)
$field10 = Vtiger_Field::getInstance('disp_assigneddate', $ordersTasksInstance);
$field11 = Vtiger_Field::getInstance('date_spread', $ordersTasksInstance);
$field12 = Vtiger_Field::getInstance('disp_assignedstart', $ordersTasksInstance); // Job Start (time)
$field13 = Vtiger_Field::getInstance('disp_actualend', $ordersTasksInstance); // Job End (time)
$field14 = Vtiger_Field::getInstance('service_provider_notes', $ordersTasksInstance); // Service Provider Notes (text)

$filter1 = Vtiger_Filter::getInstance('Local Dispatch', $ordersTasksInstance); //
if ($filter1) {
    $filter1->delete(); //IF exists delete this filter to add the new columns
}

$filter1 = new Vtiger_Filter();
$filter1->name = 'Local Dispatch';
$ordersTasksInstance->addFilter($filter1);
for ($i = 1; $i < 15; $i++) {
    $var = $i<10 ? 'field0'.$i : 'field'.$i;
    if ($$var) {
        $filter1->addField($$var, ($i - 1));
    }
}

print "<h2>ADDING vtiger_orderstasksemprel TABLE</h2>\n";

$db = PearDatabase::getInstance();

if (!Vtiger_Utils::CheckTable('vtiger_orderstasksemprel')) {
    $db->pquery("CREATE TABLE vtiger_orderstasksemprel (taskid int(11) NOT NULL, employeeid int(11) NOT NULL, role varchar(50) NOT NULL, lead varchar(2) NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8;", array());
}

print "<h2>Updating quickcreate fields & ordersid field to optional (Related To)</h2>\n";

/*
QUICKCREATE_MANDATORY     = 0;
QUICKCREATE_NOT_ENABLED   = 1;
QUICKCREATE_ENABLED       = 2;
QUICKCREATE_NOT_PERMITTED = 3;
QUICKCREATE_ENABLED       = 2;
*/

//OT 1828 - Quick create in Local Dispatch

$db->pquery("UPDATE vtiger_field SET quickcreate = 1 WHERE tablename = 'vtiger_orderstask'", array());
$db->pquery("UPDATE vtiger_field SET quickcreate = 2 WHERE tablename = 'vtiger_orderstask' AND (columnname = 'operations_task' OR columnname = 'business_line' OR columnname = 'estimated_hours' OR columnname = 'disp_assigneddate' OR columnname = 'service_date_from' OR columnname = 'service_date_to' OR columnname = 'disp_assigneddate' OR columnname = 'est_vehicle_number' OR columnname = 'crew_number')", array());
$db->pquery("UPDATE vtiger_field SET typeofdata = 'V~O' WHERE columnname = 'ordersid' AND tablename = 'vtiger_orderstask'", array()); //Related To (optional)


//OT16353 - Make Local Dispatch an extension module so it's hidden in workflows and reports

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET isentitytype = 0 WHERE name = 'LocalDispatch'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";