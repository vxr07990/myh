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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;

$Vtiger_Utils_Log = true;

echo '<h2>4114 Actuals Module - Update Layout</h2>';

/**
 * @param array $fieldNames
 * @param Vtiger_Module $moduleInstance
 */
$hideFields = function ($fieldNames, $moduleInstance)
{
    global $adb;

    echo '<h3>Hide the fields</h3>';

    $tabid = $moduleInstance->getId();
    $sql = "UPDATE vtiger_field SET presence = 1";
    $params = array();

    if (is_string($fieldNames)) {
        $fieldNames = array($fieldNames);
    }

    if (count($fieldNames) == 0) {
        // empty fields
        return;
    }

    $strFieldNames = implode("','", $fieldNames);
    $strFieldNames = "('{$strFieldNames}')";

    $sql .= " WHERE fieldname IN {$strFieldNames} AND tabid = ?";
    $params[] = $tabid;
    $adb->pquery($sql, $params);

    echo '<br>Done - Hide the fields<br>';
};

/**
 * @param array $dataBlocksAndFields
 * @param Vtiger_Module $moduleInstance
 */
/*
Example:
$dataBlocksAndFields = array(
    'LBL_APPLICATIONS_INFORMATION' => array(	// block name
        'cf_application_status' => array(		// field name
            'label' => 'Application Status',	// label
            'table' => 'vtiger_applicationscf',	// table
            'uitype' => 16,						// type
            'picklistvalues' => array('New' ,'In', 'Progress', 'Reviewed', 'Closed', 'Deferred', 'Refused')	// picklist if uitype is picklist
        )
    )
);
 */
$createFields = function ($dataBlocksAndFields, $moduleInstance)
{
    global $adb;

    echo '<h3>Create new fields</h3>';
    echo '<ul>';

    foreach ($dataBlocksAndFields as $blockLabel => $fieldList) {
        $blockInstance = Vtiger_Block::getInstance($blockLabel, $moduleInstance);

        if (!$blockInstance && $blockLabel) {
            $blockInstance = new Vtiger_Block();
            $blockInstance->label = $blockLabel;
            $blockInstance->__create($moduleInstance);

            echo "<li>Block {$blockLabel} not exist. {$blockLabel} is created</li>";
        }

        $currentFieldSeqRs = $adb->pquery("SELECT sequence FROM `vtiger_field` WHERE block = ? ORDER BY sequence DESC LIMIT 0,1", array($blockInstance->id));
        $fieldSequence = $adb->query_result($currentFieldSeqRs, 'sequence', 0);

        foreach ($fieldList as $fieldName => $fieldInfo) {
            $fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);

            if ($fieldInstance) {
                echo "<li>Field {$fieldName} exist</li>";
                continue;
            }

            // Create new field
            if ($fieldName && $fieldInfo['table']) {
                $fieldInstance = new Vtiger_Field();
                $fieldInstance->name = $fieldName;
                $fieldInstance->label = $fieldInfo['label'];
                $fieldInstance->table = $fieldInfo['table'];
                $fieldInstance->uitype = $fieldInfo['uitype'];

                // Picklist
                if ($fieldInfo['uitype'] == 15 || $fieldInfo['uitype'] == 16 || $fieldInfo['uitype'] == '33') {
                    $fieldInstance->setPicklistValues($fieldInfo['picklistvalues']);
                }

                $fieldInstance->sequence = $fieldSequence++;

                // Create field
                $fieldInstance->__create($blockInstance);

                // Related modules
                if ($fieldInfo['uitype'] == 10) {
                    $fieldInstance->setRelatedModules(array($fieldInfo["related_to_module"]));
                }

                echo "<li>Field {$fieldName} created</li>";
            } else {
                echo "<li>Invalid field {$fieldName}</li>";
            }
        }
    }

    echo '</ul>';
    echo '<br>Done - Create new fields<br>';

};

/**
 * @param array $fieldNames - format: array(fieldname1 => 1, fieldname2 => 2)
 * @param Vtiger_Module $moduleInstance
 * @param int $blockId
 */
