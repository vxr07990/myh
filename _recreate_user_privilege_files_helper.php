<?php

error_reporting(\E_ERROR);

require_once 'includes/main/WebUI.php';
require_once 'include/utils/utils.php';
require_once 'modules/Users/CreateUserPrivilegeFile.php';
require_once 'include/utils/VtlibUtils.php';
require_once 'vendor/autoload.php';

parse_str(implode('&', array_slice($argv, 1)), $_GET);

file_put_contents('logs/_rupf_master.log', date('Y-m-d H:i:s - ')."Entering _recreate_user_privilege_files_helper.php : ".$_GET['sequence']."\n", FILE_APPEND);

$userArr = json_decode($_GET['userList']);

foreach ($userArr as $userId) {
    createUserPrivilegesfile($userId);
    createUserSharingPrivilegesfile($userId);
}

file_put_contents('logs/_rupf_master.log', date('Y-m-d H:i:s - ')."Exiting _recreate_user_privilege_files_helper.php : ".$_GET['sequence']."\n", FILE_APPEND);
