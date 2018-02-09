<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 3/29/2017
 * Time: 9:54 AM
 */

class VanlineManager_Detail_View extends Vtiger_Detail_View {
    public function process(Vtiger_Request $request) {
        $recordId                = $request->get('record');
        $viewer                  = $this->getViewer($request);
        if(getenv('IGC_MOVEHQ')) {
            $viewer->assign('ACCOUNTING_INTEGRATION_ACTIVE', getenv('ACCOUNTING_INTEGRATION'));
            $viewer->assign('CONNECTED_TO_QBO', (new MoveCrm\AccountingIntegration())->isConnected($recordId));
        }
        return parent::process($request);
    }
}
