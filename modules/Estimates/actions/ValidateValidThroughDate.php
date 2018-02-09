<?php

use Carbon\Carbon;
class Estimates_ValidateValidThroughDate_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        $dateformat = $request->get('format');
        //$dateformat =  implode('-',array_unique(str_split(str_replace('-','',$dateformat))));
        switch($dateformat) {
            case 'mm-dd-yyyy': $dateformat = 'm-d-Y';break;
            case 'dd-mm-yyyy': $dateformat = 'd-m-Y';break;
            case 'yyyy-mm-dd': $dateformat = 'Y-m-d';break;
            default: break;
        }

        $dates = array();
        $dates['validtill'] = Carbon::createFromFormat($dateformat, $request->get('date'));
        $dates['today'] = Carbon::now();

        $expired = false;
        if($dates['validtill'] < $dates['today']) {
            $expired = true;
        }

        $response = new Vtiger_Response();
        $response->setResult([
            'dates' => $dates,
            'format' => $dateformat,
            'expired' => $expired
        ]);
        $response->emit();
    }
}
