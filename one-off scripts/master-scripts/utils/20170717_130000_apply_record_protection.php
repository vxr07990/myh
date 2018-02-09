<?php

//Should be run whenever flags are applied to a module. Adds the existing records to the vtiger_crmentity_flags table
//TODO: Set up another script to be run when flags change on a module. This one excludes existing records.

print "\e[32mRUNNING: ".__FILE__."<br />\n\e[0m";
$Vtiger_Utils_Log = true;
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$flagsTable = 'vtiger_crmentity_flags';
$db = PearDatabase::getInstance();

$result = $db->pquery('SHOW TABLES LIKE ?', [$flagsTable]);
if ($db->num_rows($result) < 1){
    return;
}

$moduleModels = Vtiger_Module_Model::getAll();


foreach($moduleModels as $moduleModel){
    protectExistingRecords($moduleModel);
}


function protectExistingRecords($moduleModel) {
    $db = PearDatabase::getInstance();
    if(!$moduleModel || !($flagColumns = $moduleModel->getFlagsForProtection())){
        return;
    }
    $moduleName = $moduleModel->getName();
    $protectedTableName = $moduleModel->basetable;
    $protectedIdColumn = strtolower($moduleName).'id';
    $recordsToCheck = [];
    $recordQuery = "SELECT $protectedIdColumn FROM`$protectedTableName` 
                    LEFT JOIN `vtiger_crmentity_flags` ON `vtiger_crmentity_flags`.crmid = $protectedIdColumn 
                    LEFT JOIN `vtiger_crmentity` ON `vtiger_crmentity`.crmid = $protectedIdColumn
                    WHERE `vtiger_crmentity_flags`.crmid IS NULL
                    AND `vtiger_crmentity`.deleted != 1";
    $result = $db->pquery($recordQuery, []);
    while($row = $result->fetchRow()){
        $recordsToCheck[] = $row[$protectedIdColumn];
    }

    if(empty($recordsToCheck)){
        return;
    }
    $referenceFields = [];
    $referenceQuery = "SELECT * FROM `vtiger_fieldmodulerel` WHERE relmodule = ?";
    $result = $db->pquery($referenceQuery, [$moduleName]);
    while($row = $result->fetchRow()){
        $referenceFields[$row['fieldid']] = $row['module'];
    }

    foreach($recordsToCheck as $protectRecord){
        foreach($referenceFields as $fieldId => $fieldModuleName){
            $refModuleModel = Vtiger_Module_Model::getInstance($fieldModuleName);
            if(!$refModuleModel || !($refFieldTable = $refModuleModel->basetable)){
                continue;
            }
            //getInstanceFromFieldId returns an array instead of the field object
            $refFieldArray = Vtiger_Field_Model::getInstanceFromFieldId($fieldId, $refModuleModel->getId());
            if($refFieldArray){
                $refFieldColumn = $refFieldArray[0]->get('column');
                $sql = "SELECT * FROM `$refFieldTable` WHERE $refFieldColumn = ?";
                $result = $db->pquery($sql, [$protectRecord]);
                if($db->num_rows($result) > 0){
                    if(!is_array($flagColumns)){
                        $flagColumns = [$flagColumns];
                    }
                    $valueArray = [];
                    foreach ($flagColumns as $x) {
                        $valueArray[] = 1;
                    }
                    $valueArray[] = $protectRecord;
                    $columnClause = '('.implode(',', $flagColumns).',crmid)';
                    $query = 'INSERT INTO `vtiger_crmentity_flags` '.$columnClause.' VALUES ('.generateQuestionMarks($flagColumns).',?)';
                    $db->pquery($query, $valueArray);
                    //Bail out since we only want one row added for a protected record.
                    break;
                }

            } else {
                continue;
            }
        }
    }
}

