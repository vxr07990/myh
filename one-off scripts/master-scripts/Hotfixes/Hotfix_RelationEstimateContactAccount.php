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



//include_once('vtlib/Vtiger/Menu.php');
//include_once('includes/main/WebUI.php');

echo '<h1>Begin Hotfix Repair Estimates Relation Contact Account</h1><br>';

$db = PearDatabase::getInstance();

$sql = 'SELECT tabid, name FROM `vtiger_tab` WHERE name = "Quotes" OR name = "Estimates" OR name = "Accounts" OR name = "Contacts"';
$result = $db->pquery($sql, []);
$row = $result->fetchRow();
while ($row != null) {
    switch ($row['name']) {
        case 'Quotes':
            $quoteTab = $row['tabid'];
            break;
        case 'Estimates':
            $estTab = $row['tabid'];
            break;
        case 'Accounts':
            $accountTab = $row['tabid'];
            break;
        case 'Contacts':
            $contactTab = $row['tabid'];
            break;
        default:
         //error occured
    }
    $row = $result->fetchRow();
}

if ($quoteTab && $estTab && $accountTab && $contactTab) {
    echo 'updating relatedlist<br>';
    echo "data - quote: $quoteTab <br> est: $estTab <br> account: $accountTab <br> contact: $contactTab <br>";
    $sql = 'UPDATE `vtiger_relatedlists` SET related_tabid = ? WHERE (related_tabid = ? AND tabid = ?) OR (related_tabid = ? AND tabid = ?)';
    $result = $db->pquery($sql, [$estTab, $quoteTab, $accountTab, $quoteTab, $contactTab]);
    //echo print_r($result, true);
    echo 'relatedlist updated<br>';
} else {
    echo '<h1>Error occured, not all tab ids were found</h1><br>';
}

echo '<h1>End Hotfix Repair Estimates Relation Contact Account</h1><br>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";