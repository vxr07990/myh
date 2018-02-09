<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$db = PearDatabase::getInstance();

//hide old visible business line fields
foreach(['business_line2', 'business_line_est2'] as $hidingFieldName) {
    $db->pquery("UPDATE `vtiger_field` SET presence = 1 WHERE fieldname =?", [$hidingFieldName]);
}

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

$comModuleNames = ['Leads', 'Opportunities', 'Orders', 'Estimates','Actuals', 'Accounts', 'Contracts', 'ClaimsSummary', 'OrdersTask', 'ItemCodesMapping'];
$alreadyDone = [];
foreach($comModuleNames as $moduleName){
    $$moduleName = false;
    $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
    if($moduleModel){
        $businessLineFieldName = $moduleName == 'ItemCodesMapping'?'itcmapping_businessline':(($moduleName == 'Estimates' || $moduleName == 'Actuals')?'business_line_est':'business_line');
        if(!in_array($businessLineFieldName, $alreadyDone)) {
            $businessLineField = Vtiger_Field_Model::getInstance($businessLineFieldName, $moduleModel);
            if ($businessLineField) {
                Vtiger_Utils::ExecuteQuery('TRUNCATE TABLE `vtiger_'.$businessLineFieldName.'`');
                $businessLineField->setPicklistValues($businessLinePicklist);
                $alreadyDone[] = $businessLineFieldName;
            }
        }
        $blockName = 'LBL_'.strtoupper($moduleName).'_INFORMATION';
        switch($moduleName){
            case 'Estimates':
            case 'Actuals':
                $blockName = 'LBL_QUOTE_INFORMATION';
                break;
            case 'Opportunities';
                $blockName = 'LBL_POTENTIALS_INFORMATION';
                break;
            case 'Accounts':
                $blockName = 'LBL_ACCOUNT_INFORMATION';
                break;
            case 'OrdersTask':
                $blockName = 'LBL_OPERATIVE_TASK_INFORMATION';
                break;
            case 'ItemCodesMapping':
                $blockName = 'LBL_ITEMCODES_MAPPING';
                break;
        }
        $blockModel = Vtiger_Block_Model::getInstance($blockName, $moduleModel);
        if($blockModel){
            $fieldCommodity = Vtiger_Field::getInstance('commodities', $moduleModel);
            $uiType = 16;
            $typeOfData = 'V~M';
            $columnType = 'VARCHAR(200)';
            $tableName = 'vtiger_'.strtolower($moduleName);
            if ($fieldCommodity) {
                echo "The commodities field already exists in $moduleName<br>\n";
            } else {
                switch($moduleName){
                    case 'Estimates':
                    case 'Actuals':
                        $tableName = 'vtiger_quotescf';
                        break;
                    case 'Opportunities':
                        $tableName = 'vtiger_potentialscf';
                        break;
                    case 'Leads':
                        $tableName = 'vtiger_leadscf';
                        break;
                    case 'Accounts':
                        $tableName = 'vtiger_account';
                        $uiType = 3333;
                        $typeOfData = 'V~O';
                        $columnType = 'TEXT';
                        break;
                    case 'OrdersTask':
                        $typeOfData = 'V~O';
                        break;
                    case 'ItemCodesMapping':
                        $uiType = 3333;
                        $columnType = 'TEXT';
                        break;
                };
                $fieldCommodity             = new Vtiger_Field();
                $fieldCommodity->label      = 'LBL_COMMODITIES';
                $fieldCommodity->name       = 'commodities';
                $fieldCommodity->table      = $tableName;
                $fieldCommodity->column     = 'commodities';
                $fieldCommodity->columntype = $columnType;
                $fieldCommodity->uitype     = $uiType;
                $fieldCommodity->typeofdata = $typeOfData;
                $blockModel->addField($fieldCommodity);
                $$moduleName = true;
                $fieldCommodity->setPicklistValues($commodityPicklist);
            }
        }
    }
}

//Update sort order

