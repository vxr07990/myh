<?php
require_once('modules/Emails/mail.php');
class ParticipatingAgents_Module_Model extends Vtiger_Module_Model
{
    public static function saveParticipants($fieldList, $recordId, $newRecord = false)
    {
	    //@NOTE: Don't save participants if there is no recordID.
	    if ((int)$recordId == 0) {
	        return;
        }
		$db = PearDatabase::getInstance();
        $db->startTransaction();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $currentUserId = $currentUser->getId();
		//file_put_contents('logs/devLog.log', "\n Request : ".print_r($fieldList, true), FILE_APPEND);
		//file_put_contents('logs/devLog.log', "\n recordId : ".print_r($recordId, true), FILE_APPEND);
		//things we aren't going to be changing
		//$recordId; //record Id to relate this back to the Opp
        $inbox_id = null; //for Amin's stuff this probably needs to get set ot something, I can't tell what though

		$modified_on = (new DateTime())->format('Y-m-d H:i:s');
		//no idea why this is here but we're modifying stuff so might as well do now
		$status = 1; //another Amin one let's set it to 1 since that seems to mean apply permissions
		$modified_by = 1; // get the current user's ID don't know why this needs to be here either

		//file_put_contents('logs/participantLog.log', "\n SAVE PARTICIPANTS: ".print_r($fieldList, true), FILE_APPEND);
		//file_put_contents('logs/participantLog.log', "\n numAgents: ".$fieldList['numAgents'], FILE_APPEND);
        for ($index = 0; $index <= $fieldList['numAgents']; $index++) {
			//file_put_contents('logs/participantLog.log', "\n P-ID $index : ".$fieldList['participantId_'.$index], FILE_APPEND);
            if (!$fieldList['participantId_'.$index]) {
				continue;
			}
			$deleted = $fieldList['participantDelete_'.$index];
            if($newRecord) {
                $participantId = 'none';
            } else {
                $participantId = $fieldList['participantId_'.$index];
            }
			$view_level = $fieldList['agent_permission_'.$index];
			$agents_id = $fieldList['agents_id_'.$index];
			$agent_type = $fieldList['agent_type_'.$index];
			$agentManagerId = $db->pquery('SELECT agentmanager_id FROM `vtiger_agents` WHERE agentsid = ? LIMIT 1', [$agents_id])->fetchRow()['agentmanager_id'];
            if ($deleted == 'deleted') {
//				file_put_contents('logs/devLog.log', "\n Deleted participating agent found", FILE_APPEND);
				$oldAgentQuery = "SELECT * FROM `vtiger_participatingagents` WHERE participatingagentsid=? AND rel_crmid=? AND deleted=0";
				$result = $db->pquery($oldAgentQuery, [$participantId, $recordId]);
				$oldAgentId = $result->fields['agentmanager_id'];
                if ($result->fields['status'] == 'Accepted' && $oldAgentId != $fieldList['agentid']) {
//					file_put_contents('logs/devLog.log', "\n Deleted participating agent with Accepted status found", FILE_APPEND);
                    $usersQuery = "SELECT id, email1 FROM `vtiger_users` WHERE (agent_ids LIKE ? OR agent_ids LIKE ? OR agent_ids LIKE ? OR agent_ids = ?)";
                    if(getenv('INSTANCE_NAME') == 'sirva') {
                        $usersQuery .= ' AND cf_oa_da_coordinator = 1';
                        $usersRes = $db->pquery($usersQuery, ['% '.$oldAgentId, '% '.$oldAgentId.' %', $oldAgentId.' %', $oldAgentId]);
                        if ($db->num_rows($usersRes) == 0) {
                            $usersQuery = "SELECT id, email1 FROM `vtiger_users` WHERE agent_ids LIKE ? OR agent_ids LIKE ? OR agent_ids LIKE ? OR agent_ids = ?";
                            $usersRes   = $db->pquery($usersQuery, ['% '.$oldAgentId, '% '.$oldAgentId.' %', $oldAgentId.' %', $oldAgentId]);
                        }
                    }else{
						$usersRes   = $db->pquery($usersQuery, ['% '.$oldAgentId, '% '.$oldAgentId.' %', $oldAgentId.' %', $oldAgentId]);
					}

					$users      = [];
					while ($user = $usersRes->fetchRow()) {
						$tempUser = Users_Record_Model::getInstanceById($user['id'], 'Users');
						if ($tempUser->isCoordinator()) {
							$users[$user['id']] = $user['email1'];
						}
					}
					self::emailMoveCoordinators($fieldList, $users);
				}
				//$sql = "DELETE FROM `vtiger_participatingagents` WHERE participatingagentsid=? AND rel_crmid=?";
                $sql = "UPDATE `vtiger_participatingagents` SET deleted=1 WHERE participatingagentsid=? AND rel_crmid=?";
				$db->pquery($sql, [$participantId, $recordId]);

                //Add ModTracker entry for participant deletion
                $db->query("UPDATE `vtiger_modtracker_basic_seq` SET id=id+1");
                $result = $db->query("SELECT id FROM `vtiger_modtracker_basic_seq`");
                $modTrackerId = $result->fields['id'];
                $db->pquery("INSERT INTO `vtiger_modtracker_basic` (id, crmid, module, whodid, changedon, status) VALUES (?,?,?,?,?,?)",
                            [
                                $modTrackerId, $recordId, $fieldList['module'], $currentUserId, date('Y-m-d H:i:s'), 5
                            ]);
                $db->pquery("INSERT INTO `vtiger_modtracker_relations` (id, targetmodule, targetid, changedon) VALUES (?,?,?,?)",
                            [
                                $modTrackerId, 'ParticipatingAgentsNonGuest', $participantId, date('Y-m-d H:i:s')
                            ]);
				continue;
			}
            if ($participantId == 'none') {
				//insert
				$sql = "INSERT INTO `vtiger_participatingagents` (rel_crmid, agents_id, agentmanager_id, agent_type, view_level, status)
						VALUES (?,?,?,?,?,?)";
				$db->pquery($sql, [$recordId, $agents_id, $agentManagerId, $agent_type, $view_level, 'Pending']);
				//this is probably a concurrency issue waiting to happen
				$currentParticipantId = $db->pquery('SELECT LAST_INSERT_ID()', [])->fetchRow()[0];
				//its a new participant so make a new request
				$requestsModel = Vtiger_Module_Model::getInstance('OASurveyRequests');
                if ($requestsModel && $requestsModel->isActive() && $agent_type != 'Hauling Agent') {
					$requestId = $requestsModel->saveOASurveyRequest($view_level, 'Pending', $agent_type, $agents_id, $fieldList['module'], $fieldList['agentid'], $recordId, 'create', '');
					$db->pquery("UPDATE `vtiger_participatingagents` SET oasurveyrequest_id = ? WHERE participatingagentsid = ?", [$requestId, $currentParticipantId]);
				}

				//Insert new ModTracker row and populate the relations table with info about new Participating Agent
                $db->query("UPDATE `vtiger_modtracker_basic_seq` SET id=id+1");
                $result = $db->query("SELECT id FROM `vtiger_modtracker_basic_seq`");
                $modTrackerId = $result->fields['id'];
                $db->pquery("INSERT INTO `vtiger_modtracker_basic` (id, crmid, module, whodid, changedon, status) VALUES (?,?,?,?,?,?)",
                            [
                                $modTrackerId, $recordId, $fieldList['module'], $currentUserId, date('Y-m-d H:i:s'), 4
                            ]);
                $db->pquery("INSERT INTO `vtiger_modtracker_relations` (id, targetmodule, targetid, changedon) VALUES (?,?,?,?)",
                            [
                                $modTrackerId, 'ParticipatingAgentsNonGuest', $currentParticipantId, date('Y-m-d H:i:s')
                            ]);
            } else {
				//update
				$row = $db->pquery('SELECT status, oasurveyrequest_id, agents_id FROM `vtiger_participatingagents` WHERE participatingagentsid = ? AND deleted=0', [$participantId])->fetchRow();
				$status = $row['status'];
				$requestId = $row['oasurveyrequest_id'];
                if ($row['agents_id'] != $agents_id) {
					$oldAgentQuery = "SELECT * FROM `vtiger_participatingagents` WHERE participatingagentsid=? AND rel_crmid=? AND deleted=0";
					$result = $db->pquery($oldAgentQuery, [$participantId, $recordId]);
					$oldAgentId = $result->fields['agentmanager_id'];
                    if ($result->fields['status'] == 'Accepted' && $oldAgentId != $fieldList['agentid']) {
						file_put_contents('logs/devLog.log', "\n Altered participating agent with Accepted status found", FILE_APPEND);
                        $usersQuery = "SELECT id, email1 FROM `vtiger_users` WHERE (agent_ids LIKE ? OR agent_ids LIKE ? OR agent_ids LIKE ? OR agent_ids = ?)";
                        if(getenv('INSTANCE_NAME') == 'sirva') {
                            $usersQuery .= ' AND cf_oa_da_coordinator = 1';
                        }
                        $usersRes   = $db->pquery($usersQuery, ['% '.$oldAgentId, '% '.$oldAgentId.' %', $oldAgentId.' %', $oldAgentId]);
                        if ($db->num_rows($usersRes) == 0) {
                            $usersQuery = "SELECT id, email1 FROM `vtiger_users` WHERE agent_ids LIKE ? OR agent_ids LIKE ? OR agent_ids LIKE ? OR agent_ids = ?";
                            $usersRes   = $db->pquery($usersQuery, ['% '.$oldAgentId, '% '.$oldAgentId.' %', $oldAgentId.' %', $oldAgentId]);
                        }

						$users      = [];
						while ($user = $usersRes->fetchRow()) {
							$tempUser = Users_Record_Model::getInstanceById($user['id'], 'Users');
							if ($tempUser->isCoordinator()) {
								$users[$user['id']] = $user['email1'];
							}
						}
						self::emailMoveCoordinators($fieldList, $users);
					}
					$status = 'Pending';
				}
				/*file_put_contents('logs/devLog.log', "\n\n----------\n\n", FILE_APPEND);
				file_put_contents('logs/devLog.log', "\n STAT: $status \n AID: $agents_id \n DBAID: " . $row['agents_id'], FILE_APPEND);
				file_put_contents('logs/devLog.log', "\n Debug : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
				file_put_contents('logs/devLog.log', "\n\n----------\n\n", FILE_APPEND);*/
                $sql =    "UPDATE `vtiger_participatingagents` SET agents_id=?, agentmanager_id=?, agent_type=?, view_level=?, status=?
						 WHERE participatingagentsid=? AND rel_crmid=?";
				$db->pquery($sql, [$agents_id, $agentManagerId, $agent_type, $view_level, $status, $participantId, $recordId]);
				$requestsModel = Vtiger_Module_Model::getInstance('OASurveyRequests');
                if ($requestsModel && $requestsModel->isActive() && $agent_type != 'Hauling Agent') {
					$requestsModel->saveOASurveyRequest($view_level, $status, $agent_type, $agents_id, $fieldList['module'], $fieldList['agentid'], $recordId, 'update', $requestId);
					$db->pquery("UPDATE `vtiger_participatingagents` SET oasurveyrequest_id = ? WHERE participatingagentsid = ?", [$requestId, $participantId]);
				}
			}
		}
        $db->completeTransaction();

		/*if ($fieldList['agent_permission'] && is_array($fieldList['agent_permission'])) {
			foreach ($fieldList['agent_permission'] as $id => $permission) {
				//see if it's a delete
				if ($permission == '-1') {
					$sql = "DELETE FROM `vtiger_participatingagents` WHERE participatingagentsid=? AND rel_crmid=?";
					$db->pquery($sql, [$id, $recordId]);
					continue;
				}

				//stuff that's different for each participating agent
				$agents_id   = $fieldList['agents_id_'.$id];
				$agent_type = $fieldList['agent_type'][$id]; //Which picklist item the Agent Type dropdown is
				// set to get the agents_id from the ref field and save it for each one
				$sql    = "SELECT participatingagentsid FROM `vtiger_participatingagents` WHERE participatingagentsid = ? AND rel_crmid = ?";
				$result = $db->pquery($sql, [$id, $recordId]);
				$row    = $result->fetchRow();
				if ($row) {
					//update
					$sql =
						"UPDATE `vtiger_participatingagents` SET agents_id=?, agent_type=?, view_level=?
						 WHERE participatingagentsid=? AND rel_crmid=?";
					file_put_contents('logs/devLog.log', "\n SQL: $sql \n PARAMS: ".print_r([$agents_id, $agent_type, $view_level, $id, $recordId], true), FILE_APPEND);
					$db->pquery($sql, [$agents_id, $agent_type, $view_level, $id, $recordId]);
				} else {
					//file_put_contents('logs/devLog.log', "\n Inserting a new participating agent", FILE_APPEND);
					$sql = "INSERT INTO `vtiger_participatingagents`
							  (participatingagentsid, rel_crmid, agents_id, agent_type, view_level, status)
							   VALUES (?,?,?,?,?,?)";
					//file_put_contents('logs/devLog.log', "\n Sql : ".print_r($sql, true), FILE_APPEND);
					//file_put_contents('logs/devLog.log', "\n Params : ".print_r([$id, $recordId, $inbox_id,
					// $agents_id,
					//                                                             $agent_type, $permission,
					//$modified_on,
					//                                                             $modified_by, $status], true),
					//                  FILE_APPEND);
					//file_put_contents('logs/devLog.log', "\n SQL: $sql \n PARAMS: ".print_r([$id, $recordId, $agents_id, $agent_type, $permission, $status], true), FILE_APPEND);
					$db->pquery($sql, [$id, $recordId, $agents_id, $agent_type, $permission, $status]);
				}
			}
		}*/
	}

