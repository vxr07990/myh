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


/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 9/2/2016
 * Time: 9:19 AM
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
    'New',
    'Requested',
    'Approved',
    'On Hold',
    'Denied',
    'Expired'
];

$moduleName1 = 'Contracts';
$picklistFieldName1 = 'contract_status';

$module1 = Vtiger_Module::getInstance($moduleName1);

$field1 = Vtiger_Field::getInstance($picklistFieldName1, $module1);

updatePicklist_GACSED($field1, $picklistFieldName1, $picklistOrder, $moduleName1);

function addNewPicklistItem_GACSED($newValue, $pickListName, $moduleName)
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

function updatePicklistOrder_GACSED($pickListFieldName, $picklistValues)
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
function getPicklistId_GACSED($picklistFieldName, $picklistValue)
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


function deleteCurrentPicklistValues_GACSED($picklistFieldName, $moduleName)
{
    $moduleModel = Settings_Picklist_Module_Model::getInstance($moduleName);
    $db = PearDatabase::getInstance();
    $query = 'DELETE FROM '. $moduleModel->getPickListTableName($picklistFieldName);
    $db->pquery($query);
}

function updatePicklist_GACSED($field, $picklistFieldName, $picklistOrder, $moduleName)
{
    if ($field) {
        print "<br> Field '$picklistFieldName' is already present <br>";
        deleteCurrentPicklistValues_GACSED($picklistFieldName, $moduleName);
        $picklistValues = [];
        $i              = 1;
        foreach ($picklistOrder as $value) {
            if ($value) {
                $id = getPicklistId_GACSED($picklistFieldName, $value);
                print "ID: $id<br/>";
                if ($id === false) {
                    //so we didn't find the ID we assume it doesn't exist and add it
                    print "<br> Adding $value value to picklist field $picklistFieldName. <br>";
                    $id = addNewPicklistItem_GACSED($value, $picklistFieldName, $moduleName)['id'];
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
            updatePicklistOrder_GACSED($picklistFieldName, $picklistValues);
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