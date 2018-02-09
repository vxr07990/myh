<?php

use Aws\Sdk;
use Aws\S3\Exception;
include_once 'include/Webservices/Create.php';

class Cubesheets_SaveImageAndGenerateThumbnail_Action extends Vtiger_BasicAjax_Action {

    public function process(Vtiger_Request $request) {
        $current_user = Users_Record_Model::getCurrentUserModel();
        $cubesheetRecord = Vtiger_Record_Model::getInstanceById($request->get('record'));
        $imageData = $request->get('imageData');

        if($imageData == null) {
            $response = new Vtiger_Response();
            $response->setError('No image data provided');
            $response->emit();
            return;
        }

        $title = 'ImageCapture_'.date('Ymd_His');
        $fileName = $title.'.png';
        $thumbFileName = $title.'_thumb.png';

        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));

        mkdir('storage/videoSurvey', 755);

        file_put_contents('storage/videoSurvey/'.$fileName, $data);

        $userModule = new Users();
        $createUser = $userModule->retrieveCurrentUserInfoFromFile($current_user->getId());

        $parents = [$cubesheetRecord->getId(), $cubesheetRecord->get('contact_id'), $cubesheetRecord->get('potential_id')];

        $mediaData = [
            'assigned_user_id'  => '19x'.$current_user->getId(),
            'agentid'           => getRecordAgentOwner($cubesheetRecord->get('potential_id')),
            'is_video'          => '0',
            'title'             => $title,
            'file_name'         => $fileName,
            'thumb_file_name'   => $thumbFileName,
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

        $mediaId = substr(strstr($mediaCreateResponse['id'], 'x'), 1);

        $sharedConfig = [
            'region'  => 'us-east-1',
            'version' => 'latest',
            'http'    => [
                'verify' => false
            ]
        ];
        $sdk = new Sdk($sharedConfig);
        $client = $sdk->createS3();
        $bucket = 'live-survey';
        $key = getenv('INSTANCE_NAME')."_survey_images/";

        try {
            $client->putObject(array(
                                   'Bucket' => $bucket,
                                   'Key' => $key.$mediaId.'_'.$fileName,
                                   'Body' => $data
                               ));
        } catch (Exception $e) {
            //Failed to create image on S3. Log error.
            file_put_contents('logs/s3Errors.log', date('Y-m-d H:i:s - ').print_r($e, true)."\n", FILE_APPEND);
            $response = new Vtiger_Response();
            $response->setError('Error Saving Image', $e->getMessage());
            $response->emit();
            return;
        }

        $thumbData = $this->createThumbnail('storage/videoSurvey/'.$fileName, 60, 80);

        try {
            $client->putObject(array(
                                   'Bucket' => $bucket,
                                   'Key' => $key.$mediaId.'_'.$thumbFileName,
                                   'Body' => $thumbData
                               ));
        } catch (Exception $e) {
            //Failed to create image on S3. Log error.
            file_put_contents('logs/s3Errors.log', date('Y-m-d H:i:s - ').print_r($e, true)."\n", FILE_APPEND);
            $response = new Vtiger_Response();
            $response->setError('Error Saving Thumbnail Image', $e->getMessage());
            $response->emit();
        }

        unlink('storage/videoSurvey/'.$fileName);
    }

    private function createThumbnail($filepath, $thumbnail_width, $thumbnail_height, $background=false) {
        list($original_width, $original_height, $original_type) = getimagesize($filepath);
        if ($original_width > $original_height) {
            $new_width = $thumbnail_width;
            $new_height = intval($original_height * $new_width / $original_width);
        } else {
            $new_height = $thumbnail_height;
            $new_width = intval($original_width * $new_height / $original_height);
        }
        $dest_x = intval(($thumbnail_width - $new_width) / 2);
        $dest_y = intval(($thumbnail_height - $new_height) / 2);

        if ($original_type === 1) {
            $imgt = "ImageGIF";
            $imgcreatefrom = "ImageCreateFromGIF";
        } else if ($original_type === 2) {
            $imgt = "ImageJPEG";
            $imgcreatefrom = "ImageCreateFromJPEG";
        } else if ($original_type === 3) {
            $imgt = "ImagePNG";
            $imgcreatefrom = "ImageCreateFromPNG";
        } else {
            return false;
        }

        $old_image = $imgcreatefrom($filepath);
        $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height); // creates new image, but with a black background

        // figuring out the color for the background
        if(is_array($background) && count($background) === 3) {
            list($red, $green, $blue) = $background;
            $color = imagecolorallocate($new_image, $red, $green, $blue);
            imagefill($new_image, 0, 0, $color);
            // apply transparent background only if is a png image
        } else if($background === 'transparent' && $original_type === 3) {
            imagesavealpha($new_image, TRUE);
            $color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
            imagefill($new_image, 0, 0, $color);
        }

        imagecopyresampled($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
        ob_start();
        $imgt($new_image);
        $image_data = ob_get_contents();
        ob_end_clean();
        return $image_data;
    }
}
