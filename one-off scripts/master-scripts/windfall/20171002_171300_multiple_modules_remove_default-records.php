<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 8/15/2017
 * Time: 9:07 AM
 */
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


//*/
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
require_once 'include/Webservices/Update.php';
require_once 'modules/Users/Users.php';
require_once 'include/utils/utils.php';

//*/
$db = PearDatabase::getInstance();


$itemsToClean = [
    'WFLocationTypes' => [
        'wflocationtypes_prefix' => 'WFLocations',
        ],
    'WFSlotConfiguration' => [
        'code' => 'WFLocations',
        ],
    'WFStatus' => [
        'wfstatus_code' => 'WFInventory'
        ],
    'WFConditions' => [
        'abbreviation' => 'WFInventory'
        ]
];
$relatedColumnNames = [
    'WFLocationTypes' => 'wflocation_type',
    'WFSlotConfiguration' => 'wfslot_configuration',
    'WFStatus' => 'wfstatus',
    'WFConditions' => 'wfcondition'
];


foreach($itemsToClean as $moduleName=>$associatedItems) {
    $moduleInstance = Vtiger_Module_Model::getInstance($moduleName);
    if (!$moduleInstance) {
        continue;
    }
    $defaultsTable = 'vtiger_'.strtolower($moduleName).'_defaults';
    $result        = $db->pquery('SHOW TABLES LIKE ?', [$defaultsTable]);
    if ($db->num_rows($result) == 0) {
        continue;
    }
    foreach($associatedItems as $uniqueColumn => $referencingModuleName) {
        $sql          = 'SELECT `'.$uniqueColumn.'` FROM `'.$defaultsTable.'`';
        $result       = $db->pquery($sql, []);
        $uniqueValues = [];
        while ($row = $result->fetchrow()) {
            foreach ($row as $columnName => $columnValue) {
                if (is_string($columnName)) {
                    $uniqueValues[] = $columnValue;
                }
            }
        }
        $retainValues = [];
        if(!$moduleInstance->basetableid){
            $moduleInstance->basetableid = strtolower($moduleName).'id';
        }
        if(!$moduleInstance->basetable){
            $moduleInstance->basetable = 'vtiger_'.strtolower($moduleName);
        }

        $retainSQL = 'SELECT `'.$moduleInstance->basetableid.'`, `'.$uniqueColumn.'`
                         FROM `'.$moduleInstance->basetable.'` 
                         LEFT JOIN `vtiger_crmentity` ON
                         `crmid` = `'.$moduleInstance->basetableid.'`
                         WHERE `'.$uniqueColumn.'`
                         IN ('.generateQuestionMarks($uniqueValues).')
                         AND `agentid` IS NULL
                         AND `deleted` = 0';
        $result = $db->pquery($retainSQL, $uniqueValues);
        $i = 0;
        while ($result && $row = $result->fetchrow()){
            $retainValues[$uniqueValues[$i]] = $row[$moduleInstance->basetableid];
            $i++;
        }
        foreach ($retainValues as $uniqueColumnVal => $retainRowID) {
            $relatedModuleInstance = Vtiger_Module::getInstance($referencingModuleName);
            if($relatedModuleInstance) {
                $relatedColumnName = $relatedColumnNames[$moduleName];
                $modifyList        = [];
                $modifyListSQL     = 'SELECT `'.$moduleInstance->basetableid.'`
                    FROM `'.$moduleInstance->basetable.'`
                    WHERE `'.$uniqueColumn.'` = ? 
                    AND NOT `'.$moduleInstance->basetableid.'` = ?';
                $result            = $db->pquery($modifyListSQL, [$uniqueColumnVal, $retainRowID]);
                while ($row = $result->fetchrow()) {
                    $modifyList[$uniqueColumnVal][] = $row[$moduleInstance->basetableid];
                }
                foreach ($modifyList as $val => $idArray) {
                    if (!empty($idArray)) {
                        $modifySQL = 'UPDATE `'.$relatedModuleInstance->basetable.'`
                                  SET `'.$relatedColumnName.'` = ?
                                  WHERE `'.$relatedColumnName.'`
                                  IN ('.generateQuestionMarks($idArray).')';
                        $db->pquery($modifySQL, [$retainValues[$val], $idArray]);
                    }
                }
            }
            $removalSQL = 'DELETE FROM `'.$moduleInstance->basetable.'`
                WHERE `'.$uniqueColumn.'` = ?
                AND NOT `'.$moduleInstance->basetableid.'` = ?';
            $db->pquery($removalSQL, [$uniqueColumnVal, $retainRowID]);
        }
    }


}
