<?php
/*

moveHQ initialization script. Modifies the instance with moveHQ specific updates that do not run for GVL

*/
//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');

{
    //^^^^^ this block was migrated from master_script to here.
    //This one from 0.9.2
    require_once 'one-off scripts/master-scripts/movehq/modify_orders_fields_20151014.php';

    //rest from somewhere else .. 0.10.12 i think.
    //Move Policies
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_MovePolicies.php');
    //Orders Task UI - OT2229
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_OrdersTaskUpdateDetailFields.php');
    //Adding Calendar to Orders
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Relation_OrdersSurveys.php');
    //Local Dispatch Hotfix.
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddLocalDispatch.php');
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_LocalDispatch_Link.php');
    //Insurance Module for Vehicles
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddInsuranceModule.php');
    //Employee Fields & Types
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddNewEmployeeTypes.php');
    //Trips Updates
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LDDTrips_Updates082016.php');
    //OT1711 - Vehicles Fields
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_EnhanceVehicles.php');
    //OT2646, OT2760, OT2761 - More Fields for Vehicles.
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Vehicles_Updates09092016.php');
    //OT2370 - Orders Overflow
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_OrdersOverflow.php');
    // Add TokBox fields for moveHQ instances for video survey
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_RemoveOrdersSP.php');
    require_once('one-off scripts/master-scripts/add_tokbox_fields.php');
    require_once('one-off scripts/master-scripts/ActualsModule_20160621_135458.php');
    //OT2750 Create driving violation block - Core feature place in main master-script
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_DrivingViolationModule.php');
    //OT2763 - License Plate History
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LicensePlateHistory.php');
    // OT 1745 - allow custom rate for crates
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_MoveHQ_AddCrateOptionalTariff.php');
    //OT  16745 - DC not available as origin/empty state in trip screen
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddDCInTrips.php');
    //3518 - Add filters to local dispatch tables
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_LocalDispatch_FilterTable.php');
}
//^^^^^ Above moved from an if(IGC_MOVEHQ) block in master_script.php to here. ^^^^^
//Adds invoice block to the account page and adds all the fields to that block
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddInvoiceFormatAccounts.php');
//Adds Billing Type picklist to accounts, overrides billing type picklist values with graebel specific ones (OT 2437)
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddBillingTypeAccounts.php');
//Bring business line values up to specification and add busienss line to accounts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_UpdateBusinessLine_gvl.php');
//Adds DUNS Number to accounts information
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddDUNSNumberAccounts.php');
//modify participants agent type picklist for graebel
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GraebelAgentTypes.php');
//add/remove some accounts fields from orders
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GraebelOrdersAccountFields.php');
//fixes typo in move roles (Customer Service Cordinator -> Customer Service Coordinator)
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_MoveRoleCoordinatorTypo.php');
//Add billed weight field for graebel:
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Add_BilledWeight_field.php');
//Adds a new table to the db called vtiger_account_salespersons for adding salespersons to the accounts module
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddSalesPersonTableAccounts.php');
//Sets all new valuation options in estimates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SetEstimateValuationOptions.php');
//Adds a new field to estimates called Quotation Type
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddQuotationTypeFieldEstimates.php');
//OT16196 nullifies this change.
////Adds a new field to estimates called Estimate Type OT1820
//require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddEstimateTypeField.php');
//Add a checkbox - waive peak rates checkbox to contracts tariff block
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_WaivePeakRatesContracts.php');
//Add a checkbox - waive EAC checkbox to contracts tariff block
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_WaiveEACCheckboxContracts.php');
//Add EAC Field Contracts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddEACFieldContracts.php');
//Add a field to contracts for min weight in the tariff block
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddMinWeightContracts.php');
//Add a field to employees module called Assigned To Employees
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddSharedAssignedToEmployees.php');
//Remove required from end date of contracts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_ContractEndDateNotRequired.php');
//Add Tariff to Orders.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Add_Tariff_to_Orders.php');
//Modify ExtraStops StopDescription for Graebel.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GraebelExtraStopsDescription.php');
//update graebel lead conversion field mapping
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GraebelLeadConversionPhoneAndDates.php');
//Add an option to Estimates Stage picklist
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddOptionEstimateStage.php');
//Add move policies module for graebel
//require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_MovePolicies.php');
// Removes required from the Opportunities Dest. Address Field OT-14848
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_RemoveRequiredFromDestAddressOpps.php');
// Removes orig zone and dest. zone and moves business line and billing line to another block
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_RemoveFieldsFromAddressBlockOrders.php');
// Add fields to est. cube, piece count, pack count in interstate block
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddFieldsToInterstateMoveBlock.php');
// Add field to orders received date
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddReceivedDateToOrders.php');
// Add Peak and Non-Peak discount to contracts tariff section
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddPeakNonPeakFieldsContracts.php');
// Add Valuation table to the database
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CreateValuationTariffTypesTable.php');
// Add Valuation fields to Tariff Manager
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddValuationToTariffManager.php');
// Add status picklist to contracts module
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddStatusPicklistContracts.php');
// Add Competitive Checkbox Option to opportunities
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddCompetitiveCheckboxOpp.php');
// Add Extended Sit Delivery Mileage to Contracts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddExtendedSitDeliveryMileage.php');
// Rearranges and adds fields to leads module
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddFieldsRearrangeFieldsLeads.php');
// Remove flights of stairs from Leads address details based on Kim's design mock up.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Leads_removeField.php');
// Add and Reorder some fields in account info block in accounts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddReorderAccountInfoAccounts.php');
// Add and Reorder some fields in account details block in accounts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddReorderAccountDetailsAccounts.php');
// Add new Credit Request Block to accounts along with fields according to design specs
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddCreditBlockToAccounts.php');
// Add new Credit Details Block to accounts along with fields according to design specs
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddCreditDetailsBlockAccounts.php');
// Adds a new block to accounts to accept multiple billing address and creates a table to save them to
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddBillingAddressBlockAccounts.php');
// Adds a new block to accounts for invoice settings
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddInvoiceSettingsBlockAccounts.php');
// Add New Block to accounts with options for additional roles
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddAdditionalRolesAccounts.php');
// Reorders the blocks in accounts to match design specs
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_ReorderBlocksInAccounts.php');
// Add Customer Service Coordinator to Opportunities undoing until this is clarified.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Opportunites_addCustSvcCoord.php');
// Add fields to opportunities based on kim's mock up, reorder blocks and fields in opportunities.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Opportunites_update.php');
//add Local Tariff flag to contracts:
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Contracts_addLocalTariffFlag.php');
// Add fields to opportunities based on kim's mock up, reorder blocks and fields in opportunities FOR NATIONAL.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Opportunites_updateForNational.php');

