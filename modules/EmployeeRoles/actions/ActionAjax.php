<?php

class EmployeeRoles_ActionAjax_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('checkClass');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);

            return;
        }
    }

    public static function isDriver($selectedIds)
    {
        if(is_array($selectedIds))
        {
            $selectedIds = implode(',',$selectedIds);
        }
        global $adb;
        $rs=$adb->pquery("SELECT * FROM vtiger_employeeroles WHERE employeerolesid IN (".$selectedIds.") AND (emprole_class IN ('Driver','Lease Driver', 'Owner Operator'))");
        if ($adb->num_rows($rs)>0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkClass(Vtiger_Request $request)
    {
        $selectedIds=$request->get('selected_ids');
        if ($selectedIds && self::isDriver($selectedIds)) {
            $result=array('value'=>'true');
        } else {
            $result=array('value'=>'false');
        }

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
