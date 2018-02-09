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
$Vtiger_Utils_Log = true;
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
global $adb;
$moduleInstance = Vtiger_Module::getInstance('Opportunities');
if ($moduleInstance) {
    $blockInstance = Vtiger_Block::getInstance('LBL_RECORD_UPDATE_INFORMATION', $moduleInstance);
    if ($blockInstance) {
        echo "<h3>The Record Update Information block already exists</h3><br> \n";
    } else {
        $blockInstance = new Vtiger_Block();
        $blockInstance->label = 'LBL_RECORD_UPDATE_INFORMATION';
        $moduleInstance->addBlock($blockInstance);
    }
}

//Date Created
$field1 = Vtiger_Field::getInstance('createdtime', $moduleInstance);
if ($field1) {
    echo "<li>The createdtime field already exists</li><br> \n";
    $adb->pquery("update `vtiger_field` set `block`=?,`displaytype`=? where `fieldid`=? ;", array($blockInstance->id,2, $field1->id));
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_OPPORTUNITIES_CREATEDTIME';
    $field1->name = 'createdtime';
    $field1->table = 'vtiger_crmentity';
    $field1->column = 'createdtime';
    $field1->uitype = 70;
    $field1->typeofdata = 'T~O';
    $field1->displaytype = 2;
    $blockInstance->addField($field1);
}

//Date Modified
$field2 = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);
if ($field2) {
    echo "<li>The modifiedtime field already exists</li><br> \n";
    $adb->pquery("update `vtiger_field` set `block`=?,`displaytype`=? where `fieldid`=? ;", array($blockInstance->id,2, $field2->id));
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_OPPORTUNITIES_MODIFIEDTIME';
    $field2->name = 'modifiedtime';
    $field2->table = 'vtiger_crmentity';
    $field2->column = 'modifiedtime';
    $field2->uitype = 70;
    $field2->typeofdata = 'T~O';
    $field1->displaytype = 2;
    $blockInstance->addField($field2);
}

//Created By
$field3 = Vtiger_Field::getInstance('createdby', $moduleInstance);
if ($field3) {
    echo "<li>The createdby field already exists</li><br> \n";
    $adb->pquery("update `vtiger_field` set `block`=?,`displaytype`=? where `fieldid`=? ;", array($blockInstance->id,2, $field3->id));
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_POTENTIALS_CREATEDBY';
    $field3->name = 'createdby';
    $field3->table = 'vtiger_crmentity';
    $field3->column = 'smcreatorid';
    $field3->uitype = 52;
    $field3->typeofdata = 'V~O';
    $field1->displaytype = 2;

    $blockInstance->addField($field3);
}


//add new field////////////////////////////////////
$blockInstance1 = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $moduleInstance);
if ($blockInstance1) {
    //Opportunity Status
    $field4 = Vtiger_Field::getInstance('opportunitystatus', $moduleInstance);
    if ($field4) {
        echo "<li>The opportunitystatus field already exists</li><br> \n";
    } else {
        $field4 = new Vtiger_Field();
        $field4->label = 'LBL_OPPORTUNITIES_STATUS';
        $field4->name = 'opportunitystatus';
        $field4->table = 'vtiger_potential';
        $field4->column = 'opportunitystatus';
        $field4->uitype = 16;
        $field4->columntype = 'varchar(100)';
        $field4->typeofdata = 'V~M';

        $blockInstance1->addField($field4);
        $field4->setPicklistValues(['New', 'Pending Estimate', 'Estimate Received', 'Cancelled']);
    }

    //contract
    $field5 = Vtiger_Field::getInstance('oppotunitiescontract', $moduleInstance);
    if ($field5) {
        echo "<li>The contract field already exists</li><br> \n";
    } else {
        $field5 = new Vtiger_Field();
        $field5->label = 'LBL_OPPORTUNITIES_CONTRACT';
        $field5->name = 'oppotunitiescontract';
        $field5->table = 'vtiger_potential';
        $field5->column = 'oppotunitiescontract';
        $field5->uitype = 10;
        $field5->columntype = 'varchar(100)';
        $field5->typeofdata = 'V~O';

        $blockInstance1->addField($field5);
        $field5->setRelatedModules(array('Contracts'));
    }

    //reason
    $field6 = Vtiger_Field::getInstance('opportunityreason', $moduleInstance);
    if ($field6) {
        echo "<li>The opportunitystatus field already exists</li><br> \n";
    } else {
        $field6 = new Vtiger_Field();
        $field6->label = 'LBL_OPPORTUNITIES_REASON';
        $field6->name = 'opportunityreason';
        $field6->table = 'vtiger_potential';
        $field6->column = 'opportunityreason';
        $field6->uitype = 16;
        $field6->columntype = 'varchar(255)';
        $field6->typeofdata = 'V~O';

        $blockInstance1->addField($field6);
        $field6->setPicklistValues(['value 1', 'value 2']);
    }
}

//remove mandatory Expected close date
$field7 = Vtiger_Field::getInstance('closingdate', $moduleInstance);
if ($field7) {
    $adb->pquery('UPDATE `vtiger_field` SET `typeofdata` =? WHERE `fieldid`=? ', array('D~O', $field7->id));
}

