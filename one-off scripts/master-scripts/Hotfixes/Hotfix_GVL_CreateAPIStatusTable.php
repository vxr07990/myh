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


/**
 * create a table to hold api responses for records and so we can see when we've sent one already.
 */


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$tablename = 'vtiger_api_responses';

if (!Vtiger_Utils::CheckTable($tablename)) {
    print "creating $tablename for APIs <br />\n";
    $db = PearDatabase::getInstance();
    $stmt = 'CREATE TABLE `' . $tablename . '` (
    `api_response_id` int(11) UNSIGNED AUTO_INCREMENT,
    `record_id` int(11) UNSIGNED,
    `method` varchar (55),
    `url` varchar(255),
    `payload` text,
    `response_code` varchar(55),
    `response_body` text,
    `post_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` VARCHAR (50),
    primary key(`api_response_id`),
    key `record_id` (`record_id`),
    key `method` (`method`),
    key `status` (`status`)
    )';
    $db->pquery($stmt);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";