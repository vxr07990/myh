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

if (!isset($db)) {
    $db = PearDatabase::getInstance();
}

$sql = "SELECT business_lineid FROM vtiger_business_line WHERE business_line = 'Sirva Military'";
$res = $db->query($sql);
if($db->num_rows($res)) {
    // There should seriously only be one but if not I'll just shoot myself and run a while loop.
    // Fuck it.
    while($row = $res->fetchRow()) {
        $id = $row[0];
        $sql = "UPDATE vtiger_business_line SET business_line = 'Military' WHERE business_lineid=$id";
        if(!$db->query($sql)) {
            echo "Error occurred updating 'vtiger_business_line'. Please check MySQL fail log.<br/>\n";
        }
    }
}