<?php
/* +*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************** */

require_once 'include/Webservices/Retrieve.php';
require_once 'include/Webservices/Create.php';
require_once 'include/Webservices/Delete.php';
require_once 'include/Webservices/DescribeObject.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.Globals');
vimport('includes.runtime.BaseModel');

function vtws_convertlead($entityvalues, $user, $updateLead = false)
{
    $_REQUEST['isWebserviceConvertLead'] = 1;
    global $adb, $log;
    if (empty($entityvalues['assignedTo'])) {
        $entityvalues['assignedTo'] = vtws_getWebserviceEntityId('Users', $user->id);
    }
    if (empty($entityvalues['transferRelatedRecordsTo'])) {
        $entityvalues['transferRelatedRecordsTo'] = 'Contacts';
    }


    $leadObject = VtigerWebserviceObject::fromName($adb, 'Leads');
    $handlerPath = $leadObject->getHandlerPath();
    $handlerClass = $leadObject->getHandlerClass();

    require_once $handlerPath;

    $leadHandler = new $handlerClass($leadObject, $user, $adb, $log);

    $leadInfo = vtws_retrieve($entityvalues['leadId'], $user);
    $sql = "select converted from vtiger_leaddetails where converted = 1 and leadid=?";
    $leadIdComponents = vtws_getIdComponents($entityvalues['leadId']);
    $result = $adb->pquery($sql, [$leadIdComponents[1]]);
    if ($result === false) {
        throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,
                vtws_getWebserviceTranslatedString('LBL_' .
                        WebServiceErrorCode::$DATABASEQUERYERROR));
    }
    $rowCount = $adb->num_rows($result);
    if ($rowCount > 0 && !$updateLead) {
        throw new WebServiceException(WebServiceErrorCode::$LEAD_ALREADY_CONVERTED,
                "Lead is already converted");
    }

    $entityIds = [];

    $availableModules = ['Accounts', 'Contacts', 'Opportunities'];

    if (!(($entityvalues['entities']['Accounts']['create']) || ($entityvalues['entities']['Contacts']['create']))) {
        return null;
    }

    foreach ($availableModules as $entityName) {
        if ($entityvalues['entities'][$entityName]['create']) {
            $entityvalue = $entityvalues['entities'][$entityName];
            $entityObject = VtigerWebserviceObject::fromName($adb, $entityvalue['name']);
            $handlerPath = $entityObject->getHandlerPath();
            $handlerClass = $entityObject->getHandlerClass();

            require_once $handlerPath;

            $entityHandler = new $handlerClass($entityObject, $user, $adb, $log);

            $entityObjectValues = [];
            $entityObjectValues['assigned_user_id'] = $entityvalues['assignedTo'];
            $entityObjectValues = vtws_populateConvertLeadEntities($entityvalue, $entityObjectValues, $entityHandler, $leadHandler, $leadInfo);

            // update opportunity related to property
            if ($entityvalue['name'] == 'Opportunities') {
                if (!empty($entityIds['Accounts'])) {
                    $entityObjectValues['related_to'] = $entityIds['Accounts'];
                }
                if (!empty($entityIds['Contacts'])) {
                    $entityObjectValues['contact_id'] = $entityIds['Contacts'];
                }

                //populate opp origin phone w. lead primary phone if lead origin phone is not present
                if (!$leadInfo['origin_phone1'] && $leadInfo['phone']) {
                    $entityObjectValues['origin_phone1'] = $leadInfo['phone'];
                }
                // Also need to do this for the primary phone type.
                if(!$leadInfo['origin_phone1_type'] && $leadInfo['primary_phone_type']) {
                    $entityObjectValues['origin_phone1_type'] = $leadInfo['primary_phone_type'];
                }

                $entityObjectValues['converted_from'] = $entityvalues['leadId'];
                //ram default participants in

                if (!isset($entityObjectValues['skipparticipation'])) {

                    //Remove any existing agents, and udpate them with new ones
                    if($updateLead){
                        $oppId = $adb->pquery('SELECT `potentialid` FROM `vtiger_potential` WHERE `converted_from` = ?', [explode('x', $entityvalues['leadId'])[1]])->fetchRow()['potentialid'];

                        $sql = "DELETE FROM `vtiger_participatingagents` WHERE rel_crmid=?";
                        $adb->pquery($sql, [$oppId]);
                    }


                    if (getenv('INSTANCE_NAME') == 'sirva') {
                        $agent_info = $adb->pquery('SELECT `vtiger_agents`.agentsid, `vtiger_agents`.agentmanager_id, self_haul FROM `vtiger_agents` LEFT JOIN `vtiger_agentmanager` ON `vtiger_agents`.agentmanager_id = `vtiger_agentmanager`.agentmanagerid WHERE `vtiger_agentmanager`.agentmanagerid = ?', [$entityObjectValues['agentid']])->fetchRow();
                    } else {
                        $agent_info = $adb->pquery('SELECT `vtiger_agents`.agentsid FROM `vtiger_agents` LEFT JOIN `vtiger_agentmanager` ON `vtiger_agents`.agentmanager_id = `vtiger_agentmanager`.agentmanagerid WHERE `vtiger_agentmanager`.agentmanagerid = ?', [$entityObjectValues['agentid']])->fetchRow();
                    }
                    $agentsId = $agent_info['agentsid'];
                    setParticipantInfo(0, $agentsId, 'Booking Agent', $entityObjectValues);

                    if(getenv('IGC_MOVEHQ')){
                        $entityObjectValues['numAgents'] = $agent_info['self_haul'] == '1' ? 1 : 0;
                        setParticipantInfo(1, $agentsId, 'Hauling Agent', $entityObjectValues);
                    } else {
                        $entityObjectValues['numAgents'] = $agent_info['self_haul'] == '1'?3:2;
                        setParticipantInfo(1, $agentsId, 'Origin Agent', $entityObjectValues);
                        setParticipantInfo(2, $agentsId, 'Estimating Agent', $entityObjectValues);
                        if ($entityObjectValues['numAgents'] == 3) {
                            $hauling_info = $adb->pquery('SELECT `vtiger_agents`.agentsid FROM `vtiger_agents` LEFT JOIN `vtiger_agentmanager` ON `vtiger_agents`.agentmanager_id = `vtiger_agentmanager`.self_haul_agentmanagerid WHERE `vtiger_agentmanager`.agentmanagerid = ?', [$agent_info['agentmanager_id']])->fetchRow();
                            $selfHaulAgent = $hauling_info['agentsid'] ?: $agentsId;
                            setParticipantInfo(3, $selfHaulAgent, 'Hauling Agent', $entityObjectValues);
                        }
                    }
                    //file_put_contents('logs/devLog.log', "\n EntityObjectValues : ".print_r($entityObjectValues, true), FILE_APPEND);
                }
            }


            //update the contacts relation
            if ($entityvalue['name'] == 'Contacts') {
                if (!empty($entityIds['Accounts'])) {
                    $entityObjectValues['account_id'] = $entityIds['Accounts'];
                }
            }
            //update the contacts relation
            if ($entityvalue['name'] == 'Opportunities') {
                $entityObjectValues['self_haul'] = $agent_info['self_haul'];


                if($updateLead){
                    $oppId = $adb->pquery('SELECT `potentialid` FROM `vtiger_potential` WHERE `converted_from` = ?', [explode('x', $entityvalues['leadId'])[1]])->fetchRow()['potentialid'];
                    $entityObjectValues['id'] = vtws_getWebserviceEntityId('Opportunities', $oppId);
                }
            }

            try {
                $create = true;
                if ($entityvalue['name'] == 'Accounts') {
                    $sql = "SELECT vtiger_account.accountid FROM vtiger_account,vtiger_crmentity WHERE vtiger_crmentity.crmid=vtiger_account.accountid AND vtiger_account.accountname=? AND vtiger_crmentity.deleted=0";
                    $result = $adb->pquery($sql, [$entityvalue['accountname']]);
                    if ($adb->num_rows($result) > 0) {
                        $entityIds[$entityName] = vtws_getWebserviceEntityId('Accounts', $adb->query_result($result, 0, 'accountid'));
                        $create = false;
                    }
                }
                if ($create) {
                    //file_put_contents('logs/devLog.log', "\n name : " . $entityvalue['name'], FILE_APPEND);
                    //file_put_contents('logs/devLog.log', "\n entityObjectValues : ".print_r($entityObjectValues,true), FILE_APPEND);
                    $entityRecord = ($updateLead && $entityvalue['name'] == 'Opportunities') ? vtws_revise($entityObjectValues, $user) : vtws_create($entityvalue['name'], $entityObjectValues, $user);
                    if (getenv('INSTANCE_NAME') == 'sirva' && $entityvalue['name'] == 'Opportunities') {
                        $adb->pquery('UPDATE `vtiger_potential` SET `converted_from` = ? WHERE `potentialid` = ?', [explode('x', $entityvalues['leadId'])[1], explode('x', $entityRecord['id'])[1]]);
                        $userId = $entityRecord['assigned_user_id'];
                        $wsdl   = getenv('SURVEY_SYNC_URL');
                        if ($wsdl) {
                            $params              = [];
                            $sql                 = "SELECT user_name FROM `vtiger_users` WHERE id=?";
                            $result              = $adb->pquery($sql, [$userId]);
                            $params['username']  = $adb->query_result($result, 0, 'user_name');
                            $sql                 = "SELECT accesskey FROM `vtiger_users` WHERE id=?";
                            $result              = $adb->pquery($sql, [$userId]);
                            $params['accessKey'] = $adb->query_result($result, 0, 'accesskey');
                            $params['recordID']  = $entityRecord['id'];
                            $params['address']   = getenv('SITE_URL');
                            try {
                                $soapClient = new soapclient2($wsdl, 'wsdl');
                                $soapClient->setDefaultRpcParams(true);
                                $soapProxy = $soapClient->getProxy();
                                if (method_exists($soapProxy, 'BidirectionalUpdateNotification')) {
                                    $soapResult = $soapProxy->BidirectionalUpdateNotification($params);
                                    //file_put_contents('logs/devLog.log', "\n".date('Y-m-d H:i:s - ')." Opportunities Bidirectional Sync SoapResult : ".print_r($soapResult, true), FILE_APPEND);
                                }
                            } catch (Exception $ex) {
                                //@TODO: Care about failing.
                            }
                        }
                    }
                    if(getenv('IGC_MOVEHQ') && $entityvalue['name'] == 'Opportunities'){
                        //We're just going to transfer the move roles, since the lead is ideally hidden forever.
                        $adb->pquery('UPDATE `vtiger_moveroles` SET `moveroles_orders` = ? WHERE `moveroles_orders` = ?', [explode('x', $entityRecord['id'])[1], explode('x', $entityvalues['leadId'])[1]]);
                    }
                    $entityIds[$entityName] = $entityRecord['id'];
                }
            } catch (Exception $e) {
                throw new WebServiceException(WebServiceErrorCode::$UNKNOWNOPERATION,
                        $e->getMessage() . ' : ' . $entityvalue['name']);
            }
        }
    }


    try {
        $accountIdComponents = vtws_getIdComponents($entityIds['Accounts']);
        $accountId = $accountIdComponents[1];

        $contactIdComponents = vtws_getIdComponents($entityIds['Contacts']);
        $contactId = $contactIdComponents[1];

        if (!empty($accountId) && !empty($contactId) && !empty($entityIds['Opportunities']) && !$updateLead) {
            $potentialIdComponents = vtws_getIdComponents($entityIds['Opportunities']);
            $potentialId = $potentialIdComponents[1];
            $sql = "insert into vtiger_contpotentialrel values(?,?)";
            $result = $adb->pquery($sql, [$contactId, $potentialIdComponents[1]]);
            if ($result === false) {
                throw new WebServiceException(WebServiceErrorCode::$FAILED_TO_CREATE_RELATION,
                        "Failed to related Contact with the Opportunity");
            }
        }

        if(!$updateLead){
            $transfered = vtws_convertLeadTransferHandler($leadIdComponents, $entityIds, $entityvalues);


            $relatedIdComponents = vtws_getIdComponents($entityIds[$entityvalues['transferRelatedRecordsTo']]);
            //vtws_getRelatedActivities($leadIdComponents[1], $accountId, $contactId, $relatedIdComponents[1]);
            vtws_updateConvertLeadStatus($entityIds, $entityvalues['leadId'], $user);
        }
    } catch (Exception $e) {
        foreach ($entityIds as $entity => $id) {
            vtws_delete($id, $user);
        }

        return null;
    }

    return $entityIds;
}

