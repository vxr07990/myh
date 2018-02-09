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




$create = ['WFLocations' =>
                    ['LBL_WFLOCATIONS_DETAILS' => [
                      'LBL_WFLOCATIONS_TYPE' => [
                        'name'              => 'wflocation_type',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'wflocation_type',
                        'columntype'        => 'VARCHAR(100)',
                        'uitype'            => 10,
                        'typeofdata'        => 'V~M',
                        'sequence'          => 1,
                        'setRelatedModules' => ['WFLocationTypes'],
                      ],
                      'LBL_WFLOCATIONS_TAG' => [
                        'name'              => 'tag',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'tag',
                        'columntype'        => 'VARCHAR(60)',
                        'uitype'            => 1,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 2,
                      ],
                      'LBL_WFLOCATIONS_DESCRIPTION' => [
                        'name'              => 'description',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'description',
                        'columntype'        => 'VARCHAR(255)',
                        'uitype'            => 1,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 3,
                      ],
                      'LBL_WFLOCATIONS_COMBINATION' => [
                        'name'              => 'combination', // ??????????
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'combination',
                        'columntype'        => 'VARCHAR(255)',
                        'uitype'            => 1,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 4,
                        'presence'          => 1,
                      ],
                      'LBL_WFLOCATIONS_AGENT' => [
                        'name'              => 'agent',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'agent',
                        'columntype'        => 'VARCHAR(100)',
                        'uitype'            => 10,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 5,
                        'setRelatedModules' => ['Agents'],
                      ],
                      'LBL_WFLOCATIONS_WAREHOUSE' => [
                        'name'              => 'wflocation_warehouse',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'wflocation_warehouse',
                        'columntype'        => 'VARCHAR(100)',
                        'uitype'            => 10,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 6,
                        'setRelatedModules' => ['WFWarehouse'],
                      ],
                      'LBL_WFLOCATIONS_COST' => [
                        'name'              => 'cost',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'cost',
                        'columntype'        => 'NUMERIC(65)',
                        'uitype'            => 1,
                        'typeofdata'        => 'N~O',
                        'sequence'          => 15,
                      ],
                      'LBL_WFLOCATIONS_PERCENTUSED' => [
                        'name'              => 'percentused',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'percentused',
                        'columntype'        => 'Varchar(100)',
                        'uitype'            => 9,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 16,
                      ],
                      'LBL_WFLOCATIONS_PERCENTUSEDOVERRIDE' => [
                        'name'              => 'percentusedoverride',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'percentusedoverride',
                        'columntype'        => 'Varchar(3)',
                        'uitype'            => 56,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 17,
                      ],
                      'LBL_WFLOCATIONS_ROW' => [
                        'name'              => 'row',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'row',
                        'columntype'        => 'Varchar(15)',
                        'uitype'            => 1,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 18,
                      ],
                      'LBL_WFLOCATIONS_BAY' => [
                        'name'              => 'bay',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'bay',
                        'columntype'        => 'Varchar(15)',
                        'uitype'            => 1,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 19,
                      ],
                      'LBL_WFLOCATIONS_LEVEL' => [
                        'name'              => 'level',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'level',
                        'columntype'        => 'Varchar(15)',
                        'uitype'            => 1,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 20,
                      ],
                      'LBL_WFLOCATIONS_DOUBLE_HIGH' => [
                        'name'              => 'double_high',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'double_high',
                        'columntype'        => 'Varchar(3)',
                        'uitype'            => 56,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 21,
                      ],
                      'LBL_WFLOCATIONS_CONTAINER_CAPACITY_ON' => [
                        'name'              => 'container_capacity_on',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'container_capacity_on',
                        'columntype'        => 'Varchar(3)',
                        'uitype'            => 56,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 22,
                      ],
                      'LBL_WFLOCATIONS_CONTAINER_CAPACITY' => [
                        'name'              => 'container_capacity',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'container_capacity',
                        'columntype'        => 'Varchar(3)',
                        'uitype'            => 56,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 23,
                      ],
                      'LBL_WFLOCATIONS_NAME' => [
                        'name'              => 'name',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'name',
                        'columntype'        => 'varchar(255)',
                        'uitype'            => 1,
                        'typeofdata'        => 'V~M',
                        'sequence'          => 24,
                      ],
                      'LBL_WFLOCATIONS_CREATE_MULTIPLE' => [
                        'name'              => 'create_multiple',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'create_multiple',
                        'columntype'        => 'varchar(3)',
                        'uitype'            => 56,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 25,
                      ],
                      'LBL_WFLOCATIONS_AGENTID' => [
                        'name'              => 'agentid',
                        'columntype'        => 'int(19)',
                        'uitype'            => 1002,
                        'typeofdata'        => 'I~M',
                        'table'             => 'vtiger_crmentity'
                      ],
                    ],
                    'LBL_WFLOCATIONS_INFORMATION' => [
                      'LBL_WFLOCATIONS_ACTIVE' => [
                        'name'              => 'active',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'active',
                        'columntype'        => 'VARCHAR(3)',
                        'uitype'            => 56,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 8,
                      ],
                      'LBL_WFLOCATIONS_BASE' => [
                        'name'              => 'wflocation_base',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'wflocation_base',
                        'columntype'        => 'VARCHAR(100)',
                        'uitype'            => 10,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 9,
                        'setRelatedModules' => ['WFLocations'],
                      ],
                      'LBL_WFLOCATIONS_SLOT' => [
                        'name'              => 'slot',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'slot',
                        'columntype'        => 'VARCHAR(100)',
                        'uitype'            => 1,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 10,
                      ],
                      'LBL_WFLOCATIONS_RESERVED' => [
                        'name'              => 'reserved',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'reserved',
                        'columntype'        => 'VARCHAR(3)',
                        'uitype'            => 56,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 11,
                      ],
                      'LBL_WFLOCATIONS_WFSLOT_CONFIGURATION' => [
                        'name'              => 'wfslot_configuration',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'wfslot_configuration',
                        'columntype'        => 'VARCHAR(100)',
                        'uitype'            => 10,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 7,
                        'setRelatedModules' => ['WFSlotConfiguration'],
                      ],
                      'LBL_WFLOCATIONS_OFFSITE' => [
                        'name'              => 'offsite',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'offsite',
                        'columntype'        => 'VARCHAR(3)',
                        'uitype'            => 56,
                        'typeofdata'        => 'V~O',
                        'sequence'          => 12,
                      ],
                      'LBL_WFLOCATIONS_SQUAREFEET' => [
                        'name'              => 'squarefeet',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'squarefeet',
                        'columntype'        => 'NUMERIC(65)',
                        'uitype'            => 1,
                        'typeofdata'        => 'N~O',
                        'sequence'          => 13,
                      ],
                      'LBL_WFLOCATIONS_CUBEFEET' => [
                        'name'              => 'cubefeet',
                        'table'             => 'vtiger_wflocations',
                        'column'            => 'cubefeet',
                        'columntype'        => 'NUMERIC(65)',
                        'uitype'            => 1,
                        'typeofdata'        => 'N~O',
                        'sequence'          => 14,
                      ],
                    ],
                    // Detail view info
                    'LBL_RECORDUPDATEINFORMATION' => [
                      'LBL_CREATED_TIME'       => [
                        'name'                => 'createdtime',
                        'columntype'          => 'datetime',
                        'uitype'              => 70,
                        'typeofdata'          => 'T~O',
                        'displaytype'         => 2,
                        'table'               => 'vtiger_crmentity'
                      ],
                      'LBL_MODIFIED_TIME'      => [
                        'name'                => 'modifiedtime',
                        'columntype'          => 'datetime',
                        'uitype'              => 70,
                        'typeofdata'          => 'T~O',
                        'displaytype'         => 2,
                        'table'               => 'vtiger_crmentity'
                      ],
                      'LBL_CREATED_BY'         => [
                        'name'                => 'smcreatorid',
                        'columntype'          => 'int(19)',
                        'uitype'              => 52,
                        'typeofdata'          => 'V~O',
                        'column'              => 'smcreatorid',
                        'displaytype'         => 2,
                        'table'               => 'vtiger_crmentity'
                      ],
                      'LBL_ASSIGNED_USER_ID'  => [
                        'name'                => 'smownerid',
                        'columntype'          => 'int(19)',
                        'uitype'              => 53,
                        'typeofdata'          => 'V~M',
                        'column'              => 'smownerid',
                        'displaytype'         => 2,
                        'table'               => 'vtiger_crmentity'
                      ],
                    ],
                  ]
                ];

