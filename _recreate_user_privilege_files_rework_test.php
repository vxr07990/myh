<?php

error_reporting(\E_ERROR);

require_once 'includes/main/WebUI.php';
file_put_contents('logs/_rupf_master.log', date('Y-m-d H:i:s - ')."Entering _recreate_user_privilege_files_rework_test.php\n");

global $adb;
$userres = $adb->query('SELECT id FROM vtiger_users WHERE deleted = 0');
$userArr = [];
if ($userres && $adb->num_rows($userres)) {
    while ($userrow = $adb->fetch_array($userres)) {
        $userArr[] = $userrow['id'];
    }

    $users = array_chunk($userArr, ceil(count($userArr)/8));

    $helpers = [];

    file_put_contents('logs/_rupf_master.log', date('Y-m-d H:i:s - ').count($users)." helper ".(count($users) == 1 ? "child" : "children")." being initialized\n", FILE_APPEND);
    foreach ($users as $index => $chunkedArr) {
        $cliString = 'php -f _recreate_user_privilege_files_helper.php sequence='.$index.' userList='.json_encode($chunkedArr).' >logs/_rupf_'.$index.'.log';

        $helper = proc_open($cliString, [0=>['pipe', 'r'], 1=>['pipe', 'w'], 2=>['pipe', 'w']], $pipes);
        file_put_contents('logs/_rupf_scratch.log', date('Y-m-d H:i:s - ').print_r($helper, true)."\n", FILE_APPEND);
        file_put_contents('logs/_rupf_scratch.log', date('Y-m-d H:i:s - ').(is_resource($helper) ? 'true' : 'false')."\n", FILE_APPEND);
        $helpers[] = $helper;
    }

    while (count($helpers) > 0) {
        $status = proc_get_status($helpers[0]);
        file_put_contents('logs/_rupf_proclist.log', date('Y-m-d H:i:s - ').print_r($helpers, true));
        file_put_contents('logs/_rupf_proclist.log', date('Y-m-d H:i:s - ').(is_resource($helpers[0]) ? 'true' : 'false')."\n", FILE_APPEND);
        file_put_contents('logs/_rupf_proclist.log', date('Y-m-d H:i:s - ').get_resource_type($helpers[0])."\n", FILE_APPEND);
        file_put_contents('logs/_rupf_proclist.log', date('Y-m-d H:i:s - ').print_r($status, true), FILE_APPEND);
        if (proc_get_status($helpers[0])['running'] === false) {
            proc_close(array_shift($helpers));
        } else {
            sleep('20');
        }
    }
}

file_put_contents('logs/_rupf_master.log', date('Y-m-d H:i:s - ')."Exiting _recreate_user_privilege_files_rework_test.php\n", FILE_APPEND);
