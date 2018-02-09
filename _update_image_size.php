<?php
include_once('vtlib/Vtiger/Menu.php');
include_once 'includes/main/WebUI.php';

$width = 300;
$height = 200;

echo "Starting resizing images of users and agentmanagers at $width x $height resolution". '<br />' . PHP_EOL;

$db = PearDatabase::getInstance();

$result = $db->query('SELECT CONCAT(`profile_image_path`, `profile_image_id`, "_", `profile_image_name`) AS image FROM `vtiger_users` WHERE `profile_image_name` IS NOT NULL');

while ($row =& $result->fetchRow()) {
    echo "Resizing: " . $row['image']. '<br />' . PHP_EOL;

    if(file_exists(realpath($row['image']))){
        $tempImage = new \Imagick(realpath($row['image']));
        
        $tempImage->resizeImage($width, $height, Imagick::FILTER_UNDEFINED, 0.9, true);
        
        $tempImage->writeImage($row['image']);

        echo "File " . $row['image']. " has been resized.". '<br />' . PHP_EOL;
    }else{
        echo "File " . $row['image']. " does not exists.. skipping.". '<br />' . PHP_EOL;
    }
}

$result = $db->query('SELECT CONCAT(`path`, `vtiger_attachments`.`attachmentsid`, "_", `name`) AS image FROM `vtiger_attachments` JOIN `vtiger_salesmanattachmentsrel` WHERE vtiger_attachments.attachmentsid = vtiger_salesmanattachmentsrel.attachmentsid');

while ($row =& $result->fetchRow()) {
    echo "Resizing: " . $row['image']. '<br />' . PHP_EOL;

    if(file_exists(realpath($row['image']))){
        $tempImage = new \Imagick(realpath($row['image']));
        
        $tempImage->resizeImage($width, $height, Imagick::FILTER_UNDEFINED, 0.9, true);
        
        $tempImage->writeImage($row['image']);

        echo "File " . $row['image']. " has been resized.". '<br />' . PHP_EOL;
    }else{
        echo "File " . $row['image']. " does not exists.. skipping.". '<br />' . PHP_EOL;
    }
}

echo "Image resizing complete";
