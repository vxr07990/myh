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
echo "<br><h1>Starting to add Option to Estimate Stage</h1><br>\n";

$db = PearDatabase::getInstance();

$found = false;
$valueId = 0;
$sortOrderId = 0;
$quotestageid = 0;

if (Vtiger_Utils::CheckTable('vtiger_quotestage')) {
    $sql = 'SELECT * FROM `vtiger_quotestage`';
    $result = $db->pquery($sql, []);

    while ($row = $result->fetchRow()) {
        if ($row['quotestage'] == 'Non-Current') {
            $found = true;
            break;
        }
        $sortOrderId = $row['sortorderid'] + 1;
        $valueId = $row['picklist_valueid'] + 1;
        $quotestageid = $row['quotestageid'] + 1;
    }

    if ($found) {
        echo '<p>Picklist option Non-Current already exists</p>';
    } else {
        echo '<p>Adding Non-Current to the estimate stage picklist</p>';

        $sql = 'INSERT INTO `vtiger_quotestage` (quotestageid, quotestage, presence, picklist_valueid, sortorderid) VALUES (?, ?, ?, ?, ?)';
        $params = [
            $quotestageid,
            'Non-Current',
            '0',
            $valueId,
            $sortOrderId
        ];
        $db->pquery($sql, $params);

        $sql = 'UPDATE `vtiger_quotestage_seq` SET id = ?';
        $db->pquery($sql, [$quotestageid]);

        echo '<p>Added Non-Current to the estimate stage picklist</p>';
    }
} else {
    echo '<p>vtiger_quotestage table not found</p>';
}

echo "<br><h1>Ending add Option to Estimate Stage</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";