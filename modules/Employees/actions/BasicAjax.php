<?php

class Employees_BasicAjax_Action extends Vtiger_BasicAjax_Action
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('getAgentPersonnelTimes');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }else{
            parent::process($request);
        }
    }

    public function getAgentPersonnelTimes(Vtiger_Request $request)
    {
        global $adb;
        global $current_user;

        $agentId = $request->get('agentId');

        // slelect StartTime and TimeZone
        $sql = "SELECT
                    `vtiger_fieldtimezonerel`.`timezone`,
                    `vtiger_agentmanager`.`personnel_start_time`
                FROM
                    `vtiger_agentmanager`
                INNER JOIN `vtiger_fieldtimezonerel` ON `vtiger_fieldtimezonerel`.`crmid` = `vtiger_agentmanager`.`agentmanagerid`
                INNER JOIN `vtiger_crmentity` ON `vtiger_crmentity`.`crmid` = `vtiger_agentmanager`.`agentmanagerid`
                WHERE
                    `vtiger_agentmanager`.`agentmanagerid` = ?
                AND
                    `vtiger_fieldtimezonerel`.`fieldid` IN ('personnel_start_time')
                AND
                    `vtiger_crmentity`.`deleted` = 0";

        $result = $adb->pquery($sql,array($agentId));
        $startTime = $adb->fetchByAssoc($result);

        // slelect EndTime and TimeZone
        $sql = "SELECT
                    `vtiger_fieldtimezonerel`.`timezone`,
                    `vtiger_agentmanager`.`personnel_end_time`
                FROM
                    `vtiger_agentmanager`
                INNER JOIN `vtiger_fieldtimezonerel` ON `vtiger_fieldtimezonerel`.`crmid` = `vtiger_agentmanager`.`agentmanagerid`
                INNER JOIN `vtiger_crmentity` ON `vtiger_crmentity`.`crmid` = `vtiger_agentmanager`.`agentmanagerid`
                WHERE
                    `vtiger_agentmanager`.`agentmanagerid` = ?
                AND
                    `vtiger_fieldtimezonerel`.`fieldid` IN ('personnel_end_time')
                AND
                    `vtiger_crmentity`.`deleted` = 0";

        $result = $adb->pquery($sql,array($agentId));
        $endTime = $adb->fetchByAssoc($result);

        if (!empty($startTime['timezone'])){
            $personnel_start_time = DateTimeField::convertTimeZone($startTime['personnel_start_time'], DateTimeField::getDBTimeZone(), $startTime['timezone']);
            $personnel_start_time = Vtiger_Time_UIType::getTimeValueInAMorPM($personnel_start_time->format("H:i:s"));
        }else{
            $personnel_start_time = Vtiger_Time_UIType::getTimeValueInAMorPM($startTime['personnel_start_time']);
        }

        if (!empty($endTime['timezone'])) {
            $personnel_end_time = DateTimeField::convertTimeZone($endTime['personnel_end_time'], DateTimeField::getDBTimeZone(), $endTime['timezone']);
            $personnel_end_time = Vtiger_Time_UIType::getTimeValueInAMorPM($personnel_end_time->format("H:i:s"));
        }else{
            $personnel_end_time = Vtiger_Time_UIType::getTimeValueInAMorPM($endTime['personnel_end_time']);
        }
        $result = array(
            'timeToStart'       => $personnel_start_time,
            'timeToEnd'         => $personnel_end_time,
            'timezoneToStart'   => $startTime['timezone'],
            'timezoneToEnd'     => $endTime['timezone'],
        );


        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