/*
 * populate the entity fields with the lead info.
 * if mandatory field is not provided populate with '????'
 * returns the entity array.
 */

function vtws_populateConvertLeadEntities($entityvalue, $entity, $entityHandler, $leadHandler, $leadinfo)
{
    global $adb, $log;
    $column;
    $entityName = $entityvalue['name'];
    $sql = "SELECT * FROM vtiger_convertleadmapping";
    $result = $adb->pquery($sql, []);
    if ($adb->num_rows($result)) {
        switch ($entityName) {
            case 'Accounts':$column = 'accountfid';
                break;
            case 'Contacts':$column = 'contactfid';
                break;
            case 'Opportunities':$column = 'potentialfid';
                break;
            default:$column = 'leadfid';
                break;
        }

        $leadFields = $leadHandler->getMeta()->getModuleFields();
        $entityFields = $entityHandler->getMeta()->getModuleFields();
        $row = $adb->fetch_array($result);
        $count = 1;
        do {
            $entityField = vtws_getFieldfromFieldId($row[$column], $entityFields);
            if ($entityField == null) {
                //user doesn't have access so continue.TODO update even if user doesn't have access
                continue;
            }
            $leadField = vtws_getFieldfromFieldId($row['leadfid'], $leadFields);
            if ($leadField == null) {
                //user doesn't have access so continue.TODO update even if user doesn't have access
                continue;
            }
            $leadFieldName = $leadField->getFieldName();
            $entityFieldName = $entityField->getFieldName();
            $entity[$entityFieldName] = $leadinfo[$leadFieldName];
            $count++;
        } while ($row = $adb->fetch_array($result));

        foreach ($entityvalue as $fieldname => $fieldvalue) {
            if (!empty($fieldvalue)) {
                $entity[$fieldname] = $fieldvalue;
            }
        }

        $entity = vtws_validateConvertLeadEntityMandatoryValues($entity, $entityHandler, $leadinfo, $entityName);
    }

    return $entity;
}

