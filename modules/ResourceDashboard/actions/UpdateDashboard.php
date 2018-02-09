<?php

/**
 * Resource Management Module
 *
 *
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */

class ResourceDashboard_UpdateDashboard_Action extends Vtiger_Delete_Action
{
    public function process(Vtiger_Request $request)
    {
        $moduleModel = Vtiger_Module_Model::getInstance('ResourceDashboard');

        $dashboard = $moduleModel->getDashboard($request->get('year'), $request->get('resource_type'), $request->get('month'));


        $response = new Vtiger_Response();
        $response->setResult(array($dashboard));
        $response->emit();
    }
}
