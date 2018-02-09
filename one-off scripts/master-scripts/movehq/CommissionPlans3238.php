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

require_once('vtlib/Vtiger/Menu.php');
require_once('vtlib/Vtiger/Module.php');
require_once('includes/main/WebUI.php');
require_once('includes/runtime/LanguageHandler.php');

//needs these
require_once('include/Webservices/Create.php');
require_once('modules/Vtiger/uitypes/Date.php');

echo "\nBEGINNING Create CommissionPlans Module\n";

echo "\nBEGINNING Creating Module\n";

$db = PearDatabase::getInstance();

//$commPlansInstance->setEntityIdentifier($field1);

$commPlansInstance = Vtiger_Module::getInstance('CommissionPlans');
if (!$commPlansInstance) {
    $commPlansInstance = new Vtiger_Module();
    $commPlansInstance->name = 'CommissionPlans';
    $commPlansInstance->save();
    $commPlansInstance->initTables();
    $commPlansInstance->setDefaultSharing();
    $commPlansInstance->initWebservice();
}

$arrModuleStructure = array(
    'LBL_COMMISSIONPLANDETAIL'=>array(
        'name'=>array(
            'label'=>'LBL_NAME',
            'columntype'=>'VARCHAR(100)',
            'uitype'=>1,
            'typeofdata'=>'V~M',
            'isentityidentifier'=>true,
        ),
        'description'=>array(
            'label'=>'LBL_DESCRIPTION',
            'columntype'=>'VARCHAR(255)',
            'uitype'=>1,
            'typeofdata'=>'V~O'
        ),
        'agentid'=>array(
            'label'=>'Owner',
            'columntype'=>'int(11)',
            'uitype'=>1002,
            'typeofdata'=>'V~O',
            'tablename'=>'vtiger_crmentity'
        ),
        'commissionplans_status'=>array(
            'label'=>'LBL_STATUS',
            'columntype'=>'VARCHAR(10)',
            'uitype'=>16,
            'typeofdata'=>'V~M',
            'defaultvalue'=>'Active',
            'picklistvalues'=>array('Active','Inactive'),
        ),
    ),
    'LBL_RECORDUPDATEINFORMATION'=>array(
        'createdtime'=>array(
            'label'=>'LBL_DATECREATED',
            'columntype'=>'datetime',
            'uitype'=>70,
            'typeofdata'=>'T~O',
            'displaytype'=>2,
            'table'=>'vtiger_crmentity'
        ),
        'modifiedtime'=>array(
            'label'=>'LBL_DATEMODIFIED',
            'columntype'=>'datetime',
            'uitype'=>70,
            'typeofdata'=>'T~O',
            'displaytype'=>2,
            'table'=>'vtiger_crmentity'
        ),
        'created_user_id'=>array(
            'label'=>'Created By',
            'columntype'=>'int(19)',
            'uitype'=>52,
            'typeofdata'=>'V~O',
            'columnname'=>'smcreatorid',
            'displaytype'=>2,
            'table'=>'vtiger_crmentity'
        ),
        'assigned_user_id'=>array(
            'label'=>'Assigned To',
            'columntype'=>'int(19)',
            'uitype'=>53,
            'typeofdata'=>'V~M',
            'columnname'=>'smownerid',
            'displaytype'=>2,
            'table'=>'vtiger_crmentity'
        ),
    )
);

createFieldsAndBlocks3238($commPlansInstance, $arrModuleStructure);




echo "\nBEGINNING Create CommissionPlansFilter Module\n";

$commPlanFilterInstance = Vtiger_Module::getInstance('CommissionPlansFilter');
if (!$commPlanFilterInstance) {
    $commPlanFilterInstance = new Vtiger_Module();
    $commPlanFilterInstance->name = 'CommissionPlansFilter';
    $commPlanFilterInstance->save();
    $commPlanFilterInstance->initTables();
    $commPlanFilterInstance->setDefaultSharing();
    $commPlanFilterInstance->initWebservice();
}

