<?php

class Opportunities_ActionAjax_Action extends Vtiger_ActionAjax_Action {
    public function checkPermission(Vtiger_Request $request)
    {

    }

    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('getRelatedContacts');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    function getRelatedContacts(Vtiger_Request $request) {
        global $adb;
        $accountId=$request->get('accountid');

        $query="SELECT vtiger_contracts.*            
            FROM vtiger_contracts 
            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contracts.contractsid 
            INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid OR vtiger_crmentityrel.crmid = vtiger_crmentity.crmid) 
            LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid 
            WHERE vtiger_crmentity.deleted = 0 AND (vtiger_crmentityrel.crmid IN (?) OR vtiger_crmentityrel.relcrmid IN (?))";
        $rs=$adb->pquery($query, array($accountId,$accountId));
        $infoAccount = $adb->pquery("SELECT * FROM `vtiger_account` WHERE `accountid`=?",array($accountId));
        if($adb->num_rows($rs) == 1) {
            $result=array('contractid'=>$adb->query_result($rs,0,'contractsid'),'contract_name' => $adb->query_result($rs,0,'contract_no'),
                'national_account_number' =>$adb->query_result($infoAccount,0,'national_account_number'),
                'cityAccount' =>$adb->query_result($infoAccount,0,'city'),
                'stateAccount' =>$adb->query_result($infoAccount,0,'state'),
                'zip_codeAccount' =>$adb->query_result($infoAccount,0,'zip'),
                'countryAccount' =>decode_html($adb->query_result($infoAccount,0,'country')),
                'address1Account' =>$adb->query_result($infoAccount,0,'address1'),
                'address2Account' =>$adb->query_result($infoAccount,0,'address2')
            );
        }else{
            $result=array('contractid'=>'MULTIPLE',
                'national_account_number' =>$adb->query_result($infoAccount,0,'national_account_number'),
                'cityAccount' =>$adb->query_result($infoAccount,0,'city'),
                'stateAccount' =>$adb->query_result($infoAccount,0,'state'),
                'zip_codeAccount' =>$adb->query_result($infoAccount,0,'zip'),
                'countryAccount' =>decode_html($adb->query_result($infoAccount,0,'country')),
                'address1Account' =>$adb->query_result($infoAccount,0,'address1'),
                'address2Account' =>$adb->query_result($infoAccount,0,'address2')

            );
        }
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

}