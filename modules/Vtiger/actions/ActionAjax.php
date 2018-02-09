<?php

class Vtiger_ActionAjax_Action extends Vtiger_Action_Controller
{

    function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    function __construct()
    {
        parent::__construct();
        $this->exposeMethod('getPicklistValuesTariffByOwner');
        $this->exposeMethod('getPickListValueForOwner');
        $this->exposeMethod('getRecordInfo');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);

            return;
        }
    }

    public function getPicklistValuesTariffByOwner(Vtiger_Request $request)
    {
        $adb = PearDatabase::getInstance();
        $tariffRecords = array();
        // Agent Manage Id
        $agentId = $request->get('agentId');

        $userRecordModel = new Users_Record_Model();
        $agentIdsRecord = $userRecordModel->getAgentParent($agentId, array());
        $agentIdsRecord[] = $agentId;
        $countAgentIdsRecord = count($agentIdsRecord);

        if ($countAgentIdsRecord > 0){
            $InValuesSql = str_repeat( '?,' , $countAgentIdsRecord-1).'?';

            $sqlSelectTariffsAll = "SELECT * FROM `vtiger_tariffs`
                                    INNER JOIN `vtiger_crmentity`
                                    ON `vtiger_crmentity`.`crmid` = `vtiger_tariffs`.`tariffsid`
                                    WHERE `vtiger_crmentity`.`deleted` = 0
                                    AND `vtiger_crmentity`.`agentid` IN ($InValuesSql)";
            $result = $adb->pquery($sqlSelectTariffsAll,$agentIdsRecord);

            if ($adb->num_rows($result) > 0){
                while ($item = $adb->fetchByAssoc($result)){
                    $tariffRecords[] = array('id'=>$item['tariffsid'],'name'=>$item['tariff_name']);
                }
            }
        }

        $response = new Vtiger_Response();
        $response->setResult($tariffRecords);
        $response->emit();
    }

    public function getPickListValueForOwner(Vtiger_Request $request)
    {
        $fieldName =  preg_replace('/[^0-9a-zA-Z_]/', '', $request->get('fieldname'));
        $module = $request->get('module');
        $idAgentManager = $request->get('idAgentManager');

        if($idAgentManager){
            $result = $this->getPicklistValuesByParentAgent($idAgentManager,$fieldName,$module);
        }
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
    public function getPicklistValuesByParentAgent($agentId,$fieldName,$module) {
        global $adb;
//        if($agentId !='') {
            $primaryKey = Vtiger_Util_Helper::getPickListId($fieldName);
            $params = [];
            if(Vtiger_Utils::CheckColumnExists("vtiger_$fieldName", 'agentmanager_id')) {
                if ($agentId) {
                    $query = "SELECT `$primaryKey`, `$fieldName` FROM `vtiger_$fieldName` WHERE `agentmanager_id`=? order by `sortorderid`";
                } else {
                    $agentId = NULL;
                    $query   = "SELECT `$primaryKey`, `$fieldName` FROM `vtiger_$fieldName` WHERE agentmanager_id IS ? order by `sortorderid`";
                }
                $params[] = $agentId;
            } else {
                $agentId = NULL;
                $query   = "SELECT `$primaryKey`, `$fieldName` FROM `vtiger_$fieldName` order by `sortorderid`";
            }

            $rs = $adb->pquery($query, $params);
            $num_rows = $adb->num_rows($rs);
            if ($num_rows > 0) {
                $result = array();
                for ($i = 0; $i < $num_rows; $i++) {
                    $result[$adb->query_result($rs, $i, $primaryKey)] = [
                        'value'=> decode_html(decode_html($adb->query_result($rs, $i, $fieldName))),
                        'label'=>vtranslate(decode_html(decode_html($adb->query_result($rs, $i, $fieldName))),$module)
                    ];
                }
                return $result;
            } else if($agentId) {
                // Get parent Agent
                $parentAgentId = $this->getParentAgent($agentId);
//                if($parentAgentId !='' && $parentAgentId !=0) {
                    return $this->getPicklistValuesByParentAgent($parentAgentId, $fieldName,$module);
//                }else{
//                    return [];
//                }
            }
//        }
        return [];
    }
    public function getParentAgent($agentId) {
        global $adb;
        $parentAgent='';
        $select = "SELECT `agentmanagerid`,`cf_agent_manager_parent_id`
                   FROM `vtiger_agentmanager`
                   INNER JOIN `vtiger_crmentity`
                   ON `vtiger_agentmanager`.`agentmanagerid` = `vtiger_crmentity`.`crmid`
                   WHERE `deleted` = 0
                   AND `agentmanagerid` = ?";

        $result = $adb->pquery($select, array($agentId));
        if($adb->num_rows($result)>0) {
            $parentAgent=$adb->query_result($result,0,'cf_agent_manager_parent_id');
        }
        return $parentAgent;
    }

     public function getRecordInfo(Vtiger_Request $request) {
         $record=$request->get('record');
         $recordModel=Vtiger_Record_Model::getInstanceById($record);
         $entity=$recordModel->getEntity();
         $response = new Vtiger_Response();
         $result=$entity->column_fields;
         $result['id']=$record;
         $response->setResult($result);
         $response->emit();
     }
}
