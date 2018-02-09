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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$Vtiger_Utils_Log = true;
global $adb;


$moduleInstance = Vtiger_Module::getInstance('MilitaryBases');
if ($moduleInstance) {
    echo "MilitaryBases Module exists<br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = "MilitaryBases";
    $moduleInstance->save();
    $moduleInstance->initTables();
    // Sharing Access Setup
    $moduleInstance->setDefaultSharing();
    // Webservice Setup
    $moduleInstance->initWebservice();
}


$blockInstance1 = Vtiger_Block::getInstance('LBL_MILITARY_BASES_INFORMATION', $moduleInstance);
if ($blockInstance1) {
    echo "<li>The LBL_MILITARY_BASES_BASE_INFORMATION block already exists</li><br>";
} else {
    $blockInstance1 = new Vtiger_Block();
    $blockInstance1->label = 'LBL_MILITARY_BASES_INFORMATION';
    $moduleInstance->addBlock($blockInstance1);
}

$blockInstance2 = Vtiger_Block::getInstance('LBL_RECORD_UPDATE_INFORMATION', $moduleInstance);
if ($blockInstance2) {
    echo "<li>The LBL_RECORD_UPDATE_INFORMATION block already exists</li><br>";
} else {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_RECORD_UPDATE_INFORMATION';
    $moduleInstance->addBlock($blockInstance2);
}



$field1block1 = Vtiger_Field::getInstance('gbloc', $moduleInstance);
if ($field1block1) {
    echo "<li>The 'GBLOC' field already exists</li><br>";
} else {
    $field1block1 = new Vtiger_Field();
    $field1block1->label = 'LBL_MILITARY_BASES_GBLOC';
    $field1block1->name = 'gbloc';
    $field1block1->table = 'vtiger_militarybases';
    $field1block1->column = 'gbloc';
    $field1block1->columntype = 'VARCHAR(10)';
    $field1block1->uitype = 1;
    $field1block1->typeofdata = 'V~M';
    $field1block1->sequence = 1;

    $blockInstance1->addField($field1block1);
    $moduleInstance->setEntityIdentifier($field1block1);
    echo "<li>The GBLOC field created done</li><br>";
}

$field2block1 = Vtiger_Field::getInstance('agentid', $moduleInstance);
if ($field2block1) {
    echo "<br> Field 'agentid' is already present <br>";
}else{
    $field2block1 = new Vtiger_Field();
    $field2block1->label = 'Owner';
    $field2block1->name = 'agentid';
    $field2block1->table = 'vtiger_crmentity';
    $field2block1->column = 'agentid';
    $field2block1->uitype = 1002;
    $field2block1->typeofdata = 'I~M';
    $field2block1->sequence = 2;

    $blockInstance1->addField($field2block1);
    echo "<br> Field 'agentid' is created <br>";
}

$field3block1 = Vtiger_Field::getInstance('active', $moduleInstance);
if ($field3block1) {
    echo "<br> Field 'active' is already present <br>";
} else {
    $field3block1 = new Vtiger_Field();
    $field3block1->label = 'LBL_MILITARY_BASES_ACTIVE';
    $field3block1->name = 'active';
    $field3block1->table = 'vtiger_militarybases';
    $field3block1->column = 'active';
    $field3block1->columntype = 'VARCHAR(10)';
    $field3block1->uitype = 16;
    $field3block1->typeofdata = 'V~M';
    $field3block1->sequence = 3;
    $field3block1->defaultvalue = 'Active';
    $field3block1->setPicklistValues(array('Active','Inactive'));
    $blockInstance1->addField($field3block1);

    echo "<br>Created field 'active' done<br>";
}

$field4block1 = Vtiger_Field::getInstance('location', $moduleInstance);
if ($field4block1) {
    echo "<li>The 'location' field already exists</li><br>";
} else {
    $field4block1 = new Vtiger_Field();
    $field4block1->label = 'LBL_MILITARY_BASES_LOCATION';
    $field4block1->name = 'location';
    $field4block1->table = 'vtiger_militarybases';
    $field4block1->column = 'location';
    $field4block1->columntype = 'VARCHAR(250)';
    $field4block1->uitype = 1;
    $field4block1->typeofdata = 'V~O';
    $field4block1->sequence = 4;

    $blockInstance1->addField($field4block1);
    echo "<li>The 'location' field created done</li><br>";
}

$field5block1 = Vtiger_Field::getInstance('inbound_email', $moduleInstance);
if ($field5block1) {
    echo "<li>The 'inbound_email' field already exists</li><br>";
} else {
    $field5block1 = new Vtiger_Field();
    $field5block1->label = 'LBL_MILITARY_BASES_INBOUND_EMAIL';
    $field5block1->name = 'inbound_email';
    $field5block1->table = 'vtiger_militarybases';
    $field5block1->column = 'inbound_email';
    $field5block1->columntype = 'VARCHAR(250)';
    $field5block1->uitype = 13;
    $field5block1->typeofdata = 'E~O';
    $field5block1->sequence = 5;

    $blockInstance1->addField($field5block1);
    echo "<li>The 'inbound_email' field created done</li><br>";
}

