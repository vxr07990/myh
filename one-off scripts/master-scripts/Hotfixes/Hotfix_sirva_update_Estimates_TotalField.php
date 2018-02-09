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

echo "<br><h1>Starting Removal of Business Line from account_salespersons table</h1><br>\n";

$db = PearDatabase::getInstance();

$fieldtable = 'vtiger_quotes';
$field_name = 'total';
$newType = 'decimal(25,2)';

$stmt = 'EXPLAIN `'.$fieldtable.'` `'.$field_name.'`';
if ($res = $db->query($stmt)) {
    while ($value = $res->fetchRow()) {
        if ($value['Field'] == $field_name) {
            if (strtolower($value['Type']) != strtolower($newType)) {
                echo "Updating $field_name to be a " . $newType . " type.<br />\n";
                $stmt = 'ALTER TABLE `' . $fieldtable . '` MODIFY COLUMN `' . $field_name . '` ' . $newType . ' DEFAULT NULL';
                $db->query($stmt);
            }
            //we're only affecting the $field_name so if we find it just break
            break;
        }
    }
} else {
    echo "NO $field_name column in The actual table?<br />\n";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";