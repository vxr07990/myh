<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

function saveWeeklyTS($entity)
{
    include_once 'Webservices/Create.php';
    include_once 'Webservices/Revise.php';
    include_once 'Webservices/Delete.php';
    
    global $adb;
    
    $currentUser = Users_Record_Model::getCurrentUserModel();
    
    $id = $entity->getId();
    $entity_id = explode('x', $id);
    $entity_id = $entity_id[1];

    $data = $entity->getData();
    $ddate = $data[actual_start_date];
    $cant_horas = floatval($data[total_hours]);
    
    $dt = explode("-", $ddate);
    $date  = mktime(0, 0, 0, $dt[1], $dt[2], $dt[0]); // hora, minuto, segundo, mes, dia, año
    $week  = (int)date('W', $date);
    $timestamp = strtotime($ddate);
    $day = date('D', $timestamp);
    $arr = getStartAndEndDate($week-1, '2015');
    
    switch ($day) {
        case 'Sun':
            $day = 'sunday_hours';
            break;
        case 'Mon':
            $day = 'monday_hours';
            break;
        case 'Tue':
            $day = 'tuesday_hours';
            break;
        case 'Wed':
            $day = 'wednesday_hours';
            break;
        case 'Thu':
            $day = 'thursday_hours';
            break;
        case 'Fri':
            $day = 'friday_hours';
            break;
        case 'Sat':
            $day = 'saturday_hours';
            break;
    }
    $auxe = explode("x", $data[employee_id]);
    $result = $adb->pquery("SELECT * FROM vtiger_weeklytimesheets wts INNER JOIN vtiger_crmentity cr ON wts.weeklytimesheetsid = cr.crmid WHERE cr.deleted = 0 AND wts.weeklytimesheet_id = ? AND employee_id = ?", array('Week ' . $week, $auxe[1]));
    if ($adb->num_rows($result) > 0) {
        $resultado = $adb->pquery("SELECT total_hours FROM vtiger_weeklytimesheets_wts ws INNER JOIN vtiger_timesheets ts ON ws.timesheetid = ts.timesheetsid WHERE employee_id = ? AND ts.timesheetid = ?", array($auxe[1], $entity_id));
        if ($adb->num_rows($resultado) > 0) {
            $cant_horas = $cant_horas - floatval($adb->query_result($resultado, 0, total_hours));
        }
        $wtsId = $adb->query_result($result, 0, weeklytimesheetsid);
        $weeklytimeSheet = array(
            'id' => vtws_getWebserviceEntityId('WeeklyTimeSheets', $wtsId),
            $day => floatval($adb->query_result($result, 0, $day)) + $cant_horas,
            'assigned_user_id' => vtws_getWebserviceEntityId('Users', $currentUser->id),
        );

        vtws_revise($weeklytimeSheet, $currentUser);
    } else {
        $weeklytimeSheet = array(
            'employee_id' => $data[employee_id],
            'week_start_date' => $arr[0],
            $day => floatval($adb->query_result($result, 0, $day)) + $cant_horas,
            'assigned_user_id' => vtws_getWebserviceEntityId('Users', $currentUser->id),
        );

        $wts_created = vtws_create('WeeklyTimeSheets', $weeklytimeSheet, $currentUser);
        $arr_aux = explode("x", $wts_created[id]);
        $wtsId = $arr_aux[1];
        
        $adb->pquery("UPDATE vtiger_weeklytimesheets SET weeklytimesheet_id = ? WHERE weeklytimesheetsid = ?", array('Week ' . $week, $wtsId));
    }
    $adb->pquery("INSERT INTO vtiger_weeklytimesheets_wts(wtsid, timesheetid, hours) VALUES (?,?,?)", array($wtsId, $entity_id, $cant_horas));
}
function getStartAndEndDate($week, $year)
{
    $time = strtotime("1 January $year", time());
    $day = date('w', $time);
    $time += ((7*$week)+1-$day)*24*3600;
    $return[0] = date('Y-m-d', $time);
    $time += 6*24*3600;
    $return[1] = date('Y-m-d', $time);
    return $return;
}
