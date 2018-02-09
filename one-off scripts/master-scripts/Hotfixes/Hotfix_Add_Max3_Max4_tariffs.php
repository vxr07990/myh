<?php
if (function_exists("call_ms_function_ver")) {
    $version = '1';
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";



require_once('includes/main/WebUI.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');

echo '<h1>Begin Generate Sirva Tariffs.</h1><br>';

$db = PearDatabase::getInstance();

$sql = "SELECT vanlinemanagerid FROM `vtiger_vanlinemanager` WHERE vanline_name = 'North American Van Lines'";
$result = $db->pquery($sql, array());
$row = $result->fetchRow();
$northAmericanId = $row[0];

$sql = "SELECT vanlinemanagerid FROM `vtiger_vanlinemanager` WHERE vanline_name = 'Allied'";
$result = $db->pquery($sql, array());
$row = $result->fetchRow();
$alliedId = $row[0];

generateTariff('Max 3 Allied', 'Intrastate', 'http://206.208.246.17/RatingEngine/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'Max 3', $alliedId);
generateTariff('Max 4 Allied', 'Intrastate', 'http://206.208.246.17/RatingEngine/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'Max 4', $alliedId);
generateTariff('Max 3 North American', 'Intrastate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'Max 3', $northAmericanId);
generateTariff('Max 4 North American', 'Intrastate', 'http://206.208.246.17/RatingEngine/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'Max 4', $northAmericanId);

echo '<h1>Finished Generating Sirva Tariffs.</h1>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";