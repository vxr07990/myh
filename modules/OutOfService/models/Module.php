<?php

class OutOfService_Module_Model extends Vtiger_Module_Model {

    public static function getOutOfServiceStatus($employeeId, $OSDate = ''){
	if($OSDate == ''){
	    $OSDate = date('Y-m-d');
	}
	
	$db = PearDatabase::getInstance();
        $res = $db->pquery("SELECT * FROM vtiger_outofservice WHERE outofservice_employeesid = ? AND outofservice_status = 'Out of Service' AND outofservice_effectivedate IS NOT NULL AND outofservice_effectivedate <= ? AND (outofservice_satisfieddate = '' OR outofservice_satisfieddate IS NULL)",array($employeeId, $OSDate));
        if ($db->num_rows($res) > 0) {
            return true;
        }else{
            return false;
        }
	
    }

}