$changeFieldOrders = function ($fieldNames, $moduleInstance, $blockId = 0)
{
    // Check valid
    if (!$fieldNames || count($fieldNames) == 0 || !$moduleInstance) {
        return;
    }

    global $adb;
    echo '<h3>Change field orders</h3>';

    $tabid = $moduleInstance->getId();
    $maxSequence = 0;
    $arrOrderedFields = array();

    foreach ($fieldNames as $fieldName => $sequence) {
        $sql = "UPDATE vtiger_field SET sequence = ? WHERE fieldname = ? AND tabid = ?";
        $params = array($sequence, $fieldName, $tabid);

        if ($blockId) {
            $sql .= ' AND block = ?';
            $params[] = $blockId;
        }

        $adb->pquery($sql, $params);

        // Update max sequence
        if ($maxSequence < $sequence) {
            $maxSequence = $sequence;
        }

        $arrOrderedFields[] = $fieldName;
    }

    // Reorder
    if (!$blockId) {
        $sql = "SELECT block FROM vtiger_field WHERE tabid = ? AND fieldname LIKE ?";
        $params = array($tabid, $arrOrderedFields[0]);
        $query = $adb->pquery($sql, $params);
        $blockId = $adb->query_result($query, 'block', 0);
    }

    $sql = "SELECT fieldid, fieldname, sequence
            FROM vtiger_field
            WHERE `tabid` = ? AND block = ? AND fieldname NOT IN ( " . generateQuestionMarks($arrOrderedFields) . ")
            ORDER BY sequence, fieldid ASC;";
    $params = array($tabid, $blockId, $arrOrderedFields);
    $query = $adb->pquery($sql, $params);

    if($adb->num_rows($query) > 0){
        while ($data = $adb->fetchByAssoc($query)){
            $fieldName = $data['fieldname'];
            $sql = "UPDATE vtiger_field SET sequence = ? WHERE fieldname = ? AND tabid = ? AND block = ?";
            $params = array(++$maxSequence, $fieldName, $tabid, $blockId);

            $adb->pquery($sql, $params);
        }
    }

    echo '<br>Done - Change field orders<br>';
};

/**
 * @param array $fieldNames
 * @param Vtiger_Module $moduleInstance
 */
$changeTypeofdataFields = function ($fieldNames, $moduleInstance) {
    global $adb;

    echo '<h3>Change typeofdata fields</h3>';

    $tabid = $moduleInstance->getId();

    foreach ($fieldNames as $fieldName => $typeofdata) {
        $sql = "UPDATE vtiger_field SET typeofdata = ? WHERE fieldname = ? AND tabid = ?";
        $params = array($typeofdata, $fieldName, $tabid);
        $adb->pquery($sql, $params);
    }

    echo '<br>Done - Change typeofdata fields<br>';
};

/****************************************************************************************************
 * All actions
 */

$moduleName = 'Actuals';
$moduleInstance = Vtiger_Module::getInstance($moduleName);
//$tabid = getTabid($moduleName);

// Hide the fields
$fieldsToHide = array(
    'potential_id',
    'is_primary'
);
$hideFields($fieldsToHide, $moduleInstance);

// Change order of the fields
$fieldsToReorder = array(
    'subject' => 1,
    'actuals_stage' => 2,
    'orders_id' => 3,
    'contact_id' => 4,
    'business_line_est2' => 5,
    'billing_type' => 6,
    'authority' => 7,
    'effective_tariff' => 8,
    'account_id' => 9,
    'contract' => 10,
    'validtill' => 11,
    'load_date' => 12,
    'quotation_type' => 13,
    'estimate_type' => 14,
    'assigned_user_id' => 15,
    'agentid' => 16,
    'createdtime' => 17,
    'modifiedtime' => 18,
    'quote_no' => 19,
);
$changeFieldOrders($fieldsToReorder, $moduleInstance);

// Create new fields
$fieldsToCreate = array(
    'LBL_ADDRESS_INFORMATION' => array(
        'origin_description' => array(
            'label' => 'Origin Description',
            'table' => 'vtiger_quotes',
            'columntype' => 'varchar(255)',
            'uitype' => 16,
            'typeofdata' => 'V~O',
            'picklistvalues' => array("Single Family", "Multi Family", "Office Building", "Self Storage", "Warehouse", "Other"),
        ),
        'destination_description' => array(
            'label' => 'Destination Description',
            'table' => 'vtiger_quotes',
            'columntype' => 'varchar(255)',
            'uitype' => 16,
            'typeofdata' => 'V~O',
            'picklistvalues' => array("Single Family", "Multi Family", "Office Building", "Self Storage", "Warehouse", "Other"),
        ),
    )
);
$createFields($fieldsToCreate, $moduleInstance);

// Change typeofdata fields
$fieldToChangeTypeofdata = array(
    'business_line_est2' => 'V~M',
    'orders_id' => 'V~M',
    'billing_type' => 'V~M',
    'effective_tariff' => 'I~M',
);
$changeTypeofdataFields($fieldToChangeTypeofdata, $moduleInstance);

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";