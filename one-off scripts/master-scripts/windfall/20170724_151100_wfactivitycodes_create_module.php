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

$create = ['WFActivityCodes' => [
            'LBL_WFACTIVITYCODES_DETAILS' => [
              'LBL_WFACTIVITYCODES_SHORTDESCRIPTION' => [
                'name' => 'shortdescription',
                'table' => 'vtiger_wfactivitycodes',
                'column' => 'shortdescription',
                'columntype' => 'varchar(20)',
                'uitype' => 1,
                'displaytype' => 1,
                'typeofdata' => 'V~O~LE~20',
                'quickcreate' => 0,
                'sequence' => 1,
                'filterSequence' => 1,
              ],
              'LBL_WFACTIVITYCODES_LONGDESCRIPTION' => [
                'name' => 'longdescription',
                'table' => 'vtiger_wfactivitycodes',
                'column' => 'longdescription',
                'columntype' => 'varchar(50)',
                'uitype' => 1,
                'displaytype' => 1,
                'typeofdata' => 'V~O~LE~50',
                'quickcreate' => 0,
                'sequence' => 2,
                'filterSequence' => 2,
              ],
              'LBL_WFACTIVITYCODES_BASECODE' => [
                'name' => 'wfactivitycodes_basecode',
                'table' => 'vtiger_wfactivitycodes',
                'column' => 'wfactivitycodes_basecode',
                'columntype' => 'varchar(100)',
                'uitype' => 16,
                'typeofdata' => 'V~O',
                'displaytype' => 1,
                'quickcreate' => 0,
                'sequence' => 3,
                'setPicklistValues' => [
                  'IN',
                  'OUT',
                  'MOVE',
                  'VMOVE',
                  'VOUT',
                  'CIN',
                  'COUT',
                  'CMOVE',
                  'PMOVE',
                  'POUT',
                  'MOVEUPDATE',
                  'OUTUPDATE',
                ],
                'filterSequence' => 3,
              ],
              'LBL_WFACTIVITYCODES_SYNC' => [
                'name' => 'sync',
                'table' => 'vtiger_wfactivitycodes',
                'column' => 'sync',
                'columntype' => 'tinyint(1)',
                'uitype' => 56,
                'typeofdata' => 'V~O',
                'displaytype' => 1,
                'quickcreate' => 0,
                'sequence' => 4,
                'filterSequence' => 4,
              ],
              'LBL_ASSIGNED_USER_ID'  => [
                'name'                => 'assigned_user_id',
                'columntype'          => 'int(19)',
                'uitype'              => 53,
                'typeofdata'          => 'V~M',
                'column'              => 'smownerid',
                'displaytype'         => 1,
                'sequence' => 5,
                'table'               => 'vtiger_crmentity'
              ],
              'LBL_WFACTIVITYCODES_AGENTID' => [
                'name'              => 'agentid',
                'columntype'        => 'int(19)',
                'uitype'            => 1002,
                'displaytype' => 1,
                'typeofdata'        => 'I~M',
                'table'             => 'vtiger_crmentity',
                'sequence' => 6,
              ],
            ],
            'LBL_RECORDUPDATEINFORMATION' => [
              'LBL_CREATED_TIME'      => [
                'name'                => 'createdtime',
                'columntype'          => 'datetime',
                'uitype'              => 70,
                'typeofdata'          => 'T~O',
                'displaytype'         => 2,
                'table'               => 'vtiger_crmentity'
              ],
              'LBL_MODIFIED_TIME'     => [
                'name'                => 'modifiedtime',
                'columntype'          => 'datetime',
                'uitype'              => 70,
                'typeofdata'          => 'T~O',
                'displaytype'         => 2,
                'table'               => 'vtiger_crmentity'
              ],
              'LBL_CREATED_BY'        => [
                'name'                => 'smcreatorid',
                'columntype'          => 'int(19)',
                'uitype'              => 52,
                'typeofdata'          => 'V~O',
                'column'              => 'smcreatorid',
                'displaytype'         => 2,
                'table'               => 'vtiger_crmentity'
              ],
            ],
          ],
          ];

multicreate($create);
