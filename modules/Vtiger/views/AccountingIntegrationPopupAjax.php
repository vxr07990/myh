<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 3/20/2017
 * Time: 4:55 PM
 */

class Vtiger_AccountingIntegrationPopupAjax_View extends Vtiger_AccountingIntegrationPopup_View  {
    public function __construct() {
        parent::__construct();
        $this->exposeMethod('getListViewCount');
        $this->exposeMethod('getPageCount');
    }

    public function preProcess(Vtiger_Request $request) {
        return true;
    }

    public function postProcess(Vtiger_Request $request) {
        return true;
    }

    public function process (Vtiger_Request $request) {
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
        $viewer = $this->getViewer ($request);
        $moduleName = $request->getModule();

        $this->initializeListViewContents($request, $viewer);

        echo $viewer->view('AccountingIntegrationPopupContents.tpl', $moduleName, true);
    }
}