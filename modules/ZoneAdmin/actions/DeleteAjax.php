<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_DeleteAjax_Action extends Vtiger_Delete_Action
{
    public function process(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $recordId = $request->get('record');

        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
                
        $pickListNames = array('origin_zone', 'empty_zone', 'currentzone', 'intransitzone');


        foreach ($pickListNames as $pickListName) {
            $picklistValues = Vtiger_Util_Helper::getPickListValues($pickListName);
            $picklistValues = array_flip($picklistValues);

            $deletedZone = $recordModel->get('za_zone');
            $tartgetModule = Settings_Picklist_Module_Model::getInstance('Trips');
            $tartgetModule->remove($pickListName, $picklistValues[$deletedZone], 1, 'Trips');
        }
                
                
        $recordModel->delete();

        $cvId = $request->get('viewname');
        $response = new Vtiger_Response();
        $response->setResult(array('viewname'=>$cvId, 'module'=>$moduleName));
        $response->emit();
    }
}
