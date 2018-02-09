<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once('libraries/nusoap/nusoap.php');

class EffectiveDates_MassActionAjax_View extends Vtiger_MassActionAjax_View
{
    public function handleCustomDuplication($moduleName, $selectedId, $newRecordId)
    {
        $oldEffectiveDate = Vtiger_Record_Model::getInstanceById($selectedId, $moduleName);
        $EffectiveDatesRecordModel = Vtiger_Record_Model::getInstanceById($newRecordId,$moduleName);

        // duplicate tariff services
        if (!empty($oldEffectiveDate->get('related_tariff'))){
            $lastestRecordID = $EffectiveDatesRecordModel->get('id');
            $adb = PearDatabase::getInstance();
            $selectTariffServicesRecords = "SELECT *
                FROM `vtiger_tariffservices`
                INNER JOIN `vtiger_crmentity`
                ON `vtiger_crmentity`.`crmid` = `vtiger_tariffservices`.`tariffservicesid`
                WHERE `vtiger_crmentity`.`deleted` != TRUE
                AND `vtiger_tariffservices`.`effective_date` = ?
                AND `vtiger_tariffservices`.`related_tariff` = ?";
            $result = $adb->pquery($selectTariffServicesRecords,array($selectedId, $oldEffectiveDate->get('related_tariff')));
            if ($adb->num_rows($result)){
                while ($item = $adb->fetch_array($result)){
                    $TariffServicesModuleInstance = Vtiger_Module::getInstance('TariffServices');
                    $TariffServicesFields = $TariffServicesModuleInstance->getFields();
                    $TariffServicesRecordModel = Vtiger_Record_Model::getCleanInstance('TariffServices');
                    foreach ($TariffServicesFields as $tariffServiceItemField){
                        if (isset($item[$tariffServiceItemField->column])){
                            if ($tariffServiceItemField->column == 'effective_date'){
                                $TariffServicesRecordModel->set($tariffServiceItemField->column,$lastestRecordID);
                            }else{
                                $TariffServicesRecordModel->set($tariffServiceItemField->column,$item[$tariffServiceItemField->column]);
                            }
                        }
                    }

                    $TariffServicesRecordModel->set('assigned_user_id',$item['smownerid']);
                    $TariffServicesRecordModel->save();
                }
            }
        }

        return false;
    }
}
