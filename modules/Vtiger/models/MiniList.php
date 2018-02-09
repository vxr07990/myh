<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_MiniList_Model extends Vtiger_Widget_Model
{
    protected $widgetModel;
    protected $extraData;

    protected $listviewController;
    protected $queryGenerator;
    protected $listviewHeaders;
    protected $listviewRecords;
    protected $targetModuleModel;

    public function setWidgetModel($widgetModel)
    {
        $this->widgetModel = $widgetModel;
        $this->extraData = $this->widgetModel->get('data');

        // Decode data if not done already.
        if (is_string($this->extraData)) {
            $this->extraData = Zend_Json::decode(decode_html($this->extraData));
        }
        if ($this->extraData == null) {
            throw new Exception("Invalid data");
        }
    }

    public function getTargetModule()
    {
        return $this->extraData['module'];
    }

    public function getTargetFields()
    {
        $fields = $this->extraData['fields'];
        if (!in_array("id", $fields)) {
            $fields[] = "id";
        }
        return $fields;
    }

    public function getTargetModuleModel()
    {
        if (!$this->targetModuleModel) {
            $this->targetModuleModel = Vtiger_Module_Model::getInstance($this->getTargetModule());
        }
        return $this->targetModuleModel;
    }

    protected function initListViewController()
    {
        if (!$this->listviewController) {
            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            $db = PearDatabase::getInstance();

            $filterid = $this->widgetModel->get('filterid');
            $this->queryGenerator = new QueryGenerator($this->getTargetModule(), $currentUserModel);
            $this->queryGenerator->initForCustomViewById($filterid);
            $this->queryGenerator->setFields($this->getTargetFields());

            if (!$this->listviewController) {
                $this->listviewController = new ListViewController($db, $currentUserModel, $this->queryGenerator);
            }

            $this->listviewHeaders = $this->listviewRecords = null;
        }
    }

    public function getTitle($prefix='')
    {
        $this->initListViewController();

        $db = PearDatabase::getInstance();

        $suffix = '';
        $customviewrs = $db->pquery('SELECT viewname FROM vtiger_customview WHERE cvid=?', array($this->widgetModel->get('filterid')));
        if ($db->num_rows($customviewrs)) {
            $customview = $db->fetch_array($customviewrs);
            $suffix = ' - ' . $customview['viewname'];
        }
        return $prefix . vtranslate($this->getTargetModuleModel()->label, $this->getTargetModule()). $suffix;
    }

    public function getHeaders()
    {
        $this->initListViewController();

        if (!$this->listviewHeaders) {
            $headerFieldModels = array();
            foreach ($this->listviewController->getListViewHeaderFields() as $fieldName => $webserviceField) {
                $fieldObj = Vtiger_Field::getInstance($webserviceField->getFieldId());
                $headerFieldModels[$fieldName] = Vtiger_Field_Model::getInstanceFromFieldObject($fieldObj);
            }
            $this->listviewHeaders = $headerFieldModels;
        }

        return $this->listviewHeaders;
    }

    public function getHeaderCount()
    {
        return count($this->getHeaders());
    }

    public function getRecordLimit()
    {
        return 10;
    }

    public function getRecords()
    {
        $this->initListViewController();

        if (!$this->listviewRecords) {
            $db = PearDatabase::getInstance();

            //@TODO: this needs changed so the generated query actually selects by ownership rules.
            $query = $this->queryGenerator->getQuery();
            $query .= ' ORDER BY vtiger_crmentity.modifiedtime DESC LIMIT 0, ' . $this->getRecordLimit();

            $query = str_replace(" FROM ", ",vtiger_crmentity.crmid as id FROM ", $query);

            $result = $db->pquery($query, array());

            $targetModuleName = $this->getTargetModule();
            $targetModuleFocus= CRMEntity::getInstance($targetModuleName);

            $entries = $this->listviewController->getListViewRecords($targetModuleFocus, $targetModuleName, $result);

            $this->listviewRecords = array();
            $index = 0;
            foreach ($entries as $id => $record) {
                $rawData                    = $db->query_result_rowdata($result, $index++);
                $record['id']               = $id;
                Opportunities_Record_Model::handleAdditionalListViewLogic($rawData, $record);

                $this->listviewRecords[$id] = $this->getTargetModuleModel()->getRecordFromArray($record, $rawData);
            }
        }

        return $this->listviewRecords;
    }

    /**
     * function to search an array of groups for ownership.
     *
     * @param $id
     * @param $userGroups
     *
     * @return bool
     */
    private function checkGroupOwnership($id, $userGroups)
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        /* $rv = false;

        //loop through each group.
        foreach ($userGroups as $groupID) {
            if ($this->checkOwnership($id, $groupID)) {
                $rv = true;
                break;
            }
        }

        return $rv;
    */
    }

    /**
     *
     * function to check the direct/group ownership of a record NO SalesPerson special
     *
     * @param $recordID
     * @param $ownerID
     * @param $userGroup
     *
     * @return bool
     */
    private function checkOwnership($recordID, $ownerID)
    {
        /**
         * look I was going to use what's done in modules/Opportunities/models/ListView.php
         * But I think that is pulling all the records from crmentity and I don't need all
         * the records so instead we'll just check each file
         */
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        /*$rv = false;
        $db = PearDatabase::getInstance();
        $sql = 'SELECT crmid FROM `vtiger_crmentity` WHERE'
             . ' `crmid` = ?'
             . ' AND (`smcreatorid` = ?'
             . ' OR `smownerid` = ?)';

        $crmResults = $db->pquery($sql, array($recordID, $ownerID, $ownerID));
        if ($db->num_rows($crmResults)) {
            $rv = true;
        } else {
            $sql = 'SELECT `agent_id` FROM `vtiger_participating_agents` WHERE'
                   .  ' `crmentity_id` = ? AND'
                   .  ' `agent_id` = ? AND'
                   .  ' `permission` != 3 AND'
                   .  ' `status` = 1';
            $paResults = $db->pquery($sql, array($recordID, $ownerID));
            if ($db->num_rows($paResults)) {
                $rv = true;
            }
        }
        return $rv;*/
    }

    /**
     * function to see if the user is a sales person and then as a sales person can access the record
     *
     * @param $recordId
     * @param $userRole
     * @param $moduleName
     *
     * @return bool
     */
    private function checkSalesPersonOwnership($recordId, $userRole, $moduleName)
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        /*$rv = false;
        $db = PearDatabase::getInstance();

        //salesPerson list parsing
        $sql = "SELECT rolename FROM `vtiger_role` WHERE roleid=?";
        $result = $db->pquery($sql, array($userRole));
        $row = $result->fetchRow();
        $roleName = $row[0];

        $oppRelated = array(
            'Potentials' => array('vtiger_potential', 'potentialid', 'potentialid'),
            'Opportunities' => array('vtiger_potential', 'potentialid', 'potentialid'),
            'Estimates' => array('vtiger_quotes', 'quoteid', 'potentialid'),
            'Calendar' => array('vtiger_seactivityrel', 'activityid', 'crmid'),
            'Documents' => array('vtiger_senotesrel', 'notesid', 'crmid'),
            'Stops' => array('vtiger_stops', 'stopsid', 'stop_opp'),
            'Surveys' => array('vtiger_surveys', 'surveysid', 'potential_id'),
            'Cubesheets' => array('vtiger_cubesheets', 'cubesheetsid', 'potential_id'),
        );
        $orderRelated = array(
            'Orders' => array('vtiger_orders', 'ordersid', 'ordersid'),
            'Estimates' => array('vtiger_quotes', 'quoteid', 'orders_id'),
            'Calendar' => array('vtiger_seactivityrel', 'activityid', 'crmid'),
            'Documents' => array('vtiger_senotesrel', 'notesid', 'crmid'),
            'HelpDesk' => array('vtiger_crmentityrel', 'relcrmid', 'crmid'),
            'Claims' => array('vtiger_claims', 'claimsid', 'claims_order'),
            'Stops' => array('vtiger_stops', 'stopsid', 'stop_order'),
            'OrdersMilestone' => array('vtiger_ordersmilestone', 'ordersmilestoneid', 'ordersid'),
            'OrdersTask' => array('vtiger_orderstask', 'orderstaskid', 'ordersid'),
            'Storage' => array('vtiger_storage', 'storageid', 'storage_orders'),
            'Trips' => array('vtiger_crmentityrel', 'relcrmid', 'crmid'),
        );
        $leadsRelated = array(
            'Leads' => array('vtiger_leaddetails', 'leadid', 'leadid'),
            'Calendar' => array('vtiger_seactivityrel', 'activityid', 'crmid'),
            'Documents' => array('vtiger_senotesrel', 'notesid', 'crmid'),
        );

        if(strpos($roleName, 'Sales Person') !== false &&
           (
               array_key_exists($moduleName, $orderRelated) ||
               array_key_exists($moduleName, $oppRelated) ||
               array_key_exists($moduleName, $leadsRelated)
           )
        ){
            if(array_key_exists($moduleName, $orderRelated)){
                if($moduleName == 'Orders'){
                    $sql = "SELECT sales_person FROM `vtiger_orders` WHERE ordersid=?";
                } else{
                    $sql = "SELECT vtiger_orders.sales_person FROM `vtiger_orders` INNER JOIN ".$orderRelated[$moduleName][0]." ON vtiger_orders.ordersid = ".$orderRelated[$moduleName][0].".".$orderRelated[$moduleName][2]." WHERE ".$orderRelated[$moduleName][0].".".$orderRelated[$moduleName][1]."=?";
                }

                //file_put_contents('logs/devLog.log', "\n ORDER SQL: $sql", FILE_APPEND);
                $result = $db->pquery($sql, array($recordId));
                $row = $result->fetchRow();
                $salesPerson = $row[0];
                //file_put_contents('logs/devLog.log', "\n ORDER SALES PERSON: $salesPerson", FILE_APPEND);
                if($salesPerson == $currentUserId) {
                    $rv = true;
                }
            }

            if(array_key_exists($moduleName, $oppRelated)){
                if($moduleName == 'Potentials' || $moduleName == 'Opportunities'){
                    $sql = "SELECT sales_person FROM `vtiger_potential` WHERE potentialid=?";
                } else{
                    $sql = "SELECT vtiger_potential.sales_person FROM `vtiger_potential` INNER JOIN ".$oppRelated[$moduleName][0]." ON vtiger_potential.potentialid = ".$oppRelated[$moduleName][0].".".$oppRelated[$moduleName][2]." WHERE ".$oppRelated[$moduleName][0].".".$oppRelated[$moduleName][1]."=?";
                }
                //file_put_contents('logs/devLog.log', "\n OPP SQL: $sql", FILE_APPEND);
                $result = $db->pquery($sql, array($recordId));
                $row = $result->fetchRow();
                $salesPerson = $row[0];
                //file_put_contents('logs/devLog.log', "\n OPP SALES PERSON: $salesPerson", FILE_APPEND);
                if($salesPerson == $currentUserId) {
                    $rv = true;
                }
            }
            if(array_key_exists($moduleName, $leadsRelated)){
                if($moduleName == 'Leads'){
                    $sql = "SELECT sales_person FROM `vtiger_leaddetails` WHERE leadid=?";
                } else{
                    $sql = "SELECT vtiger_leaddetails.sales_person FROM `vtiger_leaddetails` INNER JOIN ".$leadsRelated[$moduleName][0]." ON vtiger_leaddetails.leadid = ".$leadsRelated[$moduleName][0].".".$leadsRelated[$moduleName][2]." WHERE ".$leadsRelated[$moduleName][0].".".$leadsRelated[$moduleName][1]."=?";
                }
                //file_put_contents('logs/devLog.log', "\n LEAD SQL: $sql", FILE_APPEND);
                $result = $db->pquery($sql, array($recordId));
                $row = $result->fetchRow();
                $salesPerson = $row[0];
                //file_put_contents('logs/devLog.log', "\n LEAD SALES PERSON: $salesPerson", FILE_APPEND);
                if($salesPerson == $currentUserId) {
                    $rv = true;
                }
            }
            if((array_key_exists($moduleName, $oppRelated) || array_key_exists($moduleName, $orderRelated)) && $moduleName == 'Documents'){
                //extra logic to allow sales persons to see any record with no assigned order or opportunity person
                $sql = "SELECT ".$oppRelated[$moduleName][2]." FROM ".$oppRelated[$moduleName][0]." WHERE ".$oppRelated[$moduleName][1]."=?";
                $result = $db->pquery($sql, array($recordId));
                $row = $result->fetchRow();
                $assignedOpp = $row[0];

                $sql = "SELECT ".$orderRelated[$moduleName][2]." FROM ".$orderRelated[$moduleName][0]." WHERE ".$orderRelated[$moduleName][1]."=?";
                $result = $db->pquery($sql, array($recordId));
                $row = $result->fetchRow();
                $assignedOrder = $row[0];

                //file_put_contents('logs/devLog.log', "\n assopp: $assignedOpp, assord: $assignedOrder", FILE_APPEND);
                if(!$assignedOpp && !$assignedOrder) {
                    $rv = true;
                }
            }
        }
        return $rv;
    */
    }
}
