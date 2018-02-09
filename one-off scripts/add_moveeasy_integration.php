<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Module.php');
include_once('vtlib/Vtiger/Menu.php');

print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$labelName = "MoveEasy";

$db = PearDatabase::getInstance();

$query = $db->pquery("SELECT `name` FROM vtiger_settings_field WHERE `name`=? LIMIT 1", array("MoveEasy"));
$result = $query->fetchRow();
$moduleExists = false;
if($result['name'] == $labelName){
    $moduleExists = true;
    echo "MoveEasy Module Exists, Moving on.\n";
}

if(!$moduleExists) {
    $query = $db->pquery("SELECT id FROM vtiger_settings_field_seq", array());
    $result = $query->fetchRow();

    $id = $result['id'];
    $idPlusOne = $id + 1;

    $params = array(
        $idPlusOne,
        5,
        'MoveEasy',
        '',
        'MoveEasy Integration Settings',
        'index.php?module=MoveEasyIntegration&parent=Settings&view=Index',
        2,
        0,
        0
    );

    $insert = $db->pquery("INSERT INTO vtiger_settings_field (`fieldid`, `blockid`, `name`, `iconpath`, `description`, `linkto`, `sequence`, `active`, `pinned`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", $params);
    $update = $db->pquery("UPDATE vtiger_settings_field_seq SET id=$idPlusOne WHERE id=$id ", array());

    //creates moveeasy table
    $update = $db->pquery("CREATE TABLE vtiger_moveeasy_integration (agentID INT, isSubscribed INT, domain VARCHAR(50), iframe VARCHAR(200), uid VARCHAR(50), token VARCHAR(50))", array());

    $module = Vtiger_Module::getInstance('AgentManager');

    if(!$module)
    {
        print "AGENT MANAGER DOES NOT EXIST";
        return;
    }

    $block1 = Vtiger_Block::getInstance('LBL_AGENTMANAGER_INFORMATION', $module);

    if(!$block1)
    {
        print "AGENT MANAGER INFORMATION DOES NOT EXIST";
        return;
    }

    $field = Vtiger_Field::getInstance('website', $module);

    if($field)
    {
        print "Website EXISTS";
        return;
    }

    $field1 = new Vtiger_Field();
    $field1->label = 'Website';
    $field1->name = 'website';
    $field1->table = 'vtiger_agentmanager';
    $field1->column = 'website';
    $field1->columntype = 'VARCHAR(100)';
    $field1->uitype = 2;
    $field1->typeofdata = 'V~O';
    $block1->addField($field1);
    $block1->save($module);

}

print "FINISHED \e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";