function vtws_validateConvertLeadEntityMandatoryValues($entity, $entityHandler, $leadinfo, $module)
{
    $mandatoryFields = $entityHandler->getMeta()->getMandatoryFields();
    foreach ($mandatoryFields as $field) {
        if (empty($entity[$field])) {
            $fieldInfo = vtws_getConvertLeadFieldInfo($module, $field);
            if (($fieldInfo['type']['name'] == 'picklist' || $fieldInfo['type']['name'] == 'multipicklist'
                || $fieldInfo['type']['name'] == 'date' || $fieldInfo['type']['name'] == 'datetime')
                && ($fieldInfo['editable'] == true)) {
                $entity[$field] = $fieldInfo['default'];
            } else {
                $entity[$field] = '????';
            }
        }
    }

    return $entity;
}

function vtws_getConvertLeadFieldInfo($module, $fieldname)
{
    global $adb, $log, $current_user;
    $describe = vtws_describe($module, $current_user);
    foreach ($describe['fields'] as $index => $fieldInfo) {
        if ($fieldInfo['name'] == $fieldname) {
            return $fieldInfo;
        }
    }

    return false;
}

//function to handle the transferring of related records for lead
function vtws_convertLeadTransferHandler($leadIdComponents, $entityIds, $entityvalues)
{
    try {
        $entityidComponents = vtws_getIdComponents($entityIds[$entityvalues['transferRelatedRecordsTo']]);
        vtws_transferLeadRelatedRecords($leadIdComponents[1], $entityidComponents[1], $entityvalues['transferRelatedRecordsTo']);
    } catch (Exception $e) {
        return false;
    }

    return true;
}

