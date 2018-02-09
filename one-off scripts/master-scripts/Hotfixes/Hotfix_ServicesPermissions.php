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


/*
 * based on Hotfix_CommentsPermissions.php
 * This is half way fixed.
 * To use:
 *  * update the $moduleName string
 *  * update the $permissions Array
 *  * update the $activities Array
 *  * update the $profiles Array
 *  * update the $readOnlyField Array
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

{
    //the table you want to update permissions for
    $moduleName = 'Services';
    $module = Vtiger_Module::getInstance($moduleName);

    if ($module) {
        $tabId = $module->getId();


        /*
        operations are: from: class Vtiger_Action_Model
        these go into: vtiger_profile2standardpermissions
            static $standardActions = array(
                                        '0' => 'Save',
                                        '1' => 'EditView',
                                        '2' => 'Delete',
                                        '3' => 'index',
                                        '4' => 'DetailView'
                                    );

        these go into: vtiger_profile2utility
            static $utilityActions = array(
                                        '5' => 'Import',
                                        '6' => 'Export',
                                        '8' => 'Merge',
                                        '9' => 'ConvertLead',
                                        '10' => 'DuplicatesHandling'
                                    );
        */
        // you set the "permissions" for the "operation/activity/field" and the value 1 is OFF because 70 years of using
        // 0/1 to mean off/on, respectively, was too good for Vtiger!

        $permissions = array(
            '0' => '1',
            '1' => '1',
            '2' => '1',
            '3' => '1',
            '4' => '0',
        );
        $activities = array(
            '5' => 1,
            '6' => 1,
            '8' => 1,
            '9' => 1,
            '10' => 1,
        );

        //@TODO: update this to pull from the database;
        $profiles = array(
            'Vanline Profile',
            'Vanline User Profile',
            'Agent Family Administrator Profile',
            'Agent Administrator Profile',
            'Agent 2 Profile',
            'Sales Manager Profile',
            'Agency User Profile',
            'Sales Person Profile',
            //'Read-only User Profile',
            'Parent Vanline User',
        );

        //@TODO: update this to pull from the database;
        //readOnlyField sets fields named this to READ ONLY instead of RW.
        $readOnlyField = array(
                                'Created Time' => true,
                                'Modified Time' => true,
                        );

        $sql = 'SELECT `fieldid`,`fieldlabel` FROM `vtiger_field` WHERE `tabid` = ?';
        $result = $adb->pquery($sql, array($tabId));
        while ($row = $result->fetchRow()) {
            if (array_key_exists($row['fieldlabel'], $readOnlyField) && $readOnlyField[$row['fieldlabel']]) {
                $fields[$row['fieldid']] = 1;  //OFF
            } else {
                $fields[$row['fieldid']] = 0;  //ON
            }
        }

        echo "<br><h1>Begin Hotfix to add permissions for $moduleName</h1><br>";
        foreach ($profiles as $profile) {
            newPermissions($profile, $tabId, $permissions, $activities, $fields, $moduleName);
        }
        echo "<br><h1>Rebuilding user privilege files</h1><br />";
        require_once('_recreate_user_privilege_files.php');
        echo "<br><h1>End Hotfix to add permissions for $moduleName</h1><br>";
    } else {
        echo "<br /><h1> MODULE: $moduleName does not exist.</h1><br />";
    }
}

//@NOTE: or @TODO: the set/update is stupid but I was locked in already.
function newPermissions($profileName, $tabId, $permissions, $activities, $fields, $moduleName)
{
    echo "<br><h3>attempting to set $moduleName permissions for $profileName</h3><br>";
    $adb = PearDatabase::getInstance();
    //grab profile id
    $sql = "SELECT profileid FROM `vtiger_profile` WHERE profilename = ?";
    $result = $adb->pquery($sql, array($profileName));
    $row = $result->fetchRow();
    $profileId = $row[0];
    //grab permissions if they exist
    foreach ($permissions as $operation => $permission) {
        $permission0 = getOperationPermission($profileId, $operation, $tabId);

        //set permissions if they don't exist
        if ($permission0 === null) {
            echo "<br>operation $operation permissions not set for $profileName, CORRECTING...<br>";
            setOperationPermission($profileId, $operation, $permission, $tabId);
            echo "<br>DONE!<br>";
        } elseif ($permission != $permission0) {
            echo "<br>operation $operation permissions not CORRECT for $profileName, CORRECTING...<br>";
            updateOperationPermission($profileId, $operation, $permission, $tabId);
            echo "<br>DONE!<br>";
        } else {
            echo "<br>operation $operation permissions already set for $profileName, SKIPPING...<br>";
        }
    }
    foreach ($activities as $activity => $permission) {
        $permission0 = getActivityPermission($profileId, $activity, $tabId);
        if ($permission0 === null) {
            echo "<br>activity $activity permissions not set for $profileName, CORRECTING...<br>";
            setActivityPermission($profileId, $activity, $permission, $tabId);
            echo "<br>DONE!<br>";
        } elseif ($permission0 != $permission) {
            echo "<br>activity $activity permissions not CORRECT for $profileName, CORRECTING...<br>";
            updateActivityPermission($profileId, $activity, $permission, $tabId);
            echo "<br>DONE!<br>";
        } else {
            echo "<br>activity $activity permissions already set for $profileName, SKIPPING...<br>";
        }
    }

    //check/set profile2tab permissions
    $sql = "SELECT permissions FROM `vtiger_profile2tab` WHERE profileid = ? AND tabid = ?";
    $result = $adb->pquery($sql, array($profileId, $tabId));
    $row = $result->fetchRow();
    $tabPermission = $row[0];

    if ($tabPermission === null) {
        echo "<br>tab permissions not set for $profileName, CORRECTING...<br>";
        $sql = 'INSERT INTO `vtiger_profile2tab` (profileid, tabid, permissions) VALUES (?, ?, ?)';
        $adb->pquery($sql, array($profileId, $tabId, 0));
        echo "<br>DONE!<br>";
    } elseif ($tabPermission != 0) {
        echo "<br>tab permissions not CORRECT for $profileName, CORRECTING...<br>";
        $sql = 'UPDATE `vtiger_profile2tab` SET (profileid, tabid, permissions) VALUES (?, ?, ?) WHERE profileid = ? AND tabid = ?';
        $adb->pquery($sql, array($profileId, $tabId, 0, $profileId, $tabId));
        echo "<br>DONE!<br>";
    } else {
        echo "<br>tab permissions already set for $profileName, SKIPPING...<br>";
    }

    foreach ($fields as $fieldId => $readOnly) {
        $permission0 = getFieldPermission($profileId, $fieldId, $readOnly, $tabId);
        if ($permission0 === null) {
            echo "<br>field $fieldId permissions not set for $profileName, CORRECTING...<br>";
            setFieldPermission($profileId, $fieldId, $readOnly, $tabId);
            echo "<br>DONE!<br>";
        } elseif ($permission0 != $permission) {
            echo "<br>field $fieldId permissions not CORRECT for $profileName, CORRECTING...<br>";
            updateFieldPermission($profileId, $fieldId, $readOnly, $tabId);
            echo "<br>DONE!<br>";
        } else {
            echo "<br>field $fieldId permissions already set for $profileName, SKIPPING...<br>";
        }
    }
    echo "<br>$moduleName permissions set for $profileName<br>";
}

