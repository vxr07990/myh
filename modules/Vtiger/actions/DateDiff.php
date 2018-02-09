<?php

class Vtiger_DateDiff_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $preferLoadDate = $request->get('pldate');
        $preferDeliverDate = $request->get('pddate');
        if (
            $preferDeliverDate &&
            !$request->get('enddate')
        ) {
            $request->set('enddate', $preferDeliverDate);
        }

        if (
            $preferLoadDate &&
            !$request->get('startdate')
        ) {
            $request->set('startdate', $preferLoadDate);
        }
        $startDate = new DateTime($request->get('startdate'));
        $endDate = new DateTime($request->get('enddate'));
        $startDate->setTime(0, 0, 0);
        $endDate->setTime(0, 0, 0);
        $dateDiff = $startDate->diff($endDate);
        $diffDays = $dateDiff->days;
        
        if ($startDate > $endDate) {
            $diffDays = '-'.$diffDays;
        }
    
        $response = new Vtiger_Response();
        $response->setResult($diffDays);
        $response->emit();
    }
}
