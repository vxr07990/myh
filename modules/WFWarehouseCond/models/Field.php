<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 6/29/2017
 * Time: 12:40 PM
 */
include_once 'vtlib/Vtiger/Field.php';


class WFWarehouseCond_Field_Model extends Vtiger_Field_Model {

    public function getPicklistValues() {
        $fieldDataType = $this->getFieldDataType();

        //Custom logic for the multipicklists in this module and in the closely related WFDriverCond module. Will need to be revisited if additional
        //multipicklists are added to either of these modules.

        if ($fieldDataType == 'multipicklist') {
            global $adb;
            $fieldColumn = $this->column;
            $fieldTable = 'vtiger_'.$fieldColumn;
            $picklistValues = [];
            $user     = Users_Record_Model::getCurrentUserModel();
            $vanLines = $user->getAccessibleVanlinesForUser();
            foreach ($vanLines as $vanLineid=>$vanLineName){
                $vanLineLabels[] = $vanLineName;
            }
            $sql = "SELECT $fieldColumn FROM `$fieldTable` WHERE vanline IN (".generateQuestionMarks($vanLineLabels).") 
                    UNION ALL 
                    SELECT $fieldColumn FROM `$fieldTable` WHERE 
                    NOT EXISTS (
                    SELECT $fieldColumn FROM `$fieldTable` WHERE vanline IN (".generateQuestionMarks($vanLineLabels).")) AND vanline = ?";
            $result = $adb->pquery($sql, [$vanLineLabels, $vanLineLabels, 'Base']);
            while($row = $adb->fetch_row($result)){
                $picklistValues[$row[$fieldColumn]] = vtranslate($row[$fieldColumn], 'WFInventory');
            }
            return $picklistValues;
        }
        return parent::getPicklistValues();
    }

}
