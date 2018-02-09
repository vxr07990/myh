<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Orders_Relation_Model extends Vtiger_Relation_Model
{

    /**
     * Function that deletes Project related records information
     * @param <Integer> $sourceRecordId - Project Id
     * @param <Integer> $relatedRecordId - Related Record Id
     */
    public function deleteRelation($sourceRecordId, $relatedRecordId)
    {
        $sourceModule = $this->getParentModuleModel();
        $sourceModuleName = $sourceModule->get('name');
        $destinationModuleName = $this->getRelationModuleModel()->get('name');

        if ($destinationModuleName == 'Trips') {
                //Need to update the Order UIType 10 field with the trip
            $currentUser = Users_Record_Model::getCurrentUserModel();

                $orderArray['id'] = vtws_getWebserviceEntityId('Orders', $sourceRecordId);
                $orderArray['orders_trip'] = '';
            $orderArray['driver_trip'] = '';
            $orderArray['agent_trip'] = '';
            $orderArray['orders_assignedtrip'] = 0;
            try {
                vtws_revise($orderArray, $currentUser);
            } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            MoveCrm\LogUtils::LogToFile('LOG_CRM_FAILS', "VTWS ERROR = ".$exc->getMessage(), true);
            }
        } else {
//            $sourceModuleFocus = CRMEntity::getInstance($sourceModuleName);
//            $sourceModuleFocus->delete_related_module($sourceModuleName, $sourceRecordId, $destinationModuleName, $relatedRecordId);
            return parent::deleteRelation($sourceRecordId, $relatedRecordId);
        }
        return true;
    }

    public function addRelation($sourcerecordId, $destinationRecordId)
    {

	$sourceModule = $this->getParentModuleModel();
        $sourceModuleName = $sourceModule->get('name');
        $sourceModuleFocus = CRMEntity::getInstance($sourceModuleName);
        $destinationModuleName = $this->getRelationModuleModel()->get('name');

        if ($destinationModuleName == 'Trips') {
	    $tripRecordModel = Vtiger_Record_Model::getInstanceById($destinationRecordId, 'Trips');
            //Need to update the Order UIType 10 field with the trip
			$currentUser = Users_Record_Model::getCurrentUserModel();

            $orderArray['id'] = vtws_getWebserviceEntityId('Orders', $sourcerecordId);
            $orderArray['orders_trip'] = vtws_getWebserviceEntityId('Trips', $destinationRecordId);
	    $orderArray['driver_trip'] = vtws_getWebserviceEntityId('Employees', $tripRecordModel->get('driver_id'));
	    $orderArray['agent_trip'] = vtws_getWebserviceEntityId('Agents', $tripRecordModel->get('agent_unit'));
	    $orderArray['orders_assignedtrip'] = 1;

	    try {
		vtws_revise($orderArray, $currentUser);
	    } catch (Exception $exc) {
		echo $exc->getTraceAsString();
		MoveCrm\LogUtils::LogToFile('LOG_CRM_FAILS', "VTWS ERROR = ".$exc->getMessage(), true);
	    }

        } else {
            relateEntities($sourceModuleFocus, $sourceModuleName, $sourcerecordId, $destinationModuleName, $destinationRecordId);
        }
    }
}
