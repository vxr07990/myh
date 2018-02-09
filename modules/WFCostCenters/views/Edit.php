<?php

class WFCostCenters_Edit_View extends Vtiger_Edit_View
{
    public function process(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $singles = ['accounts'];
        $viewer->assign('SINGLE_FIELDS',$singles);
        parent::process($request);
    }
}
