<?php

require_once 'vtlib/Vtiger/Menu.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'modules/ModTracker/ModTracker.php';
require_once 'modules/ModComments/ModComments.php';
require_once 'includes/main/WebUI.php';
require_once 'include/Webservices/Create.php';
require_once 'modules/Users/Users.php';
require_once 'vendor/autoload.php';
require_once 'MetaVtlibFunctions.php';

$db = PearDatabase::getInstance();
$output = '';

initializeOutput($output);
createModule($output, 'Actuals');
duplicateBlocks($db, $output, 'Estimates');
duplicateFields($db, $output, 'Estimates', ['vtiger_quotes'=>'vtiger_quotes', 'vtiger_quotescf'=>'vtiger_quotescf', 'vtiger_quotesbillads'=>'vtiger_quotesbillads']);
duplicateFilters($db, $output, 'Estimates');
initSharingAndWebservices($output);
duplicateRelatedLists($db, $output, 'Estimates');
downloadScript($output, 'ActualsModule_'.date('Ymd_His').'.php');
