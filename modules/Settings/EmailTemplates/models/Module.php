<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
/**
 * Email Template Model Class
 */
class Settings_EmailTemplates_Module_Model extends Settings_Vtiger_Module_Model
{

    /**
     * Function retruns List of Email Templates
     * @return string
     */
    public function getListViewUrl()
    {
        return 'module=EmailTemplates&parent=Settings&view=List';
    }

    /**
     * Function returns all the Email Template Models
     * @return <Array of EmailTemplates_Record_Model>
     */
    public function getAll()
    {
        $db = PearDatabase::getInstance();
        $result = $db->pquery('SELECT * FROM vtiger_emailtemplates WHERE deleted = 0', array());

        $emailTemplateModels = array();
        for ($i=0; $i<$db->num_rows($result); $i++) {
            $emailTemplateModel = Settings_EmailTemplates_Record_Model::getInstance();
            $rowData = $db->query_result_rowdata($result, $i);
            if (getenv('IGC_MOVEHQ')) {
                $rowData['subject']    = str_replace('$software_name$', 'moveHQ', $rowData['subject']);
            } else {
                $rowData['subject']    = str_replace('$software_name$', 'moveCRM', $rowData['subject']);
            }
            $emailTemplateModel->setData($rowData);
            $emailTemplateModels[] = $emailTemplateModel;
        }

        return $emailTemplateModels;
    }

    /**
     * Function returns a filtered list of email templates
     * @param Array $ids - an array of agentmanager ids that are accepted
     *
     * @return <Array of EmailTemplates_Record_Model>
     */
    public function getFiltered($agentIds)
    {
        $db = PearDatabase::getInstance();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $ids = "1,".$currentUser->getId();
        $agents = explode(' |##| ', $currentUser->get('agent_ids'));
        foreach($agents as $agent) {
            $agentModel = Vtiger_Record_Model::getInstanceById($agent);
            $isAgent = $agentModel->get('record_module') == 'AgentManager';
            if($isAgent) {
                $vanlineId = $agentModel->get('vanline_id');
                if(!in_array($vanlineId, $agents)) {
                    $agents[] = $vanlineId;
                }
            }
        }

        $query = "SELECT vtiger_emailtemplates.* FROM vtiger_emailtemplates JOIN vtiger_users ON vtiger_emailtemplates.owner_id=vtiger_users.id
                               WHERE vtiger_emailtemplates.deleted = 0 AND (vtiger_emailtemplates.owner_id IN ($ids) OR vtiger_users.agent_ids REGEXP '[[:<:]](";
        foreach($agents as $key=>$agentId) {
            if($key != 0) {
                $query .= '|';
            }
            $query .= $agentId;
        }
        $query .= ")[[:>:]]')";
        $result = $db->pquery($query,array());

        $emailTemplateModels = array();
        for ($i=0; $i<$db->num_rows($result); $i++) {
            $emailTemplateModel = Settings_EmailTemplates_Record_Model::getInstance();
            $rowData = $db->query_result_rowdata($result, $i);
            if (getenv('IGC_MOVEHQ')) {
                $rowData['subject']    = str_replace('$software_name$', 'moveHQ', $rowData['subject']);
            } else {
                $rowData['subject']    = str_replace('$software_name$', 'moveCRM', $rowData['subject']);
            }
            $emailTemplateModel->setData($rowData);
            $emailTemplateModels[] = $emailTemplateModel;
        }

        return $emailTemplateModels;
    }
}
