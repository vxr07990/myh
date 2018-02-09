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



//$Vtiger_Utils_Log = true;
//include_once 'vtlib/Vtiger/Menu.php';
//include_once 'vtlib/Vtiger/Module.php';
//include_once 'modules/ModTracker/ModTracker.php';
//include_once 'modules/ModComments/ModComments.php';
//include_once 'includes/main/WebUI.php';
//include_once 'include/Webservices/Create.php';
//include_once 'modules/Users/Users.php';


if (!isset($db)) {
    $db = PearDatabase::getInstance();
}

echo "<br><h1>Begin script: Creating ParticipatingAgents module.</h1><br>\n";

//make UI type 10 in agents

$agentsModule = Vtiger_Module::getInstance('Agents');
$agentInfoBlock = Vtiger_Block::getInstance('LBL_AGENTS_INFORMATION', $agentsModule);
$agentManField = Vtiger_Field::getInstance('agentmanager_id', $agentsModule);

if ($agentManField) {
    echo "<br>agentmanager_id field already exists.<br>";
} else {
    echo "<br>Creating UI type 10 related field to link Agents & AgentMan...";
    $agentManField = new Vtiger_Field();
    $agentManField->label = 'LBL_AGENTS_AGENTMANAGERID';
    $agentManField->name = 'agentmanager_id';
    $agentManField->table = 'vtiger_agents';
    $agentManField->column = 'agentmanager_id';
    $agentManField->columntype = 'INT(19)';
    $agentManField->uitype = 10;
    $agentManField->typeofdata = 'V~O';
    
    $agentInfoBlock->addField($agentManField);
    $agentManField->setRelatedModules(array('AgentManager'));
    echo "done!<br>";
}

//make the ParticipatingAgents module

$participantsModule = Vtiger_Module::getInstance('ParticipatingAgents');
if ($participantsModule) {
    echo "ParticipantingAgents module already exists";
} else {
    $participantsModule = new Vtiger_Module();
    $participantsModule->name = 'ParticipatingAgents';
    $participantsModule->save();
}

if (!Vtiger_Utils::CheckTable('vtiger_participatingagents')) {
    echo "<br>creating vtiger_participatingagents table...";
    Vtiger_Utils::CreateTable('vtiger_participatingagents',
                              '(
								participatingagentsid INT(11) AUTO_INCREMENT,
								agents_id INT(19),
								agentmanager_id INT(19),
								rel_crmid INT(19),
								agent_type VARCHAR(30),
								view_level VARCHAR(30),
								status VARCHAR(30),
								PRIMARY KEY (participatingagentsid)
							   )', true);
    echo "done!<br>";
} else {
    echo "<br>vtiger_participatingagents already exists<br>";
}

echo '<br>Clearing out old, unused participanting agents tables...<br>';
if (Vtiger_Utils::CheckTable('vtiger_participating_agents')) {
    Vtiger_Utils::ExecuteQuery("DROP TABLE `vtiger_participating_agents`");
    echo 'vtiger_participating_agents cleared...<br>';
}

if (Vtiger_Utils::CheckTable('vtiger_potential_participatingagents')) {
    Vtiger_Utils::ExecuteQuery("DROP TABLE `vtiger_potential_participatingagents`");
    echo 'vtiger_potential_participatingagents cleared...<br>';
}

if (Vtiger_Utils::CheckTable('vtiger_orders_participatingagents')) {
    Vtiger_Utils::ExecuteQuery("DROP TABLE `vtiger_orders_participatingagents`");
    echo 'vtiger_orders_participatingagents cleared<br>';
}

echo '<br>done clearing old tables!<br>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";