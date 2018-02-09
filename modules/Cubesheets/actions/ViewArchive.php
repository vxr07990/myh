<?php

use Aws\Sdk;

class Cubesheets_ViewArchive_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $archiveId = $request->get('archive');
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

        header('Location: '.$url);
    }
}
