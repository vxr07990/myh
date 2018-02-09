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
$Vtiger_Utils_Log = true;
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$create = ['WFSlotConfiguration' =>
            ['LBL_WFSLOTCONFIGURATION_DETAILS' =>
              [
                'LBL_WFSLOTCONFIGURATION_CODE' => [
                    'name'          => 'code',
                    'table'         => 'vtiger_wfslotconfiguration',
                    'uitype'        => 1,
                    'column'        => 'code',
                    'columntype'    => 'varchar(20)',
                    'typeofdata'    => 'V~M',
                    'sequence'      => '1',
                    'defaultvalue'  => '',
                ],
                'LBL_WFSLOTCONFIGURATION_DES' => [
                    'name'          => 'description',
                    'table'         => 'vtiger_wfslotconfiguration',
                    'uitype'        => 1,
                    'column'        => 'description',
                    'columntype'    => 'varchar(255)',
                    'typeofdata'    => 'V~M',
                    'sequence'      => '2',
                    'defaultvalue'  => '',
                ],
                'LBL_WFSLOTCONFIGURATION_LABEL1' => [
                    'name'          => 'label1',
                    'table'         => 'vtiger_wfslotconfiguration',
                    'uitype'        => 1,
                    'column'        => 'label1',
                    'columntype'    => 'varchar(20)',
                    'typeofdata'    => 'V~O',
                    'sequence'      => '3',
                    'defaultvalue'  => '',
                ],
                'LBL_WFSLOTCONFIGURATION_SLOTPERCENTAGE1' => [
                    'name'          => 'slotpercentage1',
                    'table'         => 'vtiger_wfslotconfiguration',
                    'uitype'        => 9,
                    'column'        => 'slotpercentage1',
                    'columntype'    => 'INT(3)',
                    'typeofdata'    => 'N~O',
                    'sequence'      => '4',
                    'defaultvalue'  => 0,
                ],
                'LBL_WFSLOTCONFIGURATION_LABEL2' => [
                    'name'          => 'label2',
                    'table'         => 'vtiger_wfslotconfiguration',
                    'uitype'        => 1,
                    'column'        => 'label2',
                    'columntype'    => 'varchar(20)',
                    'typeofdata'    => 'V~O',
                    'sequence'      => '5',
                    'defaultvalue'  => '',
                ],
                'LBL_WFSLOTCONFIGURATION_SLOTPERCENTAGE2' => [
                    'name'          => 'slotpercentage2',
                    'table'         => 'vtiger_wfslotconfiguration',
                    'uitype'        => 9,
                    'column'        => 'slotpercentage2',
                    'columntype'    => 'INT(3)',
                    'typeofdata'    => 'N~O',
                    'sequence'      => '6',
                    'defaultvalue'  => 0,
                ],
                'LBL_WFSLOTCONFIGURATION_LABEL3' => [
                    'name'          => 'label3',
                    'table'         => 'vtiger_wfslotconfiguration',
                    'uitype'        => 1,
                    'column'        => 'label3',
                    'columntype'    => 'varchar(20)',
                    'typeofdata'    => 'V~O',
                    'sequence'      => '7',
                    'defaultvalue'  => '',
                ],
                'LBL_WFSLOTCONFIGURATION_SLOTPERCENTAGE3' => [
                    'name'          => 'slotpercentage3',
                    'table'         => 'vtiger_wfslotconfiguration',
                    'uitype'        => 9,
                    'column'        => 'slotpercentage3',
                    'columntype'    => 'INT(3)',
                    'typeofdata'    => 'N~O',
                    'sequence'      => '8',
                    'defaultvalue'  => 0,
                ],
                'LBL_WFSLOTCONFIGURATION_LABEL4' => [
                    'name'          => 'label4',
                    'table'         => 'vtiger_wfslotconfiguration',
                    'uitype'        => 1,
                    'column'        => 'label4',
                    'columntype'    => 'varchar(20)',
                    'typeofdata'    => 'V~O',
                    'sequence'      => '9',
                    'defaultvalue'  => '',
                ],
                'LBL_WFSLOTCONFIGURATION_SLOTPERCENTAGE4' => [
                    'name'          => 'slotpercentage4',
                    'table'         => 'vtiger_wfslotconfiguration',
                    'uitype'        => 9,
                    'column'        => 'slotpercentage4',
                    'columntype'    => 'INT(3)',
                    'typeofdata'    => 'N~O',
                    'sequence'      => '10',
                    'defaultvalue'  => 0,
                ],
                'LBL_WFSLOTCONFIGURATION_LABEL5' => [
                    'name'          => 'label5',
                    'table'         => 'vtiger_wfslotconfiguration',
                    'uitype'        => 1,
                    'column'        => 'label5',
                    'columntype'    => 'varchar(20)',
                    'typeofdata'    => 'V~O',
                    'sequence'      => '11',
                    'defaultvalue'  => '',
                ],
                'LBL_WFSLOTCONFIGURATION_SLOTPERCENTAGE5' => [
                    'name'          => 'slotpercentage5',
                    'table'         => 'vtiger_wfslotconfiguration',
                    'uitype'        => 9,
                    'column'        => 'slotpercentage5',
                    'columntype'    => 'INT(3)',
                    'typeofdata'    => 'N~O',
                    'sequence'      => '12',
                    'defaultvalue'  => 0,
                ],
                'LBL_WFSLOTCONFIGURATION_LABEL6' => [
                    'name'          => 'label6',
                    'table'         => 'vtiger_wfslotconfiguration',
                    'uitype'        => 1,
                    'column'        => 'label6',
                    'columntype'    => 'varchar(20)',
                    'typeofdata'    => 'V~O',
                    'sequence'      => '13',
                    'defaultvalue'  => '',
                ],
                'LBL_WFSLOTCONFIGURATION_SLOTPERCENTAGE6' => [
                    'name'          => 'slotpercentage6',
                    'table'         => 'vtiger_wfslotconfiguration',
                    'uitype'        => 9,
                    'column'        => 'slotpercentage6',
                    'columntype'    => 'INT(3)',
                    'typeofdata'    => 'N~O',
                    'sequence'      => '14',
                    'defaultvalue'  => 0,
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
        if($fieldLabel == 'LBL_WFSLOTCONFIGURATION_CODE') {
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
