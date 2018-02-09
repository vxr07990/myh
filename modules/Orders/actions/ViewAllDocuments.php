<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/12/2017
 * Time: 12:14 PM
 */

class Orders_ViewAllDocuments_Action extends Vtiger_Action_Controller
{
    protected function getRelatedRecords ($orderId, $moduleName){
        $recordList = [];
        $db = &PearDatabase::getInstance();
        $stmt = 'SELECT DISTINCT vtiger_crmentity.crmid
				FROM (
                    SELECT quoteid FROM vtiger_crmentityrel
                        INNER JOIN vtiger_quotes ON (vtiger_quotes.quoteid=vtiger_crmentityrel.relcrmid)
                        WHERE vtiger_crmentityrel.crmid=?
                    UNION SELECT quoteid FROM vtiger_crmentityrel
                        INNER JOIN vtiger_quotes ON (vtiger_quotes.quoteid=vtiger_crmentityrel.crmid)
                        WHERE vtiger_crmentityrel.relcrmid=?
                        ) tmpt INNER JOIN vtiger_quotes ON (vtiger_quotes.quoteid=tmpt.quoteid) 
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_quotes.quoteid 
                        LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid 
                        LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid WHERE vtiger_crmentity.deleted = 0 AND setype=?';
        $result = $db->pquery($stmt, [$orderId, $orderId, $moduleName]);
        while($row = $result->fetchRow()){
            $recordList[] = $row['crmid'];
        }
        return $recordList;
    }

    public function checkPermission()
    {
        return true;
    }

    public function process(Vtiger_Request $request) {
        $db = &PearDatabase::getInstance();

        $orderID = $request->get('order_id');

        $estRelated = $this->getRelatedRecords($orderID, 'Estimates');
        $actRelated = $this->getRelatedRecords($orderID, 'Actuals');

        $idList = [$orderID];
        $idList = array_merge($idList, $estRelated);
        $idList = array_merge($idList, $actRelated);

        $sql = 'SELECT filename,filetype,name,path,crm2.label,vtiger_attachments.attachmentsid FROM vtiger_notes
				inner join vtiger_senotesrel on vtiger_senotesrel.notesid= vtiger_notes.notesid
				left join vtiger_notescf ON vtiger_notescf.notesid= vtiger_notes.notesid
				inner join vtiger_crmentity on vtiger_crmentity.crmid= vtiger_notes.notesid and vtiger_crmentity.deleted=0
				inner join vtiger_crmentity crm2 on crm2.crmid=vtiger_senotesrel.crmid
				LEFT JOIN vtiger_groups
				ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_seattachmentsrel  on vtiger_seattachmentsrel.crmid =vtiger_notes.notesid
				left join vtiger_attachments on vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
				left join vtiger_users on vtiger_crmentity.smownerid= vtiger_users.id
				where crm2.crmid in ('. implode(',', $idList) .') AND vtiger_notes.filestatus = 1';

        $res = $db->pquery($sql);
        $files = [];
        $allowed = ['application/pdf', 'image/bmp', 'image/jpeg', 'image/png'];
        while($row = $res->fetchRow())
        {
            if(!in_array($row['filetype'], $allowed))
            {
                continue;
            }
            $fpath = $row['path'] . $row['attachmentsid'] . '_' . $row['filename'];
            if(file_exists($fpath)) {
                $files[] = $fpath;
            }
        }
        if(count($files) <= 0)
        {
            $response = new Vtiger_Response();
            $response->setError('Unable to find any valid documents');
            $response->emit();
            return;
        }

        $pdf = new Imagick();
        $pdf->setOption('density', '160');
        $pdf->setOption('trim', true);
        $pdf->setOption('compress', 'zip');
        $pdf->setOption('quality', '80');
        $pdf->setOption('flatten', true);
        $pdf->setOption('sharpen', '0x1.0');

        $pdf->readImages($files);
        $pdf->setImageFormat('pdf');
        $fp = fopen('php://temp', 'r+');
        $pdf->writeImagesFile($fp);
        rewind($fp);
        $out = stream_get_contents($fp);
        fclose($fp);
        header('Content-Type: application/pdf');
        header('Content-Length: '.strlen($out));
        echo $out;
    }
}
