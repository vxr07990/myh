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

/**
 * Created by PhpStorm.
 * User: DBOlin
 * Date: 2/24/2017
 * Time: 10:26 AM
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

// sigh.
include('_reset_seq_tables.php');

$module = Vtiger_Module::getInstance('AgentSequenceNumber');
if($module)
{
    $isNew = false;
} else {
    $isNew = true;
    $module = new Vtiger_Module();
    $module->name = 'AgentSequenceNumber';
    $module->save();
    $module->initTables();
    $module->setDefaultSharing();
    $module->initWebservice();
    ModTracker::enableTrackingForModule($module->id);
}

$block = Vtiger_Block::getInstance('LBL_AGENT_SEQUENCE_NUMBER_INFORMATION', $module);
if ($block) {
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_AGENT_SEQUENCE_NUMBER_INFORMATION';
    $module->addBlock($block);
}

$field = Vtiger_Field::getInstance('assigned_user_id', $module);
if ($field) {
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENT_SEQUENCE_NUMBER_ASSIGNEDTO';
    $field->name = 'assigned_user_id';
    $field->table = 'vtiger_crmentity';
    $field->column = 'smownerid';
    $field->uitype = 53;
    $field->typeofdata = 'V~M';
    $field->sequence = 10;

    $block->addField($field);
}

$field = Vtiger_Field::getInstance('createdtime', $module);
if ($field) {
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENT_SEQUENCE_NUMBER_CREATEDTIME';
    $field->name = 'createdtime';
    $field->table = 'vtiger_crmentity';
    $field->column = 'createdtime';
    $field->uitype = 70;
    $field->typeofdata = 'T~O';
    $field->displaytype = 2;
    $field->sequence = 11;

    $block->addField($field);
}

$field = Vtiger_Field::getInstance('modifiedtime', $module);
if ($field) {
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENT_SEQUENCE_NUMBER_MODIFIEDTIME';
    $field->name = 'modifiedtime';
    $field->table = 'vtiger_crmentity';
    $field->column = 'modifiedtime';
    $field->uitype = 70;
    $field->typeofdata = 'T~O';
    $field->displaytype = 2;
    $field->sequence = 12;

    $block->addField($field);
}


$field = Vtiger_Field::getInstance('agent_sn_agentmanagerid', $module);
if ($field) {
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENT_SEQUENCE_NUMBER_AGENTMANAGER';
    $field->name = 'agent_sn_agentmanagerid';    // Must be the same as column.
    $field->table = 'vtiger_agentsequencenumber';    // This is the tablename from your database that the new field will be added to.
    $field->column = 'agent_sn_agentmanagerid'; //  This will be the columnname in your database for the new field.
    $field->columntype = 'INT(11), ADD INDEX (agent_sn_agentmanagerid)';
    $field->uitype = 10;                // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field->typeofdata = 'I~M';
    $field->sequence = 1;
    $field->summaryfield = 1;

    $block->addField($field);
    $field->setRelatedModules(array('AgentManager'));
}

$field = Vtiger_Field::getInstance('agent_sn_modulename', $module);
if ($field) {
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENT_SEQUENCE_NUMBER_MODULENAME';
    $field->name = 'agent_sn_modulename';                                // Must be the same as column.
    $field->table = 'vtiger_agentsequencenumber';                        // This is the tablename from your database that the new field will be added to.
    $field->column = 'agent_sn_modulename';                            //  This will be the columnname in your database for the new field.
    $field->columntype = 'VARCHAR(50), ADD INDEX (agent_sn_modulename)';
    $field->uitype = 16;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field->typeofdata = 'V~M';
    $field->sequence = 2;
    $field->summaryfield = 1;                            // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block->addField($field);
    $field->setPicklistValues(['Orders']);
}

$field = Vtiger_Field::getInstance('agent_sn_format', $module);
if ($field) {
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENT_SEQUENCE_NUMBER_FORMAT';
    $field->name = 'agent_sn_format';                                // Must be the same as column.
    $field->table = 'vtiger_agentsequencenumber';                        // This is the tablename from your database that the new field will be added to.
    $field->column = 'agent_sn_format';                            //  This will be the columnname in your database for the new field.
    $field->columntype = 'VARCHAR(50)';
    $field->uitype = 1;                                        // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field->typeofdata = 'V~M';
    $field->sequence = 3;
    $field->summaryfield = 1;                            // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block->addField($field);
    $module->setEntityIdentifier($field);
}

if($isNew) {
    $block->save($module);
}

$db = &PearDatabase::getInstance();

$module = Vtiger_Module::getInstance('AgentSequenceNumber');
$filter1 = Vtiger_Filter::getInstance('All', $module);
if($filter1) {
    $filter1->delete();
}
$filter1 = new Vtiger_Filter();
$filter1->name      = 'All';
$filter1->isdefault = true;
$module->addFilter($filter1);
$fields = ['agent_sn_agentmanagerid','agent_sn_modulename','agent_sn_format'];
$i = 0;
foreach($fields as $fieldName)
{
    $field = Vtiger_Field::getInstance($fieldName, $module);
    $filter1->addField($field, $i++);
}

if(!Vtiger_Utils::CheckColumnExists('vtiger_modentity_num', 'agentmanagerid'))
{
    $db->pquery('ALTER TABLE vtiger_modentity_num ADD COLUMN `agentmanagerid` INT(11), ADD INDEX (agentmanagerid)');
}


$db->pquery('UPDATE vtiger_entityname SET fieldname=? WHERE tablename=? AND modulename=?',
            ['agent_sn_agentmanagerid,agent_sn_modulename', 'vtiger_agentsequencenumber', 'AgentSequenceNumber']);

$db->pquery('UPDATE vtiger_field SET uitype=4 WHERE uitype=1001');

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";