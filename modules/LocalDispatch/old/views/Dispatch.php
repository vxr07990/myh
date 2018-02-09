<?php

class LocalDispatch_Dispatch_View extends Vtiger_Index_View
{
    public function process(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $viewer->view('Dispatch.tpl', $request->getModule());
    }
}
