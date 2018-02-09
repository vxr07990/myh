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

//Just consolidating all the Claims 2.0 hotfixes so it's easier to track changes and make sense how this works.

// 1- Creating the new Claims Summary Module

include_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_ClaimsSummary.php';

//2- Updating Claims with the fields required to use as Claims Type module

include_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_Claims_ModuleUpdates.php';

//3- Adding new custom tables we need to store some custom information in this modules

include_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_Claims_AddNewBlocksTables.php';

//4- Updating Claims Items module to use as claim items (Sub module of Claims Types)

include_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_ClaimsItems_Updates.php';

//Remove claims from Menu. Now we will be linking claim summary from the main menu

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent='' WHERE name='Claims'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent='' WHERE name='ClaimItems'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent='CUSTOMER_SERVICE_TAB' WHERE name='ClaimsSummary'");


//Remove a related list we are no longer using
include_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_Claims_Remove_ClaimsClaimItem_RelatedList.php';

//Add new table for status table
include_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_Claims_Status_Table.php';

include_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Claims_FixOrdersRelatedList.php');

//Several one line updates
include_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Claims_Updates_201601109.php');

//OT17055 - Fix typo in picklist
include_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Claims_UpdatesStatusValues.php');

//OT17170 / OT17171 Adding Mod Comments & ModTracker to claims summary module
include_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Claims_AddModCommentsToClaimsSummary.php');

//OT17172 Summary grid for each claim type not displaying
include_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Claims_AddSummaryGridTable.php');

//OT17274 Fix fields in Claim Items Detail block for Fac/Res claim type
//OT17275 Fac/Res Items: Fix dropdown choices in floor type fields
//OT17277 Property claim items: Room fields should be free form fields
require_once "one-off scripts/master-scripts/Hotfixes/Hotfix_ClaimItem_UpdateClaimItemFields20161110.php";

//OT17173
require_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_ClaimItems_DailyExpense.php';

//OT17481
require_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_Claims_SPTable.php';
	
//OT17495 - Claims Distribution and Distribution date fields need to be editable
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Claims_UpdateSummaryGridTable.php');

//OT17542 - Calendar Days to Settle and Business Days to settle are not calculating
require_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_Claims_Workflow20170110.php';


/**

Claim Summary > Claims (This module is the main entry point. Just a summary of all items)
    - Claim Types (Cargo) (Former Claims module that was rename to claims types)
              - Claim Items (Piano)
                - Agent -> 20%
                - Agent -> 30%
                - Agent -> 50%
            - Claim Items (desk)
                - Agent
                - Agent
                - Agent
            - Claim Items (monitor)
                - Agent
                - Agent
                - Agent

    - Claim Types (Facility)
              - Claim Items  (broke elevator)
                - Agent
                - Agent
                - Agent
            - Claim Items  (broke Door)
                - Agent
                - Agent
                - Agent

*/


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";