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
error_reporting(E_ERROR);
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;
function updateMenu($parent, $name)
{
    global $adb;
    $adb->pquery("UPDATE vtiger_tab SET parent = '$parent' WHERE name ='$name'");
}

$adb->pquery("UPDATE vtiger_tab SET parent = ''");
$data = array(
    'SALES_MARKETING_TAB' => array('Campaigns', 'Leads', 'Opportunities', 'Surveys', 'Estimates'),
    'OPERATIONS_TAB' => array('Orders', 'LocalDispatch', 'LongDistanceDispatch', 'Trips', 'Accounts', 'Contracts', 'MovePolicies'),
    'COMMON_SERVICES_TAB' => array('Contacts', 'Documents', 'Reports', 'Calendar', 'HelpDesk'),
    'FINANCE_TAB' => array('Actuals', 'Storage', 'Claims'),
    'SYSTEM_ADMIN_TAB' => array('AgentManager', 'VanlineManager', 'MailManager', 'TariffManager'),
    'TOOLS_TAB' => array('EmailTemplates', 'SMSNotifier','AdvancedReports','PDFMaker')
);
foreach ($data as $key=>$val) {
    foreach ($val as $result) {
        updateMenu($key, $result);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";