// remove mandatory Contract Name
$field8 = Vtiger_Field::getInstance('contact_id', $moduleInstance);
if ($field8) {
    $adb->pquery('UPDATE `vtiger_field` SET `typeofdata` =? WHERE `fieldid`=? ', array('V~O', $field8->id));
}
////////////////////////////////////////////////////////
//block Account detail
$blockAccountDetail = Vtiger_Block::getInstance('LBL_POTENTIALS_NATIONALACCOUNT', $moduleInstance);
if ($blockAccountDetail) {
    echo "<h3>The Account detail block already exists</h3><br> \n";
} else {
    $blockAccountDetail = new Vtiger_Block();
    $blockAccountDetail->label = 'LBL_POTENTIALS_NATIONALACCOUNT';
    $moduleInstance->addBlock($blockAccountDetail);
}


//national account number
$field9 = Vtiger_Field::getInstance('opportunities_nat_account_no', $moduleInstance);
if ($field9) {
    echo "<li>The opportunities_nat_account_no field already exists</li><br> \n";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_OPPORTUNITIES_NATIONAL_ACCOUNT_NUMBER';
    $field9->name = 'opportunities_nat_account_no';
    $field9->table = 'vtiger_potential';
    $field9->column = 'opportunities_nat_account_no';
    $field9->columntype = 'varchar(150)';
    $field9->uitype = 1;
    $field9->typeofdata = 'V~O';
    $field9->quickcreate = 1;
    $field9->summaryfield = 0;

    $blockAccountDetail->addField($field9);
}

$fieldCompetitive = Vtiger_Field::getInstance('is_competitive', $moduleInstance);
if ($fieldCompetitive) {
    echo "<li>The createdby field already exists</li><br> \n";
    if ($fieldCompetitive->getBlockId() != $blockInstance1->id) {
        $adb->pquery("update `vtiger_field` set `block`=? where `fieldid`=? ;", array($blockInstance1->id, $fieldCompetitive->id));
    }
}
/////// Rearrange fields to match this layout for Opportunity Information Block
$Fields = array(
    1 => 'potentialname',
    2 => 'contact_id',
    3 => 'opportunitystatus',
    4 => 'opportunityreason',
    5 => 'business_line2',
    6 => 'billing_type',
    7 => 'leadsource',
    8 => 'closingdate',
    9 => 'related_to',
    10 => 'oppotunitiescontract',
    11 => 'is_competitive',
    12 => 'agentid',
    13 => 'assigned_user_id',
    14 => 'business_line',
    15 => 'sales_stage',
    17 => 'nextstep',
    18 => 'forecast_amount',
    19 => 'isconvertedfromlead',
    20 => 'sales_person',
    21 => 'converted_from',
    23 => 'leadsource_workspace',
    24 => 'leadsource_national',
    25 => 'opportunities_vanline',
    26 => 'opportunities_reason',
);
foreach ($Fields as $k => $val) {
    $adb->pquery("UPDATE `vtiger_field` SET `sequence` = ? WHERE `fieldname`=? AND `tabid`=? AND `block`=?", array($k, $val, $moduleInstance->id, $blockInstance1->id));
}
//Remove old Oppt Status field
$adb->pquery("UPDATE `vtiger_field` SET `presence`='1' WHERE `fieldname`=? AND `tabid`=? ", array( 'sales_stage', $moduleInstance->id));

//Rearrange All Blocks to match this layout
$block = array(
    1 => 'LBL_POTENTIALS_INFORMATION',
    2 => 'LBL_POTENTIALS_NATIONALACCOUNT',
    3 => 'LBL_OPPORTUNITIES_ADDITIONAL_INFORMATION',
    4 => 'LBL_POTENTIALS_PARTICIPANTS',
    5 => 'LBL_POTENTIALS_ADDRESSDETAILS',
    6 => 'LBL_POTENTIALS_DATES',
    7 => 'LBL_OPPORTUNITY_EXTRASTOPS',
    8 => 'LBL_RECORD_UPDATE_INFORMATION',
);
foreach ($block as $key => $value) {
    $adb->pquery("UPDATE `vtiger_blocks` SET `sequence` = ? WHERE `tabid`=? AND `blocklabel`=?", array($key, $moduleInstance->id, $value));
}



// Rearrange All to match this layout
$AccountDetailField = array(
    1 => 'opportunities_nat_account_no',    
    2 => 'street',
    3 => 'pobox',
    4 => 'city',
    5 => 'state',
    6 => 'zip',
    7 => 'country',
);

foreach ($AccountDetailField as $k => $val) {
    $adb->pquery("UPDATE `vtiger_field` SET `sequence` = ? WHERE `fieldname`=? AND `tabid`=? AND `block`=?", array($k, $val, $moduleInstance->id, $blockAccountDetail->id));
}
//delete LBL_OPPORTUNITIES_HHG_INFORMATION block

//create column agentmanager_id in vtiger_leadsource table
$adb->pquery("ALTER TABLE `vtiger_opportunityreason` ADD `agentmanager_id` INT(11)");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";