if($Estimates){
    $fieldSeq = [
        'subject',
        'business_line_est',
        'commodities',
        'potential_id',
        'quote_no',
        'quotestage',
        'validtill',
        'contact_id',
        'account_id',
        'assigned_user_id',
        'is_primary',
        'orders_id',
        'load_date',
        'contract',
        'createdtime',
        'billing_type',
        'modifiedtime',
        'authority',
        'agentid',
        'quotation_type',
        'pre_tax_total',
        'estimate_type',
        'conversion_rate',
        'hdnDiscountAmount',
        'hdnS_H_Amount',
        'modifiedby',
        'effective_tariff',
        'hdnSubTotal',
        'txtAdjustment',
        'hdnGrandTotal',
        'hdnTaxType',
        'hdnDiscountPercent',
        'currency_id',
        'cubesheet',
    ];
    AddMandatoryHCCC('Estimates', 'business_line_est');
    ReorderFieldsHCCC($fieldSeq, 'Estimates');
}

if($Orders){
    $fieldSeq = [
        'orders_contacts',
        'orders_no',
        'business_line',
        'commodities',
        'billing_type',
        'authority',
        'assigned_user_id',
        'agentid',
        'orders_ponumber',
        'orders_bolnumber',
        'orders_vanlineregnum',
        'ordersstatus',
        'order_reason',
        'orders_opportunities',
        'orders_relatedorders',
        'orders_account',
        'account_contract',
        'orders_elinehaul',
        'national_account_number',
        'orders_etotal',
        'tariff_id',
        'orders_discount',
        'orders_sit',
        'competitive',
        'mileage',
    ];
    AddMandatoryHCCC('Orders', 'business_line');
    ReorderFieldsHCCC($fieldSeq, 'Orders');
    updateFieldValuesHCCC('vtiger_orders', 'business_line');
}

if($Leads){
    $fieldSeq = [
        'business_line',
        'commodities',
        'agentid',
        'leadstatus',
        'reason_cancelled',
        'leadsource',
        'assigned_user_id',
    ];
    AddMandatoryHCCC('Leads', 'business_line');
    ReorderFieldsHCCC($fieldSeq, 'Leads');
    updateFieldValuesHCCC('vtiger_leadscf', 'business_line');
}

if($Actuals){
    $fieldSeq = [
        'subject',
        'actuals_stage',
        'orders_id',
        'contact_id',
        'business_line_est',
        'commodities',
        'billing_type',
        'authority',
        'effective_tariff',
        'account_id',
        'contract',
        'validtill',
        'load_date',
        'quotation_type',
        'estimate_type',
        'assigned_user_id',
        'agentid',
        'createdtime',
        'modifiedtime',
        'quote_no',
        'pre_tax_total',
        'modifiedby',
        'conversion_rate',
        'hdnDiscountAmount',
        'hdnS_H_Amount',
        'hdnSubTotal',
        'txtAdjustment',
        'hdnGrandTotal',
        'hdnTaxType',
        'hdnDiscountPercent',
        'currency_id',
    ];
    AddMandatoryHCCC('Actuals', 'business_line_est');
    ReorderFieldsHCCC($fieldSeq, 'Actuals');
    updateFieldValuesHCCC('vtiger_quotescf', 'business_line_est');
}

if($Opportunities){
    $fieldSeq = [
        'contact_id',
        'opportunitystatus',
        'potentialname',
        'opportunityreason',
        'business_line',
        'commodities',
        'billing_type',
        'amount',
        'leadsource',
        'closingdate',
        'related_to',
        'oppotunitiescontract',
        'is_competitive',
        'potential_no',
        'agentid',
        'created_user_id',
        'isconvertedfromlead',
        'converted_from',
        'leadsource_workspace',
        'leadsource_national',
    ];
    AddMandatoryHCCC('Opportunities', 'business_line');
    ReorderFieldsHCCC($fieldSeq, 'Opportunities');
    updateFieldValuesHCCC('vtiger_potentialscf', 'business_line');

}

