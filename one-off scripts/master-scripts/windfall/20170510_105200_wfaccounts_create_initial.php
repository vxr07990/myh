<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}

global $adb;
function createFieldsAndBlocks_4454($moduleInstance, $listFieldsInfo)
{
    $filter = Vtiger_Filter::getInstance('All', $moduleInstance);
    if(!$filter){
        echo "<br>BEGINNING create filter for module";
        $filter = new Vtiger_Filter();
        $filter->name = "All";
        $filter->isdefault = true;
        $moduleInstance->addFilter($filter);
    }
    foreach ($listFieldsInfo as $blockLabel => $listField) {
        $blockInstance = Vtiger_Block::getInstance($blockLabel, $moduleInstance);
        if (!$blockInstance) {
            echo "<br>BEGINNING create $blockLabel block";
            $blockInstance = new Vtiger_Block();
            $blockInstance->label = $blockLabel;
            $moduleInstance->addBlock($blockInstance);
        }

        foreach ($listField as $fieldName => $fieldInfo) {
            echo "<br>BEGINNING create $fieldName field";
            $fieldModel = Vtiger_Field_Model::getInstance($fieldName, $moduleInstance);
            if ($fieldModel) {
                echo "<br>$fieldName field already exists.";
            } else {
                $fieldModel = new Vtiger_Field();
                $fieldModel->table = $fieldInfo['table'];
                if ($fieldInfo['table'] == '') {
                    $fieldModel->table = 'vtiger_' . strtolower($moduleInstance->name);
                }
                $fieldModel->name = $fieldName;
                foreach ($fieldInfo as $option => $value) {
                    if (!in_array($option, array('picklistvalues', 'related_modules', 'isentityidentifier'))) {
                        $fieldModel->$option = $value;
                    }
                }

                $blockInstance->addField($fieldModel);
                if (isset($fieldInfo['picklistvalues'])) {
                    $fieldModel->setPicklistValues($fieldInfo['picklistvalues']);
                }
                if (isset($fieldInfo['related_modules'])) {
                    $fieldModel->setRelatedModules($fieldInfo['related_modules']);
                }
                if (isset($fieldInfo['isentityidentifier'])) {
                    $moduleInstance->setEntityIdentifier($fieldModel);
                }
                if($fieldInfo['isFilterField']){
                    $filter->addField($fieldModel,$fieldModel->id);
                }
                echo "<br>done!";
            }
        }
    }
}

