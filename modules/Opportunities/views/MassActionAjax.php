<?php

class Opportunities_MassActionAjax_View extends Vtiger_MassActionAjax_View
{
    public function showMassEditForm(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);

        $recordModel = Vtiger_Record_Model::getCleanInstance('Opportunities');
        $viewer->assign('RECORD_MODEL', $recordModel);
        setDefaultCoordinator($recordModel, $viewer);


        parent::showMassEditForm($request);
    }
}
