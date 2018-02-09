<?php

use Aws\Sdk;

class Media_Detail_View extends Vtiger_Detail_View {

    public function showModuleBasicView($request)
    {
        $this->initializeS3Client();
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel          = $this->record->getRecord();
        $detailViewLinkParams = ['MODULE' => $moduleName, 'RECORD' => $recordId];
        $detailViewLinks      = $this->record->getDetailViewLinks($detailViewLinkParams);
        $viewer               = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('MODULE_SUMMARY', $this->showModuleSummaryView($request));
        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('MODULE_NAME', $moduleName);
        $recordStrucure   = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel      = $recordModel->getModule();
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('RELATED_RECORDS', $recordModel->getRelatedRecords());
        if($recordModel->get('is_video') == 0) {
            $viewer->assign('S3_IMAGE_URL', $this->generateImageUrl($recordModel->getId().'_'.$recordModel->get('file_name')));
        } else {
            $viewer->assign('S3_VIDEO_URL', $this->generateVideoUrl($recordModel->get('archiveid')));
        }
        echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
    }

    public function showModuleDetailView(Vtiger_Request $request)
    {
        $this->initializeS3Client();
        global $hiddenBlocksArray;
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel    = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        if (!empty($recordId) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $hiddenBlocks              = $this->loadHiddenBlocksDetailView($moduleName, $recordId);
            $recordModel->hiddenBlocks = $hiddenBlocks;
        }
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel      = $recordModel->getModule();
        $viewer           = $this->getViewer($request);

        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('RELATED_RECORDS', $recordModel->getRelatedRecords());
        if($recordModel->get('is_video') == 0) {
            $viewer->assign('S3_IMAGE_URL', $this->generateImageUrl($recordModel->getId().'_'.$recordModel->get('file_name')));
        } else {
            $viewer->assign('S3_VIDEO_URL', $this->generateVideoUrl($recordModel->get('archiveid')));
        }

        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }

    private function initializeS3Client() {
        $sharedConfig = [
            'region'  => 'us-east-1',
            'version' => 'latest',
            'http'    => [
                'verify' => false
            ]
        ];
        $sdk          = new Sdk($sharedConfig);
        $this->client = $sdk->createS3();
    }

    private function generateImageUrl($fileName) {
        return $this->generateS3Url(getenv('INSTANCE_NAME')."_survey_images/".$fileName);
    }

    private function generateVideoUrl($archiveId) {
        return $this->generateS3Url(getenv('TOKBOX_API_KEY').'/'.$archiveId.'/archive.mp4');
    }

    private function generateS3Url($key) {
        if(!$this->client) {
            $this->initializeS3Client();
        }
        $cmd = $this->client->getCommand('GetObject', [
            'Bucket' => 'live-survey',
            'Key'    => $key
        ]);
        $req = $this->client->createPresignedRequest($cmd, '+1 minutes');
        $url = (string) $req->getUri();

        return $url;
    }
}
