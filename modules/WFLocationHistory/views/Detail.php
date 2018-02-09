<?php
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Retrieve.php';
include_once 'include/Webservices/Revise.php';

class WFLocationHistory_Detail_View extends Vtiger_Detail_View {

    public function process(Vtiger_Request $request) {
        $viewer  = $this->getViewer($request);
        $singles = ['location'];
        $viewer->assign('SINGLE_FIELDS', $singles);
        parent::process($request);
    }
}
