<?php
if (function_exists("call_ms_function_ver")) {
    $version = $version = 'AlwaysRun';
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


//@TODO: It feels like this should be broken into it's components with file names that make it clear what's happening
//the first foreach sets the tabs grouping under the ALL list.
//the organization details where it sets the image.
//the vtiger_users index
//the smtp settings

    $fieldMap = array('CUSTOMERS_TAB'=>array('Contacts', 'Accounts'),
                      'SALES_MARKETING_TAB'=>array('Leads', 'SalesOrder', 'Campaigns', 'ProjectTask', 'Estimates', 'Opportunities', 'Surveys', 'Actuals', 'MovePolicies'),
                      //'SALES_MARKETING_TAB'=>array('Leads', 'SalesOrder', 'Campaigns', 'ProjectTask', 'Estimates', 'Opportunities', 'Surveys'),
                      'ANALYTICS_TAB'=>array('Dashboard'),
                      'TOOLS_TAB'=>array('Emails', 'Rss', 'Portal', 'PBXManager', 'EmailTemplates', 'ModComments', 'RecycleBin', 'SMSNotifier', 'Inbox'),
                      'SUPPORT_TAB'=>array('HelpDesk', 'ServiceContracts', 'ProjectMilestone', 'Project'),
                      'INVENTORY_TAB'=>array('Products', 'PriceBooks', 'PurchaseOrder', 'Assets'),
                      'SETTINGS_TAB'=>array('ExtensionStore'),
                      'OPERATIONS_TAB'=>array('Calendar', 'Reports', 'LongDistancePlanning','Trips','LongDistanceDispatch','OPList'),
                      'CUSTOMER_SERVICE_TAB'=>array('Documents', 'Orders', 'ClaimsSummary', 'Media'),
                      'COMPANY_ADMIN_TAB'=>array('Vendors', 'Vanlines', 'Agents', 'Tariffs', 'Employees', 'Vehicles', 'Equipment','ZoneAdmin'),
                      'FINANCE_TAB'=>array('Invoice', 'Payroll', 'Deposits', 'Distribution'),
                      'SYSTEM_ADMIN_TAB'=>array('MailManager', 'VanlineManager', 'AgentManager', 'TariffManager', 'AdminSettings'),
                      ''=>array('Services', 'Faq'),
                    );

    $disable = array('ModuleDesigner', 'ResourceDashboard', 'Resources',
                     'Export', 'SocialMedia', 'FacilitiesManager', 'LocalDispatch',
                     'Rating', 'Facilities', 'Partners', 'ResourceManager', 'Warehouses',
                     'Workflow', 'WarehouseInventory', 'UserManagement', 'ServicingAgents',
                     'VanlineContacts', 'Transferees', 'AgentContacts', 'ServicesToDispatch',
                     'Equipment', 'Contractors', 'Stops'
                     );

    $hqModules = array('Orders', 'OrdersTask', 'OrdersMilestone',
                       'Vehicles', 'Claims', 'ClaimItems', 'Accidents',
                       'Storage', 'TimeSheets', 'WeeklyTimesheets', 'Trips',
                       'Invoice', 'LongDistancePlanning', 'Payroll', 'Deposits', 'Distribution','ZoneAdmin'
                       , 'Actuals', 'MovePolicies'
                       );

//@NOTE: This is done with profile_permissions doing it here is weird.
//turn on all modules, then turn off the ones we don't need
//Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET presence = 0 WHERE presence != 2");
//Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET presence = 1 WHERE name = 'Project' OR name = 'ProjectTask' OR name = 'ProjectMilestone' OR name = 'SalesOrder' OR name = 'Products' OR name = 'Assets' OR name = 'Faq' OR name = 'ExtensionStore' OR name = 'CustomerPortal' OR name = 'PBXManager' OR name = 'Rss' OR name = 'ServiceContracts' OR name = 'RecycleBin' OR name = 'Portal' OR name = 'PriceBooks' OR name = 'PurchaseOrder'");

foreach ($fieldMap as $parent=>$modules) {
    foreach ($modules as $moduleName) {
        echo "Updating parent information for $moduleName to $parent <br />";
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent='$parent' WHERE name='$moduleName'");
    }
}

//@NOTE: This is done with profile_permissions doing it here is weird.
//foreach($disable as $moduleName) {
//	echo "Disabling module $moduleName <br />";
//	Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET presence=1 WHERE name='$moduleName'");
//}
/*
//chane the core parents to our parent labels for consistency
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = 'CUSTOMERS_TAB' WHERE parent = 'Customers'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = 'SALES_MARKETING_TAB' WHERE parent = 'Sales' OR parent = 'Marketing' OR name = 'ProjectTask' OR name = 'Orders'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = 'ANALYTICS_TAB' WHERE parent = 'Analytics'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = 'TOOLS_TAB' WHERE parent = 'Tools'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = 'SUPPORT_TAB' WHERE parent = 'Support'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = 'INVENTORY_TAB' WHERE parent = 'Inventory'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = '' WHERE name = 'Services'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = 'SETTINGS_TAB' WHERE parent = 'Settings'");
//Add our Modules to the right menu items
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = 'OPERATIONS_TAB' WHERE name = 'Calendar' OR name = 'LongDistancePlanning' OR name = 'LocalDispatch' OR name = 'Reports'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = 'SALES_MARKETING_TAB' WHERE name = 'Leads' OR name = 'Estimates' OR name = 'Surveys' OR name = 'Campaigns' OR name = 'Opportunities'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = 'CUSTOMER_SERVICE_TAB' WHERE name = 'Claims' OR name = 'Documents' OR name = 'Orders'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = 'CUSTOMERS_TAB' WHERE name = 'Accounts' OR name = 'Contacts'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = 'COMPANY_ADMIN_TAB' WHERE name = 'Agents' OR name = 'Vehicles' OR name = 'Vendors' OR name = 'Equipment' OR name = 'Employees' OR name = 'Vanlines' OR name = 'Tariffs' OR name = 'TimeSheets' OR name = 'Contracts'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = 'FINANCE_TAB' WHERE name = 'Invoice'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = 'SYSTEM_ADMIN_TAB' WHERE name = 'MailManager' OR name = 'TariffManager' OR name = 'VanlineManager' OR name = 'AgentManager'");
*/
//Hide the things that shouldn't be shown
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = '' WHERE name = 'Quotes' OR name = 'Potentials' OR name = 'Cubesheets' OR name = 'Stops' OR parent = '0'");

//double bonus fix the Company Logo and set it according to the DB value of movehq
Vtiger_Utils::ExecuteQuery(
    "UPDATE `vtiger_organizationdetails` SET "
    . " `organizationname` = 'MoveHQ Inc.',"
    . " `address` = '6432 E Main Street, Suite 201',"
    . " `city` = 'Reynoldsburg',"
    . " `state` = 'Ohio',"
    . " `country` = 'United States',"
    . " `code` = '43068',"
    . " `phone` = '614-759-9148',"
    . " `fax` = '614-749-0907',"
    . " `website` = 'www.MoveHQ.com', "
    . " `logoname` = 'movehq_main.png',"
    . " `logo` = null, "
    . " `vatid` = '1234-5678-9012'"
    . " WHERE `organization_id` = 1"
    //. " `logoname` = 'MoveHQ',"
    //. " `logo` = 'layouts/vlayout/skins/images/movehq_main.png',"
);
//@NOTE: Until we update  modules/Vtiger/models/CompanyDetails.php and all the places that set logoName and logo
// we are just keeping this db format, and the logo gets stored locally as test/logo/<logoName>
if (getenv('INSTANCE_NAME') == 'sirva') {
    //Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_organizationdetails` SET `logoname` = 'SIRVA', `logo` = 'test/logo/Logo SIRVA.png' WHERE `organization_id` = 1");
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_organizationdetails` SET `logoname` = 'Logo SIRVA.png' WHERE `organization_id` = 1");
} elseif (getenv('INSTANCE_NAME') == 'uvlc') {
    //Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_organizationdetails` SET `logoname` = 'Engage', `logo` = 'test/logo/Engage.png' WHERE `organization_id` = 1");
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_organizationdetails` SET `logoname` = 'Engage.png' WHERE `organization_id` = 1");
} else {
    //MoveCRM does not exist!
    //Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_organizationdetails` SET `logoname` = 'MoveCRM.png' WHERE `organization_id` = 1");
}

//@NOTE: This is done with profile_permissions doing it here is weird.
////Enable or disable HQ modules depending on installation
//foreach($hqModules as $moduleName) {
//	Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET presence=".!(getenv('IGC_MOVEHQ'))." WHERE name='$moduleName'");
//}

//in order to not add a unique key every single run, we test for it's existence.
$db = PearDatabase::getInstance();
$stmt = 'SHOW INDEXES FROM `vtiger_users` WHERE `Column_name`=? AND NOT `Non_unique` AND `Key_name` = ?';
if ($res = $db->pquery($stmt, ['user_name', 'user_name'])) {
    if ($db->num_rows($res) > 0) {
        //unique key already exists do nothing.
    } else {
        print "Adding unique key to vtiger_users. <br />\n";
        //@TODO: this can error if there are existing duplicate user_name. It might need handled, but can't see why.
        $addUniqueStmt = "ALTER TABLE  `vtiger_users` ADD UNIQUE (`user_name`)";
        $db->pquery($addUniqueStmt);
    }
} else {
    print "Failed to determine if the unique key exists.  Probably errors out and this doesn't display.<br />\n";
}

//set the mail server smtp settings
Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_systems` (`id`, `server`, `server_port`, `server_username`, `server_password`, `server_type`, `smtp_auth`, `server_path`, `from_email_field`) VALUES (1, 'smtp02.moverdocs.com', 0, '', '', 'email', 0, NULL, '')");
if (!Vtiger_Utils::CheckTable('vtiger_systems_seq')) {
    echo "<li>creating vtiger_systems_seq </li><br>";
    Vtiger_Utils::CreateTable('vtiger_systems_seq',
                              '(
							    `id` INT(11)
								)', true);
    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_systems_seq` VALUES (1)");
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
