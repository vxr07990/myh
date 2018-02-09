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


// OT 16109, display first and last names in Employee Field in Move Roles. Pulls from Employees entity. New Employees set up correctly.
// Need to update labels in vtiger_crmentity to make sure Employees conform to firstname/lastname labels for search results.

$db = PearDatabase::getInstance();
$sql = 'SELECT * FROM `vtiger_crmentity` WHERE `setype`=?';
$query = $db->pquery($sql, ['Employees']);
echo "Updating labels in vtiger_crmentity for Employee search fields";
if (method_exists($query, 'fetchRow')) {
    echo "Table found. Updating labels.";
    while ($row = $query->fetchRow()) {
        try {
            if ($employeeRecord = Vtiger_Record_Model::getInstanceById($row['crmid'], 'Employees')) {
                $firstname = $employeeRecord->get('name');
                $lastname  = $employeeRecord->get('employee_lastname');
                $label     = $firstname.' '.$lastname;
                if ($label != $row['label']) {
                    $sql = 'UPDATE `vtiger_crmentity` SET `label` = ? WHERE `crmid`=?';
            //file_put_contents('logs/devLog.log', "\n MM HERE (Hotfix_GVL_UpdateLabelsForEmployeesInCrmentityTable.php:".__LINE__."): s (".$sql.")", FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n MM HERE (Hotfix_GVL_UpdateLabelsForEmployeesInCrmentityTable.php:".__LINE__."): r (".$label.")", FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n MM HERE (Hotfix_GVL_UpdateLabelsForEmployeesInCrmentityTable.php:".__LINE__."): row['crmid'] (".$row['crmid'].")", FILE_APPEND);
            $db->pquery($sql, [$label, $row['crmid']]);
                }
            }
        } catch (Exception $ex) {
            print $ex->getMessage();
        }
    }
}
echo "End updating labels in vtiger_crmentity<br/> \n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";