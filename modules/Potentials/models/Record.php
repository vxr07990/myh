<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Potentials_Record_Model extends Vtiger_Record_Model
{
    public function getCreateInvoiceUrl()
    {
        $invoiceModuleModel = Vtiger_Module_Model::getInstance('Invoice');
        return 'index.php?module='.$invoiceModuleModel->getName().'&view='.$invoiceModuleModel->getEditViewName().'&account_id='.$this->get('related_to').'&contact_id='.$this->get('contact_id');
    }

    /**
     * Function returns the url for create event
     * @return <String>
     */
    public function getCreateEventUrl()
    {
        $calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
        return $calendarModuleModel->getCreateEventRecordUrl().'&parent_id='.$this->getId();
    }
    public function getCreateSurveyUrl()
    {
        $surveyModuleModel = Vtiger_Module_Model::getInstance('Surveys');
        return $surveyModuleModel->getCreateRecordUrl().'&sourceRecord='.$this->getId().'&sourceModule='.$this->getModuleName().'&potential_id='.$this->getID().'&relationOperation=true';
    }

    /**
     * Function returns the url for create todo
     * @return <String>
     */
    public function getCreateTaskUrl()
    {
        $calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
        return $calendarModuleModel->getCreateTaskRecordUrl().'&parent_id='.$this->getId();
    }

    /**
     * Function to get List of Fields which are related from Opportunity to Inventory Record
     * @return <array>
     */
    public function getInventoryMappingFields()
    {
        file_put_contents('logs/PotentialMapping.log', date('Y-m-d H:i:s - ')."Entering getInventoryMappingFields function\n", FILE_APPEND);
        return array(
                array('parentField'=>'related_to', 'inventoryField'=>'account_id', 'defaultValue'=>''),
                array('parentField'=>'contact_id', 'inventoryField'=>'contact_id', 'defaultValue'=>''),
                array('parentField'=>'business_line', 'inventoryField'=>'business_line', 'defaultValue'=>''),
                array('parentField'=>'business_line2', 'inventoryField'=>'business_line2', 'defaultValue'=>''),
                array('parentField'=>'origin_address1', 'inventoryField'=>'origin_address1', 'defaultValue'=>''),
                array('parentField'=>'origin_address2', 'inventoryField'=>'origin_address2', 'defaultValue'=>''),
                array('parentField'=>'origin_city', 'inventoryField'=>'origin_city', 'defaultValue'=>''),
                array('parentField'=>'origin_state', 'inventoryField'=>'origin_state', 'defaultValue'=>''),
                array('parentField'=>'origin_phone1', 'inventoryField'=>'origin_phone1', 'defaultValue'=>''),
                array('parentField'=>'origin_phone2', 'inventoryField'=>'origin_phone2', 'defaultValue'=>''),
                array('parentField'=>'origin_zip', 'inventoryField'=>'origin_zip', 'defaultValue'=>''),
                array('parentField'=>'destination_address1', 'inventoryField'=>'destination_address1', 'defaultValue'=>''),
                array('parentField'=>'destination_address2', 'inventoryField'=>'destination_address2', 'defaultValue'=>''),
                array('parentField'=>'destination_city', 'inventoryField'=>'destination_city', 'defaultValue'=>''),
                array('parentField'=>'destination_state', 'inventoryField'=>'destination_state', 'defaultValue'=>''),
                array('parentField'=>'destination_zip', 'inventoryField'=>'destination_zip', 'defaultValue'=>''),
                array('parentField'=>'destination_phone1', 'inventoryField'=>'destination_phone1', 'defaultValue'=>''),
                array('parentField'=>'destination_phone2', 'inventoryField'=>'destination_phone2', 'defaultValue'=>''),
                array('parentField'=>'load_date', 'inventoryField'=>'pickup_date', 'defaultValue'=>''),

                array('parentField'=>'origin_address1', 'inventoryField'=>'address1', 'defaultValue'=>''),
                array('parentField'=>'origin_address2', 'inventoryField'=>'address2', 'defaultValue'=>''),
                array('parentField'=>'origin_city', 'inventoryField'=>'city', 'defaultValue'=>''),
                array('parentField'=>'origin_state', 'inventoryField'=>'state', 'defaultValue'=>''),
                array('parentField'=>'origin_phone1', 'inventoryField'=>'phone1', 'defaultValue'=>''),
                array('parentField'=>'origin_phone2', 'inventoryField'=>'phone2', 'defaultValue'=>''),
                array('parentField'=>'origin_zip', 'inventoryField'=>'zip', 'defaultValue'=>''),
                array('parentField'=>'origin_country', 'inventoryField'=>'country', 'defaultValue'=>''),
        );
    }
    public function getOrderMappingFields()
    {
        file_put_contents('logs/PotentialMapping.log', date('Y-m-d H:i:s - ')."bEntering getOrderMappingFields function\n", FILE_APPEND);
        return array(
                //Details
                //array('parentField'=>'potentialid', 'ordersField'=>'orders_potentials', 'defaultValue'=>''),
                //array('parentField'=>'potential_no', 'projectField'=>'potential_no', 'defaultValue'=>''),
                //array('parentField'=>'potentialname', 'projectField'=>'projectname', 'defaultValue'=>''),
                // array('parentField'=>'amount', 'projectField'=>'
                // array('parentField'=>'potentialtype', 'projectField'=>'
                array('parentField'=>'related_to', 'orderField'=>'orders_account', 'defaultValue'=>''),
                array('parentField'=>'contact_id', 'orderField'=>'orders_contacts', 'defaultValue'=>''),
                array('parentField'=>'business_line', 'orderField'=>'business_line', 'defaultValue'=>''),
                array('parentField'=>'business_line2', 'orderField'=>'business_line2', 'defaultValue'=>''),
                array('parentField'=>'is_competitive', 'orderField'=>'competitive', 'defaultValue'=>''),
                array('parentField'=>'billing_type', 'orderField'=>'billing_type', 'defaultValue'=>''),
                //array('parentField'=>'amount', 'projectField'=>'targetbudget', 'defaultValue'=>''),
                // array('parentField'=>'estimate_type', 'projectField'=>'
                // array('parentField'=>'pricing_type', 'projectField'=>'

                //Origin Address Fields
                array('parentField'=>'origin_address1', 'orderField'=>'origin_address1', 'defaultValue'=>''),
                array('parentField'=>'origin_address2', 'orderField'=>'origin_address2', 'defaultValue'=>''),
                array('parentField'=>'origin_city', 'orderField'=>'origin_city', 'defaultValue'=>''),
                array('parentField'=>'origin_state', 'orderField'=>'origin_state', 'defaultValue'=>''),
                array('parentField'=>'origin_zip', 'orderField'=>'origin_zip', 'defaultValue'=>''),
                array('parentField'=>'origin_country', 'orderField'=>'origin_country', 'defaultValue'=>''),
                array('parentField'=>'origin_phone1', 'orderField'=>'origin_phone1', 'defaultValue'=>''),
                array('parentField'=>'origin_phone2', 'orderField'=>'origin_phone2', 'defaultValue'=>''),
                array('parentField'=>'origin_description1', 'orderField'=>'origin_description', 'defaultValue'=>''),

                //Destination Address Fields
                array('parentField'=>'destination_address1', 'orderField'=>'destination_address1', 'defaultValue'=>''),
                array('parentField'=>'destination_address2', 'orderField'=>'destination_address2', 'defaultValue'=>''),
                array('parentField'=>'destination_city', 'orderField'=>'destination_city', 'defaultValue'=>''),
                array('parentField'=>'destination_state', 'orderField'=>'destination_state', 'defaultValue'=>''),
                array('parentField'=>'destination_zip', 'orderField'=>'destination_zip', 'defaultValue'=>''),
                array('parentField'=>'destination_country', 'orderField'=>'destination_country', 'defaultValue'=>''),
                array('parentField'=>'destination_phone1', 'orderField'=>'destination_phone1', 'defaultValue'=>''),
                array('parentField'=>'destination_phone2', 'orderField'=>'destination_phone2', 'defaultValue'=>''),
                array('parentField'=>'destination_description','orderField'=>'destination_description', 'defaultValue'=>''),

                //Dates
                array('parentField'=>'pack_date', 'orderField'=>'orders_pdate', 'defaultValue'=>''),
                array('parentField'=>'pack_to_date', 'orderField'=>'orders_ptdate', 'defaultValue'=>''),
                array('parentField'=>'preferred_ppdate', 'orderField'=>'orders_ppdate', 'defaultValue'=>''),
                array('parentField'=>'load_date', 'orderField'=>'orders_ldate', 'defaultValue'=>''),
                array('parentField'=>'load_to_date', 'orderField'=>'orders_ltdate', 'defaultValue'=>''),
                array('parentField'=>'preferred_pldate', 'orderField'=>'orders_pldate', 'defaultValue'=>''),
                array('parentField'=>'deliver_date', 'orderField'=>'orders_ddate', 'defaultValue'=>''),
                array('parentField'=>'deliver_to_date', 'orderField'=>'orders_dtdate', 'defaultValue'=>''),
                array('parentField'=>'preferred_pddate', 'orderField'=>'orders_pddate', 'defaultValue'=>''),
                array('parentField'=>'survey_date', 'orderField'=>'orders_surveyd', 'defaultValue'=>''),
                array('parentField'=>'survey_time', 'orderField'=>'orders_surveyt', 'defaultValue'=>''),
                //array('parentField'=>'followup_date', 'projectField'=>'followup_date', 'defaultValue'=>''),
                //array('parentField'=>'decision_date', 'projectField'=>'decision_date', 'defaultValue'=>'')
        );
    }

    /*public function getProjectMappingFields() {
        return array(
                //Details
                //array('parentField'=>'potentialid', 'projectField'=>'potentialid', 'defaultValue'=>''),
                //array('parentField'=>'potential_no', 'projectField'=>'potential_no', 'defaultValue'=>''),
                array('parentField'=>'potentialname', 'projectField'=>'projectname', 'defaultValue'=>''),
                // array('parentField'=>'amount', 'projectField'=>'
                // array('parentField'=>'potentialtype', 'projectField'=>'
                array('parentField'=>'related_to', 'projectField'=>'linktoaccounts', 'defaultValue'=>''),
                array('parentField'=>'contact_id', 'projectField'=>'contacts_id', 'defaultValue'=>''),
                array('parentField'=>'business_line', 'projectField'=>'business_line', 'defaultValue'=>''),
                array('parentField'=>'amount', 'projectField'=>'targetbudget', 'defaultValue'=>''),
                // array('parentField'=>'estimate_type', 'projectField'=>'
                // array('parentField'=>'pricing_type', 'projectField'=>'

                //Origin Address Fields
                array('parentField'=>'origin_address1', 'projectField'=>'origin_address1', 'defaultValue'=>''),
                array('parentField'=>'origin_address2', 'projectField'=>'origin_address2', 'defaultValue'=>''),
                array('parentField'=>'origin_city', 'projectField'=>'origin_city', 'defaultValue'=>''),
                array('parentField'=>'origin_state', 'projectField'=>'origin_state', 'defaultValue'=>''),
                array('parentField'=>'origin_zip', 'projectField'=>'origin_zip', 'defaultValue'=>''),
                array('parentField'=>'origin_phone1', 'projectField'=>'origin_phone1', 'defaultValue'=>''),
                array('parentField'=>'origin_phone2', 'projectField'=>'origin_phone2', 'defaultValue'=>''),

                //Destination Address Fields
                array('parentField'=>'destination_address1', 'projectField'=>'destination_address1', 'defaultValue'=>''),
                array('parentField'=>'destination_address2', 'projectField'=>'destination_address2', 'defaultValue'=>''),
                array('parentField'=>'destination_city', 'projectField'=>'destination_city', 'defaultValue'=>''),
                array('parentField'=>'destination_state', 'projectField'=>'destination_state', 'defaultValue'=>''),
                array('parentField'=>'destination_zip', 'projectField'=>'destination_zip', 'defaultValue'=>''),
                array('parentField'=>'destination_phone1', 'projectField'=>'destination_phone1', 'defaultValue'=>''),
                array('parentField'=>'destination_phone2', 'projectField'=>'destination_phone2', 'defaultValue'=>''),

                //Dates
                array('parentField'=>'pack_date', 'projectField'=>'pack_date', 'defaultValue'=>''),
                array('parentField'=>'pack_to_date', 'projectField'=>'pack_to_date', 'defaultValue'=>''),
                array('parentField'=>'load_date', 'projectField'=>'load_date', 'defaultValue'=>''),
                array('parentField'=>'load_to_date', 'projectField'=>'load_to_date', 'defaultValue'=>''),
                array('parentField'=>'deliver_date', 'projectField'=>'deliver_date', 'defaultValue'=>''),
                array('parentField'=>'deliver_to_date', 'projectField'=>'deliver_to_date', 'defaultValue'=>''),
                array('parentField'=>'survey_date', 'projectField'=>'survey_date', 'defaultValue'=>''),
                array('parentField'=>'survey_time', 'projectField'=>'survey_time', 'defaultValue'=>''),
                array('parentField'=>'followup_date', 'projectField'=>'followup_date', 'defaultValue'=>''),
                array('parentField'=>'decision_date', 'projectField'=>'decision_date', 'defaultValue'=>'')
        );
    }

    /**
     * Function returns the url for create quote
     * @return <String>
     */
    public function getCreateQuoteUrl()
    {
        $quoteModuleModel = Vtiger_Module_Model::getInstance('Quotes');
        return $quoteModuleModel->getCreateRecordUrl() . '&sourceRecord=' . $this->getId() . '&sourceModule=' . $this->getModuleName() . '&potential_id=' . $this->getId() . '&relationOperation=true';
    }

    public function getParticipantAgents()
    {
        $db = PearDatabase::getInstance();
        $result = $db->pquery('SELECT * FROM `vtiger_participatingagents` WHERE rel_crmid = ? AND deleted=0', array($this->getId()));
        $participantAgent = array();


        if ($result && $db->num_rows($result) > 0) {
            while ($agentInfo = $db->fetchByAssoc($result)) {
                $agentInfo['agentsLink'] = '<a href="index.php?module=Agents&view=Detail&record=' . $agentInfo['agents_id'] . '" target="_blank">' . getEntityName('Agents', array($agentInfo['agents_id']))[$agentInfo['agents_id']] . '</a>';
                $participantAgent[] = $agentInfo;
            }
        }

        return $participantAgent;
    }

    public function getPartipantAgentsEditView()
    {
        $db = PearDatabase::getInstance();
        $result = $db->pquery('SELECT * FROM `vtiger_participatingagents` WHERE rel_crmid = ? AND deleted=0', array($this->getId()));
        $participantAgent = array();
        $rowNo = 0;
        if ($result && $db->num_rows($result) > 0) {
            while ($agentInfo = $db->fetchByAssoc($result)) {
                $agentInfo['agentName'] =  getEntityName('Agents', array($agentInfo['agents_id']))[$agentInfo['agents_id']];
                $participantAgent[$rowNo] = $agentInfo;
                $rowNo++;
            }
        }

        return $participantAgent;
    }
}
