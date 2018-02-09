<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$Vtiger_Utils_Log = true;
global $adb;

$moduleName = 'SignedRecord';

// Create default custom filter (mandatory)
echo '<h3>Update Signed Documents module</h3>';
echo '<ul>';

$fieldAssignedTo = 'assigned_user_id';
$sql = 'UPDATE vtiger_field
          JOIN vtiger_tab ON (vtiger_tab.tabid = vtiger_field.tabid)
        SET uitype = 53
        WHERE vtiger_tab.name LIKE ? AND vtiger_field.fieldname LIKE ?';
$params = array($moduleName, $fieldAssignedTo);
$result = $adb->pquery($sql, $params);

echo "<li>The '{$fieldAssignedTo}' field updated</li>";
echo '</ul>';

echo '<br>Done - Update Signed Documents module<br><br>';