if($Contracts){
    $fieldSeq = [
        'contract_no',
        'nat_account_no',
        'begin_date',
        'end_date',
        'related_tariff',
        'local_tariff',
        'assigned_user_id',
        'contract_status',
        'extended_sit_mileage',
        'description',
        'contracting_entity_name',
        'initial_contract_term',
        'agentid',
        'business_line',
        'commodities',
    ];
    AddMandatoryHCCC('Contracts', 'business_line');
    ReorderFieldsHCCC($fieldSeq, 'Contracts');
    updateFieldValuesHCCC('vtiger_contracts', 'business_line');
}

if($ClaimsSummary){
    $fieldSeq = [
        'claimssummary_claimssummary',
        'claimssummary_preferred',
        'claimssummary_contactid',
        'claimssummary_valuationtype',
        'claimssummary_representative',
        'claimssummary_orderid',
        'claimssummary_accountid',
        'claimssummary_declaredvalue',
        'item_status',
        'assigned_user_id',
        'createdtime',
        'modifiedtime',
        'agentid',
        'business_line',
        'commodities',
    ];
    AddMandatoryHCCC('ClaimsSummary', 'business_line');
    ReorderFieldsHCCC($fieldSeq, 'ClaimsSummary');
    updateFieldValuesHCCC('vtiger_claimssummary', 'claimssummary_businessline');
}

if($Accounts){
    $fieldSeq = [
        'accountname',
        'account_status',
        'account_no',
        'national_account_number',
        'customer_number',
        'address1',
        'address2',
        'city',
        'state',
        'zip',
        'country',
        'phone',
        'otherphone',
        'fax',
        'email1',
        'email2',
        'emailoptout',
        'business_line',
        'commodities',
        'billing_type',
        'notify_owner',
        'member_of',
        'agentid',
        'modifiedtime',
        'createdtime',
        'assigned_user_id',
        'modifiedby',
        'isconvertedfromlead',
        'created_user_id',
        'brand',
    ];
    ReorderFieldsHCCC($fieldSeq, 'Accounts');

}

if($OrdersTask){
    $fieldSeq = [
        'ordersid',
        'agentid',
        'operations_task',
        'business_line',
        'commodities',
        'date_spread',
        'multiservice_date',
        'include_saturday',
        'include_sunday',
        'service_date_from',
        'service_date_to',
        'pref_date_service',
        'task_start',
        'participating_agent',
        'calendarcode',
        'estimated_hours',
        'specialrequest',
        'total_estimated_personnel',
        'total_estimated_vehicles',
        'notes_to_dispatcher',
        'service_provider_notes',
        'cancel_task',
        'reason_cancelled',
        'assigned_user_id',
        'createdtime',
        'modifiedtime'
    ];
    ReorderFieldsHCCC($fieldSeq, 'OrdersTask');
    updateFieldValuesHCCC('vtiger_orderstask', 'business_line');
}

if($ItemCodesMapping) {
    $fieldSeq = [
        'itcmapping_businessline',
        'commodities',
        'itcmapping_billingtype',
        'itcmapping_authority',
        'itcmapping_salesexpense',
        'itcmapping_owner_operatorexpense',
        'itcmapping_company_driverexpense',
        'itcmapping_lease_driverexpense',
        'itcmapping_packer_expense',
        'itcmapping_3rdparty_serviceexpense'
    ];
    ReorderFieldsHCCC($fieldSeq, 'ItemCodesMapping');
}

function ReorderFieldsHCCC($fieldOrder, $moduleName){
    $db = PearDatabase::getInstance();
    $module = Vtiger_Module_Model::getInstance($moduleName);
    foreach ($fieldOrder as $key => $field) {
        $fieldInstance = Vtiger_Field::getInstance($field, $module);
        $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
        $db->pquery($sql, [$key+1, $fieldInstance->id]);
    }
}