require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AgentidForOPList.php');
//adds two fields to contracts and estimates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddDistributionDiscountContractsEstimates.php');
//Removes business line from sales person table and adds commodity
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddCommodityRemoveBuisnessLineSalesPerson.php');
//Makes contracts its own module
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Contracts_makeOwnModule.php');
//Add all new options for orders payment type picklist
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Orders_AddInvoiceDetailsBlock.php');
//Removes national account from business line
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_RemoveNationalAccountFromBusinessLine.php');
//Makes payment type field a multipick list instead of regular select option
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Orders_MakePaymentTypeFieldMultipicklist.php');
//Moves the miles field in orders from the order details block to the dates block
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Orders_MoveMilesFieldToDates.php');
//Adds military information block and required fields to it
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Orders_AddMilitaryInformationBlock.php');
//adds valuaiton override to estimates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_EstAddValuationOverride.php');
//Adds Estimate Type Field to orders
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Orders_AddEstimateTypeField.php');
//create upholstery / fine-finish module
require_once('one-off scripts/master-scripts/create_upholsteryfinefinish20160607.php');
//add guaranteed price to estimates.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Estimates_Add_GuaranteedPrice_field.php');
//creates upholstery fine finish module
require_once('one-off scripts/master-scripts/create_upholsteryfinefinish20160607.php');
//create 1950-B tariff type
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVLTariffManAdd1950BType.php');
//create transportation block in est
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVLEstimatesTransPriceBlock.php');
//adds crating discount field to estimates interstate move details block
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVLEstimatesCrateDiscount.php');
//adds new billing types
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVLAddBillingTypes.php');
//create auto transportation module
require_once('one-off scripts/master-scripts/Create_AutoTrans_20160609.php');
//re-add nat acct to business lines
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVLReAddNationalAccount.php');
//Add Distribution discount fields to estimates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Estimates_AddDistributionDiscountFields.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVLReAddNationalAccount.php');
// Adds a checkbox field for competitive to the orders module
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_glv_Orders_AddCompetitiveCheckbox.php');
// Adds some weight field inputs in the weights block in orders
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Orders_AddGSAWeightFields.php');
// Adds new block GVL Information to orders and adds gvl number and contacts related field
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Orders_AddGSABlockAndFields.php');
// Add new field to orders called registered on and remove booking date field
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Orders_AddRegisterOrder.php');
//adds ICode script to employees contract
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVLEmployeesAddIcode.php');
//adds new type of move role: driver
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVLAddDriverMoveRoleType.php');
//add summary fields so that vehicles' related lists don't break
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVLVehiclesAddSummaryFields.php');
//remove extra documents related list from vehicles
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVLVehiclesRemoveDocumentsRelatedList.php');
//adds generate paperwork button to orders detail view
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVLOrdersAddPaperworkButton.php');
//Changes the ordering of the sales stage... somewhere some how this table has been changed.
//@TODO: I think this needs looked at because vtiger_sales_stage values are used in like widgets.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_leads_opportunitiy_ChangeSalesStage.php');
//add select button to contracts related list in accounts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVLAccountsAddContractsSelect.php');
//Update Vehicles to have agent number as a uitype 10, updates listview cv, and hides vehicle number
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Vehicles_updateListViewCV.php');
//Switch the vehicles fields out in the trips module with right ones.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Trips_ChangePopUpViewVehicles.php');
//add select button to contracts related list in accounts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Estimates_MoveLoadDateBackToEstimateDetailsBlock.php');
//Add block to estimates for pulling in add flat rate auto details from the contract
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Estimates_AddFlatRateAutoBlock.php');
//Adds a new field to contracts with options for business line
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Contracts_AddBusinessLine.php');
//Makes some of the fields in orders to optional
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Accounts_MarkInvoiceFieldsNotRequired.php');
//add select button to move policies related list in accounts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVLAccountsAddMovePoliciesSelect.php');
//Sets the billing type to required in opportunities
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Opportunities_MakeBillingTypeRequired.php');
//Sets the data type for zips to varchar instead of int in leads
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_ALL_RemoveIntegerOnlyZip.php');
//Sets add title field to employees ui
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Employees_AddTitleField.php');
//Adds a invoice packet field to documents
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Documents_AddInvoicePacketField.php');
//add two roles to move roles.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_MoveRoles_AddCustSvcAsst_AdmSupport.php');

//Modifies maximum RVP field to Decimal(9,2)
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Contracts_Update_Maximum_RVP.php');
//Modifies credit_amount_requested field to Decimal(11,2)
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Accounts_Update_Credit_Amount_Requested.php');
//adds confirmation fields and planned dates fields to trips/orders
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVLAddLDDConfirmed.php');
//set summary fields for employees to drive related list displays
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVLAddEmployeesSummaryFields.php');
//remove fields from Date Details block on orders
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_OT16107_Hide_Date_Fields.php');
//modify vtiger_entityname to use first name and last name for employees for saving
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_UpdateMoveRoleEmployeeField.php');
//modify vtiger-crmentity to use first name and last name for employees for updating search field
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_UpdateLabelsForEmployeesInCrmentityTable.php');
//Borrowing a Sirva Hotfix to change zip field types in vtiger_leads
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_RemoveIntegerOnlyZip.php');
//Changing uitype of zip fields that are currently set to 7 (should be 1). Will correct dropping of leading 0s.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ALL_changeZipUIType.php');
//Removing country field from Vehicles Vehicle Information block
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_RemoveCountryFieldFromVehicles.php');
//Add a document type to the Documents so we knowwhat sort of invoice document it is.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Documents_addDocumentType.php');
//Hiding SIT Number of Days fields in Estimates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_HideSITNumberDaysFields.php');
//Modifying New Orders Status picklist
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_OrdersTaskStatusPicklistUpdate.php');
//OT2000 - Add Account Type
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_AddAccountType.php');
//Adding shuttle reason fields to accessorials in Estimates (OT 2774)
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Estimates_AddReasonFieldToShuttles.php');
//OT 2669 change Vehicles status picklist
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Vehicles_StatusPicklistUpdate.php');
//OT 16124 add field to contracts search pop up
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Contracts_AddBusinessLineToPopupSearch.php');
//OT 2893 Add contact name by the order number at the header.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_AddContactNameToDetailHeader.php');
//OT 2898 Convert Confirmed fields from checkboxes to AM/PM dropdown.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ModifyLDDConfirmed.php');
//OT 2917 Update Business Line with International Choices - Orders
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_UpdateBusinessLineWithInternational.php');
//OT 1815 Adding fields to valuations in Estimates and contracts
require_once("one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Estimates_AddAdditionalValuationFields.php");
require_once("one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Contracts_AddAdditionalValuationField.php");
//OT 1812 Auto Details block modified and added to Orders
require_once("one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_VehicleTransportationUpdate.php");
require_once('one-off scripts/master-scripts/PicklistCustomizer_20160803.php');
require_once("one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_VehicleTransportationUpdate.php");
//OT 2907 add vendor agreements
require_once('one-off scripts/master-scripts/create_vendor_agreements.php');
//OT 2906 add Branch defaults
require_once('one-off scripts/master-scripts/create_branch_default_agreements_for_agents.php');
require_once("one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_VehicleTransportationUpdate.php");
//OT 2570 Added Move Roles to Opportunities and hid original Sales Person field.
require_once("one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Opportunities_SetGuestMoveRoles.php");
//OT 3106 and OT 3107 Added FEIN and I.Code fields to Vendors
require_once("one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Vendors_AddFeinAndIcodeFields.php");
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ModifyLDDConfirmed.php');
//OT 1599 Adding Valuation block to Orders
require_once("one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_AddValuationBlock.php");
//OT2901 also update Orders ordersstatus field:
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_orders_update_ordersstatus.php');
//OT2969 Added fields and behaviors to Opportunities Information block
require_once("one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Opportunities_NewStatusItemsAndRelatedPicklists.php");
//OT 1812 Reordering fields for Auto Details
require_once("one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_VehicleTransportation_FieldReorder.php");
//OT 1892 Driver Qualification Module
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddDriverQualificationModule.php');
//OT 1863 - 1865 Adding contrator fields to Vendors
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Contractor_Block.php');
//OT16196 update Picklist for Estimate_type on Estimates, Actuals and Orders to be: ['Binding', 'Not To Exceed', 'Non Binding']
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_orders_modifyEstimateTypeField.php');
// OT 16267 - leading zero being cut off in zipcode field
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ConvertZipToVarchar.php');
// OT 3155 - add in new document types
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_DocumentsAddNewDocumentTypes.php');
// OT 2902, part 1 Added in picklists.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Estimates_AddPicklistExtraLaborDesc.php');
// OT 2694 - add Type field to Vendors
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Vendors_AddTypeField.php');
// OT 15014 - update agents to allow more agents.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_workflow_update_agents.php');
// OT 16350 - Rename plate type field
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_RenamePlateTypeField.php');
// OT 16348 - Add Full Replacement value deductible picklist
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddValuationDeductiblePicklist.php');
// OT 1610 - Add Storage in billed date
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddStorageInBilledDate.php');
// OT 2971 - update accessorial fuel surcharge to allow values over 9.999.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Estimates_UpdateFuelSurchageDBCol.php');
// OT 3166 - Add Create To Do and Create Event options for Orders Task workflows.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Workflows_AddToDoCreateEventToOrdersTaskWF.php');
//OT1825 - Adding Out of service on employee screen
include_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_AddOutOfServiceModule.php';
//OT3204 - Adding items to Business Line picklists
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_BusinessLinePicklistAdditions.php');
//OT1704
include_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_VehiclesOwnerHistory.php';
//OT1705
include_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_VehiclesTermination.php';
//OT1706
include_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_VehiclesOutofService.php';
//OT1707
include_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_VehiclesTransfer.php';
// OT 16407 - Valuation in Contracts should match Estimates and Orders
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_UpdateValuationBlockInContracts.php');
// OT 16347 - Packing + Unpacking at extra stops
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddPackingItemsToStops.php');
//OT 1853 Add Fields and rename Safety Details block
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddNewEmployeesFields.php');
// OT 3176 - Additional fields for 1950-B waiting time
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddFieldsToAccessorialWaitingTime.php');
// OT 3207 - Add fields to tariff services
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddFieldsToTariffServices.php');
// OT 16118 + 16119 - Add 'Denied' and 'Expired' to contract status picklist
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddContractStatusExpiredDenied.php');

