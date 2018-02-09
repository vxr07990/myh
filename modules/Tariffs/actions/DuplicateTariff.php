<?php

class Tariffs_DuplicateTariff_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }
    public function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $record     = $request->get('record');
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
        $modelData = $recordModel->getData();
        $recordModel->set('id','');
        $recordModel->set('mode', '');

        $fieldModelList = $moduleModel->getFields();
        foreach ($fieldModelList as $fieldName => $fieldModel) {
            $fieldValue = $request->get($fieldName, null);
            $fieldDataType = $fieldModel->getFieldDataType();
            if ($fieldDataType == 'time') {
                $fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
            }
            if ($fieldValue !== null) {
                if (!is_array($fieldValue) && $fieldValue != 'id') {
                    $fieldValue = trim($fieldValue);
                }
                $recordModel->set($fieldName, $fieldValue);
            }
        }
        
        $recordModel->save();
        $tariffsid = $recordModel->getId();
        $this->duplicateEffectiveDates($record,$tariffsid);

        $this->duplicateTariffSections($record,$tariffsid);

        $loadUrl = $recordModel->getDetailViewUrl();
        header('Location: '.$loadUrl);

    }
    private function duplicateEffectiveDates($record,$tariffsid){
        global $adb;
        $sqlEffectiveDates = "SELECT * FROM vtiger_effectivedates JOIN vtiger_crmentity ON (vtiger_effectivedates.effectivedatesid = vtiger_crmentity.crmid) WHERE related_tariff = ? AND vtiger_crmentity.deleted = ?";
        $rsEffectiveDates = $adb->pquery($sqlEffectiveDates,array($record,'0'));
        if($countrsEffectiveDates = $adb->num_rows($rsEffectiveDates) > 0){
            $moduleModelEffectiveDates = Vtiger_Module_Model::getInstance('EffectiveDates');
            $fieldModelListEffectiveDates = $moduleModelEffectiveDates->getFields();

            while ($dataEffectiveDates = $adb->fetchByAssoc($rsEffectiveDates)) {

                $oldRecordModelEffectiveDates = Vtiger_Record_Model::getInstanceById($dataEffectiveDates['effectivedatesid']);
                $newRecordModelEffectiveDates = Vtiger_Record_Model::getCleanInstance("EffectiveDates");
                $newRecordModelEffectiveDates->set('id','');
                $newRecordModelEffectiveDates->set('mode', '');
                foreach ($fieldModelListEffectiveDates as $fieldName => $fieldModel) {
                    $fieldValue = $oldRecordModelEffectiveDates->get($fieldName, null);
                    if($fieldName == 'related_tariff'){
                        $fieldValue = $tariffsid;
                    }else{
                        $fieldDataType = $fieldModel->getFieldDataType();
                        if ($fieldDataType == 'time') {
                            $fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
                        }
                    }
                    if ($fieldValue !== null) {
                        if (!is_array($fieldValue) && $fieldValue != 'id') {
                            $fieldValue = trim($fieldValue);
                        }
                        $newRecordModelEffectiveDates->set($fieldName, $fieldValue);
                    }

                }
                $newRecordModelEffectiveDates->save();
                $_REQUEST['repeat'] = false;


                $this->duplicateTariffServices($dataEffectiveDates['effectivedatesid'],$newRecordModelEffectiveDates->getId(),$tariffsid);

            }
        }
    }

    private function duplicateTariffSections($record,$tariffsid){
        global $adb;
        $sqlTariffSections = "SELECT * FROM vtiger_tariffsections JOIN vtiger_crmentity ON (vtiger_tariffsections.tariffsectionsid = vtiger_crmentity.crmid) WHERE related_tariff = ? AND vtiger_crmentity.deleted = ?";
        $rsTariffSections = $adb->pquery($sqlTariffSections,array($record,'0'));
        if($countrsTariffSections = $adb->num_rows($rsTariffSections) > 0){
            $moduleModelTariffSections = Vtiger_Module_Model::getInstance('TariffSections');
            $fieldModelListTariffSections = $moduleModelTariffSections->getFields();

            while ($dataTariffSections = $adb->fetchByAssoc($rsTariffSections)) {

                $oldRecordModelTariffSections = Vtiger_Record_Model::getInstanceById($dataTariffSections['tariffsectionsid']);
                $newRecordModel=Vtiger_Record_Model::getCleanInstance("TariffSections");
                $newRecordModel->set('id','');
                $newRecordModel->set('mode', '');
                foreach ($fieldModelListTariffSections as $fieldName => $fieldModel) {
                    $fieldValue = $oldRecordModelTariffSections->get($fieldName, null);
                    if($fieldName == 'related_tariff'){
                        $fieldValue = $tariffsid;
                    }else{
                        $fieldDataType = $fieldModel->getFieldDataType();
                        if ($fieldDataType == 'time') {
                            $fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
                        }
                    }
                    if ($fieldValue !== null) {
                        if (!is_array($fieldValue) && $fieldValue != 'id') {
                            $fieldValue = trim($fieldValue);
                        }
                        $newRecordModel->set($fieldName, $fieldValue);
                    }

                }
                $newRecordModel->save();
                $_REQUEST['repeat'] = false;
                $sqlUpdateTariffServices = "SELECT * FROM vtiger_tariffservices JOIN vtiger_crmentity ON (vtiger_tariffservices.tariffservicesid = vtiger_crmentity.crmid) WHERE related_tariff = ? AND tariff_section = ? AND vtiger_crmentity.deleted = ?";
                $rsTariffServices = $adb->pquery($sqlUpdateTariffServices,array($tariffsid,$dataTariffSections['tariffsectionsid'],'0'));
                while ($data = $adb->fetchByAssoc($rsTariffServices)) {
                    $adb->pquery("UPDATE vtiger_tariffservices SET tariff_section = ? WHERE tariffservicesid = ?",array($newRecordModel->getId(),$data['tariffservicesid']));
                }
            }
        }
    }

    private function duplicateTariffServices($oldId,$newId,$tariffsid){
        global $adb;
        $sqlTariffServices = "SELECT * FROM vtiger_tariffservices JOIN vtiger_crmentity ON (vtiger_tariffservices.tariffservicesid = vtiger_crmentity.crmid) WHERE effective_date = ? AND vtiger_crmentity.deleted = ?";
        $rsTariffServices = $adb->pquery($sqlTariffServices,array($oldId,'0'));
        if($countrsTariffServices = $adb->num_rows($rsTariffServices) > 0){
            $moduleModelTariffServices = Vtiger_Module_Model::getInstance('TariffServices');
            $fieldModelListTariffServices = $moduleModelTariffServices->getFields();

            while ($dataTariffServices = $adb->fetchByAssoc($rsTariffServices)) {

                $oldRecordModelTariffServices = Vtiger_Record_Model::getInstanceById($dataTariffServices['tariffservicesid']);
                $newRecordModelTariffServices = Vtiger_Record_Model::getCleanInstance("TariffServices");
                $newRecordModelTariffServices->set('id','');
                $newRecordModelTariffServices->set('mode', '');
                foreach ($fieldModelListTariffServices as $fieldName => $fieldModel) {
                    $fieldValue = $oldRecordModelTariffServices->get($fieldName, null);
                    if($fieldName == 'effective_date'){
                        $fieldValue = $newId;
                    }elseif($fieldName == 'related_tariff'){
                        $fieldValue = $tariffsid;
                    }else{
                        $fieldDataType = $fieldModel->getFieldDataType();
                        if ($fieldDataType == 'time') {
                            $fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
                        }
                    }
                    if ($fieldValue !== null) {
                        if (!is_array($fieldValue) && $fieldValue != 'id') {
                            $fieldValue = trim($fieldValue);
                        }
                        $newRecordModelTariffServices->set($fieldName, $fieldValue);
                    }

                }
                $newRecordModelTariffServices->save();
                $_REQUEST['repeat'] = false;


            }

        }
    }

    /* Duplicate rows in specified table by reference ID, and add new CF entry (if true).
     * $module = module to duplicate rows in
     * $refRow = row to reference with $refId
     * $refId = reference ID to find rows to duplicate
     * $newId = ID for new rows to reference
     * $table = manually send table name, needed if table is named differently than module.
     *
     * return = array with status (success (true), failed (false)), and a message if failed.
     */
}

?>
