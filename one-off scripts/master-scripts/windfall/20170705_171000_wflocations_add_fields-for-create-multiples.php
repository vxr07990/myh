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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;

$module = Vtiger_Module::getInstance('WFLocations');
if(!$module) {
  echo "WFLocations does not exist";
  return;
}

$create = ['WFLocations' =>
            [
            'LBL_WFLOCATIONS_DETAILS' =>
              [
                'LBL_WFLOCATIONS_RANGE_FROM' => [
                  'name'              => 'range_from',
                  'table'             => 'vtiger_wflocations',
                  'column'            => 'range_from',
                  'columntype'        => 'VARCHAR(100)',
                  'uitype'            => 1,
                  'typeofdata'        => 'V~O',
                  'sequence'          => 1,
                ],
                'LBL_WFLOCATIONS_RANGE_TO' => [
                  'name'              => 'range_to',
                  'table'             => 'vtiger_wflocations',
                  'column'            => 'range_to',
                  'columntype'        => 'VARCHAR(100)',
                  'uitype'            => 1,
                  'typeofdata'        => 'V~O',
                  'sequence'          => 1,
                ],
                'LBL_WFLOCATIONS_ROW_TO' => [
                  'name'              => 'row_to',
                  'table'             => 'vtiger_wflocations',
                  'column'            => 'row_to',
                  'columntype'        => 'VARCHAR(100)',
                  'uitype'            => 1,
                  'typeofdata'        => 'V~O',
                  'sequence'          => 1,
                ],
                'LBL_WFLOCATIONS_BAY_TO' => [
                  'name'              => 'bay_to',
                  'table'             => 'vtiger_wflocations',
                  'column'            => 'bay_to',
                  'columntype'        => 'VARCHAR(100)',
                  'uitype'            => 1,
                  'typeofdata'        => 'V~O',
                  'sequence'          => 1,
                ],
                'LBL_WFLOCATIONS_LEVEL_TO' => [
                  'name'              => 'level_to',
                  'table'             => 'vtiger_wflocations',
                  'column'            => 'level_to',
                  'columntype'        => 'VARCHAR(100)',
                  'uitype'            => 1,
                  'typeofdata'        => 'V~O',
                  'sequence'          => 1,
                ],
              ]
            ]
          ];

multicreate($create);
