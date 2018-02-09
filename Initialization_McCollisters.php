<?php
/*

    McCollisters initialization script. Modifies the instance with McCollisters specific updates

*/
//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');

//Add VehicleLookup module if it doesn't already exist
require_once('one-off scripts/master-scripts/Hotfixes/VehicleLookup_20151012.php');

//Hot fix to remove options from business_line field
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_McCollistersBusinessLine.php');
//Hot fix to remove packing dates from Leads
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_McCollistersPackDates.php');
//Hot fix to adjust field mapping for Lead Conversion
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_McCollistersLeadConversion.php');
//Hot fix to add Mileage field to Opportunities & Orders
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_McCollistersMileageField.php');
//Hot fix to reorder dates in Orders
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_McCollistersReorderDates.php');
//Hot fix to remove weights from Orders
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_McCollistersRemoveWeights.php');
//Hot fix to add columns to vehiclelookup table
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_McCollistersVehicleLookupColumns.php');
//Hot fix to add fields to Estimates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_McCollistersEstimateFields.php');

// OT 3927 - fix lead source picklist
require_once('one-off scripts/master-scripts/mccollisters/FixLeadSourcePicklist3927.php');
// OT 3929 - lead status picklist
require_once('one-off scripts/master-scripts/mccollisters/FixLeadStatusPicklist3929.php');
// OT 3936 - remove brand field on Accounts
require_once('one-off scripts/master-scripts/mccollisters/RemoveBrandField3936.php');
// OT 3928 - business line picklist
require_once ('one-off scripts/master-scripts/mccollisters/FixBusinessLine3928.php');
// OT 3955 - Add Auto Trailer to vehicle type
require_once ('one-off scripts/master-scripts/mccollisters/AddAutoTrailerVehicleType3955.php');
// OT 3930 - billing type picklist
require_once ('one-off scripts/master-scripts/mccollisters/FixBillingTypePicklist3930.php');
// OT 3960 - add origin and destination contact to Orders
require_once ('one-off scripts/master-scripts/mccollisters/AddOriginDestinationContactFields3960.php');
// OT 4045 - add miles to orders address info
require_once ('one-off scripts/master-scripts/mccollisters/AddOrdersAddressDetailsMileage4045.php');

// OT 4063 - convert lead fix
require_once ('one-off scripts/master-scripts/mccollisters/LeadConvertFix4063.php');
// OT 4077 - Fix for update time in Leads not showing correctly
require_once ('one-off scripts/master-scripts/mccollisters/FixLeadsFields4077.php');
// OT 4065 - convert VehicleLookup to guest module
require_once ('one-off scripts/master-scripts/mccollisters/ConvertVehicleLookupToGuestModule4065.php');
// OT 4094 - move amount and probability to opp info block
require_once ('one-off scripts/master-scripts/mccollisters/MoveOppFields4094.php');
// OT 4102 - vehicle out of service status
require_once ('one-off scripts/master-scripts/mccollisters/FixOutOfServiceStatus4102.php');
// OT 4099 - unhide orders dispatch status
require_once ('one-off scripts/master-scripts/mccollisters/UnhideOrdersDispatchStatus4099.php');
// OT 4093 - sales person
require_once('one-off scripts/master-scripts/mccollisters/UnhideSalesPerson4093.php');
// OT 4107 - move mileage field to estimate details
require_once ('one-off scripts/master-scripts/mccollisters/MoveMileageField4107.php');
// OT 4118 - hide military post survey block
require_once ('one-off scripts/master-scripts/mccollisters/RemoveMilitaryPostSurveyBlock4118.php');
// OT 4168 - add driver name field to ldd info on Orders
require_once ('one-off scripts/master-scripts/mccollisters/AddDriverNameFieldToLDDOrderDetails4168.php');
// OT 4236 - add declared value to vehicle details
require_once ('one-off scripts/master-scripts/mccollisters/AddDeclaredValueToVehicleDetails4236.php');

// OT 4225 - agency orders sequencing
require_once ('one-off scripts/master-scripts/mccollisters/AddAgentOrdersSequenceNumbers.php');
