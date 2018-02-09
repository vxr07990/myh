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

if (!Vtiger_Utils::CheckTable('vtiger_session_table')) {
    echo "<li>creating vtiger_session_table </li><br>";
    Vtiger_Utils::CreateTable('vtiger_session_table',
                              '(user_id INT(10) NOT NULL UNIQUE,
                                session_id VARCHAR(30),
                                timestamp DATETIME
                               )', true);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";