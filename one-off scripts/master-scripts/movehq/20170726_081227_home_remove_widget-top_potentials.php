<?php

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}

global $adb;

// OT18936 Widget - Top Opportunities - Please turn off

$tabid = getTabid('Home');
$linkType = 'DASHBOARDWIDGET';
$linkLabel = 'Top Potentials';
$adb->pquery("DELETE FROM `vtiger_links` WHERE `tabid`=? AND `linktype`=? AND `linklabel`=?", array($tabid,$linkType,$linkLabel));

print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