// OT 3150 - Updating primary and secondary role picklists for employees/associates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Employees_UpdateRolesPicklistValues.php');
// 3272: Vehicles Module - Modifications to Vehicles Specifications Block
// 3273: Vehicles Module - Modification to Vehicle Information Block
require_once('one-off scripts/master-scripts/movehq/AddFieldsToVehiclesSpecifications.php');
// 3248: New UIType for multi-picklist to a related module.
require_once('one-off scripts/master-scripts/movehq/CreateReferenceMultiPiclistUIType.php');
require_once('one-off scripts/master-scripts/movehq/CreateUsersFieldOnEmployees.php');
require_once('one-off scripts/master-scripts/movehq/CreateMultiPicklistAll.php');
// 3268: Personnel Roles - Add scripts to Initialization_MoveHQ script
require_once('one-off scripts/master-scripts/movehq/Create_EmployeeRoles_20160908.php');
// 3286: Equipment Module Modifications - Add Scripts to Initialziation_MoveHQ script
require_once('one-off scripts/master-scripts/movehq/Add_Fields_To_Equipment_20160916.php');
require_once('one-off scripts/master-scripts/movehq/EquipmentReorderScript_20160916_153138.php');
// 3293: Creation of Container Type Module - Add scripts to Initialization_MoveHQ script
require_once('one-off scripts/master-scripts/movehq/Create_ContainerTypes_20160916.php');
// 3294: Add Containers Module to Orders - Add scripts to Initialization_MoveHQ Script
require_once('one-off scripts/master-scripts/movehq/Create_Container_20160919.php');
// 3410: Agent Module Modifications - Add scripts to Initialization_MoveHQ script
require_once('one-off scripts/master-scripts/movehq/Add_Fields_To_Agents_20160927.php');
require_once('one-off scripts/master-scripts/movehq/AgentsReorderScript_20160926_200336.php');
// 3409: Vendor Module Modifications - Add Scripts to Initialization_MoveHQ Script
require_once('one-off scripts/master-scripts/movehq/Add_Fields_To_Vendors_20160926.php');
require_once('one-off scripts/master-scripts/movehq/Hotfix_Update_Fields_Vendors_20160927.php');
require_once('one-off scripts/master-scripts/movehq/VendorsReorderScript_20160926_210830.php');
require_once('one-off scripts/master-scripts/movehq/VendorsReorderScript_20161018_183327.php');
// 3360: Billing Type Picklist - Hard Code Values throughout the program
require_once('one-off scripts/master-scripts/movehq/UpdateBillingType3360.php');
// 2864: Creation of Item Codes Module
require_once('one-off scripts/master-scripts/movehq/CreateItemCodesModule_2864.php');

// 3273: Vehicles Module - Modification to Vehicle Information Block
require_once('one-off scripts/master-scripts/movehq/UpdateBlockAndFieldForAgentManager.php');
require_once('one-off scripts/master-scripts/movehq/CapacityCalendarCounter3273.php');

// 3296: Add related field "Item Codes" to Tariff Sections Module - Apply scripts to Initialization_MoveHQ script
// 3411: Tariff Services - Add Fields to Module
require_once('one-off scripts/master-scripts/movehq/Add_ItemCodes_To_TariffServices_20160920.php');

//3363: Orders - Add an Authority Field
require_once('one-off scripts/master-scripts/movehq/AddAuthorityToOrdersModule_3363.php');
//3429: National -  Lead Module Changes
require_once('one-off scripts/master-scripts/movehq/NationalLeadModuleChanges_3429.php');

//3368: Business Line - Hard Code Selections throughout MoveHQ Core
require_once('one-off scripts/master-scripts/movehq/UpdateBusinessLineValues.php');
//3417: New UIType for Multipicklist from Related Module and "All" functionality
require_once('one-off scripts/master-scripts/movehq/CreateReferenceMultiPiclistUITypeAll.php');

//3422: Menu Updates
require_once('one-off scripts/master-scripts/movehq/MenuUpdates_3422.php');
// 3407: Creation of Revenue Grouping Module - Related to Agent Manager Module
require_once('one-off scripts/master-scripts/movehq/RevenueGrouping_3407.php');

// 3426: Employee Module - Update Fields / Blocks for Core-QA Instance
require_once('one-off scripts/master-scripts/movehq/EmployeeModuleUpdateFieldsBlocks_3426.php');
//3452: Estimate / Actual Module fix based on Business Line
require_once('one-off scripts/master-scripts/movehq/EstimateBusinessLine_3452.php');

// 3319: Create Placeholder for modules designated as Admin Tables
require_once('one-off scripts/master-scripts/movehq/MenuCleaner_3319.php');

// 3448: Item Codes - Add 2 Fields
require_once('one-off scripts/master-scripts/movehq/UpdateFieldItemsCodesModule_3448.php');

// 3425: Update Personnel Role's Classification Field Picklist
require_once('one-off scripts/master-scripts/movehq/UpdateClassificationPickList.php');

//	3317: Modify Move Roles Module to include related field to Personnel Roles Module
require_once('one-off scripts/master-scripts/movehq/UpdateMoveRolesModule_3317.php');

//3380: Lead Module - Insert module Move Roles into Block below Date Details Block. Remove Sales, Coordinator from Lead Info Block
require_once('one-off scripts/master-scripts/movehq/UpdateLeadsModule_3380.php');

//3238: Creation of Commission Plan Module
require_once('one-off scripts/master-scripts/movehq/CommissionPlans3238.php');


//3414: Create Agent Compensation Module
require_once ('one-off scripts/master-scripts/movehq/CreateAgentCompensationModule_3414.php');
require_once ('one-off scripts/master-scripts/movehq/CreateAgentCompensationGroupModule_3414.php');
require_once ('one-off scripts/master-scripts/movehq/CreateAgentCompensationItemsModule_3414.php');
require_once ('one-off scripts/master-scripts/movehq/CreateEscrowsModule_3414.php');

//3513: Order Tasks - Personnel Type Field / Vehicle Type Field
require_once('one-off scripts/master-scripts/movehq/CreatePersonnelAndVehicleUIType.php');

//3464: Orders Module - Modify / Update Fields / Blocks
require_once('one-off scripts/master-scripts/movehq/UpdateFieldAndBlockForOrders_3464.php');



//3542: Item Code Fix Bug Deletion
require_once('one-off scripts/master-scripts/movehq/ItemCodeFixBugDeletion_3542.php');
//3457: Actuals Module - Add Authority Field
require_once ('one-off scripts/master-scripts/movehq/AddAuthorityFieldToActualsModule_3457.php');
//3505: Menu Creator Module - Replaces Menu Cleaner Module
require_once('one-off scripts/master-scripts/movehq/MenuCreatorModule_3505.php');
//3520: Leads / Opportunities / Orders - Fix from hard coding Business Lines
require_once ('one-off scripts/master-scripts/movehq/LeadsOpptOrdersBusinessLines_3520.php');
//33462: Leads Module - Update / Modify fields / blocks
require_once('one-off scripts/master-scripts/movehq/Update_Modifyfieldsblocks_LeadsModule_OT3462.php');
// OT 17011 - additional info for rating crates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_MoveHQ_UpdateCrateOptionalTariff10242016.php');
// Caching for packing/bulky labels
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_AddPackingBulkyLabelCache.php');

//3267: Address List Module
require_once('one-off scripts/master-scripts/movehq/AddressList_3267.php');
//3424: Order Tasks - Update Module
require_once('one-off scripts/master-scripts/movehq/UpdateFieldsAndBlocksForOrdersTask_3424.php');
//3367: Add Military Bases Module
require_once('one-off scripts/master-scripts/movehq/Create_MilitaryBases_Module_OT3367.php');

//3451: Actuals Module - Add Escrows as Related Module
require_once('one-off scripts/master-scripts/movehq/Add_RelatedFieldEscrowsWithinActuals_OT3451.php');

//3369: Add Cariers Module
require_once('one-off scripts/master-scripts/movehq/Create_CarriersModule_OT3369.php');

//3370: Military Block within an Order - Add / Update Fields
require_once('one-off scripts/master-scripts/movehq/Update_MilitaryBlockWithinOrder_OT3370.php');

