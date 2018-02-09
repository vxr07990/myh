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

global $adb;

$create = ['WFLocationHistory' => [
            'LBL_WFLOCATIONHISTORY_DETAILS' => [
              'LBL_WFLOCATIONHISTORY_DATE' => [
                'name' => 'date',
                'table' => 'vtiger_wflocationhistory',
                'column' => 'date',
                'columntype' => 'DATE',
                'uitype' => 5,
                'typeofdata' => 'D~O',
              ],
              'LBL_WFLOCATIONHISTORY_TIME' => [
                'name' => 'time',
                'table' => 'vtiger_wflocationhistory',
                'column' => 'time',
                'columntype' => 'TIME',
                'uitype' => 14,
                'typeofdata' => 'T~O',
              ],
              'LBL_WFLOCATIONHISTORY_ASSIGNEDTO' => [
                'name' => 'assigned_user_id',
                'table' => 'vtiger_crmentity',
                'column' => 'smownerid',
                'uitype' => 53,
                'typeofdata' => 'V~O',
              ],
              'LBL_WFLOCATIONHISTORY_WAREHOUSE' => [
                'name' => 'warehouse',
                'table' => 'vtiger_wflocationhistory',
                'column' => 'warehouse',
                'columntype' => 'varchar(125)',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'quickcreate' => 0,
                'setRelatedModules' => ['WFWarehouses'],
              ],
              'LBL_WFLOCATIONHISTORY_LOCATIONTAG' => [
                'name' => 'locationtag',
                'table' => 'vtiger_wflocationhistory',
                'column' => 'locationtag',
                'columntype' => 'varchar(125)',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'quickcreate' => 0,
                'setRelatedModules' => ['WFLocationTags'],
              ],
              'LBL_WFLOCATIONHISTORY_SLOT' => [
                'name' => 'slot',
                'table' => 'vtiger_wflocationhistory',
                'column' => 'slot',
                'columntype' => 'varchar(6)',
                'uitype' => 10,
                'typeofdata' => 'V~O',
                'quickcreate' => 0,
                'setRelatedModules' => ['WFSlotConfiguration'],
              ],
            ],
          ],
        ];

$isNew = false;
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
    $isNew = true;
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
        if($fieldLabel == 'LBL_WFLOCATIONHISTORY_WAREHOUSE') {
          $moduleInstance->setEntityIdentifier($field);
        }
        if(isset($fieldAttributes['setRelatedModules'])) {
          $field->setRelatedModules($fieldAttributes['setRelatedModules']);
        }
      }
      if ($isNew) {
        $filter = Vtiger_Filter::getInstance('All', $moduleInstance);
        if(!$filter) {
          $filter = new Vtiger_Filter();
          $filter->name = 'All';
          $filter->isdefault = true;
          $moduleInstance->addFilter($filter);
        }

        $filter->addField($field);
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
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
