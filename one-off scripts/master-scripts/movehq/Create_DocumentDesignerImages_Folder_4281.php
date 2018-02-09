<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb, $current_user;
$current_user = new Users();
$activeAdmin = $current_user->getActiveAdminUser();
$current_user->retrieve_entity_info($activeAdmin->id, 'Users');

$Vtiger_Utils_Log = true;
$isNew = false;

$folderName = 'DocumentDesignerImages';
$forModule = 'Document Designer';
$folderModel = Documents_Folder_Model::getInstance();
$folderModel->set('foldername', $folderName);
$folderModel->set('description', 'Storage all images of ' . $forModule);

// Exist folder
if ($folderModel->checkDuplicate()) {
    echo "<h2>{$folderName} folder already exists</h2><br>";
} else {
    $folderModel->save();
    echo "<h2>Created {$folderName} folder</h2><br>";
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";