    public static function getParticipants($recordId)
    {
        if((int)$recordId == 0)
        {
            return [];
        }
		$participantRows = [];
		$db              = PearDatabase::getInstance();
		$sql             = 'SELECT * FROM `vtiger_participatingagents` WHERE rel_crmid=? AND deleted=0';
		$result          = $db->pquery($sql, [$recordId]);
        if ($result)  {
            while ($row = $result->fetchRow()) {
                if (!$row['agents_id']) {
                    //skip empty values, which is sort of odd.
                    continue;
                }
                try {
                    $agentsRecordModel = Agents_Record_Model::getInstanceById($row['agents_id'], 'Agents');
                } catch (Exception $ex) {
                    //this can throw an error if the record is deleted or invalid.
                    continue;
                }
                if (
                    !$agentsRecordModel ||
                    $agentsRecordModel->getModuleName() != 'Agents'
                ) {
                    //no Agents record found.
                    continue;
                }
                $data = $agentsRecordModel->getData();
                //@NOTE: legacy translation that is expected in places.
                $row['agentName'] = $agentsRecordModel->get('agentname');
                if (is_array($data)) {
                    foreach ($data as $key => $value) {
                        if (!key_exists($key, $row)) {
                            //defer to the main row.
                            $row[$key] = $value;
                        }
                    }
                }
                $participantRows[]     = $row;
            }
        }
		return $participantRows;
	}

