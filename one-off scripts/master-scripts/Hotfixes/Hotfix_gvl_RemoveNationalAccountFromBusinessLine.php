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



$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
include_once 'modules/Settings/Picklist/models/Module.php';
include_once 'modules/Settings/Picklist/models/Field.php';

echo "<h3>Starting RemoveNationalAccountFromBusinessLine</h3>\n";

$db = PearDatabase::getInstance();

if (Vtiger_Utils::CheckTable('vtiger_business_line')) {
    echo "<p>Checking vtiger_business_line for National Account option</p>\n";

    $sql = 'SELECT * FROM `vtiger_business_line` WHERE business_line = ?';
    $result = $db->pquery($sql, ['National Account']);

    $row = $result->fetchRow();
    if ($row) {
        echo "<p>National Account option is present in vtiger_business_line removing now</p>\n";

        $sql = 'SELECT * FROM `vtiger_business_line`';
        $result = $db->query($sql);

        //************** Save the current items minus the National Account ********************//
        $options = [];
        while ($row = $result->fetchRow()) {
            if ($row['business_line'] != 'National Account') {
                $options[] = $row['business_line'];
            }
        }

        //************** Delete the vtiger_business_line table ********************//
        Vtiger_Utils::ExecuteQuery('TRUNCATE TABLE `vtiger_business_line`');

        //************** Readd the tables ********************//
        $count = 0;
        foreach ($options as $option) {
            $count++;
            $sql = 'INSERT INTO `vtiger_business_line` (business_lineid, business_line, sortorderid, presence) VALUES (?, ?, ?, ?)';
            $params = [
                $count,
                $option,
                $count,
                1
            ];
            $db->pquery($sql, $params);
        }

        //************** Update the seq table ********************//
        $sql = 'UPDATE `vtiger_business_line_seq` SET id = ?';
        $db->pquery($sql, [count($options)]);

        echo "<p>Removed National Account option from vtiger_business_line </p>\n";
    } else {
        echo "<p>National Account option was not found in vtiger_business_line</p>\n";
    }
} else {
    echo "<p>vtiger_business_line table doesn't exist</p>\n";
}

echo "<h3>Ending RemoveNationalAccountFromBusinessLine</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";