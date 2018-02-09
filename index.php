<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

// Configure errors to throw an `ErrorException` so they're catchable
# error_reporting(E_ERROR);
# require_once 'config/error_handler.php';

// Overrides GetRelatedList : used to get related query
// TODO : Eliminate below hacking solution
require_once 'include/Webservices/Relation.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'includes/main/WebUI.php';
require_once 'vendor/autoload.php';
require_once 'config/database.php';

if (getenv('PHP_ENV') === 'dev') {
    ini_set('display_errors', 'on');

    $whoops = new Whoops\Run;
    $whoops->pushHandler(new Whoops\Handler\PrettyPageHandler);

    // Use a certain handler only in AJAX triggered requests
    if (Whoops\Util\Misc::isAjaxRequest()) {
        $whoops->pushHandler(new Whoops\Handler\JsonResponseHandler);
    }

    $whoops->register();
}

$webUI = new Vtiger_WebUI();
$webUI->process(new Vtiger_Request($_REQUEST, $_REQUEST));
