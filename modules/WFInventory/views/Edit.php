<?php
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Retrieve.php';
include_once 'include/Webservices/Revise.php';

class WFInventory_Edit_View extends Vtiger_Edit_View
{
    public function process(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $singles = ['article'];
        $viewer->assign('SINGLE_FIELDS',$singles);

        parent::process($request);
    }
}
