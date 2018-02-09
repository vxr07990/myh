<?php
if (function_exists("call_ms_function_ver")) {
    $version = 3;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


/**
 * hotfix to update Document types picklist
 */

$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
include_once 'modules/Settings/Picklist/models/Module.php';
include_once 'modules/Settings/Picklist/models/Field.php';

//picklist desired values and their order
$picklistOrder = [
    '3rd Party Authorization',
    '3rd Party Auto Carrier Invoice',
    '3rd Party Inspection Report',
    '3rd Party Invoice-Destination',
    '3rd Party Invoice-Origin',
    '3rd Party Invoice-Other',
    '7-Day Notification',
    'Account Authorizations',
    'Account Invoice Cover Sheet',
    'Acknowledgment of Claim',
    'Agency/Driver Codes',
    'Amended Service Request',
    'AP Check Request',
    'Appeal of Claim',
    'AR - CODC/CODR',
    'ASP – Destination',
    'ASP - Origin',
    'Auto Weight Guide Copy',
    'Bill of Lading',
    'BOL-APU',
    'BOL-DOS',
    'BOL-Hauler',
    'BOL-Overflow Hauler',
    'Billing Release Form',
    'Billing Report',
    'Brokerage Cover/Spreadsheet',
    'Brokerage Worksheet',
    'Carrier Retention Sheet',
    'Carrier Service Agreement',
    'Certificate of Destruction',
    'Change Order',
    'Check',
    'Claim Form',
    'Claim Photos',
    'Commercial Cargo Liability Agreement',
    'Commercial Material/Equipment Control Form',
    'Commercial Project Checklist ',
    'Commercial Punchlist',
    'Commercial Service Partial Waiver',
    'Commercial Service Waiver',
    'Commercial Services Agreement',
    'Commercial Services Unconditional Waiver',
    'Conclusion of Claim',
    'CSO',
    'Customer Specific Forms',
    'Damage Spreadsheet',
    'DCC Form',
    'DD 1164',
    'DD 1299',
    'DD 1840',
    'DD 1857',
    'DD 619-1',
    'DD619 Accessorial Form',
    'DD619-1 Destination Accessorial Form',
    'DD619-1 Origin Accessorial Form',
    'Destination Bill of Lading',
    'Destination GBL',
    'Distribution Report',
    'Email',
    'Email - IQ',
    'Estimate of Charges',
    'Exceptions Inventory',
    'External Communication',
    'Fax/Mail',
    'Flat Rate Auto Comparison',
    'GBL',
    'Government Voucher',
    'Graebel Property Sale/Destruction Authorization',
    'Guaranteed Price Variance',
    'GVL 833 After Delivery',
    'GVL 833 At Delivery',
    'Gypsy Moth Form',
    'High Value Inventory',
    'IC Adjustment Form',
    'In Home Survey Fact Sheet',
    'Initial Claim Notification',
    'Internal Communication',
    'Inventory',
    'Inventory Control Form',
    'Invoice',
    'Invoice Audit Checklist',
    'Main/Overflow/Split/Pack Adjustment',
    'Military 7-Day Paperwork',
    'Military Hauler Checklist',
    'Military/Government Rate Sheet',
    'NDS Worksheet',
    'One Page Agreement',
    'Order For Service',
    'Origin Bill of Lading',
    'Origin GBL',
    'Other',
    'Pack Per Inventory',
    'Packing Control Order',
    'PC Miler',
    'Piano Condition Report',
    'Pre-Existing Damage',
    'Pre-Pending Information',
    'Rate Sheet',
    'Recovery Trip Assignment',
    'Reformatted Invoice',
    'Res/Prop Pre-Existing Damage',
    'Service Request',
    'Settlement Proposal',
    'Shipper\'s Receipt',
    'Special Commodities Rate Sheet',
    'Special Compensation Report',
    'Storage Contract',
    'Storage Contract Cover Letter',
    'Storage In Transit Certification-GSA',
    'Storage Pallet Card',
    'Storage Tracking Form',
    'Table of Measurements',
    'Tariff Page',
    'Timesheets',
    'Used Vehicle Condition Report',
    'Very Important Instructions For Your Move',
    'Warehouse Receive/Delv Report',
    'Weight Tickets',
    'Worksheet'
    ];

$moduleName1 = 'Documents';
$picklistFieldName1 = 'invoice_document_type';

$module1 = Vtiger_Module::getInstance($moduleName1);

$field1 = Vtiger_Field::getInstance($picklistFieldName1, $module1);

updatePicklist_ANDT($field1, $picklistFieldName1, $picklistOrder, $moduleName1);

/**
 * function to add a new picklist using their framework to ensure linkage
 * adapted from modules/Settings/Picklist/actions/SaveAjax.php
 *
 * EXAMPLE DATA:
JG HERE (SaveAjax.php:65): moduleName (TariffManager)
JG HERE (SaveAjax.php:66): pickListName (custom_tariff_type)
JG HERE (SaveAjax.php:67) newValue : test blah
 *
 * @param string $newValue
 * @param string $pickListName
 * @param string $moduleName
 */
function addNewPicklistItem_ANDT($newValue, $pickListName, $moduleName)
{
    $response = false;
    $moduleModel = Settings_Picklist_Module_Model::getInstance($moduleName);
    $fieldModel = Settings_Picklist_Field_Model::getInstance($pickListName, $moduleModel);
    $rolesSelected = array();
    if ($fieldModel->isRoleBased()) {
        print "This field is role based which this script is not prepared to handle.<br />";
        /*
        $userSelectedRoles = $request->get('rolesSelected',array());
        //selected all roles option
        if(in_array('all',$userSelectedRoles)) {
            $roleRecordList = Settings_Roles_Record_Model::getAll();
            foreach($roleRecordList as $roleRecord) {
                $rolesSelected[] = $roleRecord->getId();
            }
        }else{
            $rolesSelected = $userSelectedRoles;
        }
        */
    } else {
        try {
            $response = $moduleModel->addPickListValues($fieldModel, $newValue, $rolesSelected);
            print "Successfully added new picklist value ($response) for $pickListName in $moduleName. <br />";
        } catch (Exception $e) {
            print 'ERROR: ' . $e->getCode() . ' -- ' . $e->getMessage() . '<br />';
        }
    }
    return $response;
}

/**
 * function to update the picklist to the new order
 * adapted from modules/Settings/Picklist/actions/SaveAjax.php
 *
 * EXAMPLE DATA:
JG HERE (SaveAjax.php:167): pickListFieldName (custom_tariff_type)
JG HERE (SaveAjax.php:169) picklistValues : Array [picklist ID] => [order sequence]
(
[1] => 1
[2] => 2
[3] => 3
[4] => 4
[5] => 5
[6] => 6
[7] => 7
[8] => 8
[9] => 9
[10] => 10
[11] => 12
[12] => 13
[13] => 14
[14] => 15
[15] => 16
[16] => 17
[17] => 18
[18] => 19
[19] => 11
)
 *
 * @param string $pickListFieldName
 * @param array $picklistValues
 */
function updatePicklistOrder_ANDT($pickListFieldName, $picklistValues)
{
    $response = false;
    $moduleModel = new Settings_Picklist_Module_Model();

    try {
        $moduleModel->updateSequence($pickListFieldName, $picklistValues);
        print "Successfully updated picklist sequence for $pickListFieldName. <br />";
        $response = true;
    } catch (Exception $e) {
        print 'ERROR: ' . $e->getCode() . ' -- ' . $e->getMessage() . '<br />';
    }
    return $response;
}

/**
 * function to pull the picklist's id's by name
 *
 * @param string $picklistFieldName
 * @param string $picklistValue
 *
 * @return int
 */
function getPicklistId_ANDT($picklistFieldName, $picklistValue)
{
    $rv = false;
    $db = PearDatabase::getInstance();
    //return * so we don't have to rely on escapeDbName here too.
    $sql = 'SELECT * FROM ' . $db->escapeDbName('vtiger_' . $picklistFieldName)
           . ' WHERE ' . $db->escapeDbName($picklistFieldName) . ' = ?'
           . ' LIMIT 1';
    $result = $db->pquery($sql, [$picklistValue]);
    $row = $result->fetchRow();

    if (is_array($row) && count($row) > 0) {
        print "$sql;<br />";
        print "$picklistValue;<br />";
        print $picklistFieldName . "<br />";
        print $row[$picklistFieldName.'id'] . "<br />";
        $rv = $row[$picklistFieldName.'id'];
    }
    return $rv;
}


function deleteCurrentPicklistValues_ANDT($picklistFieldName, $moduleName)
{
    $moduleModel = Settings_Picklist_Module_Model::getInstance($moduleName);
    $db = PearDatabase::getInstance();
    $query = 'DELETE FROM '. $moduleModel->getPickListTableName($picklistFieldName);
    $db->pquery($query);
}

function updatePicklist_ANDT($field, $picklistFieldName, $picklistOrder, $moduleName)
{
    if ($field) {
        print "<br> Field '$picklistFieldName' is already present <br>";
        deleteCurrentPicklistValues_ANDT($picklistFieldName, $moduleName);
        $picklistValues = [];
        $i              = 1;
        foreach ($picklistOrder as $value) {
            if ($value) {
                $id = getPicklistId_ANDT($picklistFieldName, $value);
                print "ID: $id<br/>";
                if ($id === false) {
                    //so we didn't find the ID we assume it doesn't exist and add it
                    print "<br> Adding $value value to picklist field $picklistFieldName. <br>";
                    $id = addNewPicklistItem_ANDT($value, $picklistFieldName, $moduleName)['id'];
                }
                if ($id !== false) {
                    //ensure we skip anything that failed to create so we don't do something unexpected
                    $picklistValues[$id] = $i++;
                }
            }
        }
        if ($picklistFieldName && is_array($picklistValues) && count($picklistValues) > 0) {
            print "picklistFieldName => $picklistFieldName<br />";
            print 'picklistValues => '.print_r($picklistValues, true).'<br />';
            updatePicklistOrder_ANDT($picklistFieldName, $picklistValues);
        } else {
            print "picklistFieldName => $picklistFieldName<br />";
            print 'picklistValues => '.print_r($picklistValues, true).'<br />';
            print 'ERROR: Unable to update picklist order. <br />';
        }
    } else {
        print "ERROR: field DOES NOT EXIST: $picklistFieldName.<br />";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";