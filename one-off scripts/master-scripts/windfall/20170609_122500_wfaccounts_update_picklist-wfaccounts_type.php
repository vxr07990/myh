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



echo "<br>Started Account Type PicklistUpdate</br>";

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

$moduleName = 'WFAccounts';
$picklistFieldName = 'wfaccounts_type';

//picklist desired values and their order
$picklistOrder = [
    'Household Goods',
    'Commercial'
];



$module = Vtiger_Module::getInstance($moduleName);
$field1 = Vtiger_Field::getInstance($picklistFieldName, $module);



if ($field1) {
    print "<br> Field '$picklistFieldName' is already present <br>";
    //@TODO: Figure out how to get the module model remove function to actually work so I'm not just blowing out the whole table like a monster.
    blowOutPickListWUPWT($picklistFieldName);

    $picklistValues = [];
    foreach ($picklistOrder as $key=>$value) {
        $id = getPicklistIdWUPWT($picklistFieldName, $value);
        print "ID: $id<br/>";
        if ($id === false) {
            //No ID found. Add it. Won't be any ID's if the picklist is blown out above.
            print "<br> Adding $value value to picklist field $picklistFieldName. <br>";
            $newItem = addNewPicklistItemWUPWT($value, $picklistFieldName, $moduleName);
            $id = $key;
            print "<br> New ID: $id <br/>";
        }
        if ($newItem !== false) {
            //ensure we skip anything that failed to create so we don't do something unexpected
            $picklistValues[$id] = $key;
        }
    }
} else {
    print "ERROR: field DOES NOT EXIST: $picklistFieldName.<br />";
}


function addNewPicklistItemWUPWT($newValue, $pickListName, $moduleName)
{
    $response = false;
    $moduleModel = Settings_Picklist_Module_Model::getInstance($moduleName);
    $fieldModel = Settings_Picklist_Field_Model::getInstance($pickListName, $moduleModel);
    if ($fieldModel->isRoleBased()) {
        print "This field is role based which this script is not prepared to handle.<br />";
    } else {
        try {
            $response = $moduleModel->addPickListValues($fieldModel, $newValue);
            print "Successfully added new picklist value ($newValue) for $pickListName in $moduleName. <br />";
        } catch (Exception $e) {
            print 'ERROR: ' . $e->getCode() . ' -- ' . $e->getMessage() . '<br />';
        }
    }
    return $response;
}



function getPicklistIdWUPWT($picklistFieldName, $picklistValue)
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

function blowOutPickListWUPWT($picklistFieldName)
{
    $db = PearDatabase::getInstance();
    $picklistTableName = 'vtiger_' . $picklistFieldName;
    echo "<br>Blowing out old picklist values now...";
    $db->pquery('TRUNCATE TABLE `'.$picklistTableName.'`', []);
    echo "<br>blowout complete, inserting new picklist values...";
}
echo "<br>Finished Account Type PicklistUpdate</br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
