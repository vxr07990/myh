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
 * update workflow tasks to be LONGTEXT instead of TEXT
 * unfortunately workflows are not a module?  this is confusing.
 * but you can see that vtiger_tab is where they find the stuff for:
    $module = Vtiger_Module::getInstance($moduleName);
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

print "<h3>BEGIN UPDATE TO workflow's event task field</h3>";
$db = PearDatabase::getInstance();
$columntype = 'LONGTEXT';
$field_name = 'task';
$table = 'com_vtiger_workflowtasks';

$stmt = 'EXPLAIN `'.$table.'` `'.$field_name.'`';
if ($res = $db->pquery($stmt)) {
    while ($value = $res->fetchRow()) {
        if ($value['Field'] == $field_name) {
            if (strtolower($value['Type']) != strtolower($columntype)) {
                echo "Updating $field_name to be a ".$columntype." type.<br />\n";
                $stmt = 'ALTER TABLE `'.$table.'` MODIFY COLUMN `'.$field_name.'` '.$columntype;
                echo "<li>running: $stmt</li><br />\n";
                $db->pquery($stmt);
            }
            break;
        }
    }
} else {
    echo "NO $field_name column in The actual table?<br />\n";
}
print "End update to workflow's event task field<br />";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";