$wfAccountsInstance = Vtiger_Module::getInstance('WFAccounts');
if(!$wfAccountsInstance){
    echo "<h3>BEING CREATE WFAccounts MODULE</h3>";
    $wfAccountsInstance = new Vtiger_Module();
    $wfAccountsInstance->name = 'WFAccounts';
    $wfAccountsInstance->save();
    $wfAccountsInstance->initTables();
    $wfAccountsInstance->setDefaultSharing();
    $wfAccountsInstance->initWebservice();
    $tableid = $wfAccountsInstance->getId();
    $sql = "SELECT * FROM `vtiger_modtracker_tabs` WHERE `vtiger_modtracker_tabs`.`tabid` = ?";
    $result = $adb->pquery($sql, array($tableid));
    if ($adb->num_rows($result) == 0) {
        $adb->pquery("insert into `vtiger_modtracker_tabs` ( `visible`, `tabid`) values (?, ?)", array('1', $tableid));
    }
}
$moduleInfos = array(
    'LBL_WFACCOUNTS_DETAIL' =>array(
        'name' => array(
            'label' => 'LBL_WFACCOUNTS_NAME',
            'columntype' => 'varchar(255)',
            'uitype' => 1,
            'typeofdata' => 'V~M',
            'isentityidentifier' => true,
            'isFilterField' =>true
        ),
        'wfaccounts_type' => array(
            'label' => 'LBL_WFACCOUNTS_TYPE',
            'columntype' => 'varchar(255)',
            'uitype' => 16,
            'typeofdata' => 'V~O',
            'picklistvalues'=>array('Residential','Commercial'),
            'isFilterField' =>true
        ),
        'company' => array(
            'label' => 'LBL_WFACCOUNTS_COMPANY',
            'columntype' => 'varchar(255)',
            'uitype' => 1,
            'typeofdata' => 'V~O',
            'isFilterField' =>true
        ),
        'national_account' => array(
            'label' => 'LBL_WFACCOUNTS_NATIONAL_ACCOUNT',
            'columntype' => 'varchar(255)',
            'uitype' => 1,
            'typeofdata' => 'V~O',
            'isFilterField' =>true
        ),
        'primary_email' => array(
            'label' => 'LBL_WFACCOUNTS_PRIMARY_EMAIL',
            'columntype' => 'varchar(255)',
            'uitype' => 13,
            'typeofdata' => 'V~O',
            'isFilterField' =>true
        ),
        'primary_phone' => array(
            'label' => 'LBL_WFACCOUNTS_PRIMARY_PHONE',
            'columntype' => 'varchar(255)',
            'uitype' => 11,
            'typeofdata' => 'V~O',
            'isFilterField' =>true
        ),
        'keep_active' => array(
            'label' => 'LBL_WFACCOUNTS_KEEP_ACTIVE',
            'columntype' => 'varchar(3)',
            'uitype' => 56,
            'typeofdata' => 'C~O',
        ),
        'download_to_device' => array(
            'label' => 'LBL_WFACCOUNTS_DOWNLOAD_TO_DEVICE',
            'columntype' => 'varchar(3)',
            'uitype' => 56,
            'typeofdata' => 'C~O',
        ),
        'description' => array(
            'label' => 'LBL_WFACCOUNTS_DESCRIPTION',
            'columntype' => 'text',
            'uitype' => 19,
            'typeofdata' => 'V~O',
        ),
        'logo' => array(
            'label' => 'LBL_WFACCOUNTS_LOGO',
            'columntype' => 'text',
            'uitype' => 69,
            'typeofdata' => 'V~O',
        ),

        'agentid' => array(
            'label' => 'LBL_WFACCOUNTS_OWNER',
            'columntype' => 'int(19)',
            'uitype' => 1002,
            'typeofdata' => 'I~M',
            'tablename' => 'vtiger_crmentity'
        )
    ),
    'LBL_WFACCOUNTS_RECORDUPDATE' => array(
        'createdtime' => array(
            'label' => 'LBL_CREATEDTIME',
            'columntype' => 'datetime',
            'uitype' => 70,
            'typeofdata' => 'T~O',
            'displaytype' => 2,
            'table' => 'vtiger_crmentity'
        ),
        'modifiedtime' => array(
            'label' => 'LBL_MODIFIEDTIME',
            'columntype' => 'datetime',
            'uitype' => 70,
            'typeofdata' => 'T~O',
            'displaytype' => 2,
            'table' => 'vtiger_crmentity'
        ),
        'created_by' => array(
            'label' => 'LBL_CREATEDBY',
            'columntype' => 'int(19)',
            'uitype' => 52,
            'typeofdata' => 'V~O',
            'column' => 'smcreatorid',
            'displaytype' => 2,
            'table' => 'vtiger_crmentity'
        ),
        'assigned_user_id' => array(
            'label' => 'LBL_ASSIGNEDTO',
            'columntype' => 'int(19)',
            'uitype' => 53,
            'typeofdata' => 'V~O',
            'column' => 'smownerid',
            'displaytype' => 2,
            'table' => 'vtiger_crmentity'
        ),
    )
);
$nationalAccountField = Vtiger_Field_Model::getInstance('national_account',$wfAccountsInstance);
if ($nationalAccountField){
    $nationalAccountField->delete();
}
createFieldsAndBlocks_4454($wfAccountsInstance,$moduleInfos);