function vtws_updateConvertLeadStatus($entityIds, $leadId, $user)
{
    global $adb, $log;
    $leadIdComponents = vtws_getIdComponents($leadId);
    if ($entityIds['Accounts'] != '' || $entityIds['Contacts'] != '') {
        $sql = "UPDATE vtiger_leaddetails SET converted = 1 where leadid=?";
        $result = $adb->pquery($sql, [$leadIdComponents[1]]);
        if ($result === false) {
            throw new WebServiceException(WebServiceErrorCode::$FAILED_TO_MARK_CONVERTED,
                    "Failed mark lead converted");
        }
        //updating the campaign-lead relation --Minnie
        $sql = "DELETE FROM vtiger_campaignleadrel WHERE leadid=?";
        $adb->pquery($sql, [$leadIdComponents[1]]);

        $sql = "DELETE FROM vtiger_tracker WHERE item_id=?";
        $adb->pquery($sql, [$leadIdComponents[1]]);

        //update the modifiedtime and modified by information for the record
        $leadModifiedTime = $adb->formatDate(date('Y-m-d H:i:s'), true);
        $crmentityUpdateSql = "UPDATE vtiger_crmentity SET modifiedtime=?, modifiedby=? WHERE crmid=?";
        $adb->pquery($crmentityUpdateSql, [$leadModifiedTime, $user->id, $leadIdComponents[1]]);
    }
    $moduleArray = ['Accounts','Contacts','Opportunities'];

    foreach ($moduleArray as $module) {
        if (!empty($entityIds[$module])) {
            $idComponents = vtws_getIdComponents($entityIds[$module]);
            $id = $idComponents[1];
            $webserviceModule = vtws_getModuleHandlerFromName($module, $user);
            $meta = $webserviceModule->getMeta();
            $fields = $meta->getModuleFields();
            $field = $fields['isconvertedfromlead'];
            $tablename = $field->getTableName();
            $tableList = $meta->getEntityTableIndexList();
            $tableIndex = $tableList[$tablename];
            $adb->pquery("UPDATE $tablename SET isconvertedfromlead = ? WHERE $tableIndex = ?", [1, $id]);
        }
    }
}

function setParticipantInfo($index, $agentsId, $agentType, &$entityObjectValues)
{
    $entityObjectValues['participantDelete_'.$index] = false;
    $entityObjectValues['participantId_'.$index] = 'none';
    $entityObjectValues['agent_permission_'.$index] = 'full';
    $entityObjectValues['agents_id_'.$index] = $agentsId;
    $entityObjectValues['agent_type_'.$index] = $agentType;
}
