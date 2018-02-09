<?php

class WFCostCenters_Detail_View extends Vtiger_Detail_View
{
    public function process(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $singles = ['accounts'];
        $viewer->assign('SINGLE_FIELDS',$singles);
        parent::process($request);
    }
}