$arrModuleStructure = array(
    'LBL_COMMISSIONPLANGROUP'=>array(
        'commissionplan'=>array(
            'label'=>'LBL_COMMISSIONPLAN',
            'columntype'=>'int(19)',
            'uitype'=>10,
            'typeofdata'=>'I~M',
            'related_modules'=>array('CommissionPlans'),
            'isentityidentifier'=>true,
        ),
        'agentid'=>array(
            'label'=>'Owner',
            'columntype'=>'int(19)',
            'uitype'=>1002,
            'typeofdata'=>'I~M',
            'tablename'=>'vtiger_crmentity'
        ),
        'business_line'=>array(
            'label'=>'LBL_BUSINESSLINE',
            'columntype'=>'VARCHAR(255)',
            'uitype'=>3333,
            'typeofdata'=>'V~M',
            'defaultvalue'=>'All',
        ),
        'billing_type'=>array(
            'label'=>'LBL_BILLINGTYPE',
            'columntype'=>'VARCHAR(255)',
            'uitype'=>3333,
            'typeofdata'=>'V~M',
            'defaultvalue'=>'All',
        ),
        'authority'=>array(
            'label'=>'LBL_AUTHORITY',
            'columntype'=>'VARCHAR(50)',
            'uitype'=>3333,
            'typeofdata'=>'V~M',
            'defaultvalue'=>'All',
            'picklistvalues'=>array('Van Line','Own Authority','Other Agent Authority')
        ),
        'commissionplansfilter_status'=>array(
            'label'=>'LBL_STATUS',
            'columntype'=>'VARCHAR(50)',
            'uitype'=>16,
            'typeofdata'=>'V~M',
            'defaultvalue'=>'Active',
            'picklistvalues'=>array('Active','Inactive')
        ),
        'related_tariff'=>array(
            'label'=>'Tariff',
            'columntype'=>'VARCHAR(255)',
            'uitype'=>1990,
            'typeofdata'=>'V~M',
            'related_modules'=>array('Tariffs','TariffManager'),
        ),
        'related_contract'=>array(
            'label'=>'Contract',
            'columntype'=>'VARCHAR(255)',
            'uitype'=>1990,
            'typeofdata'=>'V~M',
            'related_modules'=>array('Contracts'),
        ),
        'miles_from'=>array(
            'label'=>'Miles From',
            'columntype'=>'int(10)',
            'uitype'=>7,
            'typeofdata'=>'I~O',
        ),
        'miles_to'=>array(
            'label'=>'Miles To',
            'columntype'=>'int(10)',
            'uitype'=>7,
            'typeofdata'=>'I~O',
        ),
        'weight_from'=>array(
            'label'=>'Weight From',
            'columntype'=>'int(10)',
            'uitype'=>7,
            'typeofdata'=>'I~O',
        ),
        'weight_to'=>array(
            'label'=>'Weight To',
            'columntype'=>'int(10)',
            'uitype'=>7,
            'typeofdata'=>'I~O',
        ),
        'effective_date_from'=>array(
            'label'=>'Effective Date From',
            'columntype'=>'date',
            'uitype'=>5,
            'typeofdata'=>'D~O',
        ),
        'effective_date_to'=>array(
            'label'=>'Effective Date To',
            'columntype'=>'date',
            'uitype'=>5,
            'typeofdata'=>'D~O',
        ),
    ),
    'LBL_COMMISSION_PLAN_ITEMS'=>array(),
    'LBL_RECORDUPDATEINFORMATION'=>array(
        'createdtime'=>array(
            'label'=>'LBL_DATECREATED',
            'columntype'=>'datetime',
            'uitype'=>70,
            'typeofdata'=>'T~O',
            'displaytype'=>2,
            'table'=>'vtiger_crmentity'
        ),
        'modifiedtime'=>array(
            'label'=>'LBL_DATEMODIFIED',
            'columntype'=>'datetime',
            'uitype'=>70,
            'typeofdata'=>'T~O',
            'displaytype'=>2,
            'table'=>'vtiger_crmentity'
        ),
        'created_user_id'=>array(
            'label'=>'Created By',
            'columntype'=>'int(19)',
            'uitype'=>52,
            'typeofdata'=>'V~O',
            'columnname'=>'smcreatorid',
            'displaytype'=>2,
            'table'=>'vtiger_crmentity'
        ),
        'assigned_user_id'=>array(
            'label'=>'Assigned To',
            'columntype'=>'int(19)',
            'uitype'=>53,
            'typeofdata'=>'V~M',
            'columnname'=>'smownerid',
            'displaytype'=>2,
            'table'=>'vtiger_crmentity'
        ),
    )
);

