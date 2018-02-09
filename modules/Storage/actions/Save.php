<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Storage_Save_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        $authDays = $request->get('storage_adays');
        $SITDateIn = $request->get('storage_sit_datein');
        $SITWeight = $request->get('storage_sit_weight');
        $SITAuth = $request->get('storage_sit_authorization');
        if ($SITAuth == '' && $authDays != '' && $SITDateIn != '' && $SITWeight != '') {
            $newAuthNumber = $this->getModentityNumber('Storage', 'SITAUTH');
            $request->set('storage_sit_authorization', $newAuthNumber);
        }
        $PERMDateIn = $request->get('storage_perm_datein');
        $PERMWeight = $request->get('storage_perm_weight');
        $PERMAuth = $request->get('storage_perm_authorization');
        if ($PERMAuth == '' && $authDays != '' && $PERMDateIn != '' && $PERMWeight != '') {
            $newAuthNumber = $this->getModentityNumber('Storage', 'PERMAUTH');
            $request->set('storage_perm_authorization', $newAuthNumber);
        }

        $SITApprDateIn = $request->get('storage_sit_approved_datein');
        $date = '';
        if ($SITApprDateIn != '') {
            $date = $SITApprDateIn;
        } elseif ($SITDateIn != '') {
            $date = $SITDateIn;
        }
        if ($authDays != '' && $date != '') {
            $interval = new DateInterval('P'.++$authDays.'D');
            $aux = date_parse_from_format('m-d-Y', $date);
            $aux2 = $aux['year'].'-'.$aux['month'].'-'.$aux['day'];
            $newDate = new DateTime($aux2);
            $newDate->add($interval);
            $final = $newDate->format('m-d-Y');
            $request->set('storage_sit_date_perm_storage', $final);
        }
        parent::process($request);
                //participants save
                $record = $request->get('storage_orders');
        $storageAgent = $request->get('storage_agent');
        $participantId = 'none';
        $db = PearDatabase::getInstance();
        $query = 'SELECT participatingagentsid FROM vtiger_participatingagents WHERE agent_type="Warehousing Agent" AND rel_crmid=? AND deleted=0';
        $result = $db->pquery($query, [$record]);
        if ($result && $db->num_rows($result) > 0) {
            $participantId = $result->fetchRow()['participatingagentsid'];
        }
        if ($storageAgent != '') {
            $warehousingAgent = [
                        'numAgents' => 1,
                        'participantId_1' => $participantId,
                        'agent_permission_1' => 'read_only',
                        'agent_type_1' => 'Warehousing Agent',
                        'agents_id_1' => $storageAgent,
                        'module' => 'Orders',
                        'agentsid' => $request->get('assigned_user_id'),
                        ];
            $participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
            if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
                $participatingAgentsModel::saveParticipants($warehousingAgent, $record);
            }
        }
    }

    public function getModentityNumber($module, $prefix)
    {
        $currentSeq = CRMEntity::setModuleSeqNumber('increment', $module, $prefix);
        return $currentSeq;
    }
}
