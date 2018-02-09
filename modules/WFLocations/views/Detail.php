<?php
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Retrieve.php';
include_once 'include/Webservices/Revise.php';

class WFLocations_Detail_View extends Vtiger_Detail_View
{
    public function process(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $singles = ['tag','create_multiple', 'container_capacity','wfslot_configuration','cost'];
        $viewer->assign('SINGLE_FIELDS',$singles);
        $result = $this->getLocationTypeInfoForDetail($request);
        if($result){
            $viewer->assign('BASE_LOCATION',$result['base']);
            $viewer->assign('CONTAINER_LOCATION', $result['container']);
            $record = $request->get('record');
            $viewer->assign('BASE_TYPE', WFLocations_Edit_View::getBaseLocationType($record));
        }
        parent::process($request);
    }

    public function getLocationTypeInfoForDetail (Vtiger_Request $request){
        global $adb;
        $locationInstance = Vtiger_Record_Model::getInstanceById($request->get('record'));
        if(!$locationInstance){
            return false;
        }
        $locationType = trim($locationInstance->get('wflocation_type'));
        $sql = "SELECT
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
        return $result;
    }

}
