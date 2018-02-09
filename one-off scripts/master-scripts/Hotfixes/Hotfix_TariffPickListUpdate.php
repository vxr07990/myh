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
 * this will add the 400NG tariff to the picklist as a custom_tariff_type
 * This will create the field if it doesn't exist aleady... although it should.
 *
 * we should be able to pull out the add picklist portion to reuse in a class.
 *
 * @TODO: Make it so it removes picklist values NOT in our picklistOrder array.
 *
 * based on Hotfix_TariffType.php
 * @author jgriffin
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

$moduleName = 'TariffManager';
$picklistFieldName = 'custom_tariff_type';

//block creation specific variables
$picklistBlockName = 'LBL_TARIFFMANAGER_ADMINISTRATIVE';

//field creation variables
$fieldLabel = 'LBL_TARIFFMANAGER_CUSTOMTARIFFTYPE';
$fieldTable = 'vtiger_tariffmanager';
$fieldColumnType = 'VARCHAR(255)';
$fieldUIType = 16;
$fieldTypeOfData = 'V~M';
$fieldSeq = [
    'rating_url' => 1,
    'createdtime' => 2,
    'modifiedtime' => 3,
    'smownerid' => 4,
    'custom_tariff_type' => 5,
    'custom_javascript' => 6,
];

//picklist desired values and their order
$picklistOrder = [
    'TPG',
    'Allied Express',
    'TPG GRR',
    'ALLV-2A',
    'Pricelock',
    'Blue Express',
    'Pricelock GRR',
    'NAVL-12A',
    '400N Base',
    '400N/104G',
    '400NG',
    'Local/Intra',
    'Max 3',
    'Max 4',
    'Intra - 400N',
    'Canada Gov\'t',
    'Canada Non-Govt',
    'UAS',
    'Base',
];

$module = Vtiger_Module::getInstance($moduleName);
$block = Vtiger_Block::getInstance($picklistBlockName, $module);
if ($block) {
    print "<br> Block '$picklistBlockName' is already present <br>";
} else {
    print "<br> Creating Block '$picklistBlockName'<br>";
    $block = new Vtiger_Block();
    $block->label = $picklistBlockName;
    $module->addBlock($block);
}

$field1 = Vtiger_Field::getInstance($picklistFieldName, $module);
if ($field1) {
    print "<br> Field '$picklistFieldName' is already present <br>";

    $picklistValues = [];
    $i = 1;
    foreach ($picklistOrder as $value) {
        if ($value) {
            $id = getPicklistId($picklistFieldName, $value);
            if ($id !== false) {
                //so we didn't find the ID we assume it doesn't exist and add it
                print "<br> Adding $value value to picklist field $picklistFieldName. <br>";
                $id = addNewPicklistItem($value, $picklistFieldName, $moduleName);
            }
            if ($id !== false) {
                //ensure we skip anything that failed to create so we don't do something unexpected
                $picklistValues[$id] = $i++;
            }
        }
    }

    if ($picklistFieldName && is_array($picklistValues) && count($picklistValues) > 0) {
        updatePicklistOrder($picklistFieldName, $picklistValues);
    } else {
        print "picklistFieldName => $picklistFieldName<br />";
        print 'picklistValues => ' . print_r($picklistValues, true) . '<br />';
        print 'ERROR: Unable to update picklist order. <br />';
    }
} else {
    print "Creating new field: $picklistFieldName.<br />";

    $field1 = new Vtiger_Field();
    $field1->label = $fieldLabel;
    $field1->name = $picklistFieldName;
    $field1->table = $fieldTable;
    $field1->column = $picklistFieldName;
    $field1->columntype = $fieldColumnType;
    $field1->uitype = $fieldUIType;
    $field1->typeofdata = $fieldTypeOfData;

    $block->addField($field1);
    $field1->setPicklistValues($picklistOrder);

    reorderBlock($fieldSeq, $block, $module);
}

function reorderBlock($fieldSeq, $block, $module)
{
    $db = PearDatabase::getInstance();
    $push_to_end = [];
    foreach ($fieldSeq as $name=>$seq) {
        $field = Vtiger_Field::getInstance($name, $module);
        if ($field) {
            $sql = 'SELECT fieldname FROM `vtiger_field` WHERE sequence = ? AND block = ?';
            $result = $db->pquery($sql, [$seq, $block->id]);
            if ($result) {
                while ($row = $result->fetchRow()) {
                    $push_to_end[] = $row[0];
                }
            }
            Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$seq.' WHERE fieldname= "'.$name.'" AND fieldid = '.$field->id);
        }
        unset($field);
    }
    //push anything that might have gotten added and isn't on the list to the end of the block
    $max = $db->pquery('SELECT MAX(sequence) FROM `vtiger_field` WHERE block = ?', [$block->id])->fetchRow()[0]+1;
    foreach ($push_to_end as $name) {
        //only push stuff that isn't in our array of things to position to prevent moving things that were in the right order to start
        if (!array_key_exists($name, $fieldSeq)) {
            $field = Vtiger_Field::getInstance($name, $module);
            if ($field) {
                Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$max.' WHERE fieldname= "'.$name.'" AND fieldid = '.$field->id);
                $max++;
            }
        }
    }
}

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
function addNewPicklistItem($newValue, $pickListName, $moduleName)
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
            print "Successfully added new picklist value ($id) for $pickListName in $moduleName. <br />";
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
function updatePicklistOrder($pickListFieldName, $picklistValues)
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
function getPicklistId($picklistFieldName, $picklistValue)
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
        $rv = $row[$picklistFieldName.'id'];
    }
    return $rv;
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";