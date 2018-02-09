<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 7/24/2017
 * Time: 10:03 AM
 */
class RecordProtection_Record_Model extends Vtiger_Record_Model
{
    public function getModulePicklistValues(){
        $moduleModels = Vtiger_Module_Model::getAll(array(0, 2), Settings_Profiles_Module_Model::getNonVisibleModulesList());
        $picklistValues = [];
        foreach($moduleModels as $moduleModel){
            $picklistValues[$moduleModel->getName()] = vtranslate($moduleModel->getName(), $moduleModel->getName());
        }
        return $picklistValues;
    }

    public function getFlagNamePicklistValues(){
        $db=PearDatabase::getInstance();
        $picklistValues = [];
        $sql = 'DESCRIBE `vtiger_crmentity_flags';
        $result = $db->pquery($sql);
        while($row = $result->fetchRow()){
            if($row['Field'] != 'crmid'){
                $picklistValues[$row['Field']] = vtranslate($row['Field'], 'RecordProtection');
            }
        }
        return $picklistValues;
    }

    function updateFlagsOnExistingRecords($moduleModel, $agentid) {
        $db = PearDatabase::getInstance();
        if(!$moduleModel || !($flagColumns = $moduleModel->getFlagsForProtection())){
            return;
        }
        $moduleName = $moduleModel->getName();
        $protectedTableName = $moduleModel->basetable;
        $protectedIdColumn =  $moduleModel->basetableid;
        $recordsToCheck = [];
        $recordQuery = "SELECT $protectedIdColumn FROM`$protectedTableName` 
                    LEFT JOIN `vtiger_crmentity_flags` ON `vtiger_crmentity_flags`.crmid = $protectedIdColumn 
                    LEFT JOIN `vtiger_crmentity` ON `vtiger_crmentity`.crmid = $protectedIdColumn
                    WHERE `vtiger_crmentity`.deleted != 1";
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
                            $flagColumns = [$flagColumns => 1];
                        }
                        $columnArray = [];
                        $valueArray = [];
                        foreach ($flagColumns as $flagColumn => $flagValue) {
                            $columnArray[] = $flagColumn;
                            $valueArray[] = $flagValue;
                        }
                        $valueArray[] = $protectRecord;
                        $result = $db->pquery('SELECT * FROM `vtiger_crmentity_flags` WHERE crmid = ?', [$protectRecord]);
                        if($db->num_rows($result) > 0){
                            $columnClause = implode('=?, ', $columnArray);
                            $columnClause .= '=?';
                            $query = 'UPDATE `vtiger_crmentity_flags` SET '.$columnClause.' WHERE crmid = ?';
                        } else {
                            $columnClause = '('.implode(',', $columnArray).',crmid)';
                            $query = 'INSERT INTO `vtiger_crmentity_flags` '.$columnClause.' VALUES ('.generateQuestionMarks($columnArray).',?)';
                        }
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

}


