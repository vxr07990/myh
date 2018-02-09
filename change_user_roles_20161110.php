<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 11/10/2016
 * Time: 12:23 PM
 */

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

require_once 'vtlib/Vtiger/Menu.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'modules/ModTracker/ModTracker.php';
require_once 'modules/ModComments/ModComments.php';
require_once 'includes/main/WebUI.php';
require_once 'include/Webservices/Create.php';
require_once 'modules/Users/Users.php';
require_once 'vendor/autoload.php';

const FAIL = 0;
const SUCCESS = 1;
const PREV_DONE = 2;

$filePath = 'go_live/gvl/userList20161110.csv';

if (!file_exists($filePath)) {
    //no file?
    return;
}


$itemList = fopen($filePath, 'r');
$userTable = 'vtiger_users';
$roleTable = 'vtiger_user2role';
$failedAccounts = [];
$successRecords = 0;
$didNotNeedToChange = 0;
$csvRow = [];
$supportManagerRole = 'H10';

while (!feof($itemList)) {
    $userId = false;
    $csvRow = fgetcsv($itemList);
    if ($csvRow[2] != 'placeholder') {
        $userId = findAccountByEmail($csvRow[2], $userTable);
    }
    if (!$userId) {
        $userId = findAccountByName($csvRow[0], $csvRow[1], $userTable);
        if (!$userId) {
            $failedAccounts[] = "$csvRow[0], $csvRow[1]";
            continue;
        }
    }
    $success = updateAccountRole($userId, $roleTable, $supportManagerRole);
    if ($success == FAIL) {
        $failedAccounts[] = "$csvRow[0], $csvRow[1]";
    } elseif ($success == PREV_DONE) {
        $didNotNeedToChange++;
    } else {
        $successRecords++;
    }
}

echo "Successfully changed $successRecords accounts.\n $didNotNeedToChange records were already changed previously.\n";
echo "Failed to find the following accounts(missing from table or multiple accounts with same name and email):\n";
foreach ($failedAccounts as $account) {
    echo "$account \n";
}

function findAccountByEmail($email, $table)
{
    $db = PearDatabase::getInstance();
    $Id = false;
    $sql = 'SELECT id FROM `'.$table.'` WHERE email1 = ?';
    $result = $db->pquery($sql, [$email]);
    if ($db->num_rows($result) == 1) {
        $row = $result->fetchRow();
        $Id = $row[0];
    }
    return $Id;
}

function findAccountByName($lastname, $firstname, $table)
{
    $db = PearDatabase::getInstance();
    $Id = false;
    $sql = 'SELECT id FROM `'.$table.'` WHERE last_name = ? AND first_name = ?';
    $result = $db->pquery($sql, [$lastname, $firstname]);
    if ($db->num_rows($result) == 1) {
        $row = $result->fetchRow();
        $Id = $row[0];
    }
    return $Id;
}

function updateAccountRole($userId, $table, $newRole)
{
    $success = FAIL;
    $db = PearDatabase::getInstance();
    $sql = 'SELECT * from `'.$table.'` WHERE userid = ?';
    while ($result = $db->pquery($sql, [$userId])) {
        $row = $result->fetchRow();
        if (!$row) {
            return $success;
        }
        $roleCode = $row['roleid'];
        if ($roleCode == $newRole) {
            $success = PREV_DONE;
        } else {
            $alterSQL = 'UPDATE `'.$table.'` SET roleid = ? WHERE userid = ?';
            $db->pquery($alterSQL, [$newRole, $userId ]);
            $success = SUCCESS;
        }
        break;
    }
    return $success;
}
