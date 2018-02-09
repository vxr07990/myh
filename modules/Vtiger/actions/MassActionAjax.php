<?php

Class Vtiger_MassActionAjax_Action extends Vtiger_Mass_Action
{
    function __construct()
    {
        parent::__construct();
        $this->exposeMethod('checkEditableAndDeletable');
    }
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }
    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    public function checkEditableAndDeletable(Vtiger_Request $request)
    {
        global $adb;
        $module = $request->get('module');
        $moduleModel=Vtiger_Module_Model::getInstance($module);
        if(!($flags = $moduleModel->getFlagsForProtection()) && !$moduleModel->isCheckBeforeEditDeleteRequired()){
            return;
        }

        $ids = $request->get('ids');
        $recordIds = $this->getRecordsListFromRequest($request);

        if(!is_array($ids)) {
            if($ids !='all' && $ids !='') {
                $recordIds[] = $ids;
            }
        }else{
            $recordIds = array_merge($recordIds,$ids);
        }
        $result = [];

        $action = false;
        if($request->get('actionword')){
            $action = $request->get('actionword');
        }

        $query = $moduleModel->getCheckEditAndDeletableQuery($recordIds, $action);

        if($query) {
            $rs = $adb->pquery($query, $recordIds);
            if ($adb->num_rows($rs) > 0) {
                while ($row = $adb->fetchByAssoc($rs)) {
                    $result[]=$row['record_name'];
                }
            }
        }
        if($module == 'WFLocationTypes' || $module == 'WFStatus'){
            foreach($recordIds as $possibleDefault){
                $sql = 'SELECT * FROM `vtiger_'.strtolower($module).'` WHERE '.strtolower($module).'id = ? AND is_default = 1';
                $rs = $adb->pquery($sql, [$possibleDefault]);
                if($adb->num_rows($rs)>0){
                    while ($row = $adb->fetchByAssoc($rs)) {
                        $result[]=$row[strtolower($module).'id'];
                    }
                }
            }
        }

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

}
