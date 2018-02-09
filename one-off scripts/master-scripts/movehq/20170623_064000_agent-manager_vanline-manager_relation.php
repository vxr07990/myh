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

//OT18594 - Agents associated to a vanline are not displaying under the related module

echo "<h2>Updating VanlineManager/AgentManager Related List</h2>";

$vanlineManagerInstance = Vtiger_Module::getInstance('VanlineManager');
$agentInstance = Vtiger_Module::getInstance('AgentManager');
$relationLabel = 'Agents';
//Remove wrong relation
	$vanlineManagerInstance->unsetRelatedList($agentInstance, $relationLabel);
//Add relation
	$vanlineManagerInstance->setRelatedList($agentInstance, $relationLabel, array('ADD','SELECT'), 'get_dependents_list');
	
print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";