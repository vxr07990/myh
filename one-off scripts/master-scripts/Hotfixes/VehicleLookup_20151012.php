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

$Vtiger_Utils_Log = true;
include_once 'includes/main/WebUI.php';

if(!isset($db)) {
    $db = PearDatabase::getInstance();
}

$defaultChecklist = array('Operable Vehicle - means meeting standard requirements: Brakes, Runs, and Steers properly.',
                          'Doors/Windows work properly',
                          'No fluid leaks',
                          'Manual transmission requires that emergency brake works properly',
                          'Convertible tops have to be in good operable and without tears',
                          'Ground clearance over 4"',
                          'Any after factory modifications',
                          'Deactivate all aftermarket alarm system',
                          'Remove non built in items such as radios, luggage or bike racks and etc - remove exterior spare tire cover, etc.',
                          'Remove or retract Antennas',
                          'Remove all personal belongings except standard spare tire and jack (includes, sunglasses, gps, toll tags, loose change, etc.)');

$lookupModule = Vtiger_Module::getInstance('VehicleLookup');
if($lookupModule) {
    echo "Module exists";
} else {
    $lookupModule = new Vtiger_Module();
    $lookupModule->name = 'VehicleLookup';
    $lookupModule->save();

    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_business_line_seq` SET id=id+1');
    $result = $db->pquery("SELECT sortorderid FROM `vtiger_business_line` ORDER BY sortorderid DESC LIMIT 1", array());
    $row = $result->fetchRow();
    $sortorderid = $row[0]+1;
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_business_line` (business_lineid, business_line, sortorderid, presence) VALUES ((SELECT id FROM `vtiger_business_line_seq`), 'Auto Transportation', $sortorderid, 1)");
}

if(!Vtiger_Utils::CheckTable('vtiger_vehiclelookup')) {
    Vtiger_Utils::CreateTable('vtiger_vehiclelookup',
                              '(
								vehicleid INT(11) AUTO_INCREMENT,
								crmid INT(11),
								vehicle_make VARCHAR(50),
								vehicle_model VARCHAR(100),
								vehicle_year INT(6),
								vehicle_vin VARCHAR(20),
								vehicle_color VARCHAR(25),
								vehicle_odometer DECIMAL(10,1),
								license_state VARCHAR(30),
								license_number VARCHAR(30),
								vehicle_type VARCHAR(10),
								is_non_standard TINYINT(1),
								inoperable TINYINT(1),
								PRIMARY KEY (vehicleid)
							   )', true);

    echo "vehiclelookup table created";
}

if(!Vtiger_Utils::CheckTable('vtiger_vehiclelookup_checklist')) {
    Vtiger_Utils::CreateTable('vtiger_vehiclelookup_checklist',
                              '(
								itemid INT(11) AUTO_INCREMENT,
								agentmanagerid INT(11),
								checklist_string TEXT,
								PRIMARY KEY (itemid)
							   )', true);

    foreach($defaultChecklist as $checklistItem) {
        $db->pquery("INSERT INTO `vtiger_vehiclelookup_checklist` (agentmanagerid, checklist_string) VALUES (?,?)", array(0, $checklistItem));
    }

    echo "vehiclelookup_checklist table created";
}

$ordersModule = Vtiger_Module::getInstance('Orders');
$block = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $ordersModule);
$field1 = Vtiger_Field::getInstance('business_line', $ordersModule);
if($field1) {
    echo "Field business_line already exists!<br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ORDERS_BUSINESSLINE';
    $field1->name = 'business_line';
    $field1->table = 'vtiger_orders';
    $field1->column = 'business_line';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';

    $block->addField($field1);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";