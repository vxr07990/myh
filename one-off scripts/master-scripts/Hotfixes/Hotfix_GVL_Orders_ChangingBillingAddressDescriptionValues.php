<?php
//OT16984 - Updating billing address description saved values in orders to match the id of the billing address they are pulled from to facilitate being able to switch billing addresses
//based on description.

if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = &PearDatabase::getInstance();

$sql = 'SELECT vba.id, vba.address_desc, vo.bill_addrdesc, vo.ordersid 
		FROM `vtiger_accounts_billing_addresses` AS vba 
		JOIN `vtiger_orders` AS vo 
		WHERE vba.account_id = vo.orders_account 
		AND vo.bill_addrdesc = vba.address_desc';

$res = $db->pquery($sql);

if (!$res) {
    echo "Query failed. Unable to update. <br/>\n";
    echo "<br/>\n FAILED: " . __FILE__ . "<br />\n";
    return;
}

$rowCount = $db->num_rows($res);
if ($rowCount == 0) {
    echo "No rows need to be updated. <br/>\n";
    echo "<br/>\n COMPLETED: " . __FILE__ . "<br />\n";
    return;
}

$updatedCount = 0;

echo "Attempting to update $rowCount rows.<br/>\n";

while ($row = $res->fetchRow()) {
    $addrId = $row['id'];
    $addrDesc = $row['address_desc'];
    $ordersAddrDesc = $row['bill_addrdesc'];
    $ordersId = $row['ordersid'];
    $updateSQL = 'UPDATE `vtiger_orders` 
				  SET bill_addrdesc = ? 
				  WHERE ordersid = ?';
    $updateRes = $db->pquery($updateSQL, [$addrId, $ordersId]);

    if ($updateRes) {
        $updatedCount++;
        if ($updatedCount % 10 != 0) {
            echo ".";
        } elseif ($updatedCount % 50 != 0) {
            echo "|";
        } else {
            echo "|<br/>\n";
        }
    } else {
        echo "Update failed for order ID: $ordersId<br/>\n";
    }
}

echo "<br/>\n Successfully updated $updatedCount rows.<br/>\n";

echo "<br/>\n COMPLETED: " . __FILE__ . "<br />\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";