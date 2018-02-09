<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Estimates_Delete_Action extends Vtiger_Delete_Action
{
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName       = $request->getModule();
        $record           = $request->get('record');
        $recordInstance = Vtiger_Record_Model::getInstanceById($record, $moduleName);
        if($recordInstance->get('quotestage') == 'Accepted'){
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED_ESTIMATE_ACCEPTED'));
        }else{
            parent::checkPermission($request);
        }
        
    }
}