<?php

class Estimates_GetReportLocal_Action extends Estimates_GetReportBase_Action
{
    public function process(Vtiger_Request $request)
    {
        $request->set('mode','local');
        parent::process($request, 'local');
    }
}
