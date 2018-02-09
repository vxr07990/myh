<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

use Carbon\Carbon;

class Vtiger_Edit_View extends Vtiger_Index_View
{
    protected $record = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function preProcess(Vtiger_Request $request, $display = true)
    {
        /*  This doesn't work and is broken. If you find yourself here, please condition it for just your instance  */
        parent::preProcess($request, $display);
        // if this is a relation operation, pull the owner field
        //$isRelationOperation = $request->get('relationOperation');
        //$sourceModule = $request->get('sourceModule');
        //$sourceRecord = $request->get('sourceRecord');
        //if ($isRelationOperation && $sourceModule && $sourceRecord) {
        //    $src = Vtiger_Record_Model::getInstanceById($sourceRecord);
        //    if ($src) {
        //        $owner = $src->get('agentid');
        //        $request->set('agentid', $owner);
        //    }
        //}
    }

    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName       = $request->getModule();
        $record           = $request->get('record');
        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $record);
        if (!$recordPermission) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }

    public function process(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        //old securities
        /*
        $extraPermissions = $this::getExtraPermissions($request);
        if($extraPermissions[0] != true){
            if($record != null){
                throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to write is denied");
            }
        }
        */
        $viewer     = $this->getViewer($request);
        $moduleName = $request->getModule();
        $record     = $request->get('record');
        $this->setViewerForGuestBlocks($moduleName, $record, $viewer);
        if (!empty($record) && $request->get('isDuplicate') == true) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('MODE', '');
            //While Duplicating record, If the related record is deleted then we are removing related record info in record model
            $mandatoryFieldModels = $recordModel->getModule()->getMandatoryFieldModels();
            foreach ($mandatoryFieldModels as $fieldModel) {
                if ($fieldModel->isReferenceField()) {
                    $fieldName = $fieldModel->get('name');
                    if (Vtiger_Util_Helper::checkRecordExistance($recordModel->get($fieldName))) {
                        $recordModel->set($fieldName, '');
                    }
                }
            }
        } elseif (!empty($record)) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
        }
        if (!$this->record) {
            $this->record = $recordModel;
        }
        $moduleModel      = $recordModel->getModule();
        $fieldList        = $moduleModel->getFields();
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);
        foreach ($requestFieldList as $fieldName => $fieldValue) {
            $fieldModel   = $fieldList[$fieldName];
            $specialField = false;
            // We collate date and time part together in the EditView UI handling
            // so a bit of special treatment is required if we come from QuickCreate
            if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'time_start' && !empty($fieldValue)) {
                $specialField = true;
                // Convert the incoming user-picked time to GMT time
                // which will get re-translated based on user-time zone on EditForm
                $fieldValue = DateTimeField::convertToDBTimeZone($fieldValue)->format("H:i");
            }
            if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'date_start' && !empty($fieldValue)) {
                $startTime     = Vtiger_Time_UIType::getTimeValueWithSeconds($requestFieldList['time_start']);
                $startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($fieldValue." ".$startTime);
                list($startDate, $startTime) = explode(' ', $startDateTime);
                $fieldValue = Vtiger_Date_UIType::getDisplayDateValue($startDate);
            }
            if ($fieldModel->isEditable() || $specialField) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }
        $recordStructureInstance      = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        /* VGS Global Business Line Blocks */
        if (!empty($record) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $blocksToHide = $this->loadHiddenBlocksEditView($moduleName, $record);
            $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
        } elseif (empty($record) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $blocksToHide = $this->loadHiddenBlocksEditView($moduleName, '');
            $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
        } else {
            $blocksToHide = [];
            $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
        }
        global $hiddenBlocksArrayField;
        $viewer->assign('BLFIELD', $hiddenBlocksArrayField[$moduleName]);
        /* VGS Global Business Line Blocks */
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('RECORD_MODEL', $recordModel);
        $viewer->assign('MODULE_MODEL', $recordModel->getModule());
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $isRelationOperation = $request->get('relationOperation');
        //if it is relation edit
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if ($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
            //pull contact from the related thing.
            //OT 13379
            $sourceRecord = $request->get('sourceRecord');
            $sourceModule = $request->get('sourceModule');
            if ($moduleName == 'Calendar') {
                if ($sourceRecord) {
                    if ($sourceModule == 'Opportunities' || $sourceModule == 'Potentials') {
                        $sourceRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule);
                        $sourceContactId = $sourceRecordModel->get('contact_id');
                        $existingRelatedContacts[] = array(
                            'name' => Vtiger_Util_Helper::getRecordName($sourceContactId),
                            'id' => $sourceContactId
                        );
                    }
                }
            }
        }
        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
        $viewer->view('EditView.tpl', $moduleName);
    }

    public function loadHiddenBlocksEditView($moduleName, $record, $sourceModule = '')
    {
        global $hiddenBlocksArray, $hiddenBlocksArrayField;
        $blocksToHide = [];
        if (empty($record)) {
            $hiddenBlocks = $hiddenBlocksArray[$moduleName];
            $blocksToHide = [];
            foreach ($hiddenBlocks as $hiddenBlock) {
                $hiddenBlock = explode('::', $hiddenBlock);
                foreach ($hiddenBlock as $value) {
                    $blocksToHide[] = $value;
                }
            }
        } else {
            if (!empty($sourceModule)) {
                $recordModel   = Vtiger_Record_Model::getInstanceById($record, $sourceModule);
                $businessLines = $recordModel->entity->column_fields[$hiddenBlocksArrayField[$sourceModule]];
                $businessLines = array_map('trim', explode('|##|', $businessLines));
            } else {
                $recordModel   = Vtiger_Record_Model::getInstanceById($record, $moduleName);
                $businessLines = $recordModel->entity->column_fields[$hiddenBlocksArrayField[$moduleName]];
                $businessLines = array_map('trim', explode('|##|', $businessLines));
            }
            foreach ($hiddenBlocksArray[$moduleName] as $businessLine => $blocks) {
                if (!in_array($businessLine, $businessLines)) {
                    $blocksToHide = array_merge($blocksToHide, explode('::', $hiddenBlocksArray[$moduleName][$businessLine]));
                }
            }
        }

        return $blocksToHide;
    }

    public function getExtraPermissions($request)
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        /*
        $db                    = PearDatabase::getInstance();
        $userModel             = Users_Record_Model::getCurrentUserModel();
        $currentUserId         = $userModel->getId();
        $isAdmin               = $userModel->isAdminUser();
        $recordId              = $request->get('record');
        $creatorPermissions    = false;
        $memberOfParentVanline = false;
        $sql                   = "SELECT vanlineid, is_parent FROM `vtiger_users2vanline` JOIN `vtiger_vanlinemanager` ON vanlineid=vanlinemanagerid WHERE userid=?";
        $result                = $db->pquery($sql, [$currentUserId]);
        while ($row =& $result->fetchRow()) {
            $validVanlines[] = $row[0];
            if ($row['is_parent'] == 1) {
                //One of the vanlines the user is associated with is the parent. Display all records
                $memberOfParentVanline = true;
            }
        }
        $sql     = "SELECT smownerid FROM `vtiger_crmentity` WHERE crmid = ?";
        $result  = $db->pquery($sql, [$recordId]);
        $row     = $result->fetchRow();
        $ownerId = $row[0];
        if ($isAdmin || $memberOfParentVanline || $currentUserId == $ownerId) {
            $creatorPermissions = true;
        } else {
            $userGroups = [];
            $sql        = "SELECT groupid FROM `vtiger_users2group` WHERE userid=?";
            $result     = $db->pquery($sql, [$currentUserId]);
            $row        = $result->fetchRow();
            while ($row != NULL) {
                $userGroups[] = $row[0];
                $row          = $result->fetchRow();
            }
            $userGroupNames = [];
            foreach ($userGroups as $group) {
                $sql              = "SELECT groupname FROM `vtiger_groups` WHERE groupid=?";
                $result           = $db->pquery($sql, [$group]);
                $row              = $result->fetchRow();
                $userGroupNames[] = $row[0];
            }
            $groupOwned = [];
            foreach ($userGroups as $group) {
                $sql    = "SELECT crmid FROM `vtiger_crmentity` WHERE smownerid=?";
                $result = $db->pquery($sql, [$group]);
                $row    = $result->fetchRow();
                while ($row != NULL) {
                    $groupOwned[] = $row[0];
                    $row          = $result->fetchRow();
                }
            }
            foreach ($groupOwned as $owned) {
                if ($owned == $recordId) {
                    $creatorPermissions = true;
                }
            }
        }
        if ($creatorPermissions == false) {
            $sql    = "SELECT crmid FROM `vtiger_crmentityrel` WHERE relcrmid=? AND module='Orders'";
            $result = $db->pquery($sql, [$recordId]);
            $row    = $result->fetchRow();
            if ($row[0]) {
                $recordId = $row[0];
            } else {
                $sql    = "SELECT crmid FROM `vtiger_crmentityrel` WHERE relcrmid=? AND module='Opportunities'";
                $result = $db->pquery($sql, [$recordId]);
                $row    = $result->fetchRow();
                if ($row[0]) {
                    $recordId = $row[0];
                }
            }
            $sql    = "SELECT orders_id, potentialid FROM `vtiger_quotes` WHERE quoteid=?";
            $result = $db->pquery($sql, [$recordId]);
            $row    = $result->fetchRow();
            if ((!empty($row[0]) && empty($row[1])) || (!empty($row[0]) && !empty($row[1]))) {
                $recordId = $row[0];
            } elseif (empty($row[0]) && !empty($row[1])) {
                $recordId = $row[1];
            }
            $participatingAgentPermissions = 'none';
            $participatingAgents           = [];
            $sql                           = "SELECT setype FROM `vtiger_crmentity` WHERE crmid=?";
            $result                        = $db->pquery($sql, [$recordId]);
            $row                           = $result->fetchRow();
            //file_put_contents('logs/devLog.log', "\n setype: ".$row[0], FILE_APPEND);
            $contactOpps   = [];
            $contactOrders = [];
            $accountOpps   = [];
            $accountOrders = [];
            if ($row[0] == 'Contacts') {
                $sql2    = "SELECT potentialid FROM `vtiger_potential` WHERE contact_id=?";
                $result2 = $db->pquery($sql2, [$recordId]);
                $row2    = $result2->fetchRow();
                while ($row2 != NULL) {
                    $contactOpps[] = $row2[0];
                    $row2          = $result2->fetchRow();
                }
                foreach ($contactOpps as $contactOpp) {
                    $sql2    = "SELECT agentid, permissions FROM `vtiger_potential_participatingagents` WHERE opportunityid=? AND permissions!=3";
                    $result2 = $db->pquery($sql2, [$contactOpp]);
                    $row2    = $result2->fetchRow();
                    while ($row2 != NULL) {
                        $participatingAgents[] = [$row2[0], $row2[1]];
                        $row2                  = $result2->fetchRow();
                    }
                }
                $sql2    = "SELECT ordersid FROM `vtiger_orders` WHERE orders_contacts=?";
                $result2 = $db->pquery($sql2, [$recordId]);
                $row2    = $result2->fetchRow();
                while ($row2 != NULL) {
                    $contactOrders[] = $row2[0];
                    $row2            = $result2->fetchRow();
                }
                foreach ($contactOrders as $contactOrder) {
                    $sql2    = "SELECT agentid, permissions FROM `vtiger_orders_participatingagents` WHERE ordersid=? AND permissions!=3";
                    $result2 = $db->pquery($sql2, [$contactOrder]);
                    $row2    = $result2->fetchRow();
                    while ($row2 != NULL) {
                        $participatingAgents[] = [$row2[0], $row2[1]];
                        $row2                  = $result->fetchRow();
                    }
                }
            } elseif ($row[0] == 'Accounts') {
                $sql2    = "SELECT potentialid FROM `vtiger_potential` WHERE related_to=?";
                $result2 = $db->pquery($sql2, [$recordId]);
                $row2    = $result2->fetchRow();
                while ($row2 != NULL) {
                    $accountOpps[] = $row2[0];
                    $row2          = $result2->fetchRow();
                }
                foreach ($accountOpps as $accountOpp) {
                    $sql2    = "SELECT agentid, permissions FROM `vtiger_potential_participatingagents` WHERE opportunityid=? AND permissions!=3";
                    $result2 = $db->pquery($sql2, [$accountOpp]);
                    $row2    = $result->fetchRow();
                    while ($row2 != NULL) {
                        $participatingAgents[] = [$row2[0], $row2[1]];
                        $row2                  = $result2->fetchRow();
                    }
                }
                $sql2    = "SELECT ordersid FROM `vtiger_orders` WHERE orders_account=?";
                $result2 = $db->pquery($sql2, [$recordId]);
                $row2    = $result2->fetchRow();
                while ($row2 != NULL) {
                    $accountOrders[] = $row2[0];
                    $row2            = $result2->fetchRow();
                }
                foreach ($accountOrders as $accountOrder) {
                    $sql2    = "SELECT agentid, permissions FROM `vtiger_orders_participatingagents` WHERE ordersid=? AND permissions!=3";
                    $result2 = $db->pquery($sql2, [$accountOrder]);
                    $row2    = $result->fetchRow();
                    while ($row2 != NULL) {
                        $participatingAgents[] = [$row2[0], $row2[1]];
                        $row2                  = $result - 2 > fetchRow();
                    }
                }
            }
            $participatingAgentNames = [];
            $sql                     = "SELECT agentid, permissions FROM `vtiger_potential_participatingagents` WHERE opportunityid=? AND permissions!=3";
            $result                  = $db->pquery($sql, [$recordId]);
            $row                     = $result->fetchRow();
            while ($row != NULL) {
                $participatingAgents[] = [$row[0], $row[1]];
                $row                   = $result->fetchRow();
            }
            foreach ($participatingAgents as $participatingAgent) {
                $sql                       = "SELECT agentname FROM `vtiger_agents` WHERE agentsid=?";
                $result                    = $db->pquery($sql, [$participatingAgent[0]]);
                $row                       = $result->fetchRow();
                $participatingAgentNames[] = [$row[0], $participatingAgent[1]];
            }
            foreach ($participatingAgentNames as $participatingAgentName) {
                foreach ($userGroupNames as $groupName) {
                    if ($groupName == $participatingAgentName[0]) {
                        if ($participatingAgentName[1] == 0) {
                            $creatorPermissions            = true;
                            $participatingAgentPermissions = 'edit';
                        } elseif ($participatingAgentName[1] == 1 && $creatorPermissions == false && $participatingAgentPermissions != 'edit') {
                            $participatingAgentPermissions = 'full';
                        } elseif ($participatingAgentName[1] == 2 && $creatorPermissions == false && $participatingAgentPermissions != 'full') {
                            $participatingAgentPermissions = 'no_rates';
                        }
                    }
                }
            }
        }
        //sales person securities piece
        $moduleName   = $request->getModule();
        $userRole     = $userModel->getRole();
        $sql          = "SELECT rolename FROM `vtiger_role` WHERE roleid=?";
        $result       = $db->pquery($sql, [$userRole]);
        $row          = $result->fetchRow();
        $roleName     = $row[0];
        $oppRelated   = [
            'Potentials'    => ['vtiger_potential', 'potentialid', 'potentialid'],
            'Opportunities' => ['vtiger_potential', 'potentialid', 'potentialid'],
            'Estimates'     => ['vtiger_quotes', 'quoteid', 'potentialid'],
            'Calendar'      => ['vtiger_seactivityrel', 'activityid', 'crmid'],
            'Documents'     => ['vtiger_senotesrel', 'notesid', 'crmid'],
            'Stops'         => ['vtiger_stops', 'stopsid', 'stop_opp'],
            'Surveys'       => ['vtiger_surveys', 'surveysid', 'potential_id'],
            'Cubesheets'    => ['vtiger_cubesheets', 'cubesheetsid', 'potential_id'],
        ];
        $orderRelated = [
            'Orders'          => ['vtiger_orders', 'ordersid', 'ordersid'],
            'Estimates'       => ['vtiger_quotes', 'quoteid', 'orders_id'],
            'Calendar'        => ['vtiger_seactivityrel', 'activityid', 'crmid'],
            'Documents'       => ['vtiger_senotesrel', 'notesid', 'crmid'],
            'HelpDesk'        => ['vtiger_crmentityrel', 'relcrmid', 'crmid'],
            'Claims'          => ['vtiger_claims', 'claimsid', 'claims_order'],
            'Stops'           => ['vtiger_stops', 'stopsid', 'stop_order'],
            'OrdersMilestone' => ['vtiger_ordersmilestone', 'ordersmilestoneid', 'ordersid'],
            'OrdersTask'      => ['vtiger_orderstask', 'orderstaskid', 'ordersid'],
            'Storage'         => ['vtiger_storage', 'storageid', 'storage_orders'],
            'Trips'           => ['vtiger_crmentityrel', 'relcrmid', 'crmid'],
        ];
        $leadsRelated = [
            'Leads'     => ['vtiger_leaddetails', 'leadid', 'leadid'],
            'Calendar'  => ['vtiger_seactivityrel', 'activityid', 'crmid'],
            'Documents' => ['vtiger_senotesrel', 'notesid', 'crmid'],
        ];
        if (strpos($roleName, 'Sales Person') !== false &&
            (array_key_exists($moduleName, $orderRelated) || array_key_exists($moduleName, $oppRelated) || array_key_exists($moduleName, $leadsRelated))
        ) {
            $creatorPermissions            = false;
            $participatingAgentPermissions = 'none';
            //salesPerson for modules related to orders
            if (array_key_exists($moduleName, $orderRelated)) {
                if ($moduleName == 'Orders') {
                    $sql = "SELECT sales_person FROM `vtiger_orders` WHERE ordersid=?";
                } else {
                    $sql =
                        "SELECT vtiger_orders.sales_person FROM `vtiger_orders` INNER JOIN ".
                        $orderRelated[$moduleName][0].
                        " ON vtiger_orders.ordersid = ".
                        $orderRelated[$moduleName][0].
                        ".".
                        $orderRelated[$moduleName][2].
                        " WHERE ".
                        $orderRelated[$moduleName][0].
                        ".".
                        $orderRelated[$moduleName][1].
                        "=?";
                }
                //file_put_contents('logs/devLog.log', "\n ORDER SQL: $sql", FILE_APPEND);
                $result      = $db->pquery($sql, [$recordId]);
                $row         = $result->fetchRow();
                $salesPerson = $row[0];
                //file_put_contents('logs/devLog.log', "\n ORDER SALES PERSON: $salesPerson", FILE_APPEND);
                if ($salesPerson == $currentUserId) {
                    $creatorPermissions = true;
                }
            }
            //salesPerson for modules related to opps
            if (array_key_exists($moduleName, $oppRelated)) {
                if ($moduleName == 'Potentials' || $moduleName == 'Opportunities') {
                    $sql = "SELECT sales_person FROM `vtiger_potential` WHERE potentialid=?";
                } else {
                    $sql =
                        "SELECT vtiger_potential.sales_person FROM `vtiger_potential` INNER JOIN ".
                        $oppRelated[$moduleName][0].
                        " ON vtiger_potential.potentialid = ".
                        $oppRelated[$moduleName][0].
                        ".".
                        $oppRelated[$moduleName][2].
                        " WHERE ".
                        $oppRelated[$moduleName][0].
                        ".".
                        $oppRelated[$moduleName][1].
                        "=?";
                }
                //file_put_contents('logs/devLog.log', "\n OPP SQL: $sql", FILE_APPEND);
                $result      = $db->pquery($sql, [$recordId]);
                $row         = $result->fetchRow();
                $salesPerson = $row[0];
                //file_put_contents('logs/devLog.log', "\n OPP SALES PERSON: $salesPerson", FILE_APPEND);
                if ($salesPerson == $currentUserId) {
                    $creatorPermissions = true;
                }
            }
            if (array_key_exists($moduleName, $leadsRelated)) {
                if ($moduleName == 'Leads') {
                    $sql = "SELECT sales_person FROM `vtiger_leaddetails` WHERE leadid=?";
                } else {
                    $sql =
                        "SELECT vtiger_leaddetails.sales_person FROM `vtiger_leaddetails` INNER JOIN ".
                        $leadsRelated[$moduleName][0].
                        " ON vtiger_leaddetails.leadid = ".
                        $leadsRelated[$moduleName][0].
                        ".".
                        $leadsRelated[$moduleName][2].
                        " WHERE ".
                        $leadsRelated[$moduleName][0].
                        ".".
                        $leadsRelated[$moduleName][1].
                        "=?";
                }
                //file_put_contents('logs/devLog.log', "\n LEAD SQL: $sql", FILE_APPEND);
                $result      = $db->pquery($sql, [$recordId]);
                $row         = $result->fetchRow();
                $salesPerson = $row[0];
                //file_put_contents('logs/devLog.log', "\n LEAD SALES PERSON: $salesPerson", FILE_APPEND);
                if ($salesPerson == $currentUserId) {
                    $creatorPermissions = true;
                }
            }
            if ((array_key_exists($moduleName, $oppRelated) || array_key_exists($moduleName, $orderRelated)) && $moduleName == 'Documents') {
                //extra logic to allow sales persons to see any record with no assigned order or opportunity person
                $sql           = "SELECT ".$oppRelated[$moduleName][2]." FROM ".$oppRelated[$moduleName][0]." WHERE ".$oppRelated[$moduleName][1]."=?";
                $result        = $db->pquery($sql, [$recordId]);
                $row           = $result->fetchRow();
                $assignedOpp   = $row[0];
                $sql           = "SELECT ".$orderRelated[$moduleName][2]." FROM ".$orderRelated[$moduleName][0]." WHERE ".$orderRelated[$moduleName][1]."=?";
                $result        = $db->pquery($sql, [$recordId]);
                $row           = $result->fetchRow();
                $assignedOrder = $row[0];
                //file_put_contents('logs/devLog.log', "\n assopp: $assignedOpp, assord: $assignedOrder", FILE_APPEND);
                if (!$assignedOpp && !$assignedOrder) {
                    $creatorPermissions = true;
                }
            }
            //file_put_contents('logs/devLog.log', "\n IN OPP IF STATEMENT", FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n SALES PERSON: $salesPerson", FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n CURRENT USER: $currentUserId", FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n RECORDID: $recordId", FILE_APPEND);
        } //end sales person securities piece
        return [$creatorPermissions, $participatingAgentPermissions];
        */
    }
}
