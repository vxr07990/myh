<?php

class SirvaImporter_ImporterStep1_View extends Settings_Vtiger_Index_View
{
    public function process(Vtiger_Request $request)
    {
        global $site_URL;

        $viewer = $this->getViewer($request);

        $avModules = array(
            'Lead Data',
            'Lead Notes',
            'Qualified Lead Activity',
            'Extra Stops Data',
            // 'Users' -> There is already this feature
        );
        
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $agentList = $currentUserModel->getAccessibleAgents();

        $viewer->assign('ENTITY_MODULES', $avModules);
        $viewer->assign('AGENTS', $agentList);
        $viewer->view('ImporterStep1.tpl', $request->getModule());
    }
}
