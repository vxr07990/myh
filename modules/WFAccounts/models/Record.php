<?php
class WFAccounts_Record_Model extends Vtiger_Record_Model
{
    public function getImageDetails()
    {
        $db = PearDatabase::getInstance();
        $imageDetails = array();
        $recordId = $this->getId();

        if ($recordId) {
            $sql = "SELECT vtiger_attachments.*, vtiger_crmentity.setype FROM vtiger_attachments
						INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
						WHERE vtiger_crmentity.setype = 'WFAccounts Attachment' and vtiger_seattachmentsrel.crmid = ?";

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

    public function checkDuplicate() {
        $db = PearDatabase::getInstance();
        $moduleName=$this->getModule()->getName();
        $focus=CRMEntity::getInstance($moduleName);

        $query = "SELECT 1 FROM vtiger_crmentity
            INNER JOIN {$focus->table_name} ON {$focus->table_name}.{$focus->table_index} = vtiger_crmentity.crmid";
        $query .= " WHERE setype = ? AND deleted = 0";
        $params = array($moduleName);

        $accountName=$this->get('label');
        $query .=" AND name=?";
        array_push($params,$accountName);


        $record = $this->getId();
        if ($record) {
            $query .= " AND crmid != ?";
            array_push($params, $record);
        }
        $result = $db->pquery($query, $params);

        if ($db->num_rows($result)) {
            return true;
        }
        return false;
    }
}
