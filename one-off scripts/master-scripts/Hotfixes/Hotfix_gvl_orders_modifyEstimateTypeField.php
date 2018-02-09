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



//OT16196 update Picklist for Estimate_type on Estimatse and Orders to be: ['Binding', 'Not To Exceed', 'Non Binding']

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

$picklistOptions = [
    'Binding',
    'Not To Exceed',
    'Non Binding'
];
$alreadyExists = false;
$picklistFieldName = 'estimate_type';

$moduleName = 'Orders';
$blockName = 'LBL_ORDERS_INFORMATION';
$module = Vtiger_Module::getInstance($moduleName);

echo "<br><h1>Starting To add $picklistFieldName in Orders</h1><br>\n";

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    $fieldCheck = Vtiger_Field::getInstance($picklistFieldName, $module);
    if ($fieldCheck) {
        $alreadyExists = true;
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_ORDERS_ESTIMATE_TYPE';
        $field->name = $picklistFieldName;
        $field->table = 'vtiger_orders';
        $field->column = $picklistFieldName;
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~O';

        $block->addField($field);
        $field->setPicklistValues($picklistOptions);
        echo '<p>Added ' . $picklistFieldName . ' Field</p>';
    }
}

echo "<br><h1>Finished Adding $picklistFieldName to Orders </h1><br>\n";

//@NOTE: all this information is the same.
foreach (['Estimates', 'Actuals'] as $moduleName) {
    $blockName = 'LBL_QUOTE_INFORMATION';
    $module    = Vtiger_Module::getInstance($moduleName);
    echo "<br><h1>Starting To add $picklistFieldName in $moduleName</h1><br>\n";
    $block = Vtiger_Block::getInstance($blockName, $module);
    if ($block) {
        $fieldCheck = Vtiger_Field::getInstance($picklistFieldName, $module);
        if ($fieldCheck) {
            $alreadyExists = true;
        } else {
            $field             = new Vtiger_Field();
            $field->label      = 'LBL_ORDERS_ESTIMATE_TYPE';
            $field->name       = $picklistFieldName;
            $field->table      = 'vtiger_quotes';
            $field->column     = $picklistFieldName;
            $field->columntype = 'VARCHAR(100)';
            $field->uitype     = '16';
            $field->typeofdata = 'V~O';
            $block->addField($field);
            $field->setPicklistValues($picklistOptions);
            echo '<p>Added ' . $picklistFieldName . ' Field</p>';
        }
    }
    echo "<br><h1>Finished $picklistFieldName to $moduleName</h1><br>\n";

    if ($alreadyExists) {
        echo '<p>' . $picklistFieldName . ' already existed, Update ' . $picklistFieldName . ' Picklist table</p>';
        //Updating the picklist.
        //@TODO: Do this better.
        blowOutPickList_gometf($picklistFieldName);
        foreach ($picklistOptions as $key => $value) {
            $id = getPicklistId_gometf($picklistFieldName, $value);
            print "ID: $id<br/>";
            if ($id === false) {
                //No ID found. Add it. Won't be any ID's if the picklist is blown out above.
                print "<br> Adding $value value to picklist field $picklistFieldName FOR : $moduleName. <br>";
                $newItem = addNewPicklistItem_gometf($value, $picklistFieldName, $moduleName);
                $id      = $key;
                print "<br> New ID: $id <br/>";
            }
            if ($newItem !== false) {
                //ensure we skip anything that failed to create so we don't do something unexpected
                $picklistValues[$id] = $key;
            }
        }
    }
}

/**
 * function to add a new picklist using their framework to ensure linkage
 * adapted from modules/Settings/Picklist/actions/SaveAjax.php
 *
 * @param string $newValue
 * @param string $pickListName
 * @param string $moduleName
 */
function addNewPicklistItem_gometf($newValue, $pickListName, $moduleName)
{
    $response = false;
    $moduleModel = Settings_Picklist_Module_Model::getInstance($moduleName);

    if (!$moduleModel) {
        return false;
    }

    $fieldModel = Settings_Picklist_Field_Model::getInstance($pickListName, $moduleModel);
    if (
        $fieldModel &&
        $fieldModel->isRoleBased()
    ) {
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

/**
 * function to pull the picklist's id's by name
 *
 * @param string $picklistFieldName
 * @param string $picklistValue
 *
 * @return int
 */
function getPicklistId_gometf($picklistFieldName, $picklistValue)
{
    $rv = false;
    $picklistTableName = 'vtiger_' . $picklistFieldName;

    if (Vtiger_Utils::CheckTable($picklistTableName)) {
        $db = PearDatabase::getInstance();
        //return * so we don't have to rely on escapeDbName here too.
        $sql    = 'SELECT * FROM '.$db->escapeDbName($picklistTableName)
                  .' WHERE '.$db->escapeDbName($picklistFieldName).' = ?'
                  .' LIMIT 1';
        $result = $db->pquery($sql, [$picklistValue]);
        $row    = $result->fetchRow();
        if (is_array($row) && count($row) > 0) {
            print "$sql;<br />";
            print "$picklistValue;<br />";
            print $picklistFieldName."<br />";
            print $row[$picklistFieldName.'id']."<br />";
            $rv = $row[$picklistFieldName.'id'];
        }
    }

    return $rv;
}

function blowOutPickList_gometf($picklistFieldName)
{
    $db = PearDatabase::getInstance();
    $picklistTableName = 'vtiger_' . $picklistFieldName;
    if (Vtiger_Utils::CheckTable($picklistTableName)) {
        echo "<br>Blowing out old picklist values now.";
        $db->pquery('TRUNCATE TABLE `'.$picklistTableName.'`', []);
        echo "<br>blowout complete, inserting new picklist values.";
    } else {
        echo "<br>$picklistTableName does not exist.";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";