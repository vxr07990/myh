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
 * VTexpert's related calendar solution required tables to store related lists.
 *
 * CREATE TABLE `opportunities_added_calendar` (
`id` int(11) AUTO_INCREMENT,
`userid` int(11),
`sharedid` int(11),
`color` varchar(30),
PRIMARY KEY (`id`)
);


CREATE TABLE `orders_added_calendar` (
`id` int(11) AUTO_INCREMENT,
`userid` int(11),
`sharedid` int(11),
`color` varchar(30),
PRIMARY KEY (`id`)
);
 *
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


if (!Vtiger_Utils::CheckTable('opportunities_added_calendar')) {
    Vtiger_Utils::CreateTable('opportunities_added_calendar',
                                  '(
            `id` int(11) AUTO_INCREMENT,
            `userid` int(11),
            `sharedid` int(11),
            `color` varchar(30),
            PRIMARY KEY (`id`)
            )', true);
}

if (!Vtiger_Utils::CheckTable('orders_added_calendar')) {
    Vtiger_Utils::CreateTable('orders_added_calendar',
                              '(
            `id` int(11) AUTO_INCREMENT,
            `userid` int(11),
            `sharedid` int(11),
            `color` varchar(30),
            PRIMARY KEY (`id`)
            )', true);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";