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
 * Created by PhpStorm.
 * User: asmith
 * Date: 9/2/2015
 * Time: 1:48 PM
 */
//move widgets that are pointing to Potentials to Opportunities so Opportunities Dashboard will have usable widgets
echo "<br>starting decoupling widgets";
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_links` SET tabid=60 WHERE linktype = 'DASHBOARDWIDGET' AND tabid = 2");
echo "<br>moved from Potentials to Opportunities dashboard";
//change all the linkurls to point at Opportunities instead of Potentials
$sql = "SELECT linkid, linkurl FROM `vtiger_links` WHERE linktype = 'DASHBOARDWIDGET' AND (tabid=60 OR tabid=3)";
$result = $db->pquery($sql, []);
while ($row =& $result->fetchRow()) {
    $pos = false;
    //only replacing the first instance of Potentials because one of the Widgets is called TopPotentials and won't work if renamed.
    if (strpos($row['linkurl'], 'Opportunities') === false) {
        $pos = strpos($row['linkurl'], 'Potentials');
    }
    if ($pos !== false) {
        $replacedLinkUrl = substr_replace($row['linkurl'], 'Opportunities', $pos, strlen('Potentials'));
    } else {
        $replacedLinkUrl = $row['linkurl'];
    }
    if ($replacedLinkUrl != $row['linkurl']) {
        echo "<br><h1>UPDATE `vtiger_links` SET linkurl = '{$replacedLinkUrl}' WHERE linkid = {$row['linkid']}</h1>";
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_links` SET linkurl = '{$replacedLinkUrl}' WHERE linkid = {$row['linkid']}");
    }
}
//remove Leads by Industry since we no longer have the Industry field in Leads
Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_links` WHERE linktype = 'DASHBOARDWIDGET' AND linklabel = 'Leads by Industry'");
echo "<br>Finished DecouplingWidgets hotfix";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";