<?php

class ZoneAdmin_CheckZipNState_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        try {
            $form_zip_codes = explode("\n", $request->get("zips"));
            $form_states = $request->get("states");
            $check_state = ($request->get("state_checkbox") == 'true') ? true : false;
            $check_zip = ($request->get("zip_checkbox") == 'true') ? true : false;
			$agentid = $request->get("agentid");
            $id = $request->get("id");
			
            $db = PearDatabase::getInstance();
            $existe = false;
            if ($check_state) {
                $query = $db->pquery("SELECT za_state FROM vtiger_zoneadmin 
                        INNER JOIN vtiger_crmentity ON vtiger_zoneadmin.zoneadminid = vtiger_crmentity.crmid
                        WHERE deleted = 0 AND za_state != '' AND agentid = ? AND zoneadminid <> ?", array($agentid,$id));
                while ($arr = $db->fetch_row($query)) {
                    $states = array_map('trim', explode("|##|", $arr['za_state']));
                    foreach ($form_states as $state) {
                        if (in_array($state, $states)) {
                            $existe = true;
                            break;
                        }
                    }
                }
            } elseif ($check_zip) {
                foreach ($form_zip_codes as $zip) {
				//used $db->sql_escape_string because pquery doesn't work with LIKE '%?%
                    $query = $db->pquery("SELECT * FROM vtiger_zoneadmin 
                        INNER JOIN vtiger_crmentity ON vtiger_zoneadmin.zoneadminid = vtiger_crmentity.crmid 
						WHERE deleted = 0 AND zip_code LIKE '%".$db->sql_escape_string($zip)."%'  AND agentid = ? AND zoneadminid <> ?",array($agentid,$id));
                    if ($db->num_rows($query) > 0) {
                        $existe = true;
                        break;
                    }
                }
            }
            if (!$existe) {
                $result['result'] = 'OK';
            } else {
                $result['result'] = 'Error';
            }
            $msg = new Vtiger_Response();
            $msg->setResult($result);
            $msg->emit();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
}