function getActivityPermission($profileId, $activity, $tabId)
{
    $adb = PearDatabase::getInstance();
    $sql = "SELECT permission FROM `vtiger_profile2utility` WHERE profileid = ? AND tabid = ? AND activityid = ?";
    $result = $adb->pquery($sql, array($profileId, $tabId, $activity));
    $row = $result->fetchRow();
    return $row[0];
}

function setActivityPermission($profileId, $activity, $permission, $tabId)
{
    $adb = PearDatabase::getInstance();
    $sql = 'INSERT INTO `vtiger_profile2utility` (profileid, tabid, activityid, permission) VALUES (?, ?, ?, ?)';
    $adb->pquery($sql, array($profileId, $tabId, $activity, $permission));
}

function updateActivityPermission($profileId, $activity, $permission, $tabId)
{
    $adb = PearDatabase::getInstance();
    $sql = 'UPDATE `vtiger_profile2utility` SET profileid = ?, tabid = ?, activityid = ?, permission = ?
            WHERE profileid = ? AND tabid = ? AND activityid = ?';
    $adb->pquery($sql, array($profileId, $tabId, $activity, $permission, $profileId, $tabId, $activity));
}

function getOperationPermission($profileId, $operation, $tabId)
{
    $adb = PearDatabase::getInstance();
    $sql = "SELECT permissions FROM `vtiger_profile2standardpermissions` WHERE profileid = ? AND tabid = ? AND operation = ?";
    $result = $adb->pquery($sql, array($profileId, $tabId, $operation));
    $row = $result->fetchRow();
    return $row[0];
}

function setOperationPermission($profileId, $operation, $permission, $tabId)
{
    $adb = PearDatabase::getInstance();
    $sql = 'INSERT INTO `vtiger_profile2standardpermissions` (profileid, tabid, operation, permissions) VALUES (?, ?, ?, ?)';
    $adb->pquery($sql, array($profileId, $tabId, $operation, $permission));
}

function updateOperationPermission($profileId, $operation, $permission, $tabId)
{
    $adb = PearDatabase::getInstance();
    $sql = 'UPDATE `vtiger_profile2standardpermissions` SET profileid = ?, tabid = ?, operation = ?, permissions = ?
            WHERE profileid = ? AND tabid = ? AND operation = ? ';
    $adb->pquery($sql, array($profileId, $tabId, $operation, $permission, $profileId, $tabId, $operation));
}

function getFieldPermission($profileId, $fieldId, $tabId)
{
    $adb = PearDatabase::getInstance();
    $sql = "SELECT visible FROM `vtiger_profile2field` WHERE profileid = ? AND tabid = ? AND fieldid = ?";
    $result = $adb->pquery($sql, array($profileId, $tabId, $fieldId));
    $row = $result->fetchRow();
    return $row[0];
}

function setFieldPermission($profileId, $fieldId, $readOnly, $tabId)
{
    $adb = PearDatabase::getInstance();
    $sql = 'INSERT INTO `vtiger_profile2field` (profileid, tabid, fieldid, visible, readonly) VALUES (?, ?, ?, 0, ?)';
    $adb->pquery($sql, array($profileId, $tabId, $fieldId, $readOnly));
}

function updateFieldPermission($profileId, $fieldId, $readOnly, $tabId)
{
    $adb = PearDatabase::getInstance();
    $sql = 'UPDATE `vtiger_profile2field` SET profileid = ?, tabid = ?, fieldid = ?, visible = ?, readonly = ?
            WHERE profileid = ? AND tabid = ? AND fieldid = ?';
    $adb->pquery($sql, array($profileId, $tabId, $fieldId, $readOnly, $profileId, $tabId, $fieldId));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";