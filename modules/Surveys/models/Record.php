<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Quotes Record Model Class
 */
class Surveys_Record_Model extends Inventory_Record_Model
{
    public function getCreateInvoiceUrl()
    {
        $invoiceModuleModel = Vtiger_Module_Model::getInstance('Invoice');

        return "index.php?module=".$invoiceModuleModel->getName()."&view=".$invoiceModuleModel->getEditViewName()."&quote_id=".$this->getId();
    }

    public function getCreateSalesOrderUrl()
    {
        $salesOrderModuleModel = Vtiger_Module_Model::getInstance('SalesOrder');

        return "index.php?module=".$salesOrderModuleModel->getName()."&view=".$salesOrderModuleModel->getEditViewName()."&quote_id=".$this->getId();
    }

    /**
     * Function to get this record and details as PDF
     */
    public function getPDF()
    {
        $recordId = $this->getId();
        $moduleName = $this->getModuleName();

        $controller = new Vtiger_QuotePDFController($moduleName);
        $controller->loadRecord($recordId);

        $fileName = $moduleName.'_'.getModuleSequenceNumber($moduleName, $recordId);
        $controller->Output($fileName.'.pdf', 'D');
    }

    public function setParentRecordData(Vtiger_Record_Model $parentRecordModel)
    {
        $userModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $moduleName = $parentRecordModel->getModuleName();

        $data = array();
        $fieldMappingList = $parentRecordModel->getSurveyMappingFields();

        foreach ($fieldMappingList as $fieldMapping) {
            $parentField = $fieldMapping['parentField'];
            $inventoryField = $fieldMapping['inventoryField'];
            $fieldModel = Vtiger_Field_Model::getInstance($parentField,  Vtiger_Module_Model::getInstance($moduleName));
            if ($fieldModel->getPermissions()) {
                $data[$inventoryField] = $parentRecordModel->get($parentField);
            } else {
                $data[$inventoryField] = $fieldMapping['defaultValue'];
            }
        }
        if ($data['account_id'] != '0') {
            $db = PearDatabase::getInstance();
            $sql = "SELECT bill_street, bill_city, bill_state, bill_code, bill_country, bill_pobox FROM `vtiger_accountbillads` WHERE accountaddressid=?";
            $params[] = $data['account_id'];

            $result = $db->pquery($sql, $params);
            unset($params);
            $row = $result->fetchRow();

            $data['bill_street'] = $row[0];
            $data['bill_city'] = $row[1];
            $data['bill_state'] = $row[2];
            $data['bill_code'] = $row[3];
            $data['bill_country'] = $row[4];
            $data['bill_pobox'] = $row[5];
        } else {
            $db = PearDatabase::getInstance();
            $sql = "SELECT destination_address1, destination_city, destination_state, destination_country, destination_zip FROM `vtiger_potential` JOIN `vtiger_potentialscf` ON `vtiger_potential`.potentialid=`vtiger_potentialscf`.potentialid WHERE `vtiger_potential`.potentialid=?";
            $params[] = $parentRecordModel->getId();

            $result = $db->pquery($sql, $params);
            unset($params);
            $row = $result->fetchRow();

            $data['bill_street'] = $row[0];
            $data['bill_city'] = $row[1];
            $data['bill_state'] = $row[2];
            $data['bill_code'] = $row[3];
            $data['bill_country'] = $row[4];
            $data['country'] = $row[3];
        }
        if ($data['business_line'] == 'Commercial Move') {
            $data['comm_res'] = 'Commercial';
        }
        return $this->setData($data);
    }
    static function getEmployeesUsersId(){
        global $adb;
        $data = [];
        if (!Vtiger_Utils::CheckTable('vtiger_employeeroles')) {
            return $data;
        }
        $queryEmployeeRole = "SELECT employeerolesid FROM vtiger_employeeroles
                                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_employeeroles.employeerolesid
                                WHERE vtiger_employeeroles.emprole_class = 'Surveyor' AND deleted = 0";
        $rsEmployeeRole = $adb->pquery($queryEmployeeRole,[]);
        if ($rsEmployeeRole) {
            while ($rowR = $adb->fetchByAssoc($rsEmployeeRole)) {
                $employeerolesid = $rowR['employeerolesid'];
                $sql             = "SELECT vtiger_employees.userid FROM `vtiger_employees`
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_employees.employeesid
                    INNER JOIN vtiger_employeescf ON vtiger_employees.employeesid = vtiger_employeescf.employeesid
                    WHERE userid >0 and vtiger_crmentity.deleted = 0
                            AND (vtiger_employeescf.employee_primaryrole = ? OR CONCAT(',',vtiger_employeescf.employee_secondaryrole,',') LIKE ? )";
                $rs              = $adb->pquery($sql, [$employeerolesid, "%,".$employeerolesid.",%"]);
                while ($row = $adb->fetchByAssoc($rs)) {
                    $data[] = $row['userid'];
                }
            }
        }
        return $data;
    }

    public static function getSearchResult($searchKey, $module = false, $searchOpportunitie=false)
    {
        $db = PearDatabase::getInstance();

        if ($searchOpportunitie != false) {
            $query  = 'SELECT label, crmid, setype, createdtime
						FROM vtiger_crmentity
						INNER JOIN vtiger_surveys ON vtiger_surveys.surveysid = vtiger_crmentity.crmid
						WHERE label LIKE ? AND vtiger_crmentity.deleted = 0 AND vtiger_surveys.potential_id = ?';
            $params = ["%$searchKey%",$searchOpportunitie];

        } else {
            $query  = 'SELECT label, crmid, setype, createdtime FROM vtiger_crmentity WHERE label LIKE ? AND vtiger_crmentity.deleted = 0';
            $params = ["%$searchKey%"];
        }
        if ($module !== false) {
            $query .= ' AND setype = ?';
            $params[] = $module;
        }

        //Remove the ordering for now to improve the speed
        //$query .= ' ORDER BY createdtime DESC';

        $result   = $db->pquery($query, $params);
        $noOfRows = $db->num_rows($result);
        $moduleModels = $matchingRecords = $leadIdsList = [];
        for ($i = 0, $recordsCount = 0; $i < $noOfRows && $recordsCount < 100; ++$i) {
            $row = $db->query_result_rowdata($result, $i);
            if ($row['setype'] === 'Leads') {
                $convertedInfo = Leads_Module_Model::getConvertedInfo($row['crmid']);
                if ($convertedInfo[$row['crmid']]) {
                    continue;
                }
            }
            if (Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['crmid'])) {
                $row['id']  = $row['crmid'];
                $moduleName = $row['setype'];
                if (!array_key_exists($moduleName, $moduleModels)) {
                    $moduleModels[$moduleName] = Vtiger_Module_Model::getInstance($moduleName);
                }
                $moduleModel                              = $moduleModels[$moduleName];
                $modelClassName                           = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
                $recordInstance                           = new $modelClassName();
                $matchingRecords[$moduleName][$row['id']] = $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
                $recordsCount++;
            }
        }

        return $matchingRecords;
    }

}
