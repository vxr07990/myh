<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class TariffServices_Relation_Model extends Vtiger_Relation_Model {

    //@TODO: sadly you probably need to be: modules/EffectiveDates/models/Relation.php
//    public function deleteRelation($sourceRecordId, $relatedRecordId) {
//        //parent::deleteRelation($sourceRecordId, $relatedRecordId);
//
//        if (!$relatedRecordId) {
//            return true;
//        }
//
//        try {
//            $recordModel = Vtiger_Record_Model::getInstanceById($relatedRecordId);
//            $recordModel->delete();
//        } catch (Exception $ex) {
//            //blah
//        }
//
//        return true;
//    }
}
