<?php

class Cubesheets_VideoSurveyPopout_View extends Vtiger_Index_View {
    public function preProcess(Vtiger_Request $request, $display = true) {
        parent::preProcess($request, false);
    }

    public function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $viewer     = $this->getViewer($request);
        $element = $request->get('element');
        $viewer->assign('APIKEY', $element['apiKey']);
        $viewer->assign('SESSION', $element['session']);
        $viewer->assign('TOKEN', $element['token']);
        $viewer->assign('DOMAIN', $element['domain']);
        $viewer->assign('RECORD', $element['record']);
        $viewer->assign('ARCHIVEID', $element['archiveId']);
        $viewer->view('videoSurveyPopout.tpl', $moduleName);
    }

    public function postProcess(Vtiger_Request $request) {

    }
}
