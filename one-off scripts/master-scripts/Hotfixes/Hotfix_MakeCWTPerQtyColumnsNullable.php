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

// Add estimates table
echo "Modifying vtiger_quotes_cwtperqty so columns are nullable.<br/>\n";
$sql = 'ALTER TABLE `vtiger_quotes_cwtperqty` MODIFY COLUMN `quantity` INT(12) NULL, MODIFY COLUMN `rate` DECIMAL(10,2) NULL';
$result = $db->query($sql);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";