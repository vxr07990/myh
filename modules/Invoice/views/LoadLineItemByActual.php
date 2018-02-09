<?php
class Invoice_LoadLineItemByActual_View extends Vtiger_Index_View{
    function process(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $actualId = $request->get('actual_id');
        if(!empty($actualId)){
            $moduleModel = Vtiger_Module_Model::getInstance('Invoice');
            $results = $moduleModel->getRevenueDistribitionValues($actualId);
            $viewer->assign('BLOCK_DATA',$results);
        }
        echo  $viewer->view('LineItemsContent.tpl','Invoice',true);
    }
}