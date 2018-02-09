<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
$languageStrings = array(
    // Basic Strings
    'Services' => 'Services',
    'SINGLE_Services' => 'Service',
    'LBL_ADD_RECORD' => 'Add Service',
    'LBL_RECORDS_LIST' => 'Services List',

    // Blocks
    'LBL_SERVICE_INFORMATION' => 'Service Details',
    'LBL_PRICING_INFORMATION' => 'Pricing Information',
    'LBL_CUSTOM_INFORMATION' => 'Custom Information',
    'LBL_DESCRIPTION_INFORMATION' => 'Description Information',
    'LBL_MORE_CURRENCIES' => 'more currencies',
    'LBL_PRICES' => 'Service Prices',
    'LBL_PRICE' => 'Price',
    'LBL_RESET_PRICE' => 'Reset Price',
    'LBL_RESET' => 'Reset',

    //Services popup of pricebook
    'LBL_ADD_TO_PRICEBOOKS' => 'Add to PriceBooks',

    //Field Labels
    'LBL_SERVICES_SERVICENAME'=>'Service Name',
    'LBL_SERVICES_SERVICEACTIVE'=>'Active',
    'LBL_SERVICES_SERVICECATEGORY'=>'Category',
    'LBL_SERVICES_SERVICENO'=>'Service Number',
    'LBL_SERVICES_OWNER'=>'Owner',
    'LBL_SERVICES_NOOFUNITS'=>'Number of Units',
    'LBL_SERVICES_COMMISSIONRATE'=>'Commission Rate',
    'LBL_SERVICES_PRICE'=>'Price',
    'LBL_SERVICES_USAGEUNIT'=>'Usage Unit',
    'LBL_SERVICES_TAXCLASS'=>'Tax Class',
    'LBL_SERVICES_WEBSITE'=>'Website',
    'LBL_SERVICES_SALESSTARTDATE'=>'Sales Start Date',
    'LBL_SERVICES_SALESENDDATE'=>'Sales End Date',
    'LBL_SERVICES_SUPPORTSTARTDATE'=>'Support Start Date',
    'LBL_SERVICES_SUPPORTEXPIRYDATE'=>'Support Expiry Date',
    'LBL_SERVICES_CREATEDTIME'=>'Created Time',
    'LBL_SERVICES_MODIFIEDTIME'=>'Modified Time',
    'LBL_SERVICES_LASTMODIFIEDBY'=>'Last Modified By',
    'LBL_SERVICES_DESCRIPTION'=>'Description',



    //Services popup of pricebook
    'LBL_ADD_TO_PRICEBOOKS' => 'Add to PriceBooks',


    //Patch provided by http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/7884
        //Picklist values
        'Hours' => 'Hours',
        'Days' => 'Days',
        'Incidents' => 'Incidents',

        'Support' => 'Support',
        'Installation' => 'Installation',
        'Migration' => 'Migration',
        'Customization' => 'Customization',
        'Training' => 'Training',
);

if(getenv('IGC_MOVEHQ')) {
    $languageStrings['LBL_SERVICES_OWNER'] = 'Agent Owner';
}
