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

$participatingAgents = Vtiger_Module::getInstance('ParticipatingAgents');
if ($participatingAgents) {
    echo "ParticipantingAgents module already exists";
} else {
    $participatingAgents       = new Vtiger_Module();
    $participatingAgents->name = 'ParticipatingAgents';
    $participatingAgents->save();
}
$blockParticipatingAgentsInformation = Vtiger_Block::getInstance('LBL_PARTICIPATINGAGENTS_INFORMATION', $participatingAgents);
if ($blockParticipatingAgentsInformation) {
    echo "<h3>The LBL_PARTICIPATINGAGENTS_INFORMATION block already exists</h3><br> \n";
} else {
    $blockParticipatingAgentsInformation        = new Vtiger_Block();
    $blockParticipatingAgentsInformation->label = 'LBL_PARTICIPATINGAGENTS_INFORMATION';
    $participatingAgents->addBlock($blockParticipatingAgentsInformation);
}
$field0 = Vtiger_Field::getInstance('agents_id', $participatingAgents);
if ($field0) {
    echo "The agents_id field already exists<br>\n";
} else {
    $field0             = new Vtiger_Field();
    $field0->label      = 'LBL_AGENTS_ID';
    $field0->name       = 'agents_id';
    $field0->table      = 'vtiger_participatingagents';
    $field0->column     = 'agents_id';
    $field0->columntype = 'INT(19)';
    $field0->uitype     = 10;
    $field0->typeofdata = 'I~O';
    $blockParticipatingAgentsInformation->addField($field0);
    $field0->setRelatedModules(array('Agents'));
    $participatingAgents->setEntityIdentifier($field0);
}
$field1 = Vtiger_Field::getInstance('agent_type', $participatingAgents);
if ($field1) {
    echo "The agent_type field already exists<br>\n";
} else {
    $field1             = new Vtiger_Field();
    $field1->label      = 'LBL_AGENT_TYPE';
    $field1->name       = 'agent_type';
    $field1->table      = 'vtiger_participatingagents';
    $field1->column     = 'agent_type';
    $field1->columntype = 'VARCHAR(30)';
    $field1->uitype     = 16;
    $field1->typeofdata = 'V~O';
    $blockParticipatingAgentsInformation->addField($field1);
}
$field2 = Vtiger_Field::getInstance('status', $participatingAgents);
if ($field2) {
    echo "The status field already exists<br>\n";
} else {
    $field2             = new Vtiger_Field();
    $field2->label      = 'LBL_STATUS';
    $field2->name       = 'status';
    $field2->table      = 'vtiger_participatingagents';
    $field2->column     = 'status';
    $field2->columntype = 'VARCHAR(30)';
    $field2->uitype     = 1;
    $field2->typeofdata = 'V~O';
    $blockParticipatingAgentsInformation->addField($field2);
}
$field3 = Vtiger_Field::getInstance('view_level', $participatingAgents);
if ($field3) {
    echo "The view_level field already exists<br>\n";
} else {
    $field3             = new Vtiger_Field();
    $field3->label      = 'LBL_VIEW_LEVEL';
    $field3->name       = 'view_level';
    $field3->table      = 'vtiger_participatingagents';
    $field3->column     = 'view_level';
    $field3->columntype = 'VARCHAR(30)';
    $field3->uitype     = 16;
    $field3->typeofdata = 'V~M';
    $blockParticipatingAgentsInformation->addField($field3);
}
$field4 = Vtiger_Field::getInstance('rel_crmid', $participatingAgents);
if ($field4) {
    echo "The rel_crmid field already exists<br>\n";
} else {
    $field4             = new Vtiger_Field();
    $field4->label      = 'LBL_REL_CRMID';
    $field4->name       = 'rel_crmid';
    $field4->table      = 'vtiger_participatingagents';
    $field4->column     = 'rel_crmid';
    $field4->columntype = 'INT(19)';
    $field4->uitype     = 10;
    $field4->typeofdata = 'I~O';
    $blockParticipatingAgentsInformation->addField($field4);
    $field4->setRelatedModules(array('Orders', 'Opportunities'));
}
$field5 = Vtiger_Field::getInstance('oasurveyrequest_id', $participatingAgents);
if ($field5) {
    echo "The oasurveyrequest_id field already exists<br>\n";
} else {
    $field5             = new Vtiger_Field();
    $field5->label      = 'LBL_OASURVEYREQUEST_ID';
    $field5->name       = 'oasurveyrequest_id';
    $field5->table      = 'vtiger_participatingagents';
    $field5->column     = 'oasurveyrequest_id';
    $field5->columntype = 'INT(11)';
    $field5->uitype     = 1;
    $field5->typeofdata = 'I~O';
    $blockParticipatingAgentsInformation->addField($field5);
}
$field6 = Vtiger_Field::getInstance('agentmanager_id', $participatingAgents);
if ($field6) {
    echo "The agentmanager_id field already exists<br>\n";
} else {
    $field6             = new Vtiger_Field();
    $field6->label      = 'LBL_AGENTMANAGER_ID';
    $field6->name       = 'agentmanager_id';
    $field6->table      = 'vtiger_participating_agents';
    $field6->column     = 'agentmanager_id';
    $field6->columntype = 'INT(19)';
    $field6->uitype     = 10;
    $field6->typeofdata = 'I~O';
    $blockParticipatingAgentsInformation->addField($field6);
    $field6->setRelatedModules(array('Agents'));
}

$db = PearDatabase::getInstance();

$moduleNames = ['Opportunities', 'Orders'];

foreach ($moduleNames as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        continue;
    }
    $module->setGuestBlocks('ParticipatingAgents', ['LBL_PARTICIPATINGAGENTS_INFORMATION']);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
