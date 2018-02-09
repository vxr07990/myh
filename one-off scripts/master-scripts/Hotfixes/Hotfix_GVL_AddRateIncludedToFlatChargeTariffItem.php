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


//OT 16438 Adding checkbox to make flat charges optional

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = PearDatabase::getInstance();

$stmt = 'SELECT * FROM information_schema.columns WHERE table_schema = "'.getenv('DB_NAME').'" AND table_name = "vtiger_quotes_perunit" AND column_name = "rate_included" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'vtiger_quotes_perunit rate_included column already exists'.PHP_EOL;
} else {
    $stmt = 'ALTER TABLE `vtiger_quotes_perunit` ADD COLUMN `rate_included` BIT(1) DEFAULT 0';
    $db->pquery($stmt);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";