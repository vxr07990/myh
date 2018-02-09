<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

$patch_version = '-20141007';  // -ve timestamp before release, +ve timestamp after release.
$modified_database = '';
$applicationVersion = [
    'major' => '1',
    'minor' => '15',
    'revision' => '0'
]; 

$vtiger_current_version = implode('.', $applicationVersion);
$_SESSION['vtiger_version'] = $vtiger_current_version;
putenv('vtiger_version='.$vtiger_current_version);
putenv('major_version='.$applicationVersion['major']);
putenv('minor_version='.$applicationVersion['minor']);
putenv('revision_version='.$applicationVersion['revision']);