    public static function getParticipantPicklistValues()
    {
		//assemble/return picklist values here
		$picklistValues = [];
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT * FROM `vtiger_agent_type` ORDER BY sortorderid ASC', []);
        while ($row =& $result->fetchRow()) {
			$picklistValues[$row['agent_type']] = $row['agent_type'];
		}
		return $picklistValues;
	}

    public static function getParticipantAgentsPicklistValues($ordersId)
    {
                //assemble/return picklist values here
                $picklistValues = [];
        if ($ordersId != null) {
                    $agents = ParticipatingAgents_Module_Model::getParticipants($ordersId);
		    $i = 0;
                    foreach ($agents as $agent) {
                        $string = '('.$agent['agent_number'].') '.'('.$agent['agent_type'].') '.$agent['agentName'];
			    $picklistValues[$i]['value'] = $agent['agents_id'];
                        $picklistValues[$i]['label'] = $string;
			$i++;
                    }
                }
		return $picklistValues;
	}

	public static function getGraebelDefaultParticipatingCarriers(){
	    if(getenv('INSTANCE_NAME') != 'graebel'){
	        return;
        }
        $defaultRows = [];
        $defaultInterstateCarrierNum = 836;
        $defaultRows['InterstateCarrier'] = self::getDefaultCarrierForAgentNumber($defaultInterstateCarrierNum);
        $defaultCommoditiesCarrierNum = 836;
        $defaultRows['CommoditiesCarrier'] = self::getDefaultCarrierForAgentNumber($defaultCommoditiesCarrierNum);
        $defaultIntraTexasCarrierNum = 898;
        $defaultRows['IntraTexasCarrier'] = self::getDefaultCarrierForAgentNumber($defaultIntraTexasCarrierNum);
//        $defaultIntraLocalGSACarrierNum = 936;
//        $defaultRows['IntraLocalGSACarrier'] = self::getDefaultCarrierForAgentNumber($defaultIntraLocalGSACarrierNum);
        return $defaultRows;
    }

