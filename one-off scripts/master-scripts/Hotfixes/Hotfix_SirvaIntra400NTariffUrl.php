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


//*/
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
//*/
echo "<br><h1>Starting Tariff Manager Update </h1><br>\n";

/////////////////////////////////////////////
$name = '400N Base Allied';
$ratingUrl = 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/Base400N/RatingService400N.svc?wsdl';
$js = 'Estimates_BaseSIRVA_Js';

updateTariffManager($name, $ratingUrl, $js);

/////////////////////////////////////////////
$name = 'Intra - 400N Allied';
$ratingUrl = 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/Base400N/RatingService400N.svc?wsdl';
$js = 'Estimates_BaseSIRVA_Js';

updateTariffManager($name, $ratingUrl, $js);

//////////////////////////////////////////////////
$name = 'Intra - 400N North American';
$ratingUrl = 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/Base400N/RatingService400N.svc?wsdl';
$js = 'Estimates_BaseSIRVA_Js';

updateTariffManager($name, $ratingUrl, $js);
////////////////////////////////////////////
$name = '400N/104G North American';
$ratingUrl = 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/Base400N/RatingService400N.svc?wsdl';
$js = 'Estimates_BaseSIRVA_Js';

updateTariffManager($name, $ratingUrl, $js);


echo "<br><h1>Finished </h1><br>\n";


function updateTariffManager($name, $ratingUrl, $js)
{
    $db = PearDatabase::getInstance();

    $sql = 'SELECT tariffmanagerid FROM `vtiger_tariffmanager` WHERE tariffmanagername = ?';
    $result = $db->pquery($sql, [$name]);
    $row = $result->fetchRow();

    if ($row != null) {
        echo "<p>Begin update of $name.</p><br>";

        $sql = "UPDATE vtiger_tariffmanager SET rating_url = ?, custom_javascript = ? WHERE tariffmanagerid = ?";
        $result = $db->pquery($sql, array($ratingUrl, $js, $row[0]));

        echo "<p>$name generated.</p><br>";
        echo '<p>Done Updating '.$name.'</p>';
    } else {
        echo '<h1 style="color:orange;">WARNING: an interstate tariff doesn\'t exists with the name '.$name.'</h1><br>';
        return;
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";