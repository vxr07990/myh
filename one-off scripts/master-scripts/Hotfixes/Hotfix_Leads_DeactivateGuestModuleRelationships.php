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

$moduleName = 'Leads';
$guestModules = ['ExtraStops', 'MoveRoles'];
$module = Vtiger_Module_Model::getInstance('Leads');

if(!$module){
    print "Unable to find $moduleName. Exiting. <br />\n ";
    return;
}

foreach($guestModules as $guest){
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_guestmodulerel` SET active = '0' WHERE hostmodule = '$moduleName' AND guestmodule = '$guest'");
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";