    public static function getDefaultCarrierForAgentNumber($agent_number){
        $db              = PearDatabase::getInstance();
        $carrierRows = [];
        $sql    = 'SELECT agentname, agent_number, agentsid FROM `vtiger_agents` WHERE agent_number = ?';
        $result = $db->pquery($sql, [$agent_number]);
        $row    = $result->fetchRow();
        if ($row) {
            $carrierRows['agentName']    = $row['agentname'];
            $carrierRows['agents_id']    = $row['agentsid'];
        }
        return $carrierRows;
    }

    public static function emailMoveCoordinators($fieldList, $users)
    {
//		file_put_contents('logs/devLog.log', "\n Entering emailMoveCoordinators function", FILE_APPEND);
		$db = PearDatabase::getInstance();
		$sql = "SELECT label FROM `vtiger_crmentity` WHERE crmid=?";
		$result = $db->pquery($sql, [$fieldList['record']]);
		//Set variables for email
		global $vtiger_current_version;
        if (getenv('IGC_MOVEHQ')) {
            $softwareName  = 'MoveHQ';
            $developerName = 'WIRG';
            $developerSite = 'www.mobilemover.com';
            $logo          = '<img src="test/logo/MoveHQ.png" title="MoveHQ.png" alt="MoveHQ.png">';
            $website       = 'www.mobilemover.com';
            $supportTeam   = 'MoveHQ Support Team';
            $supportEmail  = getenv('SUPPORT_EMAIL_ADDRESS');
        } elseif (getenv('INSTANCE_NAME') == 'sirva') {
            $softwareName = 'MoveCRM';
            $developerName = 'SIRVA';
            $developerSite = 'www.igcsoftware.com';
            $logo = '<img src="test/logo/MoveCRM.png" title="MoveCRM.png" alt="MoveCRM.png">';
            $website = 'www.igcsoftware.com';
            $supportTeam = 'MoveCRM Support Team';
            $supportEmail = getenv('SUPPORT_EMAIL_ADDRESS');
        } else {
            $softwareName  = 'MoveCRM';
            $developerName = 'IGC Software';
            $developerSite = 'www.igcsoftware.com';
            $logo          = '<img src="test/logo/MoveCRM.png" title="MoveCRM.png" alt="MoveCRM.png">';
            $website       = 'www.igcsoftware.com';
            $supportTeam   = 'MoveCRM Support Team';
            $supportEmail  = getenv('SUPPORT_EMAIL_ADDRESS');
        }
		$subject = $softwareName.' '.$fieldList['module'].' Participation Removed';
		$message = '<div style="border:2px solid #204e81;"><div style="padding:10px;background-color:#fafafb;">Participation on a record has been removed. Details are provided below:
						<br /><br /> Record ID: '.$fieldList['record'].'
						<br />		 Record Name: '.$result->fields['label'].'</div><div style="background-color:#204e81;padding:5px;vertical-align:middle">
						<p align="center" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;"><span style="font-weight:bold;">Powered by '.$softwareName.' '.$vtiger_current_version.'   &copy; '.date("Y").' <a href="http://'.$developerSite.'" style="color:#ffffff;font-size:.9em;font-family:Arial, Helvetica, sans-serif;line-height:11px;text-decoration:none;">'.$developerName.'</a></span></p>
						</div></div>';
        $userEmails = [];
        foreach($users as $uid => $uemail) {
            $userEmails[] = $uemail;
        }
		$mail_status = send_mail('Participating Agents', implode(',', $userEmails), $supportTeam, $supportEmail, $subject, $message, '', '', '', '', '', true);
	}
}
