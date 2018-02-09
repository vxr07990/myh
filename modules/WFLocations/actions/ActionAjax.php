<?php

class WFLocations_ActionAjax_Action extends Vtiger_ActionAjax_Action
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('getInfoItemLocationType');
        $this->exposeMethod('getPrimaryLocationInfo');
        $this->exposeMethod('getBaseLocationSlots');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    public function getInfoItemLocationType(Vtiger_Request $request)
    {
        global $adb;

        $locationType = trim($request->get('location'));
        $sql = "SELECT
                    `vtiger_wflocationtypes`.`wflocationtypes_prefix`,
                    `vtiger_wflocationtypes`.`base`,
                    `vtiger_wflocationtypes`.`container`,
                    `vtiger_wflocationtypes`.`is_default`
                FROM `vtiger_wflocationtypes`
                INNER JOIN `vtiger_crmentity`
                ON `vtiger_crmentity`.`crmid` = `vtiger_wflocationtypes`.`wflocationtypesid`
                WHERE `vtiger_crmentity`.`deleted` = 0
                AND `vtiger_wflocationtypes`.`wflocationtypesid` = ?";

        $dataResult = $adb->pquery($sql,array($locationType));
        if ($adb->num_rows($dataResult)){
            $result = $adb->fetchByAssoc($dataResult);
        }

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
    public function getPrimaryLocationInfo(Vtiger_Request $request){
        $record = $request->get('record');
        $result = array();
        if(!empty($record) && is_numeric($record)){
            $recordModel = Vtiger_Record_Model::getInstanceById($record);
            $result['tag'] = $recordModel->get('tag');
            $result['base_location_type'] = WFLocations_Edit_View::getBaseLocationType($record, true);
        }
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

    public function getBaseLocationSlots(Vtiger_Request $request){
        $baseLocation = $request->get('record');
        $result = false;
        if(!empty($baseLocation) && is_numeric($baseLocation)){
            $recordModel = Vtiger_Record_Model::getInstanceById($baseLocation);
            $recordModule = Vtiger_Module::getInstance($recordModel->get('record_module'));
            $baseSlot = Vtiger_Field_Model::getInstance('base_slot', $recordModule);
            $result['picklistArray'] = $baseSlot->getPicklistValues($baseLocation);
        }
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

}
