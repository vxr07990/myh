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


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('includes/main/WebUI.php');

echo "<br>begin hotfix sirva order estimate services<br>";

$db = PearDatabase::getInstance();

$orderedServices =  [
                        'Transportation',
                        'Fuel Surcharge',
                        'Packing',
                        'Unpacking',
                        'Valuation',
                        'Origin Accessorials',
                        'Destination Accessorials',
                        'Bulky Items',
                        'IRR',
                        'Origin SIT',
                        'Destination SIT',
                        'Miscellaneous Services',
                    ];

//assemble vtiger_service columns so we can conditionalize column creation
echo "<br>assembling service columns...";
$serviceColumns = [];
$result = $db->pquery('EXPLAIN `vtiger_service`', []);
while ($row =& $result->fetchRow()) {
    $serviceColumns[] = $row['Field'];
}
echo "done!<br>";

//create sequence column if it doesn't exist
if (!in_array('sequence', $serviceColumns)) {
    echo "<br>no sequence column found. creating now...";
    Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_service` ADD sequence INT(5)");
    echo "done!<br>";
} else {
    echo "<br>pre-existing sequence column detected. no action taken<br>";
}

//set service sequences
foreach ($orderedServices as $index => $serviceName) {
    echo "<br>setting sequence $index for service: $serviceName...";
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_service` SET sequence = $index WHERE servicename = '$serviceName'");
    echo "done!<br>";
}

echo "<br>end hotfix sirva order estimate services<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";