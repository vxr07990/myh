<?php

class ClaimsSummary_BasicAjax_Action extends Vtiger_BasicAjax_Action
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('getPersonnelForClaimsSummary');
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

	public function getPersonnelForClaimsSummary(Vtiger_Request $request){
        global $adb;

		$currentUser = Users_Record_Model::getCurrentUserModel();
		$accesibleAgents = $currentUser->getBothAccessibleOwnersIdsForUser();
		array_push($accesibleAgents, $request->get('agentId'));
        $searchValue = $adb->sql_escape_string($request->get('search_value'));

        $sql = "SELECT vtiger_employees.* , vtiger_agentmanager.personnel_end_time, vtiger_agentmanager.personnel_start_time, vtiger_agentmanager.agentmanagerid as agentmanid ";
        $sql .= " FROM vtiger_employees INNER JOIN vtiger_crmentity ON vtiger_employees.employeesid = vtiger_crmentity.crmid  INNER JOIN vtiger_agentmanager ON vtiger_crmentity.agentid = vtiger_agentmanager.agentmanagerid ";
        $sql .= " INNER JOIN vtiger_employeescf ON vtiger_employees.employeesid = vtiger_employeescf.employeesid ";
        $sql .= " LEFT JOIN vtiger_employeeroles r1 ON vtiger_employeescf.employee_primaryrole=r1.employeerolesid 
                    LEFT JOIN vtiger_employeeroles r2 ON vtiger_employeescf.employee_secondaryrole LIKE CONCAT('%',r2.employeerolesid,'%') ";
        $sql .= " WHERE deleted=0 AND employee_status='Active' ";
        $sql .= " AND vtiger_crmentity.agentid IN (" . generateQuestionMarks($accesibleAgents) . ") ";
        $sql .= " AND (r1.emprole_class = 'Claims Adjuster' OR r2.emprole_class = 'Claims Adjuster') ";
        $sql .= " AND CONCAT(vtiger_employees.name,'', vtiger_employees.employee_lastname) LIKE '%$searchValue%' ";


		$qresult = $adb->pquery($sql,array($accesibleAgents));
		$result = array();
		if ($adb->num_rows($qresult) > 0) {
			while ($arr = $adb->fetch_array($qresult)) {
				$name = $arr['name'] . " " . $arr['employee_lastname'];
				$result[] = array(
					'label'=>decode_html($name),
					'value'=>decode_html($name),
					'id'=>$arr['employeesid']
				);
			}
		}


        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
	}
}
