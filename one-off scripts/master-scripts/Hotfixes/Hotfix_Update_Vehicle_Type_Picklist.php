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


Vtiger_Utils::ExecuteQuery('TRUNCATE TABLE `vtiger_vehicle_type`;');
Vtiger_Utils::ExecuteQuery('INSERT INTO `vtiger_vehicle_type`(`vehicle_typeid`, `vehicle_type`, `sortorderid`) VALUES (NULL, \'Cube Van\', 2), (NULL, 
\'Double Trailer\', 3), (NULL, \'Drop Trailer\', 4), (NULL, \'Flatbed Trailer\', 5), (NULL, \'Flat Trailer\', 6), (NULL, \'Freight Trailer\', 7), (NULL, \'Pack Van\', 8), (NULL, \'Pallet Trailer\', 9), (NULL, \'Passenger Van\', 10), (NULL, \'Straight Truck\', 11), (NULL, \'Tractor\', 12), (NULL, \'Truck\', 13);');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";