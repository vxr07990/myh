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
 * update agent_ids to be text
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

foreach (['Users'] as $moduleName) {
    print "<h2>START update agent_ids column update for $moduleName module. </h2>\n";
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
    } else {
        $field_name = 'agent_ids';
        //$columntype = 'VARCHAR(10000) DEFAULT NULL';
        //after some research and debate decided TEXT, because a long varchar is handled the same as a text,
        //and in innodb the text is stored the same as varchar, so there's no advantage in using a long varchar.
        $columntype = 'TEXT';
        $field0     = Vtiger_Field::getInstance($field_name, $module);
        if ($field0) {
            $db   = PearDatabase::getInstance();
            echo "<li>Checking table definition for $field_name</li><br />\n";
            $stmt = 'EXPLAIN `'.$field0->table.'` `'.$field_name.'`';
            if ($res = $db->pquery($stmt)) {
                while ($value = $res->fetchRow()) {
                    if ($value['Field'] == $field_name) {
                        if (strtolower($value['Type']) != strtolower($columntype)) {
                            echo "Updating $field_name to be a ".$columntype." type.<br />\n";
                            $stmt = 'ALTER TABLE `'.$field0->table.'` MODIFY COLUMN `'.$field_name.'` '.$columntype;
                            echo "<li>running: $stmt</li><br />\n";
                            $db->pquery($stmt);
                        }
                        break;
                    }
                }
            } else {
                echo "NO $field_name column in The actual table?<br />\n";
            }
        } else {
            print "Failed to find: $field_name<br />\n";
        }
    }
    print "<h2>END update column for $moduleName module. </h2>\n";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";