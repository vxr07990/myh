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
 * This will reorder the sales stage picklist;
 * Then it will update the tag: Sales Stage to Opportunity Disposition.
 */

$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
include_once 'modules/Settings/Picklist/models/Module.php';
include_once 'modules/Settings/Picklist/models/Field.php';

/*
$languageStrings['Prospecting']				= 'New';
$languageStrings['Qualification']			= 'Attempted Contact';
$languageStrings['Needs Analysis']			= 'Survey Scheduled';
$languageStrings['Value Proposition']		= 'Pending';
$languageStrings['Closed Won']				= 'Booked';
$languageStrings['Closed Lost']				= 'Lost';
$languageStrings['Id. Decision Makers']		= 'Inactive';
$languageStrings['Perception Analysis']		= 'Ready to Book';
$languageStrings['Proposal or Price Quote'] = 'Duplicate';
$languageStrings['Negotiation or Review'] 	= '';
*/

$moduleName = 'Opportunities'; //or Potentials
$picklistFieldName = 'sales_stage';

$salesStages = [
        'Prospecting',
        'Qualification',
        'Needs Analysis',
        'Value Proposition',
        'Closed Won',
        'Closed Lost',
        'Id. Decision Makers',
        'Perception Analysis',
        'Proposal or Price Quote',
        'Negotiation or Review',
    ];


print "<h2>Starting to update Sales Stage to Sirva specific order.</h2>";
//welp we're going to just kick it and hope for the wheels to turn!
foreach ($salesStages as $index => $value) {
    $selectSql = 'SELECT * FROM `vtiger_sales_stage` WHERE '
        . ' `sales_stage` = ? and `sortorderid` <> ?'
        . ' LIMIT 1';
    $selectResult = $db->pquery($selectSql, array($value, $index));

    if ($selectResult && $selectRow = $selectResult->fetchRow()) {
        $updateSql = 'UPDATE `vtiger_sales_stage` SET '
            . ' `sortorderid` = ?'
            . ' WHERE `sales_stage` = ?'
            . ' LIMIT 1';
        $db->pquery($updateSql, array($index, $value));
    } else {
        // if it's not there or not right it'll be entered but not here because sales_stage is tied to other logic
        // and that should be reviewed probably.
    }
}
print "<h2>End updating Sales Stage to Sirva specific order.</h2>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";