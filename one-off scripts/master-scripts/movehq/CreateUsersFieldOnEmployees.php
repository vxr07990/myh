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

global $adb;
// Exited fields on Employees
// Users field - Employees field

/* Users Blocks
    User Login & Role
    Calendar Settings
    More Information
    User Advanced Options
    Currency and Number Field Configuration
    SMTP User information
    Microsoft Exchange
 * */
$arrMappings= array(
    'first_name'=>'name',
    'last_name'=>'employee_lastname',
    'email1'=>'employee_email',
    'phone_mobile'=>'employee_mphone',
    'phone_home'=>'employee_hphone',
    'address_street'=>'address1',
    'address_city'=>'city',
    'address_state'=>'state',
    'address_postalcode'=>'zip',
    'address_country'=>'country',
    'title'=>'employees_title',
    'imagename'=>'imagename',
    'status'=>'employee_status',
);
$arrBlocksFields=array();
// Get all fields of Users
$query="SELECT fieldid, columnname,tablename,fieldname, fieldlabel, maximumlength, displaytype, typeofdata, uitype, blocklabel
        FROM `vtiger_field`
        INNER JOIN vtiger_blocks ON vtiger_blocks.blockid=vtiger_field.block
        WHERE `vtiger_field`.`tabid` = '29' AND fieldname NOT IN (".generateQuestionMarks(array_keys($arrMappings)).")";
$result=$adb->pquery($query, array_keys($arrMappings));
if ($adb->num_rows($result)>0) {
    while ($row=$adb->fetch_array($result)) {
        $blocklabel = vtranslate($row['blocklabel'], 'Users');
        $arrBlocksFields[$blocklabel][$row['fieldid']]=array(
            'columnname' => $row['columnname'],
            'fieldname' => $row['fieldname'],
            'displaytype' => $row['displaytype'],
            'maximumlength' => $row['maximumlength'],
            'typeofdata' => $row['typeofdata'],
            'uitype' => $row['uitype'],
            'fieldlabel' => vtranslate($row['fieldlabel'], 'Users')
        );
    }
}

// Create Blocks And Fields
$moduleModel = Vtiger_Module_Model::getInstance('Employees');

$blockObject = Vtiger_Block::getInstance('Move HQ User', $moduleModel);
if ($blockObject) {
    echo "<li> the Move HQ User block already exists on Employees module</li><br>";
} else {
    // Create "Move HQ User" block and field
    $blockInstance = new Settings_LayoutEditor_Block_Model();
    $blockInstance->set('label', 'Move HQ User');
    $blockInstance->set('iscustom', '1');
    $blockId = $blockInstance->save($moduleModel);
    $blockObject = Vtiger_Block::getInstance('Move HQ User', $moduleModel);
}
$blockModel = Vtiger_Block_Model::getInstanceFromBlockObject($blockObject);

$field=Vtiger_Field_Model::getInstance("move_hq_user", $moduleModel);
if ($field) {
    echo "<li> the Move HQ User field already exists on Employees module</li><br>";
} else {
    $fieldModel = new Vtiger_Field_Model();
    $fieldModel->set('name', 'move_hq_user')
        ->set('table', 'vtiger_employeescf')
        ->set('generatedtype', 1)
        ->set('uitype', 16)
        ->set('label', 'Move HQ User')
        ->set('typeofdata', 'V~O')
        ->set('displaytype', 1)
        ->set('columntype', "varchar(250)");
    $blockModel->addField($fieldModel);
    $fieldModel->setPicklistValues(array('Yes', 'No'));
}



foreach ($arrBlocksFields as $blocklabel => $fields) {
    $blockObject = Vtiger_Block::getInstance($blocklabel, $moduleModel);
    if ($blockObject) {
        echo "<li> the {$blocklabel} block already exists on Employees module</li><br>";
    } else {
        // Create block
        $blockInstance = new Settings_LayoutEditor_Block_Model();
        $blockInstance->set('label', $blocklabel);
        $blockInstance->set('iscustom', '1');
        $blockId = $blockInstance->save($moduleModel);
        $blockObject = Vtiger_Block::getInstance($blocklabel, $moduleModel);
    }
    $blockModel = Vtiger_Block_Model::getInstanceFromBlockObject($blockObject);

    // Create Fields
    foreach ($fields as $fieldId => $fieldInfo) {
        $fieldM=Vtiger_Field_Model::getInstance($fieldInfo['fieldname'], $moduleModel);
        if ($fieldM) {
            echo "<li> the {$fieldInfo['fieldname']} field already exists on Employees module</li><br>";
        } else {
            if (in_array($fieldInfo['uitype'], array('21', '1003'))) {
                $columntype = 'text';
            } else {
                $columntype = "varchar({$fieldInfo['maximumlength']})";
            }
            $tablename = 'vtiger_employeescf';
            if ($fieldInfo['fieldname'] == 'roleid') {
                $tablename = 'vtiger_user2role';
            }
            $fieldModel = new Vtiger_Field_Model();
            $fieldModel->set('name', $fieldInfo['fieldname'])
                ->set('table', $tablename)
                ->set('generatedtype', 1)
                ->set('uitype', $fieldInfo['uitype'])
                ->set('label', $fieldInfo['fieldlabel'])
                ->set('typeofdata', $fieldInfo['typeofdata'])
                ->set('displaytype', $fieldInfo['displaytype'])
                ->set('columntype', $columntype);
            $blockModel->addField($fieldModel);
        }
    }
}


// Add userid to employees
$adb->pquery("ALTER TABLE `vtiger_employees` ADD COLUMN `userid` int(11) ;");

echo "SUCCESS";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";