<?php

error_reporting(\E_ERROR);

require_once 'includes/main/WebUI.php';
require_once 'include/utils/utils.php';
require_once 'modules/Users/CreateUserPrivilegeFile.php';
require_once 'include/utils/VtlibUtils.php';
require_once 'vendor/autoload.php';

use Stringy\StaticStringy as S;

vtlib_RecreateUserPrivilegeFiles();

$dir = 'user_privileges';
$files = new \FilesystemIterator($dir);

foreach ($files as $file) {
    if (!S::startsWith($file->getFilename(), 'user_privileges_')) {
        continue;
    }

    $id = sscanf($file->getFilename(), 'user_privileges_%d.php')[0];
    createUserSharingPrivilegesfile($id);
}
