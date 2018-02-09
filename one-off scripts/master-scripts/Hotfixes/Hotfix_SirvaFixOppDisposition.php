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
 * Goal is to update the opportunity disposition that says Mobe to Move.
 */

$Vtiger_Utils_Log = true;

//this is for OT1884 and OT13404

//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');
//include_once('modules/ModTracker/ModTracker.php');

echo "<h2>Begin SIRVA Opportunity Disposition Picklist Hotfix To correct fields</h2>";
//encap in a function to avoid and name scope issues.
SirvaFixOpportunityDispositions();
echo "<h2>END SIRVA Opportunity Disposition Picklist Hotfix To correct typo</h2>";

function SirvaFixOpportunityDispositions()
{
    $opportunityDispositions = [
                                    'New',
                                    'Attempted Contact',
                                    'Survey Scheduled',
                                    'Pending',
                                    'Booked',
                                    'Inactive',
                                    'Lost',
                                    'Ready to Book',
                                    'Duplicate',
                                ];

    $opportunityDetailDispositions = [
                                    'Appointment Cancelled',
                                    'Capacity/Scheduling',
                                    'Incomplete Customer Info',
                                    'Move Date Has Passed',
                                    'Move too Small',
                                    'Moving Themselves',
                                    'National Account Move',
                                    'No Contact',
                                    'No Longer Moving',
                                    'Not Serviceable',
                                    'Other',
                                    'Out of Time',
                                    'Past Experience',
                                    'Pricing'
                                    ];

    $oppsModule = Vtiger_Module::getInstance('Opportunities');

    //Update picklist for the Opportunity_disposition
    $oppDisposition = Vtiger_Field::getInstance('opportunity_disposition', $oppsModule);
    if ($oppDisposition) {
        echo "<br> Field 'opportunity_disposition' is already present. Updating picklist values.<br>";
        Vtiger_Utils::ExecuteQuery('TRUNCATE TABLE `vtiger_opportunity_disposition`');
        $oppDisposition->setPicklistValues($opportunityDispositions);
    } else {
        echo "<br> Field 'opportunity_disposition' not present. Creating it now<br>";
        $oppDisposition = new Vtiger_Field();
        $oppDisposition->label = 'LBL_OPPORTUNITY_OPPORTUNITYDISPOSITION';
        $oppDisposition->name = 'opportunity_disposition';
        $oppDisposition->table = 'vtiger_potential';
        $oppDisposition->column = 'opportunity_disposition';
        $oppDisposition->columntype = 'VARCHAR(255)';
        $oppDisposition->uitype = 16;
        $oppDisposition->typeofdata = 'V~O';
        $oppDisposition->quickcreate = 0;

        $oppsInfo->addField($oppDisposition);
        $oppDisposition->setPicklistValues($opportunityDispositions);
        echo "<br> Field 'opportunity_disposition' added.<br>";
    }

    //update picklist for opportunity detail disposition
    $oppDetailDisposition = Vtiger_Field::getInstance('opportunity_detail_disposition', $oppsModule);
    if ($oppDetailDisposition) {
        echo "<br> Field 'opportunity_detail_disposition' is already present. Updating picklist values.<br>";
        Vtiger_Utils::ExecuteQuery('TRUNCATE TABLE `vtiger_opportunity_detail_disposition`');
        $oppDetailDisposition->setPicklistValues($opportunityDetailDispositions);
    } else {
        echo "<br> Field 'opportunity_detail_disposition' not present. Creating it now<br>";
        $oppDetailDisposition = new Vtiger_Field();
        $oppDetailDisposition->label = 'LBL_OPPORTUNITY_OPPORTUNITYDETAILDISPOSITION';
        $oppDetailDisposition->name = 'opportunity_detail_disposition';
        $oppDetailDisposition->table = 'vtiger_potential';
        $oppDetailDisposition->column = 'opportunity_detail_disposition';
        $oppDetailDisposition->columntype = 'VARCHAR(255)';
        $oppDetailDisposition->uitype = 16;
        $oppDetailDisposition->typeofdata = 'V~O';
        $oppDetailDisposition->quickcreate = 0;

        $oppsInfo->addField($oppDetailDisposition);
        $oppDetailDisposition->setPicklistValues($opportunityDetailDispositions);
        echo "<br> Field 'opportunity_detail_disposition' added.<br>";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";