//3421: Update "Agent Type" Picklist Values in Participating Agents module
require_once('one-off scripts/master-scripts/movehq/Update_PickListValues_AgentType_OT3421.php');

// 3341: Time Calculator Setup Module - Related module to Agent Manager
require_once('one-off scripts/master-scripts/movehq/CreateModuleTimeCalculator_3341.php');

//3280: Add Owner Field to Customize Picklist
require_once ('one-off scripts/master-scripts/movehq/Add_Field_to_PicklistCustomizer_3280.php');

require_once('one-off scripts/master-scripts/movehq/CreateLongCarriesFlightsElevatorsModules_3341.php');


//3373: Opportunity Module - Create a new block 'Referral'
require_once ('one-off scripts/master-scripts/movehq/UpdateOpportunities_3373.php');

//3377: Google Routing Setup Module - Block within Agent Manager Module
require_once('one-off scripts/master-scripts/movehq/GoogleRoutingSetupModule_3377.php');

//3372: Opportunities - Order Details
require_once ('one-off scripts/master-scripts/movehq/UpdateOpportunities_3372.php');

// 3412: Parent Agent / Child Agent Solution for assigning Records
require_once ('one-off scripts/master-scripts/movehq/AddParentAgentManagerField_3412.php');

// 3275 Account - Additional Roles Modifications
require_once ('one-off scripts/master-scripts/movehq/AccountAdditionalRolesModifications_3275.php');
// 3427: Order Tasks - Add Default to Participating Agents Field
require_once ('one-off scripts/master-scripts/movehq/Fix_EmptyShowFullOrdersDetail_3427.php');

//3668: Commission Plans - Modifications / Bugs
require_once ('one-off scripts/master-scripts/movehq/UpdateCommissionPlansFilter_3668.php');

//3453 Project Module - Add Related Modules
require_once ('one-off scripts/master-scripts/movehq/Create_OrderEstimateLinkRelatedTo_ProjectModule.php');

//3270 Accounts - Customer Number / National Account Field - only saves numeric values, needs to to allow alpha / numeric
require_once ('one-off scripts/master-scripts/movehq/ChangeTypeForFields_AccountModule_OT3270.php');
//3415 Container Type Module - Remove Pricing Block / Fields
require_once ('one-off scripts/master-scripts/movehq/UpdateContainerTypeModule3415.php');
//3641 Agent Module - Add New PickList (Agent Type)
require_once ('one-off scripts/master-scripts/movehq/AddFieldTypeToAgentModule_OT3641.php');
//3456: Actuals Module - Add related field Agent Compensation
require_once ('one-off scripts/master-scripts/movehq/CreateAgentCompensationForActuals_3456.php');

// 3463: Opportunity Module - Modify / Update Fields / Blocks
require_once ('one-off scripts/master-scripts/movehq/UpdateOpportunityModule_3463.php');

// 3524 Move Policies - Modify Module for Core-QA Instance
require_once ('one-off scripts/master-scripts/movehq/MakeTariffFieldNoLongerMandatoryInMovePolicies_3524.php');
//3416 Containers Module - Remove Pricing Block / Fields
require_once ('one-off scripts/master-scripts/movehq/UpdateContainersModule_3416.php');

//3655 Orders - Update Fields
require_once ('one-off scripts/master-scripts/movehq/UpdateOrderFields_Within_OdersModule_OT3655.php');

//3691 Local Tariff Module(s) - update modules for multi tenant environment
require_once ('one-off scripts/master-scripts/movehq/UpdateLocalTariffModule_3691.php');

//3656 Orders Module - Authority Picklist has Incorrect Values
require_once ('one-off scripts/master-scripts/movehq/Update_PicklistValues_Authority_Within_Orders.php');
//3591 OrdersTask Module - Modify "Dispatch Services" block fields
require_once ('one-off scripts/master-scripts/movehq/UpdateOrdersTaskModule_3591.php');

//3727 Order Tasks - Create Address Guest Block
require_once ('one-off scripts/master-scripts/movehq/OrdersTaskAddresses_3727.php');

//3718 Lead / Opportunities / Orders - View=Detail, Fields showing that should not be
require_once ('one-off scripts/master-scripts/movehq/Move_Hide_Fields_OnDetails_LeadsOpportunitiesOrders_OT3718.php');

//OT3617 -- remove the block that says remove this block from Equipments.
require_once ('one-off scripts/master-scripts/movehq/EquipmentRemoveBlock_3617.php');

//3696 -- Opportunities - Business Line  (doesn't convert correctly from a lead)
require_once ('one-off scripts/master-scripts/movehq/updateForConvertLeads_3696.php');

// 3515: Time zone changes in moveCRM
require_once ('one-off scripts/master-scripts/movehq/CreateDateTimeZoneFields.php');

//3774 Implement Parent / Child Relationship (OT Item 3412) for Local Tariff fields
require_once ('one-off scripts/master-scripts/movehq/Update_TypeField_PicklistValues_LocalTariff_Tariff_OT3774.php');

//3609: Invoice Module - Modify module to new design
require_once ('one-off scripts/master-scripts/movehq/UpdateFieldAndBlockForInvoice_3609.php');

//3775 Reports Module - Update module to handle Multi-Tenant environment
require_once ('one-off scripts/master-scripts/movehq/AddColumForReport.php');

//OT3689 -- Estimates don't rate: presence = 1 to some sirva fields:
require_once ('one-off scripts/master-scripts/movehq/EstimatesFixAccessorialVehicleField_3689.php');

//Updates for detail line items from GVL version to make them work at least.
// OT 3181 - Add service providers distribution split to Actuals line items line 358
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddTableForDetailedLineItemsToActualsServiceProviders.php');

// OT 16725 line 392
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_UpdateServiceProviderInfo092816.php');

//OT 3338 - add Location and GCS_Flag to the detail line items line 398
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_detaillineitem_addLocGCSFields.php');

//OT16826 added keys to vtiger_detailed_lineitems.dli_relcrmid and dli_service_providers.dli_id line 421
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddKeysToDetailedLineItemsServiceProviderTable.php');

//OT 3383 Add additional fields to detailed line items 429
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_detaillineitem_addPhaseEventFields.php');

//OT3461 -- add metro flag to detailed line items: 483
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_detaillineitem_addMetroField.php');

// OT 17368 - Hide valuation override field line 507
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_HideValuationOverrideField.php');
// 3934 Accounts Module - Update Business Line fields
require_once('one-off scripts/master-scripts/movehq/UpdateUITypeBusinessLineInAccount_3934.php');

// OT 3786 Opportunity - Creating New
require_once('one-off scripts/master-scripts/movehq/Rearrange3fields_Module_Opportunities.php');

// OT 3941 Survey Appointment Module - Add Owner Field
require_once('one-off scripts/master-scripts/movehq/AddOwnerField_SurveyAppointments_Module_OT3941.php');

//3826 - Extensions :  Lead Company Lookup
require_once ('one-off scripts/master-scripts/movehq/Create_Module_Extension_ LeadCompanyLookup_OT3826.php');
//3731 Move Roles Module in Leads / Opportunites / Orders
require_once ('one-off scripts/master-scripts/movehq/AddRecordGuestmodulerelForLeads.php');


//3830 - Extensions :  Related Record Counts
require_once('one-off scripts/master-scripts/movehq/CreateRelatedRecordCountsModule_3830.php');
//3783 - Extensions :  Listview colors
require_once('one-off scripts/master-scripts/movehq/ExtensionsListviewcolors_3783.php');
// 3784 - Extensions :  Favorite Customs List
require_once ('one-off scripts/master-scripts/movehq/Create_Extension_FavoriteCustomsList_OT3784.php');
//3827 - Extensions :  Data Export Tracker
require_once('one-off scripts/master-scripts/movehq/Create_DataExportTracking_OT3827.php');
//3691 Local Tariff Module(s) - update modules for multi tenant environment
require_once('one-off scripts/master-scripts/movehq/UpdateLocalTariffModule_3691.php');
// OT 3897 - Adjusting Actuals related list to function for non-GVL HQ
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_UpdateActualsStagePicklist.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_SetActualStageFromQuoteStageForPreviousRecords.php');
// OT3756 - Reordering Invoice Details block within the Orders module
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_AddFieldsToInvoiceDetails.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_HideCommodityField.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Oders_AddBillingAddressRelatedFields.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_MakeInvoiceAddrDescAPicklist.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_ChangingBillingAddressDescriptionValues.php');
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_bill_addrdesc_table.php');


