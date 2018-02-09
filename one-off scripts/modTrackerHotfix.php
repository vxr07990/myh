<?php
//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');

$tariffManagerInstance = Vtiger_Module::getInstance('TariffManager');
if ($tariffManagerInstance && !ModTracker::isTrackingEnabledForModule('TariffManager')) {
    echo "<br> TariffManager exists, adding modTracker <br>";
    ModTracker::enableTrackingForModule($tariffManagerInstance->id);
} else {
    echo "<br> TariffManager doesn't exist or already has modTracker, no action taken.";
}

$employeesInstance = Vtiger_Module::getInstance('Employees');
if ($employeesInstance && !ModTracker::isTrackingEnabledForModule('Employees')) {
    echo "<br> Employees exists, adding modTracker <br>";
    ModTracker::enableTrackingForModule($employeesInstance->id);
} else {
    echo "<br> Employees doesn't exist or already has modTracker, no action taken.";
}

$tariffsInstance = Vtiger_Module::getInstance('Tariffs');
if ($tariffsInstance && !ModTracker::isTrackingEnabledForModule('Tariffs')) {
    echo "<br> Tariffs exists, adding modTracker <br>";
    ModTracker::enableTrackingForModule($tariffsInstance->id);
} else {
    echo "<br> Tariffs doesn't exist or already has modTracker, no action taken.";
}

$surveysInstance = Vtiger_Module::getInstance('Surveys');
if ($surveysInstance && !ModTracker::isTrackingEnabledForModule('Surveys')) {
    echo "<br> Surveys exists, adding modTracker <br>";
    ModTracker::enableTrackingForModule($surveysInstance->id);
} else {
    echo "<br> Surveys doesn't exist or already has modTracker, no action taken.";
}

$agentsInstance = Vtiger_Module::getInstance('Agents');
if ($agentsInstance && !ModTracker::isTrackingEnabledForModule('Agents')) {
    echo "<br> Agents exists, adding modTracker <br>";
    ModTracker::enableTrackingForModule($agentsInstance->id);
} else {
    echo "<br> Agents doesn't exist or already has modTracker, no action taken.";
}

$vanlinesInstance = Vtiger_Module::getInstance('Vanlines');
if ($vanlinesInstance && !ModTracker::isTrackingEnabledForModule('Vanlines')) {
    echo "<br> Vanlines exists, adding modTracker <br>";
    ModTracker::enableTrackingForModule($vanlinesInstance->id);
} else {
    echo "<br> Vanlines doesn't exist or already has modTracker, no action taken.";
}

$vanlineManagerInstance = Vtiger_Module::getInstance('VanlineManager');
if ($vanlineManagerInstance && !ModTracker::isTrackingEnabledForModule('VanlineManager')) {
    echo "<br> VanlineManager exists, adding modTracker <br>";
    ModTracker::enableTrackingForModule($vanlineManagerInstance->id);
} else {
    echo "<br> VanlineManager doesn't exist or already has modTracker, no action taken.";
}

$agentManagerInstance = Vtiger_Module::getInstance('AgentManager');
if ($agentManagerInstance && !ModTracker::isTrackingEnabledForModule('AgentManager')) {
    echo "<br> AgentManager exists, adding modTracker <br>";
    ModTracker::enableTrackingForModule($agentManagerInstance->id);
} else {
    echo "<br> AgentManager doesn't exist or already has modTracker, no action taken.";
}