foreach($create as $module=>$data) {
  $moduleInstance = Vtiger_Module::getInstance($module);
  if ($moduleInstance) {
    echo "<h2>$module already exists </h2><br>";
  } else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $module;
    $moduleInstance->save();
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
  }

  foreach ($data as $blockLabel=>$fields) {
    $blockInstance = Vtiger_Block::getInstance($blockLabel, $moduleInstance);

    if ($blockInstance) {
      echo "<h3>The $blockLabel block already exists</h3><br> \n";
    } else {
      $blockInstance = new Vtiger_Block();
      $blockInstance->label = $blockLabel;
      $moduleInstance->addBlock($blockInstance);
    }

    foreach($fields as $fieldLabel=>$fieldAttributes) {
      $field = Vtiger_Field::getInstance($fieldLabel, $moduleInstance);
      if ($field) {
        echo "<br> $fieldLabel already exists <br>";
      } else {
        $field = new Vtiger_Field();
        $field->label = $fieldLabel;
        $field->name = $fieldAttributes['name'];
        $field->table = $fieldAttributes['table'];
        $field->column = $fieldAttributes['column'];
        $field->columntype = $fieldAttributes['columntype'];
        $field->uitype = $fieldAttributes['uitype'];
        $field->typeofdata = $fieldAttributes['typeofdata'];
        $field->displaytype = isset($fieldAttributes['displaytype']) ? $fieldAttributes['displaytype'] : 1;
        $field->presence = isset($fieldAttributes['presence']) ? $fieldAttributes['presence'] : 0;
        $field->sequence = $fieldAttributes['sequence'];
        $blockInstance->addField($field);
        if($fieldLabel == 'LBL_WFLOCATIONS_NAME') {
          $moduleInstance->setEntityIdentifier($field);
        }
        if(isset($fieldAttributes['setRelatedModules'])) {
          $field->setRelatedModules($fieldAttributes['setRelatedModules']);
        }
      }
    }
  }
  //Menu
  $parentLabel = 'COMPANY_ADMIN_TAB';
  $db = PearDatabase::getInstance();
  if ($db) {
      $stmt = 'UPDATE vtiger_tab SET parent=? WHERE tabid=?';
      $db->pquery($stmt, [$parentLabel, $moduleInstance->id]);
  } else {
      Vtiger_Utils::ExecuteQuery("UPDATE vtiger_tab SET parent='" . $parentLabel . "' WHERE tabid=" . $moduleInstance->id);
  }
}
