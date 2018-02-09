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


function deleteProfile($profileName, $db)
{
    $sql = "SELECT profileid FROM `vtiger_profile` WHERE profilename = ?";
    $result = $db->pquery($sql, array($profileName));
    $row = $result->fetchRow();
    $profileId = $row[0];
    
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_profile` WHERE profileid = $profileId");
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_profile2globalpermissions` WHERE profileid = $profileId");
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_profile2tab` WHERE profileid = $profileId");
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_profile2standardpermissions` WHERE profileid = $profileId");
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_profile2utility` WHERE profileid = $profileId");
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_profile2field` WHERE profileid = $profileId");
    echo "<br>$profileName deleted sucessfully<br>";
}
function deleteRole($roleName, $db)
{
    $sql = "SELECT roleid FROM `vtiger_role` WHERE rolename = ?";
    $result = $db->pquery($sql, array($roleName));
    $row = $result->fetchRow();
    $roleId = $row[0];
    
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_role` WHERE roleid = '$roleId'");
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_role2profile` WHERE roleid = '$roleId'");
    echo "<br>$roleName deleted sucessfully<br>";
}
function deleteGroup($groupName, $db)
{
    $sql = "SELECT groupid FROM `vtiger_groups` WHERE groupname = ?";
    $result = $db->pquery($sql, array($groupName));
    $row = $result->fetchRow();
    $groupId = $row[0];
    
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_groups` WHERE groupid = $groupId");
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_group2role` WHERE groupid = $groupId");
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_group2rs` WHERE groupid = $groupId");
    Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_users2group` WHERE groupid = $groupId");
}
function updateRoleName($roleName, $toRoleName, $db)
{
    $sql = "SELECT roleid FROM `vtiger_role` WHERE rolename = ?";
    $result = $db->pquery($sql, array($roleName));
    $row = $result->fetchRow();
    $roleId = $row[0];
    
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_role` SET rolename = '$toRoleName' WHERE roleid = '$roleId'");
}
$profiles = array('Sales Profile','Support Profile','Guest Profile');
$roles = array('Vice President', 'Sales Manager', 'Sales Person');
$groups = array('Team Selling', 'Marketing Group', 'Support Group');

updateRoleName('CEO', 'Administrator', $db);
foreach ($profiles as $name) {
    deleteProfile($name, $db);
}
foreach ($roles as $name) {
    deleteRole($name, $db);
}
foreach ($groups as $name) {
    deleteGroup($name, $db);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";