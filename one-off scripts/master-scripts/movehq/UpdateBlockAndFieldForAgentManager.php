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
error_reporting(E_ERROR);
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

// Create New Block

echo "<h2>BEGINNING Create New Blocks For Agent Manager</h2>";
$moduleModel= Vtiger_Module_Model::getInstance('AgentManager');
$blockList = $moduleModel->getBlocks();
$blockListLabel = array_keys($blockList);

echo "<br>BEGINNING Create Agency Logo Block<br>";
if (!in_array('LBL_AGENTMANAGER_LOGO', $blockListLabel)) {
    $block1 = new Vtiger_Block_Model();
    $block1->set('label', 'LBL_AGENTMANAGER_LOGO');
    $block1->set('iscustom', '1');
    $block1->set('sequence', '2');
    $agentLogoBlockId = $block1->save($moduleModel);
}

echo "<br>BEGINNING Create Capacity Calendar Counter Setup Block<br>";
if (!in_array('LBL_CAPACITY_CALENDAR_COUNTER_SETUP', $blockListLabel)) {
    $block2 = new Vtiger_Block_Model();
    $block2->set('label', 'LBL_CAPACITY_CALENDAR_COUNTER_SETUP');
    $block2->set('iscustom', '1');
    $block2->save($moduleModel);
}

echo "<br>BEGINNING Create Agency Defaults Block<br>";
if (!in_array('LBL_AGENTMANAGER_DEFAULTS', $blockListLabel)) {
    $block3 = new Vtiger_Block_Model();
    $block3->set('label', 'LBL_AGENTMANAGER_DEFAULTS');
    $block3->set('iscustom', '1');
    $block3->save($moduleModel);
}

echo "<br>BEGINNING Create Agency Notes Block<br>";
if (!in_array('LBL_AGENTMANAGER_NOTES', $blockListLabel)) {
    $block4 = new Vtiger_Block_Model();
    $block4->set('label', 'LBL_AGENTMANAGER_NOTES');
    $block4->set('iscustom', '1');
    $block4->save($moduleModel);
}

echo "<br>BEGINNING Create Record Update Information Block<br>";
if (!in_array('LBL_AGENTMANAGER_RECORD_UPDATE_INFORMATION', $blockListLabel)) {
    $block5 = new Vtiger_Block_Model();
    $block5->set('label', 'LBL_AGENTMANAGER_RECORD_UPDATE_INFORMATION');
    $block5->set('iscustom', '1');
    $block5->save($moduleModel);
}

echo "<br>COMPLELE Create New Blocks For Agent Manager<br>";

echo "<h2>BEGINNING Create New Field For Agent Manager</h2>";

