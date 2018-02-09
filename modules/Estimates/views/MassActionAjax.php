<?php

class Estimates_MassActionAjax_View extends Vtiger_MassActionAjax_View
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showMassEditForm(Vtiger_Request $request)
    {
        $moduleName              = $request->getModule();
        $selectedIds             = $request->get('selected_ids');
        $viewer                  = $this->getViewer($request);
        $permission              =  true;

        foreach ($selectedIds as $recordId) {
            $recordInstance = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            if ($recordInstance->get('quotestage') == 'Accepted') {
                $permission = false;
            }
        }

        if ($permission) {
            parent::showMassEditForm($request);
        } else {
            $viewer->assign('MESSAGE', vtranslate('LBL_PERMISSION_DENIED_ESTIMATE_ACCEPTED', $moduleName));
            echo $viewer->view('MassEditFormNoEditable.tpl', $moduleName, true);
        }
    }
}
