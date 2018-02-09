<?php

class SirvaImporter_ImporterResults_View extends Settings_Vtiger_Index_View
{
    public function process(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        
        $viewer->assign('UPLOADED', $request->get('imported'));
        $viewer->assign('SUCCESS', $request->get('success'));
        $viewer->assign('FAILED', $request->get('failed'));
        $viewer->assign('LOG', $request->get('log_file'));


        $viewer->view('ImporterResults.tpl', $request->getModule());
    }
}
