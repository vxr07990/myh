<?php

$Vtiger_Utils_Log = true;
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
/*
$tariffList = array('MOG57'=>'Interstate', 'RES1'=>'Intrastate', 'Government 530'=>'Interstate');
if(!$db)$db = PearDatabase::getInstance();

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `typeofdata` = 'E~M' WHERE  `columnname` LIKE  'email1' AND `tablename` LIKE 'vtiger_account'");

try {
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());

    $data = array (
        'vanline_id' => 133770,
        'vanline_name' => 'United Van Lines (Canada) Ltd.',
        'address1' => '7229 Pacific Circle',
        'city' => 'Mississauga',
        'state' => 'ON',
        'zip' => 'L5T 1S9',
        'country' => 'Canada',
        'phone1' => '905-564-6400',
        'website' => 'http://www.uvl.ca',
        'assigned_user_id' => '19x1'
    );

    //$vanline = vtws_create('VanlineManager', $data, $current_user);
    $wsid = $vanline['id'];
    $temp = explode('x', $wsid);
    $vanlineid = $temp[1];

    foreach($tariffList as $tariffName=>$tariffType) {
        $data = array (
            'tariffmanagername' => $tariffName,
            'tariff_type' => $tariffType,
            'rating_url' => 'https://awsdev1.movecrm.com/RatingEngine/UVLC/RatingService.svc',
            'assigned_user_id' => '19x1',
            'custom_tariff_type' => 'Base'
        );

        //$tariff = vtws_create('TariffManager', $data, $current_user);
        $wsid = $tariff['id'];
        $temp = explode('x', $wsid);
        $tariffid = $temp[1];

        //$sql = "INSERT INTO `vtiger_tariff2vanline` VALUES (?,?,?)";
        //$db->pquery($sql, array($vanlineid, $tariffid, 1));
    }
} catch (WebServiceException $ex) {
    echo $ex->getMessage();
}
*/
//require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_CN_Applicable_Service_Block.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Modify_UVLC_Fields.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Set_Default_IRS_Amount.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Order_UVLC_SIT.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_UVLC_Estimates_Fields.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_UVLC_Order_Number.php');
//Add the Language field to Opportunities - must be run before lead conversion hotfix
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Opportunities_AddLanguage.php');
//Field changes for UVL
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_UVL_Fields_1.php');
