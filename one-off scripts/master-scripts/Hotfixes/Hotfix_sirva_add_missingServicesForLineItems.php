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
$db = PearDatabase::getInstance();

if (!$db) {
    print "NO DB:<... more stuff>";
    return;
}

require_once 'includes/main/WebUI.php';
require_once 'include/Webservices/Create.php';
require_once 'modules/Users/Users.php';

$newServiceNames = [
    'Transportation',
    'Containers',
    'Packing',
    'Unpacking',
    'Crating',
    'Uncrating',
    'Upholstery',
    'Fine Finish',
    'Origin Accessorials',
    'Destination Accessorials',
    'Origin SIT',
    'Destination SIT',
    'Vehicle Transports',
    'Valuation',
    'Bulkies',
    'Miscellaneous Items',
];

foreach ($newServiceNames as $service) {
    echo "Preparing to create ". $service . " services<br />\n";
    $stmt = 'SELECT * FROM vtiger_service WHERE servicename=? LIMIT 1';
    $res  = $db->pquery($stmt, [$service]);
    if ($db->num_rows($res) == 0) {
        createServiceSAMSFLI($service);
    } else {
        print "Service ".$service." already exists.<br />\n";
    }
}
echo "file complete<br />\n";

function createServiceSAMSFLI($name) {
    echo "Entering createService($name) <br />\n";
    $db = PearDatabase::getInstance();

    try {
        echo "Entering try block <br />\n";
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
        echo "\$current_user retrieved <br />\n";
        $data = array(
        'servicename'=>$name,
        'assigned_user_id'=>'19x1',
        'qty_per_unit'=>0,
        'unit_price'=>0,
        'discontinued'=>1,
        'currency_id'=>1,
        'commissionrate'=>0);
        echo "Preparing to call vtws_create <br />\n";
        $service = vtws_create('Services', $data, $current_user);
        print_r($service);
        echo "<br />\n";
        $wsid = $service['id'];
        $temp = explode('x', $wsid);
        $crmid = $temp[1];
        $sql = "INSERT INTO `vtiger_producttaxrel` (productid,taxid,taxpercentage) VALUES (?,?,?)";
        for ($i = 1; $i <= 3; $i++) {
            $result = $db->pquery($sql, array($crmid, $i, 0));
        }
    } catch (WebServiceException $ex) {
        echo $ex->getMessage();
    }
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";