$listFieldsInfo = array(
    'LBL_AGENTMANAGER_INFORMATION'=>array(
        'agentmanager_status'=>array(
            'label'=>'LBL_STATUS',
            'columntype'=>'VARCHAR(75)',
            'uitype'=>16,
            'typeofdata'=>'V~M',
            'picklistvalues'=>array('Active','Inactive'),
            'defaultvalue'=>'Active',
        ),
    ),
    'LBL_AGENTMANAGER_ADDRESSINFORMATION'=>array(
        'agentmanager_mailing_address_1'=>array(
            'label'=>'LBL_AGENTMANAGER_MAILING_ADDRESS_1',
            'columntype'=>'VARCHAR(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ),
        'agentmanager_mailing_address_2'=>array(
            'label'=>'LBL_AGENTMANAGER_MAILING_ADDRESS_2',
            'columntype'=>'VARCHAR(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ),
        'agentmanager_mailing_city'=>array(
            'label'=>'LBL_AGENTMANAGER_MAILING_CITY',
            'columntype'=>'VARCHAR(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ),
        'agentmanager_mailing_state'=>array(
            'label'=>'LBL_AGENTMANAGER_MAILING_STATE',
            'columntype'=>'VARCHAR(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ),
        'agentmanager_mailing_zip'=>array(
            'label'=>'LBL_AGENTMANAGER_MAILING_ZIP',
            'columntype'=>'VARCHAR(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ),
        'agentmanager_mailing_country'=>array(
            'label'=>'LBL_AGENTMANAGER_MAILING_COUNTRY',
            'columntype'=>'VARCHAR(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ),
    ),
    'LBL_AGENTMANAGER_DEFAULTS'=>array(
        'order_task_start_time'=>array(
            'label'=>'LBL_ORDER_TASK_START_TIME',
            'columntype'=>'TIME',
            'uitype'=>14,
            'typeofdata'=>'T~O',
        ),
        'order_task_end_time'=>array(
            'label'=>'LBL_ORDER_TASK_END_TIME',
            'columntype'=>'TIME',
            'uitype'=>14,
            'typeofdata'=>'T~O',
        ),
        'personnel_start_time'=>array(
            'label'=>'LBL_PERSONNEL_START_TIME',
            'columntype'=>'TIME',
            'uitype'=>14,
            'typeofdata'=>'T~O',
        ),
        'personnel_end_time'=>array(
            'label'=>'LBL_PERSONNEL_END_TIME',
            'columntype'=>'TIME',
            'uitype'=>14,
            'typeofdata'=>'T~O',
        ),
        'order_cost_overhead_percent'=>array(
            'label'=>'LBL_ORDER_COST_OVERHEAD_PERCENT',
            'columntype'=>'DECIMAL(7,3)',
            'uitype'=>9,
            'typeofdata'=>'N~O',
        ),
        'agency_color'=>array(
            'label'=>'LBL_AGENCY_COLOR',
            'columntype'=>'VARCHAR(255)',
            'uitype'=>16,
            'typeofdata'=>'V~O',
            'picklistvalues'=>array("Red","Orange","Yellow","Green","Blue","Indigo","Violet"),
        ),
        'default_deposit_type'=>array(
            'label'=>'LBL_DEFAULT_DEPOSIT_TYPE',
            'columntype'=>'VARCHAR(255)',
            'uitype'=>16,
            'typeofdata'=>'V~O',
            'picklistvalues'=>array('Percent','Flat Amount')
        ),
        'default_deposit_amount'=>array(
            'label'=>'LBL_DEFAULT_DEPOSIT_AMOUNT',
            'columntype'=>'DECIMAL(7,3)',
            'uitype'=>9,
            'typeofdata'=>'N~O',
        ),
        'payroll_week_start_date'=>array(
            'label'=>'LBL_PAYROLL_WEEK_START_DATE',
            'columntype'=>'VARCHAR(255)',
            'uitype'=>16,
            'typeofdata'=>'V~O',
            'picklistvalues'=>array("Sunday","Monday","Tuesday", "Wednesday", "Thursday","Friday","Saturday"),
        ),
        'quickbooks_class'=>array(
            'label'=>'LBL_QUICKBOOKS_CLASS',
            'columntype'=>'VARCHAR(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O',
        ),
    ),
    'LBL_AGENTMANAGER_NOTES'=>array(
        'agentmanager_notes'=>array(
            'label'=>'LBL_AGENTMANAGER_NOTES',
            'columntype'=>'text',
            'uitype'=>19,
            'typeofdata'=>'V~O',
        ),
    ),
    'LBL_AGENTMANAGER_RECORD_UPDATE_INFORMATION'=>array(
        'agentmanager_created_by'=>array(
            'label'=>'LBL_AGENTMANAGER_CREATED_BY',
            'columntype'=>'text',
            'uitype'=>19,
            'typeofdata'=>'V~O',
            'displaytype'=>2,
        ),
    ),
);

foreach ($listFieldsInfo as $blockLabel => $listField) {
    foreach ($listField as $fieldName =>$fieldInfo) {
        echo "<br>BEGINNING create $fieldName field<br>";
        $fieldModel = Vtiger_Field_Model::getInstance($fieldName, $moduleModel);
        if ($fieldModel) {
            echo "<br>$fieldName field already exists.<br>";
        } else {
            $blockInstance = Vtiger_Block::getInstance($blockLabel, $moduleModel);
            $fieldModel = new Vtiger_Field();
            $fieldModel->name = $fieldName;
            $fieldModel->table = 'vtiger_agentmanager';
            $fieldModel->column = $fieldName;
            $fieldModel->label = $fieldInfo['label'];
            $fieldModel->columntype= $fieldInfo['columntype'];
            $fieldModel->uitype= $fieldInfo['uitype'];
            $fieldModel->typeofdata= $fieldInfo['typeofdata'];

            if (isset($fieldInfo['displaytype'])) {
                $fieldModel->displaytype= $fieldInfo['displaytype'];
            }
            if (isset($fieldInfo['defaultvalue'])) {
                $fieldModel->defaultvalue= $fieldInfo['defaultvalue'];
            }

            $blockInstance->addField($fieldModel);
            if (isset($fieldInfo['picklistvalues'])) {
                $fieldModel->setPicklistValues($fieldInfo['picklistvalues']);
            }
            if (isset($fieldInfo['related_modules'])) {
                $fieldModel->setRelatedModules($fieldInfo['related_modules']);
            }
            echo "done!<br>";
        }
    }
}


global $adb;
// Move field
$tabid=getTabid('AgentManager');
$agentLogoBlock = Vtiger_Block::getInstance('LBL_AGENTMANAGER_LOGO', $moduleModel);
$adb->pquery("UPDATE vtiger_field SET block=? WHERE fieldname= 'imagename' AND tabid = ?", array($agentLogoBlock->id, $tabid));
$agentUpdateInfoBlock = Vtiger_Block::getInstance('LBL_AGENTMANAGER_RECORD_UPDATE_INFORMATION', $moduleModel);
$adb->pquery("UPDATE vtiger_field SET block=? WHERE fieldname= 'createdtime ' AND tabid = ?", array($agentUpdateInfoBlock->id, $tabid));
$adb->pquery("UPDATE vtiger_field SET block=? WHERE fieldname= 'modifiedtime' AND tabid = ?", array($agentUpdateInfoBlock->id, $tabid));
$adb->pquery("UPDATE vtiger_field SET block=?, displaytype = 2 WHERE fieldname= 'assigned_user_id' AND tabid = ?", array($agentUpdateInfoBlock->id, $tabid));

//Udate field name for OrdersTask
$ordersTaskId = getTabid('OrdersTask');
$adb->pquery("UPDATE `vtiger_field` SET `fieldname`='start_date' WHERE (`fieldname`='startdate' AND tabid = ?)", array($ordersTaskId));
$adb->pquery("UPDATE `vtiger_field` SET `fieldname`='end_date' WHERE (`fieldname`='enddate' AND tabid = ?)", array($ordersTaskId));


//3466: Agent Manager - Incomplete design from original design (OT Item: 3292)
$imageField = Vtiger_Field::getInstance('imagename', $moduleModel);
if (!$imageField) {
    $imageField = new Vtiger_Field();
    $imageField->label = 'LBL_AGENTMANAGER_IMAGENAME';
    $imageField->name = 'imagename';
    $imageField->table = 'vtiger_agentmanager';
    $imageField->column = 'imagename';
    $imageField->columntype = 'varchar(255)';
    $imageField->uitype = 69;
    $imageField->typeofdata = 'V~O';
    $agentLogoBlock->addField($imageField);
}

//update field type for created by field
$adb->pquery("UPDATE vtiger_field SET uitype = 52 WHERE `fieldname` = 'agentmanager_created_by' AND `tabid` = ?", array($tabid));

// update sequence for record update information block
$adb->pquery("UPDATE vtiger_field SET sequence = 1 WHERE `fieldname` = 'createdtime' AND `tabid` = ?", array($tabid));
$adb->pquery("UPDATE vtiger_field SET sequence = 2 WHERE `fieldname` = 'modifiedtime' AND `tabid` = ?", array($tabid));
$adb->pquery("UPDATE vtiger_field SET sequence = 3 WHERE `fieldname` = 'agentmanager_created_by' AND `tabid` = ?", array($tabid));
$adb->pquery("UPDATE vtiger_field SET sequence = 4 WHERE `fieldname` = 'smownerid' AND `tabid` = ?", array($tabid));

//update sequence for Address Details block
$adb->pquery("UPDATE vtiger_field SET sequence = 7 WHERE `fieldname` = 'agentmanager_mailing_address_1' AND `tabid` = ?", array($tabid));
$adb->pquery("UPDATE vtiger_field SET sequence = 8 WHERE `fieldname` = 'agentmanager_mailing_address_2' AND `tabid` = ?", array($tabid));
$adb->pquery("UPDATE vtiger_field SET sequence = 9 WHERE `fieldname` = 'agentmanager_mailing_city' AND `tabid` = ?", array($tabid));
$adb->pquery("UPDATE vtiger_field SET sequence = 10 WHERE `fieldname` = 'agentmanager_mailing_state' AND `tabid` = ?", array($tabid));
$adb->pquery("UPDATE vtiger_field SET sequence = 11 WHERE `fieldname` = 'agentmanager_mailing_zip' AND `tabid` = ?", array($tabid));
$adb->pquery("UPDATE vtiger_field SET sequence = 12 WHERE `fieldname` = 'agentmanager_mailing_country' AND `tabid` = ?", array($tabid));
$adb->pquery("UPDATE vtiger_field SET sequence = 13 WHERE `fieldname` = 'phone1' AND `tabid` = ?", array($tabid));
$adb->pquery("UPDATE vtiger_field SET sequence = 14 WHERE `fieldname` = 'phone2' AND `tabid` = ?", array($tabid));
$adb->pquery("UPDATE vtiger_field SET sequence = 15 WHERE `fieldname` = 'fax' AND `tabid` = ?", array($tabid));
$adb->pquery("UPDATE vtiger_field SET sequence = 16 WHERE `fieldname` = 'email' AND `tabid` = ?", array($tabid));


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";