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



//*/
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
//*/

// tableName => fieldName
$listOfFields = [
    ['table' => 'vtiger_accountbillads', 'name' => 'bill_state'],
    ['table' => 'vtiger_accountshipads', 'name' => 'ship_state'],
    ['table' => 'vtiger_leadaddress',    'name' => 'state'],
    ['table' => 'vtiger_contactaddress', 'name' => 'mailingstate'],
    ['table' => 'vtiger_contactaddress', 'name' => 'otherstate'],
    ['table' => 'vtiger_vendor',         'name' => 'state'],
    ['table' => 'vtiger_quotesbillads',  'name' => 'bill_state'],
    ['table' => 'vtiger_quotesshipads',  'name' => 'ship_state'],
    ['table' => 'vtiger_pobillads',      'name' => 'bill_state'],
    ['table' => 'vtiger_poshipads',      'name' => 'ship_state'],
    ['table' => 'vtiger_sobillads',      'name' => 'bill_state'],
    ['table' => 'vtiger_soshipads',      'name' => 'ship_state'],
    ['table' => 'vtiger_invoicebillads', 'name' => 'bill_state'],
    ['table' => 'vtiger_invoiceshipads', 'name' => 'ship_state'],
    ['table' => 'vtiger_users',          'name' => 'address_state'],
    ['table' => 'vtiger_leadscf',        'name' => 'origin_state'],
    ['table' => 'vtiger_leadscf',        'name' => 'destination_state'],
    ['table' => 'vtiger_quotescf',       'name' => 'origin_state'],
    ['table' => 'vtiger_quotescf',       'name' => 'destination_state'],
    ['table' => 'vtiger_potentialscf',   'name' => 'origin_state'],
    ['table' => 'vtiger_potentialscf',   'name' => 'destination_state'],
    ['table' => 'vtiger_potential',      'name' => 'state'],
    ['table' => 'vtiger_vanlines',       'name' => 'vanline_state'],
    ['table' => 'vtiger_agents',         'name' => 'agent_state'],
    ['table' => 'vtiger_vanlinemanager', 'name' => 'state'],
    ['table' => 'vtiger_agentmanager',   'name' => 'state'],
    ['table' => 'vtiger_tariffs',        'name' => 'tariff_state'],
    ['table' => 'vtiger_quotesbillads',  'name' => 'bill_state'],
    ['table' => 'vtiger_quotescf',       'name' => 'origin_state'],
    ['table' => 'vtiger_quotescf',       'name' => 'destination_state'],
    ['table' => 'vtiger_potentialscf',   'name' => 'origin_state'],
    ['table' => 'vtiger_potentialscf',   'name' => 'destination_state'],
    ['table' => 'vtiger_potential',      'name' => 'state'],
    ['table' => 'vtiger_stops',          'name' => 'stops_state'],
    ['table' => 'vtiger_surveys',        'name' => 'state'],
    ['table' => 'vtiger_employees',      'name' => 'state'],
    ['table' => 'vtiger_employees',      'name' => 'employee_dlstate'],
    ['table' => 'vtiger_vehicles',       'name' => 'vehicle_platestate'],
    ['table' => 'vtiger_orders',         'name' => 'origin_state'],
    ['table' => 'vtiger_orders',         'name' => 'destination_state'],
    ['table' => 'vtiger_trips',          'name' => 'origin_state'],
    ['table' => 'vtiger_trips',          'name' => 'empty_state'],
    ['table' => 'vtiger_zoneadmin',      'name' => 'za_state'],
    ['table' => 'vtiger_vendor',         'name' => 'origin_state'],
    ['table' => 'vtiger_contracts',      'name' => 'billing_state'],
    ['table' => 'vtiger_potential',      'name' => 'ba_state'],
    ['table' => 'vtiger_potential',      'name' => 'oa_state'],
    ['table' => 'vtiger_potential',      'name' => 'ea_state'],
    ['table' => 'vtiger_potential',      'name' => 'ha_state'],
    ['table' => 'vtiger_potential',      'name' => 'da_state'],
    ['table' => 'vtiger_localcarrier',   'name' => 'state'],
    ['table' => 'vtiger_extrastops',     'name' => 'extrastops_state'],
    ['table' => 'vtiger_ordersbillads',  'name' => 'bill_state'],
];

$whereClause = "`typeofdata` NOT LIKE '%UC_SHORT%' AND (";
foreach ($listOfFields as $key => $fieldInfo) {
    $whereClause .="(`tablename` = '".$fieldInfo['table']."' AND `fieldname` = '".$fieldInfo['name']."')";
    if ($key !== end(array_keys($listOfFields))){
        $whereClause .= ' OR';
    }
}
$whereClause .= ')';

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `typeofdata` = CONCAT(`typeofdata`, '~UC_SHORT') WHERE $whereClause");

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