$field6block1 = Vtiger_Field::getInstance('outbound_email', $moduleInstance);
if ($field6block1) {
    echo "<li>The 'outbound_email' field already exists</li><br>";
} else {
    $field6block1 = new Vtiger_Field();
    $field6block1->label = 'LBL_MILITARY_BASES_OUTBOUND_EMAIL';
    $field6block1->name = 'outbound_email';
    $field6block1->table = 'vtiger_militarybases';
    $field6block1->column = 'outbound_email';
    $field6block1->columntype = 'VARCHAR(250)';
    $field6block1->uitype = 13;
    $field6block1->typeofdata = 'E~O';
    $field6block1->sequence = 6;

    $blockInstance1->addField($field6block1);
    echo "<li>The 'outbound_email' field created done</li><br>";
}

$field7block1 = Vtiger_Field::getInstance('qa_email', $moduleInstance);
if ($field7block1) {
    echo "<li>The 'qa_email' field already exists</li><br>";
} else {
    $field7block1 = new Vtiger_Field();
    $field7block1->label = 'LBL_MILITARY_BASES_QA_EMAIL';
    $field7block1->name = 'qa_email';
    $field7block1->table = 'vtiger_militarybases';
    $field7block1->column = 'qa_email';
    $field7block1->columntype = 'VARCHAR(250)';
    $field7block1->uitype = 13;
    $field7block1->typeofdata = 'E~O';
    $field7block1->sequence = 7;

    $blockInstance1->addField($field7block1);
    echo "<li>The 'qa_email' field created done</li><br>";
}


$field1block2 = Vtiger_Field::getInstance('createdtime', $moduleInstance);
if ($field1block2) {
    echo "<li>The 'createdtime' field already exists</li><br> \n";
} else {
    $field1block2 = new Vtiger_Field();
    $field1block2->label = 'Created Time';
    $field1block2->name = 'createdtime';
    $field1block2->table = 'vtiger_crmentity';
    $field1block2->column = 'createdtime';
    $field1block2->uitype = 70;
    $field1block2->typeofdata = 'T~O';
    $field1block2->displaytype = 2;

    $blockInstance2->addField($field1block2);
    echo "<li>The 'createdtime' field created done</li><br>";
}

$field2block2 = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);
if (!$field2block2) {
    $field2block2 = new Vtiger_Field();
    $field2block2->label = 'Modified Time';
    $field2block2->name = 'modifiedtime';
    $field2block2->table = 'vtiger_crmentity';
    $field2block2->column = 'modifiedtime';
    $field2block2->uitype = 70;
    $field2block2->typeofdata = 'T~O';
    $field2block2->displaytype = 2;

    $blockInstance2->addField($field2block2);
    echo "<li>The 'modifiedtime' field created done</li><br>";
}

$field3block2 = Vtiger_Field::getInstance('modifiedby', $moduleInstance);
if ($field3block2) {
    echo "<br> Field 'modifiedby' is already present. <br>";
} else {
    $field3block2 = new Vtiger_Field();
    $field3block2->label = 'Last Modified By';
    $field3block2->name = 'modifiedby';
    $field3block2->table = 'vtiger_crmentity';
    $field3block2->column = 'modifiedby';
    $field3block2->uitype = 52;
    $field3block2->typeofdata = 'V~O';
    $field3block2->displaytype = 2;

    $blockInstance2->addField($field3block2);
    echo "<li>The 'modifiedby' field created done</li><br>";
}

$field4block2 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if ($field4block2) {
    echo "<br> Field 'assigned_user_id' is already present. <br>";
} else {
    $field4block2 = new Vtiger_Field();
    $field4block2->label = 'Assigned To';
    $field4block2->name = 'assigned_user_id';
    $field4block2->table = 'vtiger_crmentity';
    $field4block2->column = 'smownerid';
    $field4block2->uitype = 53;
    $field4block2->typeofdata = 'V~M';

    $blockInstance2->addField($field4block2);
    echo "<li>The 'assigned_user_id' field created done</li><br>";
}


$sqlSelect = "SELECT * FROM `vtiger_tab` WHERE `vtiger_tab`.`name`=?";
$rs = $adb->pquery($sqlSelect,array('MilitaryBases'));
if ($adb->num_rows($rs) > 0){
    $sqlUpdate = "UPDATE `vtiger_tab` SET `vtiger_tab`.`tabsequence` = '-1', `vtiger_tab`.`parent` = '' WHERE `vtiger_tab`.`name`='MilitaryBases'";
    $adb->pquery($sqlUpdate);
    echo "remove module MilitaryBases from Menu<br>";
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";