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


if(!Vtiger_Utils::CheckTable('twilio_accountmap')) {
    Vtiger_Utils::CreateTable('twilio_accountmap',
                              '(
                                  `vanlinemanagerid` int(11) NOT NULL,
                                  `account_sid` VARCHAR(50) NOT NULL,
                                  `auth_token` VARCHAR(50) NOT NULL,
                                  PRIMARY KEY(`vanlinemanagerid`),
                                  UNIQUE KEY(`account_sid`)
                              )', true);
}

if(!Vtiger_Utils::CheckTable('twilio_servicemap')) {
    Vtiger_Utils::CreateTable('twilio_servicemap',
                              '(
                                  `agentmanagerid` int(11) NOT NULL,
                                  `service_sid` VARCHAR(50) NOT NULL,
                                  PRIMARY KEY(`agentmanagerid`),
                                  UNIQUE KEY(`service_sid`)
                              )', true);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