createFieldsAndBlocks3238($commPlanFilterInstance, $arrModuleStructure);


echo "\nBEGINNING Create CommissionPlansItem Module\n";
$commPlanItemInstance = Vtiger_Module::getInstance('CommissionPlansItem');
if (!$commPlanItemInstance) {
    $commPlanItemInstance = new Vtiger_Module();
    $commPlanItemInstance->name = 'CommissionPlansItem';
    $commPlanItemInstance->save();
    $commPlanItemInstance->initTables();
    $commPlanItemInstance->setDefaultSharing();
    $commPlanItemInstance->initWebservice();
}


$arrModuleStructure = array(
    'LBL_COMMISSIONPLANITEMSDETAIL'=>array(
        'commissionplan_default'=>array(
            'label'=>'Default',
            'columntype'=>'varchar(10)',
            'uitype'=>16,
            'typeofdata'=>'V~M',
            'picklistvalues'=>array('True','False'),
            'defaultvalue'=>'True',
        ),
        'commissionplan_group'=>array(
            'label'=>'Group',
            'columntype'=>'int(19)',
            'uitype'=>10,
            'typeofdata'=>'I~O',
            'related_modules'=>array('RevenueGrouping')
        ),
        'itemcodefrom'=>array(
            'label'=>'Item Code From',
            'columntype'=>'int(19)',
            'uitype'=>10,
            'typeofdata'=>'I~O',
            'related_modules'=>array('ItemCodes')
        ),
        'itemcodeto'=>array(
            'label'=>'Item Code To',
            'columntype'=>'int(19)',
            'uitype'=>10,
            'typeofdata'=>'I~O',
            'related_modules'=>array('ItemCodes')
        ),
        'rate'=>array(
            'label'=>'Rate',
            'columntype'=>'decimal(10,2)',
            'uitype'=>7,
            'typeofdata'=>'NN~M',
        ),
        'commissiontype'=>array(
            'label'=>'Commission Type',
            'columntype'=>'varchar(50)',
            'uitype'=>16,
            'typeofdata'=>'V~M',
            'picklistvalues'=>array('Percent','Per Hour','Per Hour Overtime','Per Hour Double Time',' Per Piece','Per Mile','Per Stop','Per Job CWT','Per Billing CWT','Per Gross CWT','Flat Rate'),
            'isentityidentifier'=>true,
        ),
        'commissionbasis'=>array(
            'label'=>'Commission Basis',
            'columntype'=>'varchar(50)',
            'uitype'=>16,
            'typeofdata'=>'V~M',
            'picklistvalues'=>array('Revenue Amount','Invoice Amount','Net Amount'),
        ),
        'commissionplansfilterid'=>array(
            'label'=>'Commission Plans Filter',
            'columntype'=>'int(19)',
            'uitype'=>10,
            'typeofdata'=>'I~O',
            'related_modules'=>array('CommissionPlansFilter'),
        ),
    ),
);

createFieldsAndBlocks3238($commPlanItemInstance, $arrModuleStructure);

