<?php
class WFImages_Record_Model extends Vtiger_Record_Model {

    public function getImageDetails() {
        $db           = PearDatabase::getInstance();
        $imageDetails = [];
        $recordId     = $this->getId();
        if ($recordId) {
            $sql = "SELECT vtiger_attachments.*, vtiger_crmentity.setype FROM vtiger_attachments
						INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
						WHERE vtiger_crmentity.setype = 'WFImages Attachment' and vtiger_seattachmentsrel.crmid = ?";
            $result = $db->pquery($sql, [$recordId]);
            $imageId   = $db->query_result($result, 0, 'attachmentsid');
            $imagePath = $db->query_result($result, 0, 'path');
            $imageName = $db->query_result($result, 0, 'name');
            //decode_html - added to handle UTF-8 characters in file names
            $imageOriginalName = decode_html($imageName);
            if (!empty($imageName)) {
                $imageDetails[] = [
                    'id'      => $imageId,
                    'orgname' => $imageOriginalName,
                    'path'    => $imagePath.$imageId,
                    'name'    => $imageName
                ];
            }
        }

        return $imageDetails;
    }
}
