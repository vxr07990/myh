<?php
if (function_exists("call_ms_function_ver")) {
    $version = 'AlwaysRun';
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";



include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<br><h3>Validating that all TariffSections records have a corresponding Services record for line items</h3><br>";

if (!$db) {
    $db = PearDatabase::getInstance();
}

$sql = "SELECT DISTINCT(section_name) FROM `vtiger_tariffsections`";
$result = $db->query($sql);

while ($row =& $result->fetchRow()) {
    $sql = "SELECT * FROM `vtiger_service` WHERE servicename=?";
    $res = $db->pquery($sql, [$row['section_name']]);
    if ($db->num_rows($res) == 0) {
        try {
            $user         = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
            $data         = [
                'servicename'      => $row['section_name'],
                'assigned_user_id' => '19x1',
                'qty_per_unit'     => 0,
                'unit_price'       => 0,
                'discontinued'     => 1,
                'currency_id'      => 1,
                'commissionrate'   => 0];
            $service      = vtws_create('Services', $data, $current_user);
            $decodedService = json_decode($service);
            echo "<br><h4>New Service record added: ".$service->result->servicename."</h4><br>\n";
            $wsid = $service['id'];
            $crmid = explode('x', $wsid)[1];
            $sql = "INSERT INTO `vtiger_producttaxrel` (productid,taxid,taxpercentage) VALUES (?,?,?)";
            for ($i = 1; $i <= 3; $i++) {
                $db->pquery($sql, array($crmid, $i, 0));
            }
        } catch (WebServiceException $ex) {
            file_put_contents('logs/failedServiceCreates', date('Y-m-d H:i:s - ')."Webservice exception caught in Hotfix_TariffSections_ValidateServices.php : ".print_r($ex, true)."\n", FILE_APPEND);
            echo "Webservice exception caught in Hotfix_TariffSections_ValidateServices.php : ".print_r($ex, true)."<br>\n";
        }
    } else {
        echo "<br><h4>Service record with servicename ".$row['section_name']." already exists in database. Skipping.</h4><br>\n";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";