//3740 : Orders - Add National Account Number to the Order Details Block
require_once ('one-off scripts/master-scripts/movehq/UpdateOrdersModule_3740.php');
//3642 Agent Module - Merge Bugs
require_once ('one-off scripts/master-scripts/movehq/MoveAndRemoveBlock_AgentModule_OT3642.php');
//3935 Accounts Module - Billing Type field update
require_once('one-off scripts/master-scripts/movehq/UpdateBillingTypeFieldInAccountModule_3935.php');
//3590 OrdersTask Module - Add value to "Dispatch Status" picklist
require_once('one-off scripts/master-scripts/movehq/UpdateOrdersTaskModulev2_3590.php');
//3951 Bug: Tariff Services Module - Update after Release
require_once ('one-off scripts/master-scripts/movehq/TariffServices_3951.php');
//3948 Local Tariff Module - Updates after Release
require_once ('one-off scripts/master-scripts/movehq/MakeOwnerMandatory_OT3948.php');
//3949 Tariff Sections Module - Updates after Release
require_once ('one-off scripts/master-scripts/movehq/Add_Record_Link_Update_Module_TariffSections_OT3949.php');
// OT 3954 Agent Manager - Filter Option not sorting
require_once('one-off scripts/master-scripts/movehq/RemoveField_LBL_AGENTMANAGER_NO_AgentManager_OT3954.php');
// 3893 Leads - Move the Owner Field
require_once('one-off scripts/master-scripts/movehq/Update_LeadField_3893.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Opportunities_TogglePhoneFieldsOn.php');
//4011 Remove Parent / Child Relationship from all modules / fields
require_once ('one-off scripts/master-scripts/movehq/Remove_ParentAgent_Field_On_AgentManager_OT4011.php');
//Capacity Calendar
//OT4136: Capacity Calendar - Available as a selection for Menu Creator  Menu Editor
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddCapacityCalendar.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Holiday_Module.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_DailyNotes_Module.php');
//OT4214: Capacity Calendar Counter - remove guest block from Agent Manager - becoming a stand alone module
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CapacityCalendarCounter_Updates.php');
// OT 3759 - Rename move policies to account policies
require_once ('one-off scripts/master-scripts/movehq/RenameMovePoliciesToAccountPolicies3759.php');
//Add Media module for video surveys
require_once('one-off scripts/master-scripts/Media_20161222.php');
//4070	 Bug: Authority field is missing in Estimates
require_once ('one-off scripts/master-scripts/movehq/AddAuthorityFieldToEstimatesModule_4070.php');

//4016 Estimate Module - Remove / Hide "Contact Details" block
require_once ('one-off scripts/master-scripts/movehq/Hidden_ContactDetailsblock_EstimatesModule_OT4116.php');
// OT4120  Orders Module - Invoice Details block - update layout
require_once ('one-off scripts/master-scripts/Update_InvoiceDetailsblock_OrdersModule_OT4120.php');
//4075 Bug: Personnel - Values change after accessing record from user module
require_once('one-off scripts/master-scripts/movehq/UpdateStatusField_4075.php');
// OT4182 -- BUG: Opportunity Module - Hide Vanline / Reason field
require_once('one-off scripts/master-scripts/movehq/Core_Opportunity_HideVanlineReason.php');
//Add Media module for video surveys
require_once('one-off scripts/master-scripts/Media_20161222.php');

// 3818	Dispatch Status not available in LDD filters
// 3819	Add Dispatch Status field in Order Details
// 3822	Change dispatch status picklist
//  Un-do 3464 :/
require_once ('one-off scripts/master-scripts/movehq/LDD_UpdateDisptachStatus.php');

////// Pre-prod verification fixes
// Extra Stops
require_once ('one-off scripts/master-scripts/movehq/FixForPreProdExtraStops.php');

// Estimates Eff date / FS / IRR
require_once ('one-off scripts/master-scripts/movehq/PreProdFixEstimatesEffDateFSAndIRR.php');
// Estimates Sync (authority to optional)
require_once ('one-off scripts/master-scripts/movehq/PreProdEstimateSyncFix.php');
// Estimates Sync (containers)
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddCartonOnlyOptionToPacking.php');
// Estimates hide fields
require_once ('one-off scripts/master-scripts/movehq/PreProdRemoveSomeEstimateFields.php');
//Change how users are linked to Employees: //@TODO: Should be temporary solution until a better is implemented.
require_once('one-off scripts/master-scripts/movehq/Employees_Add_UserIDField.php');
// OPList business_line column type
require_once ('one-off scripts/master-scripts/movehq/PreProdOPListFixes.php');
// driver and agent in trips
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Orders_Update_LDD_Block20161222.php');
// actual delivery date
require_once ('one-off scripts/master-scripts/movehq/PreProdActualDeliveryDate.php');
// valuation block mismatch between Estimates and Actuals
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_UpdateActualsToParityWithEstimates.php');
// Extra field in ExtraStops
require_once ('one-off scripts/master-scripts/movehq/PreProdRemoveExtraStopAutogenField.php');
////// End pre-prod verification fixes
// 4114 Actuals Module - Update Layout
require_once ('one-off scripts/master-scripts/movehq/UpdateFieldAndBlockForActualModule_4114.php');
//4021 Orders Module - Contact Name​
require_once('one-off scripts/master-scripts/movehq/RemoveContactNameOrderModule_4021.php');
// 3950
require_once ('one-off scripts/master-scripts/movehq/Add_Record_Link_Update_Module_ Effective_Dates_OT3950.php');
// 4000 VTiger Extension - Add VTiger Proposal & Doc/Form Designer + Electronic Signature
require_once ('one-off scripts/master-scripts/movehq/Create_Extension_Module_DocumentDesigner_OT4000.php');
require_once ('one-off scripts/master-scripts/movehq/Create_Extension_Module_SignedDocuments_OT4000.php');

// Authority showing access denied in listview
require_once ('one-off scripts/master-scripts/movehq/FixAuthorityPicklist4254.php');


//OT4216 -- Orders Tasks - Update Personnel / Vehicles Guest Blocks
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_AddFields_OrdersTask_OT4216.php');


//OT4078 -- remove a picklist.
require_once('one-off scripts/master-scripts/movehq/Core_Documents_HideInvoicePacket.php');
//hide the taskNo field from the orders tasks
require_once('one-off scripts/master-scripts/movehq/UpdateOrdersTask_removeTaskNo.php');
//4214  - Fix Mandatory field
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CalendarCounter_FixCounterMand.php');
// OT 3979 - Vehicles - Remove fields in vehicle specifications -> vehicle_tareweight and vehicle_outsideheight
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Vehicles_Remove_Fields_OT3979.php');
//OT3978
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Vehicles_Update_PlateState_Picklist.php');
// 3658 - update for CPU block
require_once ('one-off scripts/master-scripts/movehq/UpdateCPUBlock3658.php');

//Fix Orders labels
require_once('one-off scripts/master-scripts/movehq/updateOrderLabels.php');
//3518 Add missing Hotfix
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_LocalDispatch_FilterTable.php');

// Accounting Integration
require_once ('one-off scripts/master-scripts/Hotfixes/CreateAccountingIntegration.php');
require_once ('one-off scripts/master-scripts/movehq/AddOrdersCustomerNumber3957.php');

//4282 VTiger Extension - Document Designer - Listview​
require_once('one-off scripts/master-scripts/movehq/Convert_module_entity_Quotingtool_OT4282.php');
// OT4366 - Adjusting mandatory fields in Estimates/Actuals
require_once('one-off scripts/master-scripts/movehq/Hotfix_EstimatesActuals_UpdateMandatoryFields.php');
//OT4571 - Restoring visibility to Assigned To and Created By fields
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Leads_AddAssignedUserAndCreatedByFields.php');

//OT4183 - Adding 'Lost' to 'Opportunity Status' picklist
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Opportunities_UpdateOpportunityStatusPicklist.php');
//3592 - Local Dispatch Actuals Module
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LDActuals_AddCustomTables.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LocalDispatchActuals_AddBlockCpuActualsAndEquipmentActuals.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_OrdersTask_NewActualFilter.php');

//OT4597 - Summary fields on Opportunities detail view
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Opportunities_AddSummaryFields.php');
// OT3986 - Adding All filter to Carriers
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_AddAllFilterToCarriers.php');
//4281	VTiger Extension - Document Designer - Permissions​
require_once ('one-off scripts/master-scripts/movehq/UpdateQuotingToolModule_4281.php');

