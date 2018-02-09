<?php

$languageStrings = array(
    'SINGLE_ItemCodes' => 'Item Code',
    'ItemCodes' => 'Item Codes',
    'ItemCodes ID' => 'Item Code ID',
    'LBL_RECORDS_LIST' => 'Item Codes List',
    'LBL_ADD_RECORD' => 'Add Item Code',

    // Fields
    'LBL_ITEMCODES_NUMBER' => 'Item Code Number (Agent Defined)',
    'LBL_ITEMCODES_STATUS' => 'Status',
    'LBL_ITEMCODES_DESC' => 'Description',
    'LBL_OWNER' => 'Owner',

    // @TODO: Unclear which of these is currently correct (to next tag)
    'LBL_REVENUE_GROUP' => 'Revenue Group',
    'LBL_IGC_TARIFF_SERICE_CODE' => 'Web Tariff Service Code',
    'LBL_VANLINE_CODE' => 'Vanline Code',

    'LBL_ITEMCODES_GROUP' => 'Revenue Group',
    'LBL_ITEMCODES_TARIFFSERVICECODE' => 'Web Tariff Service Code',
    'LBL_ITEMCODES_VANCODE' => 'Vanline Code',
    // @TODO: Unclear which of these is currently correct (end tag)

    'LBL_DEFAULT_REVENUE_AGENT' => 'Default Revenue Agent',
    'LBL_APPEAR_ON_INVOICE' => 'Appear on Invoice',
    'LBL_ITEMCODES_CREATEDTIME' => 'Created Time',
    'LBL_ITEMCODES_MODIFIEDTIME' => 'Modified Time',
    'LBL_ITEMCODES_CREATEDBY' => 'Created By',
    'LBL_ITEMCODES_ASSIGNED_TO' => 'Assigned To',

    // Blocks
    'LBL_CUSTOM_INFORMATION' => 'Custom Information',
    'LBL_ITEMCODES_DETAILS' => 'Item Code Details',
    'LBL_ITEMCODES_MAPPING' => 'Item Code Mapping',
);

if(getenv('IGC_MOVEHQ')) {
    $languageStrings['LBL_OWNER'] = 'Agent Owner';
}


$jsLanguageStrings = array(
    'LBL_BUSINESSLINE' => 'Business Line',
    'LBL_COMMODITIES' => 'Commodity',
    'LBL_BILLING_TYPE' => 'Billing Type',
    'LBL_AUTHORITY' => 'Authority'
);
