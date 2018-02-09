<?php
class WFImages_Edit_View extends Vtiger_Edit_View
{
    function process(Vtiger_Request $request)
    {
        $record     = $request->get('record');
        $viewer = $this->getViewer($request);
        if(!empty($record)){
            $recordModel = Vtiger_Record_Model::getInstanceById($record);
            $viewer->assign('IMAGE_DETAILS', $recordModel->getImageDetails());
        }
        parent::process($request);
    }
}
