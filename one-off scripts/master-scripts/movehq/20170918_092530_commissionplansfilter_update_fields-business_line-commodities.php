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

$db = &PearDatabase::getInstance();

$businessLinePicklist = [
    'Interstate',
    'Intrastate',
    'Local',
    'International'
];
//Set up Commodity fields

$commodityPicklist = [
    'Household',
    'Electronics',
    'Display & Exhibits',
    'General Commodities',
    'Auto',
    'Commercial'
];

$comModuleNames = ['CommissionPlansFilter'];
$alreadyDone = [];
foreach($comModuleNames as $moduleName) {
    $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
    if ($moduleModel) {
        $businessLineFieldName = 'business_line_complansfilter';
        if (!in_array($businessLineFieldName, $alreadyDone)) {
            $businessLineField = Vtiger_Field_Model::getInstance($businessLineFieldName, $moduleModel);
            if ($businessLineField) {
                Vtiger_Utils::ExecuteQuery('TRUNCATE TABLE `vtiger_'.$businessLineFieldName.'`');
                $businessLineField->setPicklistValues($businessLinePicklist);
                $alreadyDone[] = $businessLineFieldName;
            }
        }
        $blockName  = 'LBL_COMMISSIONPLANGROUP';
        $blockModel = Vtiger_Block_Model::getInstance($blockName, $moduleModel);
        if ($blockModel) {
            $fieldCommodity = Vtiger_Field::getInstance('commodities', $moduleModel);
            $tableName      = 'vtiger_commissionplansfilter';
            $uiType         = 3333;
            $typeOfData     = 'V~M';
            $columnType     = 'TEXT';
            if ($fieldCommodity) {
                echo "The commodities field already exists in $moduleName<br>\n";
            } else {
                $fieldCommodity             = new Vtiger_Field();
                $fieldCommodity->label      = 'LBL_COMMODITIES';
                $fieldCommodity->name       = 'commodities';
                $fieldCommodity->table      = $tableName;
                $fieldCommodity->column     = 'commodities';
                $fieldCommodity->columntype = $columnType;
                $fieldCommodity->uitype     = $uiType;
                $fieldCommodity->typeofdata = $typeOfData;
                $blockModel->addField($fieldCommodity);
                $fieldCommodity->setPicklistValues($commodityPicklist);
            }
        }
    }
}
$moduleName = 'CommissionPlansFilter';
$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
if ($moduleModel) {
    //Update sort order
    $fieldSeq = [
        'commissionplan',
        'agentid',
        'commodities',
        'business_line_complansfilter',
        'billing_type',
        'authority',
        'related_tariff',
        'related_contract',
        'commissionplansfilter_status',
        'miles_from',
        'miles_to',
        'weight_from',
        'weight_to',
        'effective_date_from',
        'effective_date_to',
    ];
    foreach ($fieldSeq as $key => $field) {
        $fieldInstance = Vtiger_Field::getInstance($field, $moduleModel);
        if ($fieldInstance) {
            $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
            $db->pquery($sql, [$key + 1, $fieldInstance->id]);
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