// 4281	VTiger Extension - Document Designer - Permissions
require_once ('one-off scripts/master-scripts/movehq/Create_DocumentDesignerImages_Folder_4281.php');
//OT4357 - Order Module - Resequence & Add Required fields in Order Details Block to top.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Orders_RearrangeBlocksAndFields.php');

//4387 VTiger Extension - Document Designer - Available Modules [Configuration]​
require_once ('one-off scripts/master-scripts/movehq/CreateQuotingToolConfigurations_Table_4387.php');
// OT4405 - Removing Graebel options from picklists for VehicleOwnerHistory
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_RemoveGraebelpicklists.php');
// OT4404 - Adding VehicleMaintenance to vtiger_ws_entity
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_VehicleMaintenance_WebServiceInit.php');
// OT4402 - Adding fields to filter for list view
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_DrivingViolation_AddFilterFields.php');
// OT4445 - Making Member Of field in Users mandatory
require_once('one-off scripts/master-scripts/movehq/Hotfix_Users_MakeMemberOfMandatory.php');
// OT 4137 - changes to contact quick create
require_once ('one-off scripts/master-scripts/movehq/UpdateContactQuickCreate4137.php');
// OT 4108 4096, 4098
require_once ('one-off scripts/master-scripts/movehq/SetDefaultValues4108.php');
// OT 4313 - add orders related list to contacts
require_once ('one-off scripts/master-scripts/movehq/AddOrdersRelatedListToContacts4313.php');
// Hide vehicle fields on opportunities
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Opportunities_HideVehicleFields.php');
//OT4587 - Making billing type non-mandatory in Opportunities
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Opportunities_MakeBillingTypeNonMandatory.php');
//OT 4570 Remove guest module relationships from Leads
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Leads_DeactivateGuestModuleRelationships.php');
//OT18290 - Remove Service Status field from Out of Service module
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_OutOfServiceModule_HideField.php');
// OT 4412 - change function for orders related to orders
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Fix_Orders_Orders_RelatedList.php');
//OT4687 - Make Load Date mandatory on Opportunities
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Opportunities_MakeFieldsMandatory.php');
//OT18235 - Updated All Orders filter fields and order
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Orders_ChangeDefaultFilter.php');
//OT4597 - Summary fields on Opportunities detail view
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Opportunities_AddSummaryFields.php');
//OT4183 - Adding 'Lost' to 'Opportunity Status' picklist
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Opportunities_UpdateOpportunityStatusPicklist.php');
//OT4656 - Reordering fields in Survey Appointments
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Surveys_UpdateFieldOrder.php');
//OT 4672 Make Effective Tariff field mandatory in Estimates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Estimates_MakeFieldsMandatory.php');
//OT 4686 Make Participating Agent field mandatory on Orders Tasks.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_OrdersTask_MakeFieldsMandatory.php');
//OT4165 - Add mileage field to Orders
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Orders_MileageField.php');
//OT4654 - Setting default value for survey_status to Assigned
require_once('one-off scripts/master-scripts/movehq/Hotfix_SetDefaultSurveyStatus.php');
//OT4708 Agent Manager - Default "Payroll Week Start Date" field to Sunday
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_AgentManager_WSDP_DefaultValue.php');
//OT4669 - Changes to Local Dispatch default filter
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_LocalDispatch_View_Filter_Change.php');
//OT4667 - Changes to default filter for LDD.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_LDD_Default_View_Filter_Change.php');
//OT18416 - Changed default filter for Emails
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_AddDefaultFilterToEmails.php');
//OT18419 - Fixing issues with VehicleMaintenance that prevent list view from loading
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_FixVehicleMaintenanceSaveIssues.php');
//OT18419 - Add default filter for VehicleMaintenance
require_once('one-off scripts/master-scripts/movehq/VehicleMaintenance_AddFilter.php');
//OT18420 - Adding filter to MenuCreator
require_once('one-off scripts/master-scripts/movehq/MenuCreator_AddFilter.php');
//OT18264 - Adding filter to MilitaryBases
require_once('one-off scripts/master-scripts/movehq/MilitaryBases_AddFilter.php');
//OT3968 - Adding actual weight to orders module
require_once ('one-off scripts/master-scripts/movehq/Add_Field_Orders_3968.php');
//OT4476 - Commodity field creation
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_CreatingCommodity.php');
//OT4482 Create Owner field that includes Vanline Manager Record
require_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_add_uitype_1020.php';
//OT4713 - Add Booked to Order Status picklist
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Orders_UpdateOrdersStatusPicklist.php');
//OT4644 - Moving and hiding fields in Trips
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Trips_HideFieldsAndMoveField.php');
// OT18370 - Hide Project field in Orders
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Orders_HideProjectField.php');
// 4630	 BUG: Document Designer - Signed Record is not created
require_once ('one-off scripts/master-scripts/movehq/Update_Extension_Module_SignedDocuments_4630.php');
//OT4258
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CustomView_NewField.php');
//OT3518 Default Filters Right Tables Local Dispatch
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CreateDefaultFilters_OT3518.php');
// 4839 Document Designer - Portrait/Landscape
require_once('one-off scripts/master-scripts/movehq/Add_Columns_To_Module_DocumentDesigner_OT4839.php');
//4846	 Document Designer - Signed Record No
require_once('one-off scripts/master-scripts/movehq/Update_SignedRecordModule_4846.php');
//OT18543 - Adding default filter to RevenueGrouping
require_once('one-off scripts/master-scripts/movehq/20170519_150000_RevenueGrouping_add_filter.php');
//OT4619 Vendors Module - Add Out of Service as Related Module
require_once('one-off scripts/master-scripts/movehq/Hotfix_AddVendorsOutOfService.php');
//OT4611 Vendors Related Lists
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Vendors_Updates_20170612.php');
//OT4622 Out of Service Module for Personnel Module - Clean up "Status" picklist values
//OT4623 Out of Service Module for Vehicles - Clean up "Status" picklist values
require_once "one-off scripts/master-scripts/Hotfixes/Hotfix_CleanUp_OutOfServiceStatus_Picklist.php";
//OT4810 Carriers Module Fields
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CarriersFields.php');
require_once('one-off scripts/master-scripts/movehq/20170612_120000_Settings_update_moduleList.php');
//OT4503	Orders Module - Billing Details Block - Add Customer lookup field
require_once('one-off scripts/master-scripts/movehq/Add_BillingCustomer_Fields_To_Orders.php');
//OT4458 Setting up Record Protection module
require_once('one-off scripts/master-scripts/movehq/20170721_101100_recordprotection_create_module.php');
//OT4617  Vehicles Module - Add "Service Provider" look up field - resequence fields
require_once('one-off scripts/master-scripts/movehq/20170621_103000_vehicles_add_service_provider_lookup_field.php');
//OT3996 Menu Creator - Add "Menu Editor" from CRM Settings to module
require_once('one-off scripts/master-scripts/movehq/20170629_072900_create_menu_editor_table.php');
//OT3997 Customized Picklist module - Add Picklist Dependency
require_once "one-off scripts/master-scripts/movehq/20170621_083115_picklistcustomizer_add_uitype_custompicklist.php";
require_once "one-off scripts/master-scripts/movehq/20170621_091612_picklistcustomizer_create_table_custompicklist.php";
require_once "one-off scripts/master-scripts/movehq/20170621_092540_picklistcustomizer_update_uitype_picklist-to-custompicklist.php";
require_once "one-off scripts/master-scripts/movehq/20170621_093158_picklistcustomizer_add_picklist-values-from-old-picklist-tables.php";
require_once "one-off scripts/master-scripts/movehq/20170621_094145_picklistcustomizer_create_table_custom_picklist_dependency.php";
// OT18714 - Bug: Remove US Navy choice from picklist
require_once('one-off scripts/master-scripts/movehq/20170622_175046_orders_update_picklist-transferee_military_branch.php');
// OT18704 - Make Vehicle Unit # Required Field
require_once('one-off scripts/master-scripts/movehq/20170622_171615_vehicles_update_field-vechiles_unit.php');
// OT18702 - Local Operation Task Allows Negative Numbers for Crew and Vehicles
require_once('one-off scripts/master-scripts/movehq/20170621_103115_orderstask_update_fields-num_of_personal-and-num_of_vehicles.php');
// OT18621 - Remove/Hide Brand field from Accounts module
require_once "one-off scripts/master-scripts/movehq/20170622_113458_accounts_update_field-brand-hide.php";
//OT18594 - Agents associated to a vanline are not displaying under the related module
require_once('one-off scripts/master-scripts/movehq/20170623_064000_agent-manager_vanline-manager_relation.php');
// OT4868 - Personnel Roles Setup "Classification" picklist dependency on "Classification Type" picklist value
require_once "one-off scripts/master-scripts/movehq/20170622_182830_employeeroles_add_picklistdependency-emprole_class_type-to-emprole_class.php";
//OT4861 ClaimsSummary Representative Field
require_once('one-off scripts/master-scripts/movehq/20170626_075900_claimssummary_update_representative-field_uitype.php');

