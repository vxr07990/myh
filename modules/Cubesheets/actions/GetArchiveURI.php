<?php

require_once('libraries/opentok.phar');
use OpenTok\OpenTok;
use Aws\Sdk;
use Carbon\Carbon;

class Cubesheets_GetArchiveURI_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $record = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, 'Cubesheets');
        $sessionId = $recordModel->getTokboxSession();

        $openTok = new OpenTok(getenv('TOKBOX_API_KEY'), getenv('TOKBOX_API_SECRET'));
        $archives = $openTok->listArchives()->getItems();

        foreach ($archives as $archive) {
            if ($archive->__get('sessionId') == $sessionId) {
                $archiveIds[] = ['id'=>$archive->__get('id'), 'createdTime'=>$archive->__get('createdAt')];
            }
        }

        $archivesToShow = '<div style="text-align:center"><h3>The following archives have<br />been found for this record:</h3>';
        $hasArchive = false;

        foreach ($archiveIds as $archiveArray) {
            $archiveId = $archiveArray['id'];
            $key = getenv('TOKBOX_API_KEY').'/'.$archiveId.'/archive.mp4';
            $sharedConfig = [
                'region'  => 'us-east-1',
                'version' => 'latest',
                'http'    => [
                    'verify' => false
                ]
            ];
            $sdk = new Sdk($sharedConfig);
            $client = $sdk->createS3();
            $result = $client->listObjects(['Bucket' => 'live-survey']);
            //Check if archive file exists
            $archiveExists = false;
            foreach ($result['Contents'] as $object) {
                if ($object['Key'] == $key) {
                    $archiveExists = true;
                }
            }
            //        echo "<pre>".print_r($result['Contents'], true)."</pre>";
            $cmd = $client->getCommand('GetObject', [
                'Bucket' => 'live-survey',
                'Key'    => $key
            ]);
            $req = $client->createPresignedRequest($cmd, '+1 minutes');
            $url = (string) $req->getUri();
            if ($archiveExists) {
                $time = substr($archiveArray['createdTime'], 0, -3);
                $archiveTime = Carbon::createFromFormat('U', $time);
                $archivesToShow .= "<br /><button type='button' class='archiveButton' data-url='$url'>".$archiveTime->toDateTimeString()."</button><br />";
            }
            $hasArchive = $hasArchive || $archiveExists;
        }

        $archivesToShow .= "&nbsp;</div>";

        $response = new Vtiger_Response();
        if ($hasArchive) {
            $response->setResult($archivesToShow);
        } else {
            $response->setError('404', "No archives found for this record.");
        }

        $response->emit();
    }
}