function createFieldsAndBlocks3238($moduleInstance, $listFieldsInfo)
{
    global $adb;
    foreach ($listFieldsInfo as $blockLabel => $listField) {
        $blockInstance = Vtiger_Block::getInstance($blockLabel, $moduleInstance);
        if (!$blockInstance) {
            $blockInstance = new Vtiger_Block();
            $blockInstance->label = $blockLabel;
            $moduleInstance->addBlock($blockInstance);
        }
        foreach ($listField as $fieldName =>$fieldInfo) {
            echo "\nBEGINNING create $fieldName field\n";
            $fieldModel = Vtiger_Field_Model::getInstance($fieldName, $moduleInstance);
            if ($fieldModel) {
                echo "\n$fieldName field already exists.\n";
                if(in_array($fieldName,array('created_user_id','assigned_user_id'))) {
                    $adb->pquery("UPDATE `vtiger_field` SET `columnname`=? WHERE (`fieldid`=?);", array($fieldInfo['columnname'],$fieldModel->getId()));
                }
            } else {
                $fieldModel = new Vtiger_Field();
                $fieldModel->table=$fieldInfo['table'];
                if($fieldInfo['table'] =='') {
                $fieldModel->table = 'vtiger_'.strtolower($moduleInstance->name);
                }

                $columnname = $fieldInfo['columnname'];
                if($columnname == '') {
                    $columnname = $fieldName;
                }
                $fieldModel->columnname = $columnname;
                $fieldModel->name = $fieldName;
                foreach ($fieldInfo as $option =>$value) {
                    if (!in_array($option, array('picklistvalues', 'related_modules'))) {
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
                echo "done!\n";
            }
        }
    }
}
//Update menu on setting for Commission Plan module
$adb->pquery("UPDATE vtiger_settings_field SET `name` = 'CommissionPlans' , `description`= 'CommissionPlans' , `linkto` = 'index.php?module=CommissionPlans&view=List' WHERE `name` = 'CommPlans'");

//Update data type of field has uitype = 3333
$rsUIType = $adb->pquery("SELECT fieldname,columnname FROM vtiger_field WHERE tabid = ? and (uitype = 3333 or uitype = 1990 or uitype = 1989)", array(getTabid('CommissionPlansFilter')));
while ($row = $adb->fetchByAssoc($rsUIType)) {
    $adb->pquery("ALTER TABLE {$commPlanFilterInstance->basetable} MODIFY COLUMN {$row['columnname']} text");
}

//Update related nmodule for group field on CommissionPlansItem
$groupFieldModel = Vtiger_Field_Model::getInstance('commissionplan_group', $commPlanItemInstance);
if ($groupFieldModel) {
    $adb->pquery("UPDATE vtiger_fieldmodulerel SET module = 'CommissionPlansItem', relmodule = 'RevenueGroupingItem' WHERE fieldid =?", array($groupFieldModel->getId()));
}

$rsCheckRelatedLink = $adb->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid = ?  AND related_tabid = ?", array(getTabid('CommissionPlans'), getTabid('CommissionPlansFilter')));
if ($adb->num_rows($rsCheckRelatedLink) == 0) {
    $commPlansInstance ->setRelatedList($commPlanFilterInstance, 'Commission Plans Filter', 'ADD', 'get_dependents_list');
}

//Update field
$adb->pquery("UPDATE vtiger_field SET typeofdata = 'V~O' WHERE fieldname = 'rate' AND tabid = ?",[getTabid('CommissionPlansItem')]);
$adb->pquery("UPDATE vtiger_field SET defaultvalue = 'Yes' WHERE fieldname = 'commissionplan_default' AND tabid = ?",[getTabid('CommissionPlansItem')]);
//Update picklist value for commissionplan_default
$adb->pquery("UPDATE vtiger_commissionplan_default SET commissionplan_default = 'Yes' WHERE commissionplan_default = 'True'",[]);
$adb->pquery("UPDATE vtiger_commissionplan_default SET commissionplan_default = 'No' WHERE commissionplan_default = 'False'",[]);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";