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



include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';

echo "<br><h1>Begin Hotfix to add permissions for comments</h1><br>";

function newCommentPermissions($profileName)
{
    echo "<br><h3>attempting to set comment permissions for $profileName</h3><br>";
    $adb = PearDatabase::getInstance();
    //grab profile id
    $sql = "SELECT profileid FROM `vtiger_profile` WHERE profilename = ?";
    $result = $adb->pquery($sql, array($profileName));
    $row = $result->fetchRow();
    $profileId = $row[0];
    //grab permissions if they exist
    $permission0 = getCommentPermission($profileId, 0);
    $permission1 = getCommentPermission($profileId, 1);
    $permission2 = getCommentPermission($profileId, 2);
    $permission3 = getCommentPermission($profileId, 3);
    $permission4 = getCommentPermission($profileId, 4);
    //set permissions if they don't exist
    if ($permission0 === null) {
        echo "<br>operation 0 permissions not set for $profileName, CORRECTING...<br>";
        setCommentPermission($profileId, 0, 0);
        echo "<br>DONE!<br>";
    } else {
        echo "<br>operation 0 permissions already set for $profileName, SKIPPING...<br>";
    }
    if ($permission1 === null) {
        echo "<br>operation 1 permissions not set for $profileName, CORRECTING...<br>";
        setCommentPermission($profileId, 1, 0);
        echo "<br>DONE!<br>";
    } else {
        echo "<br>operation 1 permissions already set for $profileName, SKIPPING...<br>";
    }
    if ($permission2 === null) {
        echo "<br>operation 2 permissions not set for $profileName, CORRECTING...<br>";
        setCommentPermission($profileId, 2, 0);
        echo "<br>DONE!<br>";
    } else {
        echo "<br>operation 2 permissions already set for $profileName, SKIPPING...<br>";
    }
    if ($permission3 === null) {
        echo "<br>operation 3 permissions not set for $profileName, CORRECTING...<br>";
        setCommentPermission($profileId, 3, 0);
        echo "<br>DONE!<br>";
    } else {
        echo "<br>operation 3 permissions already set for $profileName, SKIPPING...<br>";
    }
    if ($permission4 === null) {
        echo "<br>operation 4 permissions not set for $profileName, CORRECTING...<br>";
        setCommentPermission($profileId, 4, 0);
        echo "<br>DONE!<br>";
    } else {
        echo "<br>operation 4 permissions already set for $profileName, SKIPPING...<br>";
    }
    
    //check/set profile2tab permissions
    $sql = "SELECT permissions FROM `vtiger_profile2tab` WHERE profileid = ? AND tabid = 42";
    $result = $adb->pquery($sql, array($profileId));
    $row = $result->fetchRow();
    $tabPermission = $row[0];
    
    //file_put_contents('logs/devLog.log', "\n $profileName - $profileId - $tabPermission - ".gettype($tabPermission), FILE_APPEND);

    if ($tabPermission === null) {
        echo "<br>tab permissions not set for $profileName, CORRECTING...<br>";
        $sql = 'INSERT INTO `vtiger_profile2tab` (profileid, tabid, permissions) VALUES (?, 42, ?)';
        $adb->pquery($sql, array($profileId, 0));
        echo "<br>DONE!<br>";
    } else {
        echo "<br>tab permissions already set for $profileName, SKIPPING...<br>";
    }
    
    //check/set profile2field permissions
    fixCommentFieldPermission($profileId, 597, 0);
    fixCommentFieldPermission($profileId, 598, 0);
    fixCommentFieldPermission($profileId, 599, 1);
    fixCommentFieldPermission($profileId, 600, 1);
    fixCommentFieldPermission($profileId, 601, 0);
    fixCommentFieldPermission($profileId, 602, 1);
    fixCommentFieldPermission($profileId, 603, 0);
    fixCommentFieldPermission($profileId, 691, 0);
    fixCommentFieldPermission($profileId, 710, 0);
    fixCommentFieldPermission($profileId, 711, 0);
    fixCommentFieldPermission($profileId, 745, 1);
    fixCommentFieldPermission($profileId, 1792, 0);
    fixCommentFieldPermission($profileId, 1793, 0);

    echo "<br>comment permissions set for $profileName<br>";
}

function getCommentPermission($profileId, $operation)
{
    $adb = PearDatabase::getInstance();
    $sql = "SELECT permissions FROM `vtiger_profile2standardpermissions` WHERE profileid = ? AND tabid = 42 AND operation = ?";
    $result = $adb->pquery($sql, array($profileId, $operation));
    $row = $result->fetchRow();
    //file_put_contents('logs/devLog.log', "\n $profileId - $operation - ".$row[0], FILE_APPEND);
    return $row[0];
}

function setCommentPermission($profileId, $operation, $permission)
{
    $adb = PearDatabase::getInstance();
    $sql = 'INSERT INTO `vtiger_profile2standardpermissions` (profileid, tabid, operation, permissions) VALUES (?, 42, ?, ?)';
    $adb->pquery($sql, array($profileId, $operation, $permission));
}

function fixCommentFieldPermission($profileId, $fieldId, $readOnly)
{
    $adb = PearDatabase::getInstance();
    $sql = "SELECT visible FROM `vtiger_profile2field` WHERE profileid = ? AND tabid = 42 AND fieldid = ?";
    $result = $adb->pquery($sql, array($profileId, $fieldId));
    $row = $result->fetchRow();
    //file_put_contents('logs/devLog.log', "\n $profileId - $operation - ".$row[0], FILE_APPEND);
    if ($row[0] === null) {
        echo "<br>field $fieldId permissions not set, CORRECTING...<br>";
        $sql = 'INSERT INTO `vtiger_profile2field` (profileid, tabid, fieldid, visible, readonly) VALUES (?, 42, ?, 0, ?)';
        $adb->pquery($sql, array($profileId, $fieldId, $readOnly));
        echo "<br>DONE!<br>";
    } else {
        echo "<br>field $fieldId permissions already set, SKIPPING...<br>";
    }
}

newCommentPermissions('Vanline Profile');
newCommentPermissions('Vanline User Profile');
newCommentPermissions('Agent Family Administrator Profile');
newCommentPermissions('Agent Administrator Profile');
newCommentPermissions('Agent 2 Profile');
newCommentPermissions('Sales Manager Profile');
newCommentPermissions('Agency User Profile');
newCommentPermissions('Sales Person Profile');
newCommentPermissions('Read-only User Profile');

require_once('_recreate_user_privilege_files.php');

echo "<br><h1>End Hotfix to add permissions for comments</h1><br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";