require_once('one-off scripts/master-scripts/movehq/20170629_100000_media_add_archive-url-webservice.php');
//4881 Tariffs (Business Line / Commodity)
require_once('one-off scripts/master-scripts/movehq/20170621_112800_tariff_add_commodity_add_business_line.php');
//OT4514 - Add customer_number field to Contacts module
require_once('one-off scripts/master-scripts/movehq/20170622_164500_contacts_add_customer-number.php');
//OT 3829 - Extensions :  Tooltip Manager
require_once('one-off scripts/master-scripts/movehq/Create_Module_TooltipManager_OT3829.php');
require_once('one-off scripts/master-scripts/movehq/AddTooltipModuleToUserConfigurable_OT3829.php');
// OT18712 - Bug: Format of GBL Number field should allow alpha-numeric
require_once('one-off scripts/master-scripts/movehq/20170621_121200_orders_update_table-vtiger_orders.php');
// OT18936 Widget - Top Opportunities - Please turn off
require_once "one-off scripts/master-scripts/movehq/20170726_081227_home_remove_widget-top_potentials.php";
// OT18938 Widget - Total Amount by Sales Stage - Please turn off
require_once "one-off scripts/master-scripts/movehq/20170726_090027_home_remove_widget-funnel_amount.php";
// OT4801 - Estimate Module - Add picklist "Pricing Type" to Estimate Details block - resequence fields
// OT4804 - Estimate Module - Add checkbox field to Estimate Details block
require_once("one-off scripts/master-scripts/movehq/20170626_071043_estimates_update_fields.php");
// OT4798 - Estimates - Remove / Hide the picklist "Quotation Type" within Estimate Details block
// OT4799 - Estimate Module - Remove / Hide fields from "Move Details" block
require_once("one-off scripts/master-scripts/movehq/20170626_084700_estimates_hide_fields.php");
//4621 OrdersTask Module - Add "Service Provider" field
require_once('one-off scripts/master-scripts/movehq/20170621_074300_orderstask_add_service-provider_field.php');
// OT18937 Widget - Revenue by Salesperson - Please turn off
require_once "one-off scripts/master-scripts/movehq/20170726_084427_home_remove_widget-total_revenue.php";
//OT5215	Personnel - update UIType for "Related MoveHQ User"
require_once('one-off scripts/master-scripts/movehq/20170801_060500_new_uitype_employeesuserpicklist.php');
//OT5339 -- Add MC Number to the Agent Roster record.
require_once ('one-off scripts/master-scripts/movehq/20170825_142730_agentmanager_add_field-agents_mc_number.php');
//OT5339 -- Add DOT Number to the Agent Roster record.
require_once ('one-off scripts/master-scripts/movehq/20170825_143030_agentmanager_add_field-agents_dot_number.php');
//OT5339 -- Order Agent Roster to put the MC/PUC/DOT number in the requested positions.
require_once ('one-off scripts/master-scripts/movehq/20170825_13330_agents_reorder_fields-lbl_agents_information.php');
//OT5342 -- update agent_puc to be varchar.
require_once('one-off scripts/master-scripts/movehq/20170825_172530_agents_update_field-agent_puc.php');
//OT5272	Contact Mouse Hover popup - update information being shown
require_once('one-off scripts/master-scripts/movehq/20170823_103000_contacts_update_tooltip-fields.php');
//OT5233	Updates to the Contacts Module (Edit/Create View)
require_once('one-off scripts/master-scripts/movehq/20170823_083000_contacts_update_fields.php');
//OT5269 Leads - Modify Lead Information Block
require_once('one-off scripts/master-scripts/movehq/20170823_114000_leads_update_fields-sequence.php');
//OT19118 -- Add custom reports password as a field to agentmanager records. (NOTE: existed in the database but not as a field.)
require_once('one-off scripts/master-scripts/movehq/20170824_163630_agentmanager_add_field-custom_reports_pw.php');
// OT19139 - No negative entries
require_once ('one-off scripts/master-scripts/movehq/20170807_160610_orderstask_update_fields-packingqty-cartonqty-unpackingqty.php');
//OT5171 - Remove Cubic Feet Capacity field
require_once('one-off scripts/master-scripts/movehq/20170810_093727_vehicles_remove_field-vehicle_feetcapacity.php');
//OT19172  Prod: Local Dispatch - +Create New Filter Is In The Wrong Place
require_once('one-off scripts/master-scripts/movehq/20170816_102700_orderstasks_remove_action_link.php');
//OT 19122 Industry field in customized picklist module not working correctly
require_once('one-off scripts/master-scripts/movehq/20170821_113000_accounts_update_picklist-tables.php');
// OT4801 - Estimate Module - Add picklist "Pricing Type" to Estimate Details block - resequence fields
// OT4804 - Estimate Module - Add checkbox field to Estimate Details block
require_once("one-off scripts/master-scripts/movehq/20170626_071043_estimates_update_fields.php");
// OT4798 - Estimates - Remove / Hide the picklist "Quotation Type" within Estimate Details block
// OT4799 - Estimate Module - Remove / Hide fields from "Move Details" block
require_once("one-off scripts/master-scripts/movehq/20170626_084700_estimates_hide_fields.php");
// OT19127 - Estimates - BUG: Pricing (Estimates) - Old records / SurveyHHG not setting "Pricing Type" value which breaks conditionalizing
require_once("one-off scripts/master-scripts/movehq/20170809_171327_estimates_update_field-pricing_mode.php");
//OT5280 Picklist Customizer - Blank State & Default Values
require_once('one-off scripts/master-scripts/movehq/20170824_074327_orders_update_field-ordersstatus.php');
//OT5321    Event Module - Remove / Hide "Visibility" field
require_once('one-off scripts/master-scripts/movehq/20170823_121000_calendar_remove_field-visibility.php');
//OT18636 Resetting valuation_deductible field values to only contain Full Value Protection and Released Valuation
require_once('one-off scripts/master-scripts/movehq/20170830_170700_estimates_modify_field-valuation_deductible.php');
//OT18379 - Orders to Orders Related List
require_once('one-off scripts/master-scripts/movehq/20170407_105800_change_orders_to_orders_relatedlist.php');
//OT19133 - Actual # of Crew field NO Negative
require_once('one-off scripts/master-scripts/movehq/20170807_072100_no_negatives_numbers.php');
//OT5272 Contact Mouse Hover popup - update information being shown
require_once('one-off scripts/master-scripts/movehq/20170823_103000_contacts_update_tooltip-fields.php');
//OT5307 - Modify Local Operations Task
require_once('one-off scripts/master-scripts/movehq/20170824_150900_orderstask_update_fields.php');
//3850	VTiger Extension - Notifications/Reminder
require_once ('one-off scripts/master-scripts/movehq/20170914_123405_notifications_create.php');
// OT5256 Leads Source Default
require_once('one-off scripts/master-scripts/movehq/20170907_154927_leads_update_fields-leadsource.php');
// OT5304 - Update Personnel Assigned to Task block
require_once('one-off scripts/master-scripts/movehq/20170911_094827_orderstask_add_field_timeoff.php');
//OT4634 - ItemCodes - Update UIType for "IGC Tariff Service Code"
require_once('one-off scripts/master-scripts/movehq/20170810_090000_itemcodes_update_tariffservicecode.php');
//OT4634 - ItemCodes - Add tables to cache values for "IGC Tariff Service Code"
require_once('one-off scripts/master-scripts/movehq/20170906_165000_itemcodes_add_tables_tariffservicecode.php');
//OT5194 Menu Creator - add owner field from OT Item 4482
require_once('one-off scripts/master-scripts/movehq/20170911_menucreator_update_agentid_uitype.php');
//OT5323 Local Operations Tasks - Add Record Update Information Block
require_once('one-off scripts/master-scripts/movehq/20170914_113500_orderstask_add_block_record-update-info.php');
//OT 5347 Generate file out of Leads module and ftp to a server (with workflow custom function handler)
require_once('one-off scripts/master-scripts/movehq/republic/20170912_082805_workflows_add_leads_custom_handler.php');
//OT4626 - Remove commissionplan_default ('Auto Commission') field from commission plan items.
require_once('one-off scripts/master-scripts/movehq/20170918_084530_commissionplans_hide_field-commissionplan_default.php');
//OT5274 Contacts - Add Record Update Information Block
require_once('one-off scripts/master-scripts/movehq/20170918_065500_contacts_create_block-recordupdateinformation.php');
//OT4940 Change dropdown option in Survey Type field in Surveys Module
require_once('one-off scripts/master-scripts/movehq/20170918_100500_surveys_update_survey_type_picklist_value.php');
//OT5273 Opportunities - Modify Opportunity Information and Record Update Information Blocks
require_once('one-off scripts/master-scripts/movehq/20170918_074000_opportunities_update_blocks.php');
// OT5249 - Claim Status Pick list
require_once('one-off scripts/master-scripts/movehq/20170913_102227_claimssummary_update_field-item_status.php');
//5268 Update "Operations Task" Pick List in "Local Operations Task" Module
require_once('one-off scripts/master-scripts/movehq/20170907_070500_orderstask_update_field-operations-task.php');
// OT5550 - User Module Default Date Format and Time Zone
require_once('one-off scripts/master-scripts/movehq/20171005_224527_users_update-date_format-time_zone.php');
// OT5469 - Modules in List View - "Actions" drop down button - Add Import and Export options
require_once('one-off scripts/master-scripts/movehq/20171003_191727_various_update_actions_dropdown_button.php');
//OT4800 Estimate Module - Update "Move Details" block for Local Tariffs
require_once("one-off scripts/master-scripts/movehq/20170905_073427_estimates_add_fields.php");
// OT5390 - Remove/hide old fields in SIT details block
require_once('one-off scripts/master-scripts/movehq/20170926_080527_estimates_hide_fields-distribution_discount_percentage-distribution_discount.php');
//OT4583 Commission Plan Group module - Break apart Business Line into Commodity and Business Line
require_once('one-off scripts/master-scripts/movehq/20170918_092530_commissionplansfilter_update_fields-business_line-commodities.php');
// OT5300 - Adding "Default Resource Width" To local dispatch filters
require_once('one-off scripts/master-scripts/movehq/20170922_091227_orderstask_create_table-localdispatch_resourcewidth.php');
//OT5270	Order Module - Add Lead Source Field.
require_once('one-off scripts/master-scripts/movehq/20170824_165500_orders_add_field-leadsource.php');
//OT19416 - Reworking PicklistCustomizer
require_once('one-off scripts/master-scripts/movehq/20170928_131600_picklistcustomizer_create_table_exceptions.php');
require_once('one-off scripts/master-scripts/movehq/20171010_104000_picklistcustomizer_update_custom-picklist-values.php');
// OT5553 Local Dispatch - Add Filter Functionality to default Resource window collapsed or expanded
require_once('one-off scripts/master-scripts/movehq/20171007_134727_orderstask_update_table-localdispatch_resourcewidth.php');

