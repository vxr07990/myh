<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 3/20/2017
 * Time: 5:21 PM
 */

class Vtiger_AccountingIntegrationActionAjax_Action extends Vtiger_Action_Controller {

    function checkPermission(Vtiger_Request $request) {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        if(!$currentUser->canAccessAgent($request->get('agentid')))
        {
            throw new Exception('Access denied');
        }
        return;
    }

    function __construct() {
        parent::__construct();
        $this->exposeMethod('isActive');
    }

    public function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);

            return;
        }
    }

    public function isActive(Vtiger_Request $request) {
        $integration = new \MoveCrm\AccountingIntegration();
        $res = new Vtiger_Response();
        $res->setResult(['access' => $integration->isConnected($request->get('agentid'))]);
        $res->emit();
    }
}
