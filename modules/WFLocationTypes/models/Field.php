<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 6/29/2017
 * Time: 12:40 PM
 */
include_once 'vtlib/Vtiger/Field.php';

/**
 * Opportunities Field Model Class
 */
class WFLocationTypes_Field_Model extends Vtiger_Field_Model {

    public function getPicklistValues($warehouse = 0, $record = 0)
    {
        if ($this->get('name') == 'wflocationtypes_prefix') {
            $baseList    = parent::getPicklistValues();
            $db          = PearDatabase::getInstance();
            $sql         = "SELECT wflocationtypes_prefix FROM `vtiger_wflocationtypes_defaults`";
            $result      = $db->pquery($sql, []);
            while ($row = $result->fetchrow()) {
                unset($baseList[$row['wflocationtypes_prefix']]);
            }
            if($warehouse > 0){
                $sql = "SELECT wflocationtypes_prefix FROM `vtiger_wflocationtypes` WHERE warehouse = ? AND wflocationtypesid <> ?";
                $result = $db->pquery($sql, [$warehouse, $record]);
                while ($row = $result->fetchrow()) {
                    unset($baseList[$row['wflocationtypes_prefix']]);
                }
            }
            return $baseList;
        }
        return parent::getPicklistValues();
    }

}