require_once('one-off scripts/master-scripts/movehq/20171102_091600_helpdesk_update_status.php');
//OT19541	BUG: OrdersTask - View = List - Update Default filter / remove filter options tied to Local Dispatch
require_once('one-off scripts/master-scripts/movehq/20171024_113000_orderstask_update_filters-All.php');
//OT19542   BUG: Capacity Calendar Filters - Create Default Filter / Remove incorrect filter options
require_once('one-off scripts/master-scripts/movehq/20171024_121500_orderstask_create_filters-Capacity-Calendar.php');
//OT19543 BUG: Local Dispatch - Update default filter - remove incorrect filters as options
require_once('one-off scripts/master-scripts/movehq/20171024_114500_orderstask_update_filters-Local-Dispatch-Day-Page.php');
// OT19522 - Add the flag to Tariff Section to check if the section should override the bottom line discount.
require_once ('one-off scripts/master-scripts/movehq/20171013_084330_tariffsections_add_field-bottomline_discount_override.php');
//OT19250
require_once('one-off scripts/master-scripts/movehq/20171106_155500_global_update_picklist-values.php');
//OT4877 - Splitting business_line and commodities in AgentCompensationGroup
require_once('one-off scripts/master-scripts/movehq/20171108_163100_agentcompensationgroup_update_business-line.php');

//OT19745 - Prod validation fail, change validation rules for local bottomline discount.
require_once ('one-off scripts/master-scripts/movehq/20171129_081130_estimates_update_field-local_bl_discount.php');
//OT19745 - Prod validation fail, change validation rules for local bottomline discount.
require_once ('one-off scripts/master-scripts/movehq/20171129_085830_estimates-alter_field-sectiondiscount.php');
//OT19540 BUG: OrdersTask Filter options across views (Capacity Calendar / Local Dispatch / List View)
require_once('one-off scripts/master-scripts/movehq/20171116_132300_allmodules_filter_update.php');
// OT4776: OT4776: Hotfix to change the data type of the "Competitive" field to a checkbox and update existing DB values.
require_once('one-off scripts/master-scripts/movehq/20171130_113105_opportunities_change_field_type.php');
// OT5695: Remove "trips" as a related module from the Trips module.
require_once('one-off scripts/master-scripts/movehq/20171130_093445_trips-update-related-modules-list.php');
// OT5703: ZoneAdmin module - hide id field and change entity identifier
require_once('one-off scripts/master-scripts/movehq/20171129_154530_zone_admin_detail_view_change_header_column.php');
//OT19721   CFD: Documents Module - Folder picklist not multi-tenant
require_once ('one-off scripts/master-scripts/movehq/20171129_114500_documents_update_folders-uitype.php');
//OT19767 - Make Vanline field within AgentManager mandatory
require_once ('one-off scripts/master-scripts/movehq/20171208_145100_agentmanager-alter_field-vanline_id.php');
// OT19671 - BUG: Local Dispatch - Vehicles table - Update default filter label
require_once('one-off scripts/master-scripts/movehq/20171201_090738_orderstask_update_filter-s2id_autogen6.php');
//OT19630 - Propagate picklist customizations to other fields with same fieldname
require_once ('one-off scripts/master-scripts/movehq/20171211_090900_picklistcustomizer_add_exceptions-for-matching-fieldnames.php');
//OT19642 - Disabling leadsource field in Accounts module
require_once ('one-off scripts/master-scripts/movehq/20171212_110600_accounts_remove_leadsource.php');
//OT5511 - Add MC & DOT to Vanline Manager
require_once ('one-off scripts/master-scripts/movehq/20170105_131300_vanlinemanager_add_field-mc_number-dot_number.php');
//OT5513 - Add state number to agent manager
require_once ('one-off scripts/master-scripts/movehq/20170105_171000_agentmanager_add_field-state_number.php');
//OT5514 - remove licensing numbers from agent roster.
require_once  ('one-off scripts/master-scripts/movehq/20170108_110100_agents_hide_fields-agent_puc-agents_mc_number-agents_dot_number.php');
// OT5247 - Order Status Picklist
require_once('one-off scripts/master-scripts/movehq/20170828_113827_orders_update_field-ordersstatus.php');
// OT4812: Add 'Pounds Per Man Per Hour' field to the Hourly Set block in the Tariff Services module
require_once('one-off scripts/master-scripts/movehq/20171229_143120_tariffservices_add_field-ppmph-hourly-set-block.php');
//OT5139 Don't allow for negatives in the pricing module
require_once('one-off scripts/master-scripts/movehq/20170907_082927_estimates_update_fields_min-value-zero.php');
require_once('one-off scripts/master-scripts/movehq/20170907_114327_orderstask_update_fields_min-value-zero.php');
// OT5139 - ExtraStops - Don't allow for negatives in the pricing module
require_once('one-off scripts/master-scripts/movehq/20180122_090727_extrastops_field_typeofdata_update-extrastops_weight.php');
//OT18740 - set default IRR value to 4 for all interstate/intrastate records
require_once ('one-off scripts/master-scripts/movehq/20180118_121800_estimates_add_default-irr-value.php');
//20103 - HIDE the sit fuel surcharges this is literally in an email.
require_once ('one-off scripts/master-scripts/movehq/20170131_172530_estimates_hide_fields-sit_origin_fuel_percent-sit_dest_fuel_percent.php');
//OT20106 - make the vehicle type not a custom pick list until we know how to search for trucks/trailers.
require_once('one-off scripts/master-scripts/movehq/20170131_222030_vehicles_update_field-vehicle_type.php');
