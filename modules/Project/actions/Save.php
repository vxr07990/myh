<?php

class Project_Save_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        $surveyTime = Vtiger_Time_UIType::getTimeValueWithSeconds($request->get('survey_time'));
        $datetime = DateTimeField::convertToDBTimeZone($request->get('survey_date').' '.$surveyTime);
        
        $request->set('survey_time', $datetime->format('H:i:s'));
        $request->set('survey_date', $datetime->format('Y-m-d'));
        
        parent::process($request);
    }
}
