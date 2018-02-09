<?php

require_once('libraries/opentok.phar');
require_once('include/Webservices/Create.php');
use OpenTok\OpenTok;
use OpenTok\OutputMode;

class Cubesheets_ToggleArchive_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        if (!getenv('VIDEO_SURVEY_ARCHIVING')) {
            return;
        }
        $db = PearDatabase::getInstance();
        $numStreams = $request->get('numStreams');
        $recordId   = $request->get('record');
        $sessionId  = $request->get('sessionId');
        $archiveId  = $request->get('archiveId');
        if ($recordId) {
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'Cubesheets');
            $sessionId   = $recordModel->getTokboxSession();
        } elseif (empty($sessionId)) {
            return;
        }

        $archiveOptions = array(
            'name' => '',
            'hasAudio' => true,
            'hasVideo' => true,
            'outputMode' => OutputMode::COMPOSED
        );

        $opentok = new OpenTok(getenv('TOKBOX_API_KEY'), getenv('TOKBOX_API_SECRET'));

        if ($numStreams == 2 && $archiveId == '') {
            $archive = $opentok->startArchive($sessionId, $archiveOptions);

            //Store archive ID in database so that we don't have to use callbacks
            $sql = "INSERT INTO `vtiger_tokbox_archives` (sessionid, archiveid, created_at) VALUES (?,?,?)";
            $db->pquery($sql, [$sessionId, $archive->id, date('Y-m-d H:i:s')]);

            $response = new Vtiger_Response();
            $response->setResult($archive->id);
            $response->emit();
            return;
        } elseif ($numStreams < 2 && $archiveId != '') {
            $opentok->stopArchive($archiveId);

            $current_user = Users_Record_Model::getCurrentUserModel();
            $cubesheetRecord = Vtiger_Record_Model::getInstanceById($request->get('record'));

            $userModule = new Users();
            $createUser = $userModule->retrieveCurrentUserInfoFromFile($current_user->getId());

            $parents = [$cubesheetRecord->getId(), $cubesheetRecord->get('contact_id'), $cubesheetRecord->get('potential_id')];

            $mediaData = [
                'assigned_user_id'  => '19x'.$current_user->getId(),
                'agentid'           => getRecordAgentOwner($cubesheetRecord->get('potential_id')),
                'is_video'          => '1',
                'title'             => 'Video Archive '.date('Y-m-d H:i:s'),
                'archiveid'         => $archiveId,
                'parent_ids'        => $parents
            ];

            try {
                $mediaCreateResponse = vtws_create('Media', $mediaData, $createUser);
            } catch (Exception $e) {
                $response = new Vtiger_Response();
                $response->setError('Error Creating Media Record', $e->getMessage());
                $response->emit();
                return;
            }

            $response = new Vtiger_Response();
            $response->setResult('');
            $response->emit();
        } else {
            $response = new Vtiger_Response();
            $response->setResult($archiveId);
            $response->emit();
        }
    }
}
