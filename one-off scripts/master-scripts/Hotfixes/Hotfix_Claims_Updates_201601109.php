<?php

if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


$db = PearDatabase::getInstance();

//OT17247 - Take out dropdown list in exceptions field and make a free form field
Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET uitype=2 WHERE columnname='claimitemsdetails_exceptions' AND tablename='vtiger_claimitems'");

//OT17248 - Format 3 fields as dollar amount fields
Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET uitype=71 WHERE (columnname='claimitemsdetails_originalcost' OR columnname='claimitemsdetails_replacementcost' OR columnname='claimitemsdetails_amount') AND tablename='vtiger_claimitems'");

//OT17249 - Inventory number should not be a required field
Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET typeofdata='V~O' WHERE columnname='inventory_number' AND tablename='vtiger_claimitems'");

//OT17251 - Claim Number sequencing

$result = $db->pquery('SELECT * FROM vtiger_modentity_num WHERE semodule=? AND start_id!=?', array('ClaimsSummary', 10000000));
if ($result && $db->num_rows($result) > 0) {
    $resultClaims = $db->pquery('SELECT claimssummaryid FROM vtiger_claimssummary INNER JOIN vtiger_crmentity ON vtiger_claimssummary.claimssummaryid = vtiger_crmentity.crmid 
				    WHERE deleted=0');
    if ($resultClaims && $db->num_rows($resultClaims) > 0) {
        $cur_id = 10000000;
        while ($row = $db->fetch_row($resultClaims)) {
            $db->pquery('UPDATE vtiger_claimssummary SET claimssummary_claimssummary=? WHERE claimssummaryid=?', array($cur_id, $row['claimssummaryid']));
            $cur_id = $cur_id + 1;
        }
    
        $db->pquery("UPDATE vtiger_modentity_num SET prefix='', start_id=?, cur_id=? WHERE semodule=?", array(10000000, $cur_id, 'ClaimsSummary'));
    } else {
        $db->pquery("UPDATE vtiger_modentity_num SET prefix='', start_id=?, cur_id=? WHERE semodule=?", array(10000000, 10000000, 'ClaimsSummary'));
    }
}

//OT17280 - Add the business line field to claims main screen

$claimsSummaryInstance = Vtiger_Module::getInstance('ClaimsSummary');
$claimsBlock = Vtiger_Block::getInstance('LBL_CLAIMSSUMMARY_INFORMATION', $claimsSummaryInstance);
$bl_field = Vtiger_Field::getInstance('business_line', $claimsSummaryInstance);

if (!$bl_field) {
    $bl_field = new Vtiger_Field();
    $bl_field->label = 'Business Line';
    $bl_field->name = 'business_line';  //Use the same name to pickup the existing dropdown table
    $bl_field->table = 'vtiger_claimssummary';
    $bl_field->column = 'claimssummary_businessline';
    $bl_field->columntype = 'VARCHAR(150)';
    $bl_field->uitype = 16;
    $bl_field->typeofdata = 'V~O';
    $claimsBlock->addField($bl_field);
}

//OT17250 - Add the related module, Documents under the Claims module

$moduleInstance = Vtiger_Module::getInstance('ClaimsSummary');
$docsInstance = Vtiger_Module::getInstance('Documents');

$db = PearDatabase::getInstance();
$result = $db->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=?", array($moduleInstance->id, $docsInstance->id));

if ($result && !$db->num_rows($result)) {
    $moduleInstance->setRelatedList($docsInstance, 'Documents', array('ADD,SELECT'), 'get_attachments');
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";