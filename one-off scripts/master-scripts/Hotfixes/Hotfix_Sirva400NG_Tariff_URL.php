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


    /*/
    $Vtiger_Utils_Log = true;
    include_once 'vtlib/Vtiger/Menu.php';
    include_once 'vtlib/Vtiger/Module.php';
    include_once 'modules/ModTracker/ModTracker.php';
    include_once 'modules/ModComments/ModComments.php';
    include_once 'includes/main/WebUI.php';
    include_once 'include/Webservices/Create.php';
    include_once 'modules/Users/Users.php';
    echo "<br><h1>Starting </h1><br>\n";

    echo '<p>Begin Updating Sirva 400N/104G Allied Tariff.</p><br>';

    $name = '400N/104G Allied';
    //$ratingUrl = 'https://awsdev1.movecrm.com/RatingEngine/SIRVA/Base400N/RatingService400N.svc?wsdl';

    $db = PearDatabase::getInstance();

    $sql = 'SELECT tariffmanagerid FROM `vtiger_tariffmanager` WHERE tariffmanagername = ?';
    $result = $db->pquery($sql, [$name]);
    $row = $result->fetchRow();

    if($row != null){
        echo "<p>Begin update of $name.</p><br>";

        $sql = "UPDATE vtiger_tariffmanager SET rating_url = ? WHERE tariffmanagerid = ?";
        //$result = $db->pquery($sql, array($ratingUrl, $row[0]));


        //file_put_contents('logs/devLog.log', "\n $name DATA: ".print_r($data, true), FILE_APPEND);
        echo "<p>$name DATA: ".print_r($data, true)."</p><br>";

    } else {
        echo '<h1 style="color:orange;">WARNING: an interstate tariff doesn\'t exists with the name '.$name.'</h1><br>';
        return;
    }

    echo "<p>$name generated.</p><br>";
    echo '<p>Done Updating '.$name.'</p>';
    echo "<br><h1>Finished </h1><br>\n";
*/;


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";