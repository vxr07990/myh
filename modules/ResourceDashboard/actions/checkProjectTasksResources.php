<?php

/**
 * Resource Management Module
 *
 *
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */

class ResourceDashboard_checkProjectTasksResources_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    public function process(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $recordId = $request->get('recordid');
        $endDate = $request->get('enddate');
        $startDate = $request->get('startdate');

        
        $resourceDashModel = Vtiger_Module_Model::getCleanInstance($moduleName);

        if (!$resourceDashModel->validateTaskResources($recordId, $startDate, $endDate)) {
            $result = array('success'=>false);
        } else {
            $result = array('success'=>true, 'message'=>vtranslate('LBL_RESOURCE_CONFLICT', $moduleName));
        }
                
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
