<?php
class Employees_Record_Model extends Vtiger_Record_Model
{
    /**
     * Function to get Image Details
     * @return <array> Image Details List
     */
    public function getImageDetails()
    {
        $db = PearDatabase::getInstance();
        $imageDetails = array();
        $recordId = $this->getId();

        if ($recordId) {
            $sql = "SELECT vtiger_attachments.*, vtiger_crmentity.setype FROM vtiger_attachments
						INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
						WHERE vtiger_crmentity.setype = 'Employees Attachment' and vtiger_seattachmentsrel.crmid = ?";

            $result = $db->pquery($sql, array($recordId));

            $imageId = $db->query_result($result, 0, 'attachmentsid');
            $imagePath = $db->query_result($result, 0, 'path');
            $imageName = $db->query_result($result, 0, 'name');

            //decode_html - added to handle UTF-8 characters in file names
            $imageOriginalName = decode_html($imageName);

            if (!empty($imageName)) {
                $imageDetails[] = array(
                        'id' => $imageId,
                        'orgname' => $imageOriginalName,
                        'path' => $imagePath.$imageId,
                        'name' => $imageName
                );
            }
        }
        return $imageDetails;
    }

    public function updateRelatedTrips()
    {
        $db = PearDatabase::getInstance();
        $recordId = $this->getId();

        $params = array(
                $this->get('employee_lastname'),
                $this->get('name'),
                $this->get('employee_no'),
                $this->get('employee_mphone'),
                $this->get('employee_email'),
                $recordId,

            );

        $db->pquery("UPDATE vtiger_trips, vtiger_crmentity SET trips_driverlastname=?, trips_driverfirstname=?, trips_driverno=?, trips_drivercellphone=?, 
                                trips_driversemail=? 
                                WHERE vtiger_trips.tripsid = vtiger_crmentity.crmid 
                                AND deleted=0 
                                AND driver_id=?", $params);
    }
    public static function getSearchResult($searchKey, $module = false, $searchMoveRole=false)
    {
        if (getenv('INSTANCE_NAME') == 'graebel') {
            $searchMoveRole = false;
        }
        $db = PearDatabase::getInstance();

        if ($searchMoveRole != false) {
            $query  = 'SELECT label, crmid, setype, createdtime 
						FROM vtiger_crmentity 
						INNER JOIN vtiger_employeescf ON vtiger_employeescf.employeesid = vtiger_crmentity.crmid
						WHERE vtiger_crmentity.label LIKE ? AND vtiger_crmentity.deleted = 0 AND (vtiger_employeescf.employee_primaryrole =? OR CONCAT(",",vtiger_employeescf.employee_secondaryrole,",") LIKE ?)';
            $params = ["%$searchKey%",$searchMoveRole,"%,$searchMoveRole,%"];
        } else {
            $query  = 'SELECT label, crmid, setype, createdtime FROM vtiger_crmentity WHERE label LIKE ? AND vtiger_crmentity.deleted = 0';
            $params = ["%$searchKey%"];
        }
        if ($module !== false) {
            $query .= ' AND setype = ?';
            $params[] = $module;
        }

        if(!getenv('DISABLE_REFERENCE_FIELD_LIMIT_BY_OWNER')) {
            if ($_REQUEST['agentId']){
                $query .= " AND vtiger_crmentity.agentid=?";
                $params[]=$_REQUEST['agentId'];
            }
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

    public function getLinkedUser()
    {
        global $adb;
        $recordId = $this->getId();
        $rsUserId = $adb->pquery("SELECT userid FROM vtiger_employees WHERE employeesid=?", array($recordId));
        $userid = $adb->query_result($rsUserId, 0, 'userid');
        if ($userid !='') {
            $userRecordModel = Vtiger_Record_Model::getInstanceById($userid, 'Users');
        } else {
            $userRecordModel = Vtiger_Record_Model::getCleanInstance('Users');
        }

        return $userRecordModel;
    }
}
