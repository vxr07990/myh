<?php

class Estimates_TPGPricelock_View extends Estimates_Edit_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('show');
        $this->exposeMethod('hide');
    }

    public function process(Vtiger_Request $request)
    {
        //If it isn't an Ajax request bounce the user back to home.
        //if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest'){header('Location: index.php?module=Home&view=DashBoard');}
        $mode = $request->getMode();
        echo $this->invokeExposedMethod($mode, $request);

        return;
    }

    public function show($request)
    {
        $viewer = $this->getViewer($request);
        parent::assignVars($viewer, $request);
        $type = $request->get('type');
        if ($type === 'edit') {
            $viewer->assign('MODULE', 'Estimates');
            $viewer->assign('MODULE_NAME', 'Estimates');
            $viewer->view('TPGPricelockEdit.tpl', 'Estimates');
        } elseif ($type === 'detail') {
            $viewer->assign('MODULE', 'Estimates');
            $viewer->assign('MODULE_NAME', 'Estimates');
            $viewer->view('TPGPricelockDetail.tpl', 'Estimates');
        }
    }

    public function hide($request)
    {
        $viewer = $this->getViewer($request);
        $type   = $request->get('type');
        parent::assignVars($viewer, $request);
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
