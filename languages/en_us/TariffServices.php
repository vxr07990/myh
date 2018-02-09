<?php

$languageStrings = array(
	'TariffServices' => 'Tariff Services',
	'SINGLE_TariffServices' => 'Tariff Service',

	'LBL_ADD_RECORD' => 'Add Tariff Service',
	'LBL_RECORDS_LIST' => 'Tariff Services List',
  'LBL_TARIFFSERVICES_FLATRATEBYWEIGHT' => 'Flat Rate By Weight',
	'LBL_TARIFFSERVICES_NAME' => 'Service Name',
	'LBL_TARIFFSERVICES_RELATEDSECTION' => 'Tariff Section',
	'LBL_TARIFFSERVICES_RATETYPE' => 'Rate Type',
	'LBL_TARIFFSERVICES_EFFECTIVEDATE' => 'Effective Date',
	'LBL_TARIFFSERVICES_RELATEDTARIFF' => 'Tariff',
	'LBL_TARIFFSERVICES_REQUIRED' => 'Is Required',
	'LBL_TARIFFSERVICES_APPLICABILITY' => 'Applicability',
	'LBL_TARIFFSERVICES_DISCOUNTABLE' => 'Is Discountable',
	'LBL_TARIFFSERVICES_RATE' => 'Rate',
	'LBL_TARIFFSERVICES_INCHES' => 'Inches Added',
	'LBL_TARIFFSERVICES_MINCRATECUBE' => 'Minimum Crate Cube',
	'LBL_TARIFFSERVICES_CRATINGRATE' => 'Crating Rate',
	'LBL_TARIFFSERVICES_UNCRATINGRATE' => 'Uncrating Rate',
	'LBL_TARIFFSERVICES_HASVAN' => 'Has Van',
	'LBL_TARIFFSERVICES_TRAVELTIME' => 'Has Travel Time',
	'LBL_TARIFFSERVICES_ADDMANRATE' => 'Additional Man Rate',
	'LBL_TARIFFSERVICES_ADDVANRATE' => 'Additional Van Rate',
	'LBL_TARIFFSERVICES_RELEASEDVAL' => 'Has Released Valuation',
	'LBL_TARIFFSERVICES_RELEASEDVALAMOUNT' => 'Default Released Valuation Amount',
	'LBL_TARIFFSERVICES_CHARGEPER' => 'Charge Per',
	'LBL_TARIFFSERVICES_SALESTAX' => 'Sales Tax',
	'LBL_TARIFFSERVICES_HASCONTAINERS' => 'Has Containers',
	'LBL_TARIFFSERVICES_HASPACKING' => 'Has Packing',
	'LBL_TARIFFSERVICES_HASUNPACKING' => 'Has Unpacking',
	'LBL_TARIFFSERVICES_CWTBYWEIGHT' => 'CWT by Weight',
	'LBL_TARIFFSERVICES_POUNDS' => 'Pounds Per Man Per Hour',
	'LBL_TARIFFSERVICES_INVOICEABLE' => 'Invoiceable',
	'LBL_TARIFFSERVICES_DISTRIBUTABLE' => 'Distributable',
	'LBL_TARIFFSERVICES_SERVICELINE' => 'Business Group',
	'LBL_TARIFFSERVICES_SERVICECODE' => 'Service Code',

	'LBL_TARIFFSERVICES_INFORMATION' => 'Tariff Service Information',
	'LBL_TARIFFSERVICES_DISCOUNTABLE' => 'Discountable',
	'LBL_TARIFFSERVICES_BASEPLUS' => 'Base Plus Transportation',
	'LBL_TARIFFSERVICES_BREAKPOINT' => 'Break Point Transportation',
	'LBL_TARIFFSERVICES_WEIGHTMILEAGE' => 'Weight/Mileage Transportation',
	'LBL_TARIFFSERVICES_BULKY' => 'Bulky List',
	'LBL_TARIFFSERVICES_CHARGEPERHUNDRED' => 'Charge Per $100 (Valuation)',
	'LBL_TARIFFSERVICES_COUNTYCHARGE' => 'County Charge List',
	'LBL_TARIFFSERVICES_CRATINGITEM' => 'Crating Item',
	'LBL_TARIFFSERVICES_FLATCHARGE' => 'Flat Charge',
	'LBL_TARIFFSERVICES_HOURLYAVG' => 'Hourly Average (Lb/Man/Hr)',
	'LBL_TARIFFSERVICES_HOURLYSET' => 'Hourly Set',
	'LBL_TARIFFSERVICES_HOURLYSIMPLE' => 'Hourly Simple (Per Qty/Per Hour)',
	'LBL_TARIFFSERVICES_PACKING' => 'Packing Items',
	'LBL_TARIFFSERVICES_CUFT' => 'Per Cu Ft',
	'LBL_TARIFFSERVICES_CUFTPERDAY' => 'Per Cu Ft/Per Day',
	'LBL_TARIFFSERVICES_CUFTPERMONTH' => 'Per Cu Ft/Per Month',
	'LBL_TARIFFSERVICES_CWT' => 'Per CWT',
	'LBL_TARIFFSERVICES_CWTPERDAY' => 'Per CWT/Per Day',
	'LBL_TARIFFSERVICES_CWTPERMONTH' => 'Per CWT/Per Month',
	'LBL_TARIFFSERVICES_QTY' => 'Per Quantity',
	'LBL_TARIFFSERVICES_QTYPERDAY' => 'Per Quantity/Per Day',
	'LBL_TARIFFSERVICES_QTYPERMONTH' => 'Per Quantity/Per Month',
	'LBL_TARIFFSERVICES_VALUATION' => 'Tabled Valuation',
	'LBL_BASESERVICE' => 'Charge',
	'LBL_TARIFFSERVICES_SERVICECHARGE' => 'Service Charge',
	'LBL_BASESERVICEAPPLIES' => 'Applies to',
	'LBL_BASE_CHARGE_SERVICE_MATRIX' => 'Use Charge Matrix',
    'LBL_TARIFFSERVICES_CWTPERQTY' => 'CWT Per Quantity',

    //Added by EN 20160920
    'LBL_TARIFFSERVICES_RELATED_ITEMCODES' => 'Items Code',
    'LBL_ASSIGN_TO_MODULE' => 'Assign To Module',
    'LBL_ASSIGN_TO_RECORD' => 'Assign To Record',

    'LBL_POUNDS_PER_MAN_PER_HOUR' => 'Average Lb/Man/Hour',
);

if (getenv('INSTANCE_NAME') == 'sirva') {
    $languageStrings['LBL_TARIFFSERVICES_SIT_ITEM'] = 'SIT Items';
    $languageStrings['LBL_TARIFFSERVICES_CARTAGE'] = 'Cartage (CWT Rate)';
    $languageStrings['LBL_TARIFFSERVICES_FIRST_DAY'] = 'First Day (CWT Rate)';
    $languageStrings['LBL_TARIFFSERVICES_ADDITIONAL_DAY'] = 'Additional Day (CWT Rate)';
    $languageStrings['LBL_TARIFFSERVICES_CWTPERQTY'] = 'CWT Per Quantity';
}
