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


require_once 'include/database/PearDatabase.php';

if (!$db) {
    $db = PearDatabase::getInstance();
}

$sql = "SELECT tabid FROM vtiger_tab WHERE name IN ('Accidents', 'Accounts', 'AdminSettings', 'AgentManager', 'Agents', 'Assets', 'Calendar', 'Campaigns', 'ClaimItems', 'Claims', 'Contacts', 'Contracts', 'Cubesheets', 'Documents', 'EffectiveDates', 'Employees', 'Equipment', 'Estimates', 'Events', 'Faq', 'HelpDesk', 'Inbox', 'Invoice', 'Leads', 'LongDistanceDispatch', 'ModComments', 'MoveRoles', 'OPList', 'Opportunities', 'Orders', 'OrdersMilestone', 'OrdersTask', 'PBXManager', 'Potentials', 'PriceBooks', 'Products', 'Project', 'ProjectMilestone', 'ProjectTask', 'PurchaseOrder', 'Quotes', 'Reports', 'SalesOrder', 'ServiceContracts', 'Services', 'SMSNotifier', 'Stops', 'Storage', 'Surveys', 'TariffManager', 'TariffReportSections', 'Tariffs', 'TariffSections', 'TariffServices', 'TimeOff', 'TimeSheets', 'Trips', 'VanlineManager', 'Vanlines', 'Vehicles', 'Vendors', 'WeeklyTimeSheets', 'ZoneAdmin')";
$tabids = $db->pquery($sql, []);

while ($row =& $tabids->fetchRow()) {
    $tab = $row['tabid'];

    $db->pquery("INSERT INTO `vtiger_profile2standardpermissions` (`tabid`, `operation`, `profileid`, `permissions`)
					SELECT $tab, 6, `profileid`, 0
					FROM `vtiger_profile`
					WHERE NOT EXISTS(SELECT *
										FROM `vtiger_profile2standardpermissions` AS t1
										WHERE `vtiger_profile`.`profileid` = t1.`profileid`
										AND t1.`operation` = 6
										AND t1.`tabid` = $tab
									   )
						AND `profilename` IN ('Vanline Profile', 'Vanline User Profile', 'Agent Family Administrator Profile', 'Agent Administrator Profile', 'Agent 2 Profile', 'Sales Manager Profile')", []);
}

$tabids = $db->pquery($sql, []);

while ($row =& $tabids->fetchRow()) {
    $db->pquery("INSERT INTO `vtiger_profile2standardpermissions` (`tabid`, `operation`, `profileid`, `permissions`)
					SELECT $tab, 5, `profileid`, 0
					FROM `vtiger_profile`
					WHERE NOT EXISTS(SELECT *
										FROM `vtiger_profile2standardpermissions` AS t1
										WHERE `vtiger_profile`.`profileid` = t1.`profileid`
										AND t1.`operation` = 5
										AND t1.`tabid` = $tab
									   )
						AND `profilename` IN ('Vanline Profile', 'Vanline User Profile', 'Agent Family Administrator Profile', 'Agent Administrator Profile', 'Agent 2 Profile', 'Sales Manager Profile')", []);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";