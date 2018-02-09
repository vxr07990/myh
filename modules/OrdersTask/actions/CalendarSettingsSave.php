<?php

class OrdersTask_CalendarSettingsSave_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $currentUserId = Users_Record_Model::getCurrentUserModel()->getId();
        $percentage1 = $request->get('percentage1');
        $percentage2 = $request->get('percentage2');
        $percentage3 = $request->get('percentage3');
        $color1 = $request->get('color1');
        $color2 = $request->get('color2');
        $color3 = $request->get('color3');
        $saturday = ($request->get('saturday') == 'Yes') ? 1 : 0;
        $sunday = ($request->get('sunday') == 'Yes') ? 1 : 0;
        
        $sql = "INSERT INTO vtiger_calendar_settings (`userid`,`percentage_1`,`percentage_2`,`percentage_3`,`color_1`,`color_2`,`color_3`,`saturday_work_day`,`sunday_work_day`) "
                . " VALUES ($currentUserId,'$percentage1','$percentage2','$percentage3','$color1','$color2','$color3',$saturday,$sunday) "
                . " ON DUPLICATE KEY UPDATE `percentage_1`='$percentage1',`percentage_2`='$percentage2',`percentage_3`='$percentage3',`color_1`='$color1',`color_2`='$color2',`color_3`='$color3',`saturday_work_day`=$saturday,`sunday_work_day`=$sunday";
        $result = $db->pquery($sql);
        $msg = new Vtiger_Response();
        $msg->setResult("Ok");
        $msg->emit();
    }
}
