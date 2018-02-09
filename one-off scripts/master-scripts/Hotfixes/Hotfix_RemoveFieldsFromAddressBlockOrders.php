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
echo "<br><h1>Starting Hotfix Remove Fields From Address Block Orders</h1><br>\n";

$ordersInstance = Vtiger_Module::getInstance('Orders');

$block = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $ordersInstance);

$field1 = Vtiger_Field::getInstance('origin_zone', $ordersInstance);
$field2 = Vtiger_Field::getInstance('empty_zone', $ordersInstance);

$field3 = Vtiger_Field::getInstance('business_line', $ordersInstance);
$field4 = Vtiger_Field::getInstance('billing_type', $ordersInstance);

$db = PearDatabase::getInstance();

// DELETE Origin Zone
if ($field1) {
    $sql = 'DELETE FROM `vtiger_field` WHERE fieldid = ?';
    $db->pquery($sql, [$field1->id]);
    $sql = 'ALTER TABLE `vtiger_orders` DROP COLUMN origin_zone';
    $db->pquery($sql, [$field1->id]);

    echo '<p>Removed Origin Zone field from orders</p>';
}

// DELETE Empty Zone
if ($field2) {
    $sql = 'DELETE FROM `vtiger_field` WHERE fieldid = ?';
    $db->pquery($sql, [$field2->id]);
    $sql = 'ALTER TABLE `vtiger_orders` DROP COLUMN empty_zone';
    $db->pquery($sql, [$field2->id]);

    echo '<p>Removed Empty Zone field from orders</p>';
}

// Move Business Line
if ($field3) {
    $sql = 'UPDATE `vtiger_field` SET block = ? WHERE fieldid = ?';
    $db->pquery($sql, [$block->id, $field3->id]);

    echo '<p>Moved Business Line in orders</p>';
}

// Move Billing Type
if ($field4) {
    $sql = 'UPDATE `vtiger_field` SET block = ? WHERE fieldid = ?';
    $db->pquery($sql, [$block->id, $field4->id]);

    echo '<p>Moved Billing Type in orders</p>';
}


echo "<br><h1>Finished Hotfix Remove Fields From Address Block Orders</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";