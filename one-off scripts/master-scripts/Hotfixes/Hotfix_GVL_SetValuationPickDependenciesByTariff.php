<?php
if (function_exists("call_ms_function_ver")) {
    $version = 7;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


//OT 16724 - Valuation drop down for MMI Tariff should include different items from 1950B tariff.

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
//Need to add a value to the base picklist as it exists.

echo "<br>Starting to set picklist for Valuation based on effective tariff</br>\n";
$newValues = [
    'Full Value Protection',
    'Vehicle Coverage',
    'Carrier Based Liability',
    'Vehicle Transport',
    'Full Replacement Value',
    'Replacement Value Protection',
    'Full Value Replacement',
    'Released Value',
    'Free FVP',
    '$0.60 Released',
    'Option A',
    'Option B',
    'Option C'
];


$module = Vtiger_Module::getInstance('Actuals');

if (!$module) {
    echo "<br>Module missing. Exiting.";
    return;
}

$field = Vtiger_Field::getInstance('valuation_deductible', $module);

if (!$field) {
    echo "Valuation_deductible field missing. Unable to update picklist. Exiting.";
    return;
}

$db = PearDatabase::getInstance();

$db->pquery('TRUNCATE TABLE `vtiger_valuation_deductible`');
// same picklist table is used in contracts, estimates, actuals, and orders, so this should update them all
$field->setPicklistValues($newValues);
echo "Valuation_deductible picklist updated.";

// valuation options are now magically handled, so get rid of picklist dependency

$db->pquery('DELETE FROM vtiger_picklist_dependency WHERE targetfield=?', ['valuation_deductible']);

return;


$affectedModules = [
    //'Contracts',
    'Estimates',
    'Actuals',
    'Orders'
];

$targetField = 'valuation_deductible';

foreach ($affectedModules as $moduleName) {
    $sourceField = 'effective_tariff';
    $currentModule = Vtiger_Module::getInstance($moduleName);
    if (!$currentModule) {
        echo "Was not able to update $modulename module.";
        continue;
    }
    if ($moduleName == 'Orders') {
        $sourceField = 'tariff_id';
    }
    setPicklistDependenciesSVPD($currentModule, $sourceField, $targetField);
}

function setPicklistDependenciesSVPD($module, $sourceField, $targetField)
{
    //Note: These are strings, not arrays.
    // 1950-B, 400N, 400N - 104G, AIReS, RMX400, ISRS200-A
    $targetVal1 = '["Full Replacement Value","Carrier Based Liability"]';
    // MMI
    $targetVal2 = '["Replacement Value Protection","Released Value"]';
    // MSI
    $targetVal3 = '["Released Value","Full Value Replacement"]';
    // Cap Relo
    $targetCapRelo = '["Option A","Option B","Option C"]';
    // (all others)
    $targetValOther = '["$0.60 Released","Full Value Protection","Vehicle Coverage","Carrier Based Liability","Vehicle Transport","Full Replacement Value","Full Value Replacement","Replacement Value Protection","Released Value","Free FVP"]';
    $db = PearDatabase::getInstance();
    $selectSql = 'SELECT * FROM `vtiger_picklist_dependency` WHERE '
                 .' `tabid` = ? AND '
                 .' `sourcefield`= ? AND '
                 .' `targetfield` = ?';
    $selectResult = $db->pquery($selectSql, array($module->id, $sourceField, $targetField));

    if ($selectResult->numRows() > 0) {
        echo "<br>Dependencies already exist. Removing.<br>\n";
        $deleteSql = 'DELETE FROM `vtiger_picklist_dependency` WHERE '
                     .' `tabid` = ? AND '
                     .' `sourcefield`= ? AND '
                     .' `targetfield` = ?';
        $removed = $db->pquery($deleteSql, array($module->id, $sourceField, $targetField));
        if ($removed) {
            echo "Dependencies successfully reset for $sourceField and $targetField in $module->name<br>\n";
        }
    }
        //Gotta figure out the minimum ID I'm allowed.
        $idCheckSql = 'SELECT MAX(`id`) AS `maxID` FROM `vtiger_picklist_dependency` LIMIT 1';
    $idCheckResult = $db->pquery($idCheckSql);
    if ($idCheckResult && $idCheckRow = $idCheckResult->fetchRow()) {
        $minID = $idCheckRow['maxID'] + 1;
    } else {
        $minID = 1;
    }

    $dependencyID = $minID;
        //Special case. Where picklist is sourced from a table that uses the name of the field, use this next line. Otherwise, base off of line below.
        //$sourceFieldSql = 'SELECT `'.$sourceField.'` FROM `vtiger_'.$sourceField.'`';
        $sourceFieldSql = 'SELECT `custom_tariff_type`, `tariffmanagerid` FROM `vtiger_tariffmanager`';
    $sourceChoices = $db->pquery($sourceFieldSql);
        //When picklist is sourced from table that uses the name of the field, replace all $choiceRow['tariffmanagername'] with $choiceRow[$sourceField]
        while ($choiceRow = $sourceChoices->fetchRow()) {
            if (in_array($choiceRow['custom_tariff_type'], ['1950-B', '400N Base', '400N/104G', 'AIReS', 'RMX400', 'ISRS200-A'])) {
                $targetList = $targetVal1;
            } elseif ($choiceRow['custom_tariff_type'] == 'MMI') {
                $targetList = $targetVal2;
            } elseif ($choiceRow['custom_tariff_type'] == 'MSI') {
                $targetList = $targetVal3;
            } elseif ($choiceRow['custom_tariff_type'] == '09CapRelo') {
                $targetList = $targetCapRelo;
            } else {
                $targetList = $targetValOther;
            }
            $insertSql = 'INSERT INTO `vtiger_picklist_dependency` SET
                `id` = ?,
                `tabid` = ?,
                `sourcefield` = ?,
                `targetfield` = ?,
                `sourcevalue` = ?,
                `targetvalues` = ?,
                `criteria` = ?';
            $db->pquery($insertSql, array($dependencyID++, $module->id, $sourceField, $targetField, $choiceRow['tariffmanagerid'], $targetList, null));
        }
        //One more insert to add a null value

        $insertSql = 'INSERT INTO `vtiger_picklist_dependency` SET
                `id` = ?,
                `tabid` = ?,
                `sourcefield` = ?,
                `targetfield` = ?,
                `sourcevalue` = ?,
                `targetvalues` = ?,
                `criteria` = ?';
    $db->pquery($insertSql, array($dependencyID, $module->id, $sourceField, $targetField, '', '[]', null));
    echo"<br> Updated dependencies for $module->name Module<br>\n";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";