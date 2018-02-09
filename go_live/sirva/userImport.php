<?php

require_once('includes/main/WebUI.php');
require_once('include/Webservices/Create.php');
require_once('include/Webservices/DescribeObject.php');
require_once('modules/Users/Users.php');
require_once('modules/Users/views/Edit.php');

function userImport()
{
    $filePath = 'go_live/sirva/SirvaUsers.csv';
    echo "Start User Import <br>";
    $db = PearDatabase::getInstance();

    $users = [];

    $rosterList = fopen($filePath, 'r');

    $headers = fgetcsv($rosterList);

    $csv = fgetcsv($rosterList);

    while (!empty($csv)) {
        $users[] = $csv;
        $csv = fgetcsv($rosterList);
    }

    foreach ($users as $user) {
        $isVanline = (strlen($user[5]) == 0);
        $data = [];
        $data['user_name']        = $user[0];
        $data['email1']           = $user[3];
        $data['first_name']       = $user[1];
        $data['last_name']        = $user[2];
        $data['roleid']           = $isVanline ? Users_Edit_View::getRoleIdByName('Child Van Line User') : Users_Edit_View::getRoleIdByName('Sales Manager'); //If they have a code, make them Agent admin, if not, make them child vanline
        $data['agent_ids']        = getAgentVanlineId(($isVanline ? $user[6] : $user[4]), $isVanline);
        $data['user_password']    = randomPassword();
        $data['confirm_password'] = $data['user_password'];

        try {
            $user = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
            $newUser = vtws_create('Users', $data, $current_user);
        } catch (WebServiceException $ex) {
            echo $ex->getMessage();
            echo "<br><br>";
            die;
        }
    }
}

function randomPassword()
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function getAgentVanlineId($agentVanlineName, $isVanline)
{
    $db = PearDatabase::getInstance();

    if ($isVanline) {
        return $db->getOne("SELECT `vanlinemanagerid` FROM `vtiger_vanlinemanager` WHERE `vanline_name` = '$agentVanlineName'");
    }
    return $db->getOne("SELECT `agentmanagerid` FROM `vtiger_agentmanager` WHERE `agency_name` = '$agentVanlineName'");
}
