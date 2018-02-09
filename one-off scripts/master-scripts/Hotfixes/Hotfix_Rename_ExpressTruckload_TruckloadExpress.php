<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

echo "---- TARIFF MANAGER ----<br/>\n";
$sql = "SELECT tariffmanagerid FROM vtiger_tariffmanager WHERE tariffmanagername = 'Express Truckload' AND custom_tariff_type = 'Express Truckload'";
$res = $db->query($sql);
if($res && $db->num_rows($res) > 0) {
    echo "Converting Express Truckload to Truckload Express.<br/>\n";

    $id = $res->fetchRow()[0];
    $sql = "UPDATE vtiger_tariffmanager SET tariffmanagername = 'Truckload Express', custom_tariff_type = 'Truckload Express' WHERE tariffmanagerid = $id";
    $db->query($sql);
}else{
    echo "Express Truckload already converted to Truckload Express.<br/>\n";
}

echo "---- CUSTOM TARIFF TYPE ----<br/>\n";
$sql = "SELECT custom_tariff_typeid FROM vtiger_custom_tariff_type WHERE custom_tariff_type = 'Express Truckload'";
$res = $db->query($sql);
if($res && $db->num_rows($res) > 0) {
    echo "Converting Express Truckload to Truckload Express.<br/>\n";

    $id = $res->fetchRow()[0];
    $sql = "UPDATE vtiger_custom_tariff_type SET custom_tariff_type = 'Truckload Express' WHERE custom_tariff_typeid = $id";
    $db->query($sql);
}else{
    echo "Express Truckload already converted to Truckload Express.<br/>\n";
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";