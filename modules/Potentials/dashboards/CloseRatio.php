<?php

class Potentials_CloseRatio_Dashboard extends Vtiger_IndexAjax_View
{
    public function process(Vtiger_Request $request)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        //file_put_contents('logs/WidgetTest.log', date('Y-m-d H:i:s - ').print_r($request, true)."\n", FILE_APPEND);

        $linkId = $request->get('linkid');
        $page = $request->get('page');
        if (empty($page)) {
            $page = 1;
        }
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $page);
        
        $user = $request->get('type');

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $models = $moduleModel->getClosingRatio($pagingModel, $user);

        $widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());

        $viewer->assign('WIDGET', $widget);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('MODELS', $models);
        $viewer->assign('PAGING', $pagingModel);
        $viewer->assign('CURRENTUSER', $currentUser);

        $content = $request->get('content');
        //file_put_contents('logs/WidgetTest.log', date('Y-m-d H:i:s - ')."Preparing to view dashboard tpl file(s)\n", FILE_APPEND);
        if (!empty($content)) {
            //file_put_contents('logs/WidgetTest.log', date('Y-m-d H:i:s - ')."Preparing to view CloseRatioContents.tpl\n", FILE_APPEND);
            $viewer->view('dashboards/CloseRatioContents.tpl', $moduleName);
        } else {
            file_put_contents('logs/WidgetTest.log', date('Y-m-d H:i:s - ')."Preparing to view CloseRatio.tpl\n", FILE_APPEND);
            $viewer->view('dashboards/CloseRatio.tpl', $moduleName);
        }
        //file_put_contents('logs/WidgetTest.log', date('Y-m-d H:i:s - ')."Exiting CloseRatio process function\n", FILE_APPEND);
    }
}
