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



include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';

echo "<br>Begin quickswapping comments and special terms to be in info block (leads/opps)<br><br>";

$oppsModule = Vtiger_Module::getInstance('Opportunities');
$leadsModule = Vtiger_Module::getInstance('Leads');

$oppsInfo = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $oppsModule);
if (!$oppsInfo) {
    $oppsInfo = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $oppsModule);
}

$leadsInfo = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $leadsModule);

$leadsId=$leadsInfo->id;

$oppsId=$oppsInfo->id;

$db = PearDatabase::getInstance();

$result = $db->pquery('SELECT sequence FROM `vtiger_field` WHERE block = ? ORDER BY sequence DESC', array($leadsId));
$row = $result->fetchRow();
$leadSeq = $row[0];

$sql = 'UPDATE `vtiger_field` SET block = '.$leadsId.', sequence = '.$leadSeq.' WHERE fieldlabel = "LBL_LEADS_EMPLOYERCOMMENTS"';
Vtiger_Utils::ExecuteQuery($sql);

$leadSeq++;

$sql = 'UPDATE `vtiger_field` SET block = '.$leadsId.', sequence = '.$leadSeq.' WHERE fieldlabel = "LBL_LEADS_SPECIALTERMS"';
Vtiger_Utils::ExecuteQuery($sql);

$result = $db->pquery('SELECT sequence FROM `vtiger_field` WHERE block = ? ORDER BY sequence DESC', array($oppsId));
$row = $result->fetchRow();
$oppSeq = $row[0];

$sql = 'UPDATE `vtiger_field` SET block = '.$oppsId.', sequence = '.$oppSeq.' WHERE fieldlabel = "LBL_OPPORTUNITY_EMPLOYERCOMMENTS"';
Vtiger_Utils::ExecuteQuery($sql);

$oppSeq++;

$sql = 'UPDATE `vtiger_field` SET block = '.$oppsId.', sequence = '.$oppSeq.' WHERE fieldlabel = "LBL_OPPORTUNITY_SPECIALTERMS"';
Vtiger_Utils::ExecuteQuery($sql);

echo "<br>Quickswap Complete<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";