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

//Hotfix_Add_New_Blocks_Tables_Claims_ClaimItems

$db = PearDatabase::getInstance();
echo "<br /> Adding vtiger_claimitems_settlementamount table (ClaimItems) <br />";

$db->pquery("CREATE TABLE IF NOT EXISTS `vtiger_claimitems_settlementamount` (
  `id` int(11) NOT NULL,
  `claimitemsid` varchar(10) NOT NULL,
  `payment_type` varchar(50) NOT NULL,
  `amount` varchar(10) NOT NULL,
  `amount_denied` varchar(10) NOT NULL,
  `item_omitted` varchar(3) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;", array());

echo "<br /> Done!  <br />";

echo "<br /> Adding vtiger_claims_sprgrid table (Claims & ClaimItems) <br />";

$db->pquery("CREATE TABLE IF NOT EXISTS `vtiger_claims_sprgrid` (
  `sprid` int(19) NOT NULL,
  `rel_crmid` int(19) NOT NULL,
  `agents_id` int(19) DEFAULT NULL,
  `agent_name` varchar(50) DEFAULT NULL,
  `vendors_id` int(19) DEFAULT NULL,
  `vendors_name` varchar(50) DEFAULT NULL,
  `respon_percentage` varchar(5) NOT NULL,
  `respon_amount` varchar(50) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;", array());

echo "<br /> Done!  <br />";

echo "<br /> Adding vtiger_claimitems_originalconditions table (ClaimItems) <br />";

$db->pquery("CREATE TABLE IF NOT EXISTS `vtiger_claimitems_originalconditions` (
  `id` int(11) NOT NULL,
  `claimitemsid` varchar(10) NOT NULL,
  `inventory_number` varchar(100) NOT NULL,
  `tag_color` varchar(100) NOT NULL,
  `original_conditions` varchar(100) NOT NULL,
  `exceptions` varchar(100) NOT NULL,
  `date_taken` varchar(10) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;", array());

echo "<br /> Done!  <br />";

echo "<br /> Adding vtiger_claims_payments table (Claims) <br />";

$db->pquery("CREATE TABLE IF NOT EXISTS `vtiger_claims_payments` (
  `paymentId` int(11) NOT NULL,
  `claimsId` varchar(10) NOT NULL,
  `paymentFees` varchar(50) NOT NULL,
  `feesDate` varchar(10) NOT NULL,
  `feesAmount` varchar(10) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;", array());

echo "<br /> Done!  <br />";

echo "<br /> Adding vtiger_claimitems_settlementamount table (Claims) <br />";

$db->pquery("CREATE TABLE IF NOT EXISTS `vtiger_claimitems_settlementamount` (
 `id` int(11) NOT NULL,
 `claimitemsid` varchar(10) NOT NULL,
 `payment_type` varchar(50) NOT NULL,
 `amount` varchar(10) NOT NULL,
 `amount_denied` varchar(10) NOT NULL,
 `item_omitted` varchar(3) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;");

echo "<br /> Done!  <br />";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";