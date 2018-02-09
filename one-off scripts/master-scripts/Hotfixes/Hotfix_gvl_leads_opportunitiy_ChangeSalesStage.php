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
 */

//I am taking this from some sirva specific thing and making it for GVL.
//as it points out sales_stage is stupidly tied all OVER backend logic.
//so language file only updates.

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
/*
 * //TODO: so somebody has already changed these, god knows
Qualified Prospect
Developing Proposal
Submitted Proposal
Best and Final
Closed Abandoned
Closed Lost
Closed Won
Closed Trading
*/

$moduleName = 'Opportunities'; //or Potentials
$picklistFieldName = 'sales_stage';
$salesStages = [
        'Qualified Prospect'  => 1,
        'Developing Proposal' => 2,
        'Submitted Proposal'  => 3,
        'Best and Final'      => 4,
        'Closed Won'          => 5,
        'Closed Trading'      => 6,
        'Closed Lost'         => 7,
        'Closed Abandoned'    => 8
    ];


print "<h2>Starting to update Sales Stage to GVL specific order.</h2>";
//welp we're going to just kick it and hope for the wheels to turn!
$db = PearDatabase::getInstance();
foreach ($salesStages as $value => $index) {
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
print "<h2>End updating Sales Stage to GVL specific order.</h2>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";