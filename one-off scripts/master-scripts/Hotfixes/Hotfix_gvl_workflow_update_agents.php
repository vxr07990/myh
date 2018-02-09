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
 *
 *Goals:
 * update workflow agent to be text because it's varchar(100)
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$tablename = 'com_vtiger_workflows';
$field_name = 'agents';
//$columntype = 'VARCHAR(10000) DEFAULT NULL';
//after some research and debate decided TEXT, because a long varchar is handled the same as a text,
//and in innodb the text is stored the same as varchar, so there's no advantage in using a long varchar.
$columntype = 'TEXT';
$db   = PearDatabase::getInstance();
echo "<li>Checking $tablename definition for $field_name</li><br />\n";
$stmt = 'EXPLAIN `'.$tablename.'` `'.$field_name.'`';
if ($res = $db->pquery($stmt)) {
    while ($value = $res->fetchRow()) {
        if ($value['Field'] == $field_name) {
            if (strtolower($value['Type']) != strtolower($columntype)) {
                echo "Updating $field_name to be a ".$columntype." type.<br />\n";
                $stmt = 'ALTER TABLE `'.$tablename.'` MODIFY COLUMN `'.$field_name.'` '.$columntype;
                echo "<li>running: $stmt</li><br />\n";
                $db->pquery($stmt);
            }
            break;
        }
    }
} else {
    echo "NO $field_name column in The actual table?<br />\n";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";