<?php
if (function_exists("call_ms_function_ver")) {
    $version = 'AlwaysRun';
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


// OT 3235 - On Graebel, Business Line picklists should all draw from the same list. This will synchronize vtiger_business_line_est with vtiger_business_line

if (getenv('INSTANCE_NAME') == 'graebel') {
    if (!isset($db)) {
        $db = PearDatabase::getInstance();
    }
    $parentTable = 'vtiger_business_line';
    echo "<br>Starting clone of $parentTable<br/>";
    $current_values = [];
    //Get all current values for the business_line field
    $sql    = "SELECT * FROM `$parentTable`";
    $result = $db->pquery($sql, []);
    while ($row =& $result->fetchRow()) {
        $business_line    = $row['business_line'];
        $sortorderid    = $row['sortorderid'];
        $current_values[] = $business_line;
        $current_sort[] = $sortorderid;
    }
    //Field could be in any or all of these modules
    $moduleNames = ['Actuals', 'Estimates', 'Quotes'];
    $fieldName = 'business_line_est';
    foreach ($moduleNames as $moduleName) {
        $module = Vtiger_Module::getInstance($moduleName);
        $field = Vtiger_Field::getInstance($fieldName, $module);
        if ($field) {
            updatePicklistValuesSBLP($field, $current_values, $current_sort);
            //Only need to update the table once.
            break;
        }
        echo "<br>Unable to find $fieldName </br>";
    }
    echo "<br>Completed updating $tableName to match $parentTable<br/>";
}

function updatePicklistValuesSBLP($field, $pickList, $sort)
{
    $fieldName = $field->name;
    $tableName = 'vtiger_'.$fieldName;
    $db  = PearDatabase::getInstance();
    $sql = "TRUNCATE TABLE `$tableName`";
    $db->pquery($sql, []);
    $keyField = $fieldName.'id';
    $presenceValue = 1;
    foreach ($pickList as $index => $value) {
        $insertSql = 'INSERT INTO `'.$tableName.'` SET
                `presence` = ?,
                `'.$keyField.'` = ?,
                `'.$fieldName.'` = ?,
                `sortorderid` = ?';
        $db->pquery($insertSql, array($presenceValue, $index+1, $value, $sort[$index]));
    }
    //Update business_line_est_seq table to match max id of business_line_est table
    $db->pquery("UPDATE `vtiger_business_line_est_seq` SET id=?", array($index+1));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";