<?php

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;

$rsCheck = $adb->pquery("SELECT * FROM vtiger_ws_fieldtype WHERE fieldtype = 'referencemultipicklist' AND uitype=1989", array());

if ($adb->num_rows($rsCheck) == 0) {
    $adb->pquery("INSERT INTO vtiger_ws_fieldtype(fieldtype,uitype) VALUES('referencemultipicklist',1989)", array());
}
