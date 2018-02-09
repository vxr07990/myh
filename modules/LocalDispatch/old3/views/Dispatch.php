<?php

class LocalDispatch_Dispatch_View extends Vtiger_Index_View
{
    public function process(Vtiger_Request $request)
    {
        global $adb;

        $result = $adb->run_query_allrecords("SELECT * FROM vtiger_vehicles AS t1 JOIN vtiger_crmentity AS t2 ON t1.vehiclesid = t2.crmid WHERE t2.deleted = 0");

        $vehicles = null;
        foreach ($result as $res) {
            if ($res['vehicle_status'] == 'Active') {
                $status = 'available';
            } elseif ($res['vehicle_status'] == 'Disposed') {
                $status = 'warning';
            } elseif ($res['vehicle_status'] == 'Out of Service') {
                $status = 'unavailable';
            }
            $vehicles .= '<tr><td class="'.$status.'"></td><td>'.$res['vehicle_number'].'</td><td>'.$res['vehicle_type'].'</td><td>'.$res['vehicle_cubec'].'</td><td>'.$res['vehicle_milesnum'].'</td></tr>';
        }

        // Get Employees
        $emp = $adb->run_query_allrecords("SELECT t1.employee_status AS person_status, t1.name AS firstname, t1.employee_lastname AS lastname, 'Employee' AS person_type, '000' AS available, '000' AS scheduled, '000' AS worked FROM vtiger_employees AS t1 JOIN vtiger_crmentity AS t2 ON t1.employeesid = t2.crmid WHERE t2.deleted = 0");

        // Get Contractors
        $con = $adb->run_query_allrecords("SELECT t1.contractor_status AS person_status, t1.name AS firstname, t1.contractor_elname AS lastname, 'Contractor' AS person_type, '000' AS available, '000' AS scheduled, '000' AS worked FROM vtiger_contractors AS t1 JOIN vtiger_crmentity AS t2 ON t1.contractorsid = t2.crmid WHERE t2.deleted = 0");

        $merged = array_merge($emp, $con);

        $crew = null;
        $i = 1;
        foreach ($merged as $res) {
            if ($res['person_status'] == 'Active') {
                $status = 'available';
            } elseif ($res['person_status'] == 'Suspended') {
                $status = 'unavailable';
            } elseif ($res['person_status'] == 'Terminated') {
                $status = 'unavailable';
            }

// removed myClass from the td status. 02252015
          $crew .= '<tr><td class="'.$status.'"></td><td data-id="'.$i.'">'.$res['firstname'].' '.$res['lastname'].'</td><td>'.$res['person_type'].'</td><td>'.$res['available'].'</td><td>'.$res['scheduled'].'</td><td>'.$res['worked'].'</td></tr>';
            $i++;
        }

        $viewer = $this->getViewer($request);
        $viewer->assign('CREW', $crew);
        $viewer->assign('VEHICLES', $vehicles);
        $viewer->view('Dispatch.tpl', $request->getModule());
    }
}
