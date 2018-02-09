<?php

include_once('vtlib/Vtiger/Module.php');
include_once('vtlib/Vtiger/Link.php');

$moduleInstance = Vtiger_Module::getInstance('Home');
$moduleInstance->addLink('DASHBOARDWIDGET', 'Opportunity Win/Loss Ratios', 'index.php?module=Opportunities&view=ShowWidget&name=CloseRatio');
