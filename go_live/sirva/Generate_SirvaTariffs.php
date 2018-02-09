<?php

require_once('includes/main/WebUI.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');

echo '<h1>Begin Generate Sirva Tariffs.</h1><br>';

/*$sql = "SELECT id FROM `vtiger_ws_entity` WHERE name = 'Users'";
$result = $db->pquery($sql, array());
$row = $result->fetchRow();
$UsersWsId = $row[0];

$sql = "SELECT id FROM `vtiger_ws_entity` WHERE name = 'Tariffs'";
$result = $db->pquery($sql, array());
$row = $result->fetchRow();
$TariffsWsId = $row[0];

$sql = "SELECT id FROM `vtiger_ws_entity` WHERE name = 'TariffSections'";
$result = $db->pquery($sql, array());
$row = $result->fetchRow();
$TariffSectionsWsId = $row[0];

$sql = "SELECT id FROM `vtiger_ws_entity` WHERE name = 'EffectiveDates'";
$result = $db->pquery($sql, array());
$row = $result->fetchRow();
$EffectiveDatesWsId = $row[0]; */

function generateTariff($name, $type, $ratingUrl, $customJS, $customType, $vanlineId)
{
    $db = PearDatabase::getInstance();
    
    $sql = 'SELECT tariffmanagerid FROM `vtiger_tariffmanager` WHERE tariffmanagername = ?';
    $result = $db->pquery($sql, [$name]);
    $row = $result->fetchRow();
    if ($row != null) {
        echo '<h1 style="color:orange;">WARNING: an interstate tariff already exists with the name '.$name.'</h1><br>';
        return;
    }
    
    //file_put_contents('logs/devLog.log', "\n Begin generation of $name", FILE_APPEND);
    echo "<h1>Begin generation of $name.</h1><br>";
    
    $data = array(
        'tariffmanagername' => $name,
        'tariff_type' => $type,
        'rating_url' => $ratingUrl,
        'custom_javascript' => $customJS,
        'custom_tariff_type' => $customType,
        'assigned_user_id' => '19x1',
        'Vanline'.$vanlineId.'State' => 'assigned',
        'assignVanline'.$vanlineId.'Agents' => 'on',
    );
    
    $sql = "SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE vanline_id = ?";
    $result = $db->pquery($sql, array($vanlineId));
    $row = $result->fetchRow();
    
    while ($row != null) {
        $data['assignAgent'.$row[0]] = 'on';
        $row = $result->fetchRow();
    }
    
    //file_put_contents('logs/devLog.log', "\n $name DATA: ".print_r($data, true), FILE_APPEND);
    echo "<h1>$name DATA: ".print_r($data, true)."</h1><br>";
    
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
    $newService = vtws_create('TariffManager', $data, $current_user);

    //file_put_contents('logs/devLog.log', "\n $name generated", FILE_APPEND);
    echo "<h1>$name generated.</h1><br>";
}

$db = PearDatabase::getInstance();

$sql = "SELECT vanlinemanagerid FROM `vtiger_vanlinemanager` WHERE vanline_name = 'North American Van Lines'";
$result = $db->pquery($sql, array());
$row = $result->fetchRow();
$northAmericanId = $row[0];

$sql = "SELECT vanlinemanagerid FROM `vtiger_vanlinemanager` WHERE vanline_name = 'Allied'";
$result = $db->pquery($sql, array());
$row = $result->fetchRow();
$alliedId = $row[0];

//generateTariff('NA TPG', 'Interstate', 'http://206.208.246.17/RatingEngine/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'TPG', $northAmericanId);

//Allied Tariffs
generateTariff('TPG Allied', 'Interstate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'TPG', $alliedId);
generateTariff('UAS Allied', 'Interstate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'UAS', $alliedId);
generateTariff('Allied Express', 'Interstate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'Allied Express', $alliedId);
generateTariff('TPG GRR Allied', 'Interstate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'TPG GRR', $alliedId);
generateTariff('ALLV 2-A Allied', 'Interstate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'ALLV-2A', $alliedId);
generateTariff('400N Base Allied', 'Interstate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_BaseSIRVA_Js', '400N Base', $alliedId);
generateTariff('400N/104G Allied', 'Interstate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/Base400N/RatingService400N.svc?wsdl', 'Estimates_BaseSIRVA_Js', '400N/104G', $alliedId);
generateTariff('Intra - 400N Allied', 'Intrastate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_BaseSIRVA_Js', 'Intra - 400N', $alliedId);
//generateTariff('Local/Intra Allied', 'Intrastate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'Local/Intra', $alliedId);
generateTariff('400NG Allied', 'Interstate', 'https://awsdev1.movecrm.com/RatingEngineDev/Base400NG/RatingService.svc?wsdl', 'Estimates_BaseSIRVA_Js', '400NG', $alliedId);
//generateTariff('Max 3 Allied', 'Intrastate', 'http://206.208.246.17/RatingEngine/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'Max 3', $alliedId);
//generateTariff('Max 4 Allied', 'Intrastate', 'http://206.208.246.17/RatingEngine/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'Max 4', $alliedId);

//NA Tariffs
generateTariff('Pricelock North American', 'Interstate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'Pricelock', $northAmericanId);
generateTariff('UAS North American', 'Interstate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'UAS', $northAmericanId);
generateTariff('Blue Express', 'Interstate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'Blue Express', $northAmericanId);
generateTariff('Pricelock GRR North American', 'Interstate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'Pricelock GRR', $northAmericanId);
generateTariff('NAVL-12A North American', 'Interstate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'NAVL-12A', $northAmericanId);
generateTariff('400N Base North American', 'Interstate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_BaseSIRVA_Js', '400N Base', $northAmericanId);
generateTariff('400N/104G North American', 'Interstate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_BaseSIRVA_Js', '400N/104G', $northAmericanId);
generateTariff('Intra - 400N North American', 'Intrastate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_BaseSIRVA_Js', 'Intra - 400N', $northAmericanId);
//generateTariff('Local/Intra North American', 'Intrastate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'Local/Intra', $northAmericanId);
generateTariff('400NG North American', 'Interstate', 'https://awsdev1.movecrm.com/RatingEngineDev/Base400NG/RatingService.svc?wsdl', 'Estimates_BaseSIRVA_Js', '400NG', $northAmericanId);
//generateTariff('Max 3 North American', 'Intrastate', 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'Max 3', $northAmericanId);
//generateTariff('Max 4 North American', 'Intrastate', 'http://206.208.246.17/RatingEngine/RatingService.svc?wsdl', 'Estimates_TPGTariff_Js', 'Max 4', $northAmericanId);

echo '<h1>Through Generate Sirva Tariffs.</h1>';