function AddMandatoryHCCC($moduleName, $fieldName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $changingField = Vtiger_Field::getInstance($fieldName, $module);
        if ($changingField) {
            $typeOfData = $changingField->typeofdata;
            $isMatch = preg_match('/~O/', $typeOfData);
            if ($isMatch === false) {
                print "ERROR: couldn't preg_match?";
            } elseif ($isMatch) {
                $typeOfData = preg_replace('/~O/', '~M', $typeOfData);
                print "<br>$moduleName $fieldName needs converting to mandatory<br>\n";
                $stmt = "UPDATE `vtiger_field` SET `typeofdata` = ?"
                        //. " `quickcreate` = 1"
                        ." WHERE `fieldid` = ? LIMIT 1";
                print "$stmt\n";
                print "$typeOfData, " . $changingField->id  ."<br />\n";
                $db->pquery($stmt, [$typeOfData, $changingField->id]);
                print "<br>$moduleName $fieldName is converted to mandatory<br>\n";
            } else {
                print "<br>$moduleName $fieldName is already mandatory<br>\n";
            }
        } else {
            print "<br />failed to find: $fieldName in $moduleName<br />\n";
        }
    } else {
        print "<br />failed to load module $moduleName<br />\n";
    }
}

function updateFieldValuesHCCC($tableName, $businessline){
    $oldBusinessLine = $businessline.'2';
    if($tableName == 'vtiger_orderstask' || $tableName == 'vtiger_contracts' || $tableName == 'vtiger_claimssummary'){
        $oldBusinessLine = $businessline;
    }
    $commodityValues = [
        'Household',
        'Electronics',
        'Display & Exhibits',
        'General Commodities',
        'Auto',
        'Commercial'
    ];
    foreach($commodityValues as $commodity){
        $businessLineVal = $commodity;
        if($businessLineVal == 'Household'){
            $businessLineVal = 'HHG';
        }
        $sql = 'UPDATE `'.$tableName.'` SET commodities = \''.$commodity.'\' WHERE '.$oldBusinessLine.' LIKE \'%'.$businessLineVal.'%\'';
        Vtiger_Utils::ExecuteQuery($sql);
    }

    $newBLvalues = [
        'Interstate',
        'Intrastate',
        'Local',
        'International'
    ];

    foreach($newBLvalues as $newBLvalue){
        $sql = 'UPDATE `'.$tableName.'` SET '.$businessline.' = \''.$newBLvalue.'\' WHERE '.$oldBusinessLine.' LIKE\'%'.$newBLvalue.'%\'';
        Vtiger_Utils::ExecuteQuery($sql);
    }

    //SQL statement declared here due to oddity with phpstorm and displaying ampersand
//    $exhibitsSQL = 'UPDATE `'.$tableName.'` SET commodity = "Display & Exhibits" WHERE '.$oldBusinessLine.' LIKE "%Display & Exhibits%"';
//    Vtiger_Utils::ExecuteQuery("UPDATE `$tableName` SET commodity = 'Auto' WHERE $oldBusinessLine LIKE '%Auto%'");
//    Vtiger_Utils::ExecuteQuery("UPDATE `$tableName` SET commodity = 'Commercial' WHERE $oldBusinessLine LIKE '%Commercial%'");
//    Vtiger_Utils::ExecuteQuery("UPDATE `$tableName` SET commodity = 'Household' WHERE $oldBusinessLine LIKE '%HHG%'");
//    Vtiger_Utils::ExecuteQuery($exhibitsSQL);
//    Vtiger_Utils::ExecuteQuery("UPDATE `$tableName` SET commodity = 'Electronics' WHERE $oldBusinessLine LIKE '%Electronics%'");
//    Vtiger_Utils::ExecuteQuery("UPDATE `$tableName` SET commodity = 'General Commodities' WHERE $oldBusinessLine LIKE '%Commodities%'");
//    Vtiger_Utils::ExecuteQuery("UPDATE `$tableName` SET $businessline = 'Interstate' WHERE $oldBusinessLine LIKE '%Interstate%'");
//    Vtiger_Utils::ExecuteQuery("UPDATE `$tableName` SET $businessline = 'Intrastate' WHERE $oldBusinessLine LIKE '%Intrastate%'");
//    Vtiger_Utils::ExecuteQuery("UPDATE `$tableName` SET $businessline = 'International' WHERE $oldBusinessLine LIKE '%International%'");
//    Vtiger_Utils::ExecuteQuery("UPDATE `$tableName` SET $businessline = 'Local' WHERE $oldBusinessLine LIKE '%Local%'");
};
