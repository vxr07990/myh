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



//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

function newLeadstatusEntry($name, $sequence)
{
    $db = PearDatabase::getInstance();
    Vtiger_Utils::ExecuteQuery('UPDATE vtiger_leadstatus_seq SET id = id + 1');
    Vtiger_Utils::ExecuteQuery('UPDATE vtiger_picklistvalues_seq SET id = id + 1');
    echo "<br>updated sequence tables<br>";
    $result = $db->pquery('SELECT id FROM `vtiger_leadstatus_seq`', array());
    $row = $result->fetchRow();
    $leadStatusId = $row[0];
    echo "<br>lead status id set: ".$leadStatusId."<br>";
    $result = $db->pquery('SELECT id FROM `vtiger_picklistvalues_seq`', array());
    $row = $result->fetchRow();
    $picklistValueId = $row[0];
    echo "<br>picklist value id set: ".$picklistValueId."<br>";
    $sql = 'INSERT INTO `vtiger_leadstatus` (leadstatusid, leadstatus, presence, picklist_valueid, sortorderid) VALUES (?, ?, 1, ?, ?)';
    $db->pquery($sql, array($leadStatusId, $name, $picklistValueId, $sequence));
}

echo "<br>Begin Sirva job status (lead disposition) hotfix<br>";

$newStatuses = ['New', 'Attempted Contact', 'Fax/Busy', 'No Answer', 'Left Voicemail', 'Prefer Call Back', 'Do Not Call Requested', 'Wrong/Disconnected #', 'Survey Scheduled', 'Completed', 'Pending', 'Booked', 'Inactive', 'Lost', 'Duplicate'];

if (Vtiger_Utils::CheckTable('vtiger_leadstatus')) {
    echo "<br>vtiger_leadstatus exists! Truncating...<br>";
    Vtiger_Utils::ExecuteQuery('TRUNCATE TABLE `vtiger_leadstatus`');
    echo "<br>completed truncating...adding Sirva specific job statuses<br>";
    foreach ($newStatuses as $index => $status) {
        echo "<br> adding status: ".$status;
        newLeadstatusEntry($status, $index+1);
    }
} else {
    echo "<br>vtiger_leadstatus not found! No action taken<br>";
}

$leadsModule = Vtiger_Module::getInstance('Leads');
if ($leadsModule) {
    $leadsInfo = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $leadsModule);
    if ($leadsInfo) {
        echo "<br> block 'LBL_LEADS_INFORMATION' exists, attempting to alter lead status label<br>";
        $leadStatus = Vtiger_Field::getInstance('leadstatus', $leadsModule);
        if ($leadStatus) {
            echo "<br>lead status exists continuing with label swap<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_LEADDISPOSITION', typeofdata = 'V~M' WHERE fieldlabel = 'LBL_LEADS_LEADSTATUS'");
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'V~M', defaultvalue = 'New' WHERE fieldname = 'leadstatus'");
            echo "<br>lead status label swap done<br>";
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";