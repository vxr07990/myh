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


echo "Clearing out broken fields<br>\n";
$sql = "SELECT * FROM `vtiger_field` AS `a` WHERE NOT EXISTS(SELECT * FROM (SELECT * FROM `vtiger_field` GROUP BY tabid, columnname, fieldname) AS `b` WHERE `a`.fieldid = `b`.fieldid)";
$result = $db->pquery($sql, []);
while ($row =& $result->fetchRow()) {
    $sql2 = "DELETE FROM `vtiger_field` WHERE fieldid = ?";
    echo "Deleting Duplicate Field : ".$row['fieldid']."<br>\n";
    $db->pquery($sql2, [$row['fieldid']]);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";