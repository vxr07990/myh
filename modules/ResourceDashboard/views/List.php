<?php

/**
 * Resource Management Module
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */
class ResourceDashboard_List_View extends Vtiger_Index_View
{
    public function process(Vtiger_Request $request)
    {
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $moduleModel      = Vtiger_Module_Model::getInstance('ResourceDashboard');
        $viewer           = $this->getViewer($request);
        $viewer->assign('DASHTABLE', $moduleModel->getDashboard());
        $viewer->assign('MONTHS', $this->monthArray());
        $viewer->assign('YEARS', $this->yearArray());
        $viewer->view('List.tpl', $request->getModule());
    }

    public function monthArray()
    {
        $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        return array_combine(range(1, count($months)), array_values($months));
    }

    public function yearArray()
    {
        $years = [];
        for ($i = -2; $i <= 3; $i++) {
            array_push($years, date('Y', strtotime('+'.$i.' year')));
        }

        return $years;
    }
}
