<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/*********************************************************************************
 * @author 			Louis Robinson
 * @file 			/Estimates/actions/MassSave.php
 * @function 		Estimates Module for moveCRM
 * @company 		IGC SOftware
 * @description 	Extends Quotes functionality
 * @contact 		lrobinson@igcsoftware.com
 *
 *********************************************************************************/
class Estimates_MassSave_Action extends Vtiger_MassSave_Action
{
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName       = $request->getModule();
        $recordIds = $this->getRecordsListFromRequest($request);
        foreach ($recordIds as $recordId) {
            $recordInstance = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            if($recordInstance->get('quotestage') == 'Accepted'){
                throw new AppException(vtranslate('LBL_PERMISSION_DENIED_ESTIMATE_ACCEPTED'));
            }
        }
        
        parent::checkPermission($request);
        
    }
}
