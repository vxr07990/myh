<?php

class Estimates_CustomTariffDetail_View extends Estimates_Detail_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('showTPG');
        $this->exposeMethod('showBase');
    }

    public function process(Vtiger_Request $request)
    {
        //If it isn't an Ajax request bounce the user back to home.
        //if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest'){header('Location: index.php?module=Home&view=DashBoard');}
        $mode = $request->getMode();
        echo $this->invokeExposedMethod($mode, $request);

        return;
    }

    public function showTPG($request)
    {
        $viewer = $this->getViewer($request);
        //file_put_contents('logs/devLog.log', "\n request in showTPG : ".print_r($request, true), FILE_APPEND);
        parent::assignVars($viewer, $request);
        //$recordModel = Vtiger_Record_Model::getInstanceById($request->get('record'));
        //$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $type        = $request->get('type');
        $record      = $request->get('record');
        $moduleName  = $request->getModule();
        $tariff_type = $request->get('tariff_type');
        $viewer->assign('MODULE', 'Estimates');
        $viewer->assign('MODULE_NAME', 'Estimates');
        $viewer->assign('TARIFF_TYPE', $tariff_type);
        if (!empty($record)) {
            $viewer->assign('CUSTOM_RATES', Vtiger_DetailView_Model::getInstance($moduleName, $record)->getRecord()->getApplyCustomRates());
            $recordModel = Vtiger_Record_Model::getInstanceById($record);
            //$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        } else {
            $viewer->assign('CUSTOM_RATES', ['apply_custom_sit_rate_override' => '0', 'apply_custom_pack_rate_override' => '0',
                                             'apply_custom_sit_rate_override_dest'=>'0', 'tpg_custom_crate_rate' => '0']);
        }
        if ($type === 'edit') {
            $viewer->view('TPGPricelockEdit.tpl', 'Estimates');
        } elseif ($type === 'detail') {
            $viewer->view('TPGPricelockDetail.tpl', $moduleName);
        }
    }

    public function showBase($request)
    {
        $viewer = $this->getViewer($request);
        $type   = $request->get('type');
        parent::assignVars($viewer, $request);
        $record = $request->get('record');
        if ($record) {
            $recordModel = Vtiger_Record_Model::getInstanceById($record);
            $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        }
        if ($type === 'edit') {
            $viewer->assign('UNHIDE', true);
            $viewer->assign('MODULE', 'Estimates');
            $viewer->assign('MODULE_NAME', 'Estimates');
            $viewer->view('RateEstimateEdit.tpl', 'Estimates');
        } elseif ($type === 'detail') {
            $viewer->assign('MODULE', 'Estimates');
            $viewer->assign('MODULE_NAME', 'Estimates');
            $viewer->view('RateEstimateDetail.tpl', 'Estimates');
        }
    }
}
