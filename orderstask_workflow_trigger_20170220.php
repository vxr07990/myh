<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 2/20/2017
 * Time: 1:19 PM
 */
//One time script to trigger a save on all orderstask records to trigger workflows that work off of saves

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "SKIPPING: " . __FILE__ . "<br />\n";
        return;
    }
}
print "RUNNING: " . __FILE__ . "<br />\n";

require_once 'vtlib/Vtiger/Menu.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'modules/ModTracker/ModTracker.php';
require_once 'modules/ModComments/ModComments.php';
require_once 'includes/main/WebUI.php';
require_once 'include/Webservices/Create.php';
require_once 'modules/Users/Users.php';
require_once 'vendor/autoload.php';



$moduleNames = [
    'OrdersTask'
];

$statusField = 'dispatch_status';
$testVal = 'Completed';


foreach($moduleNames as $moduleName){
    SaveEventOnAllModuleRecords($moduleName, $statusField, $testVal);
}

function SaveEventOnAllModuleRecords($moduleName, $statusField, $testVal)
{
    global $current_user;
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
    $db = PearDatabase::getInstance();
    $modifiedModuleName = strtolower($moduleName);
    $table = $db->escapeDBName('vtiger_'.$modifiedModuleName);
    $idField = $modifiedModuleName.'id';
    $sql = "SELECT $idField from $table WHERE $statusField = ?";
    $i = 0;
    $result = $db->pquery($sql, [$testVal]);
    while ($row = $result->fetchRow()) {
        try {
            $task = Vtiger_Record_Model::getInstanceById($row[$idField], $moduleName);
            if ($task) {
                $i++;
                $task->set('mode', 'edit');
                $task->save();
            }
        } catch (Exception $e){
            print "Failed to load record number ".$e->getMessage()."<br />\n";
        }
    }
    print "Triggered save on $i records. <br />\n";
}


print "FINISHED: " . __FILE__ . "<br />\n";
