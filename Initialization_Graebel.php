<?php
/*

    Sirva initialization script. Modifies the instance with Sirva specific updates

*/
//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');

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
//Add billing address to Orders.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Add_BillingAddress_to_Orders.php');
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
// OT 16094 - Updating commodity field in billing addresses to text type to take business line multipicklist values.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Accounts_ModifyBillingAddressBlock.php');
// OT 3157 - remove active checkbox for Documents
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_HideActiveCheckboxInDocuments.php');
// OT 3041 - hide unneeded discounts in Contracts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_HideUnneededDiscounts.php');
// OT 16422 - Changing Bottom Line Distribution Discount to decimal type to prevent rounding errors
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ConvertBottomLineDistDiscountToDecimal.php');
// OT 16499 - Fixing Actuals
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_UpdateActualsToParityWithEstimates.php');
// OT 3188 - Adding values to fleet type picklist.
require_once('one-off scripts/master-scripts/Hotfix_GVL_EmployeesUpdateFleetTypePicklistt.php');
//OT 3117 - Add secondary_block and secondary_sequence columns to vtiger_field and set values for Workspace fields
//require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_ConfigureWorkspaceSequence.php');
//OT 3234 - Lock down Actuals and Estimates records when affiliated Orders record is locked
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_AddLockField.php');
//OT16533 webservice input of an Account, removes fields: commodity invoice_document_format invoice_delivery_format
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Accounts_removeCommodityInvoiceFields.php');
//OT3253 fix up account's save.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Accounts_removeBrand.php');
//OT1754 Add MSI tariff and OT1614 Add 400NG tariff
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_TariffManagerUpdateCustomTariffType.php');
// OT 16365 - Carton only column for packing
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddCartonOnlyOptionToPacking.php');

// OT 3258 Adding additional roles to Move Roles
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_MoveRoles_AddItemsToRolesPicklist.php');
// OT 3257 - Add more roles to employees
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddNewOptionsToEmployeeRoles.php');

// OT 16414 Title field in documents made non-mandatory
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Documents_MakeTitleNonMandatory.php');
//OT 16438 Adding field to determine if flat charge should be included in rate
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddRateIncludedToFlatChargeTariffItem.php');
//OT 3265 Adding Service Provider field to Move Roles.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_MoveRoles_AddServiceProviderField.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Vendors_UpdateSummaryfieldValues.php');
// OT 3179 - Add commission to move roles
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddCommissionToMoveRoles.php');
//OT3285 Creates vtiger_api_responses for Invoice API to store results of posts.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_CreateAPIStatusTable.php');
// OT 3181 - Add service providers distribution split to Actuals line items
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddTableForDetailedLineItemsToActualsServiceProviders.php');
// OT 3297 - Delivery date on actuals
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddActualsDeliveryDate.php');
// OT 16634 & 3255 - Add Actuals Stages
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_UpdateActualsStagePicklist.php');
// OT 3298 Add E-mail and Phone Number to Orders Invoice Details in Orders
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_AddFieldsToInvoiceDetails.php');
//OT 16633 On Orders Commodity Field needs to be removed
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_HideCommodityField.php');
// OT 16624 - Add related contact to Accounts for billing type = Consumer/COD
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddTransfereeRelatedContactToAccounts.php');
// OT 16678 - Add RVP to Valuation picklist
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddRVPToValuationBlockInAllModules.php');

// OT 16261 - Add Debris/Minimum packing to Estimates and Actuals
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Estimates_DebrisRemovalFieldsInAccessorials.php');
//OT 3256 - Add Addresses Module
//require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Addresses_CreateModule.php');
//OT 3256 - Add Billing Addresses Module
//require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_BillingAddresses_CreateModule.php');
// OT 16667 - Accounts/Orders/Estimates/Actuals relations
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddActualsRelatedListToAccounts.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddEstimateActualsRelationship.php');
// OT 16666 - Orders Invoice Details fields required for block.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Oders_AddBillingAddressRelatedFields.php');
//OT16609 update to invoice_format and invoice_pkg_format picklist;
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CorrectInvoiceFormatPicklist.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Free_FVP_Fields.php');
// OT 16570 - Change Vendor Business Name to text field
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_SetVendorBusinessNameToText.php');
// OT 16263 & 16346
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddAccessorialsToExtraStops.php');
// OT 16725
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_UpdateServiceProviderInfo092816.php');
// Non existent OT item
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_SetTransfereeContactFieldNonmandatory.php');
// OT 16724 - Update picklist dependencies for valuation dropdown based on tariffs.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_SetValuationPickDependenciesByTariff.php');
//OT 3338 - add Location and GCS_Flag to the detail line items
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_detaillineitem_addLocGCSFields.php');
//OT16352 Calculate Net Weight
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Actuals_AddWeightFields.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_SetValuationPickDependenciesByTariff.php');
// OT 16757 - Actual Stage is not always displaying under when veiwing the actuals under an order
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_SetActualStageFromQuoteStageForPreviousRecords.php');
// OT 16748 - Wrong related Estimates to Orders
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_RemoveDuplicatesFromCRMEntityRelTableAndFixOrdersRelations.php');
//OT16679 - Remove Out of Service block
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_RemoveOutOfServiceBlock.php');
//OT3313
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddOutOfServiceModule_Updates_05102016.php');
//OT3240 - Adding more columns to contracts popup
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Contracts_Popup_Columns.php');
// OT 3346 - Updating Valuation Information block in Contracts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Contracts_UpdateValuationBlock-10042016.php');
// OT 16796 Moving Fuel Surcharge field in Estimates and Actuals
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Estimates_MoveFuelSurchargeField.php');
//OT 3345 - Add field for Project Name
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_OrdersAddFieldForProjectName.php');
// OT 16801 - Valuation for WorkSpace business line in Orders
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddWorkSpaceDeclaredValueFieldToValuation.php');
//OT16826 added keys to vtiger_detailed_lineitems.dli_relcrmid and dli_service_providers.dli_id
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddKeysToDetailedLineItemsServiceProviderTable.php');
// OT 16831 - Move weight fields
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ReorderWeightFieldsOnInterstateMoveDetails.php');
//OT3338 build a map for tariff items to extra stuff graebel wants for detailed line itesm.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_CreateTariffItemMapTable.php');
//OT16846 add include in packet field to documents and hide invoice packet type
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Documents_addIncludePacketField.php');
//OT 3383 Add additional fields to detailed line items
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_detaillineitem_addPhaseEventFields.php');
// OT 16759 - Convert contracts business_line to multiselect
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ConvertContractBusinessLineToMultiselect.php');
// OT 3176 - Turns out this is wrong, remove the fields
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_HideUnneededAccesorialWaingTimeFields.php');
// OT 16665 - Updating Mandatory fields in Orders to match what's required for Orders API
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_MakeAPIFieldsMandatory.php');
// OT 3393 - Hide unneeded field and move expedited field to interstate move details
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_FixExpeditedShippingFieldLocation.php');
// OT 3394 - Add more stop types
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddMoreExtraStopLocationTypes.php');
// OT 3401 - Add Service Code to local tariff services
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_LocalTariffsAddServiceCodeFields.php');
// NO OT item - hide distribution_discount and distribution_discount_percentage fields
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_HideUnneededDiscountFields.php');
// OT 16969 - Convert debris removal to checkbox
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ConvertDebrisRemovalFieldsToCheckboxes.php');
// OT 3406 - Remove SIT fuel surcharges from Estimates and Actuals
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Estimates_HideSITFuelSurchargeFields.php');
// OT 3399 - SIT Authorization #
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddSITAuthorizationNumber.php');
// OT 16727 - update Order's order_no to be uitype=4 instead of the special 1001.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ConvertOrders_no_toUIType4.php');
// OT 3413 - Add uitype 4 to all default module filters
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddAutoRecordNumberToBaseAllFilters.php');
// OT 16993 - Tariff in detail view of estimate wrong (not the attached Contract tariff)
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_UpdateEstimateActualTariffFromContract.php');
// OT 17002 - Update extra stop type picklist
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_UpdateExtraStopLocationTypePicklist.php');
// OT 16302 - Hide unneeded discount fields in Contracts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_HideUnneededDiscountsFromContracts.php');
// OT 17010 - OT Loading Unloading should be checkboxes
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ConvertOTLoadingToCheckbox.php');
// OT 3430 - Changes to Bulky Auto rating for 1950B
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddTotalAutoWeightFieldToQuotes.php');
// OT 3428 - Security for integration with invoiced/distributed flags for line items
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddIntegrationUserTable.php');
// OT 16942 - Orders Task related view fields
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddRequestedFieldsToSummaryForOrdersTask.php');
// OT 16954 - Discount should be formatted as %
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ChangeUITypeOfDiscountPercentTo9.php');
// OT 17041 - Fix overflow order sequence number
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_CreateOverflowOrderSequenceTable.php');
// OT 17164 - Make load date and delivery date mandatory in Actuals @NOTE NOTE NOTE this makes them OFF.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Actuals_MakeLoadAndDeliveryDatesMandatory.php');
// OT 17152 - update invoice_finance_charge to be a decimal value in the database instead of an integer. OT17149 - update invoice_finance_charge to match
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_Orders_UpdateFinanceField.php');
// OT 17071 - Orders - Estimates/Actuals relation list view
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_FixOrdersActualsRelatedList.php');
//OT 17045 - Fix for orders/trips related list
require_once('one-off scripts/master-scripts/Hotfixes/HotFix_Fix_OrdersTripsRelatedList.php');
//OT3461 -- add metro flag to detailed line items:
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_detaillineitem_addMetroField.php');
//OT3381 - Adding Driver Violation
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_MovingViolation.php');
//OT17074 - Add new fields into Storage Location Block
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Storage_AddAddressFields.php');
// OT 17208 - CWT by weight not saving decimals
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ConvertCWTByWeightRateToDecimal.php');
// OT 17266 - Wrong tariff on estimates that have contracts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_EnforceContractTariffOnEstimates.php');

//OT3511 -- add a new field to allow the tariff number to be set on local tariffs:
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_LocalTariffs_AddTariffID.php');
// OT 17228
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ChangeOversizedOnVehicleTransportToDropdown.php');
// OT 17301 - Add miles field to local moves
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddMilesFieldToLocalMoveDetails.php');
//OT16986 - Update payment_terms field on orders to match columntype of payment_terms in Accounts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_UpdatePaymentTermsFieldType.php');
//OT17341 - Add fields to Estimates/Actuals for list view and related list views
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Estimates_AddNewSummaryFields.php');

//OT17106 - Fix Vehicle Maintenance permissions
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_FixVehicleMaintenanceSaveIssues.php');
// OT 17368 - Hide valuation override field
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_HideValuationOverrideField.php');
// OT17150 - Add ready to invoice and distribute totals
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddReadyToInvoiceAndDistributeTotals.php');
//OT16984 - Convert billing address description field to a picklist in Orders.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_MakeInvoiceAddrDescAPicklist.php');
// OT 16984 - update database values for billing address descriptions
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_ChangingBillingAddressDescriptionValues.php');
//OT17296 - Contractor Type showing as access denied
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_FixContractorType.php');
// OT 16787 - GSA500
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddGSA500AccessorialFields.php');
// Add table for interstate service charges
require_once('one-off scripts/master-scripts/sirva/AddServiceChargesTable.php');
// OT 3562 - update local tariff item codes
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_TransformLocalTariffItemCodes.php');
// OT 2793 - Add fields for Consignee  in orders at Origin, Destination, and Extra Stops
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_ExtraStops_AddConsigneeFields.php');
// OT 17461 - Contract Valuation -- requires updating existing values in database
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_UpdateContractValuationTypeTableValues.php');
// OT 2790 - Hide Van Line Registration and Registered Date fields in Orders
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_HideVLRegistrationFields.php');
// OT 2576 - Adding a field to connect an associate to a user.
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_WorkFlows_AddEmployeeLookupField.php');
// OT 2093 - Add Claimaint to contact types.
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Contacts_AddNewContactType.php');
// OT 17529 - Removing unused discount fields from Estimates.
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Estimates_HideUnneededDiscounts.php');
//Fix order status values
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Fix_Orders_Status.php');
// OT 3616
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddVehicleTransportOnlyCheckbox.php');
// Fix for missing columns ???
require_once ('one-off scripts/master-scripts/movehq/NationalLeadModuleChanges_3429.php');
//Fix for orders complaining that vtiger_bill_addrdesc doesn't exist: (just build the uitype 16 tables for it)
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_bill_addrdesc_table.php');
// Hide potential_no field on Opportunities
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_HidePotentialNoField.php');
// Hide orderstask_no field from OrdersTask
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_HideOrdersTask_noField.php');
// Hide ordersmilestone_no field from OrdersMilestone
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_HideOrdersMilestone_noField.php');
//OT3626 - Adding new values to Orders Picklist
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Orders_UpdateOrderStatus.php');
//OT17605 - Changing dli_invoice_number to varchar
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Estimates_ChangeDLIInvoiceNumberToString.php');
//OT3734 - Add Driver and Agent Name fields
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Orders_Update_LDD_Block20161222.php');
//OT3539 - Add 2290 Expiration date field
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Vehicles_UpdateFields20161117.php');
//OT3537 - 3538
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Workflows_for_OutOfService.php');
//OT3688 - Conversion of Participating Agents to guest blocks
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ConvertParticipatingAgentsToGuestModule.php');
//OT3809 - Update to Vehicle Transportation mandatory fields
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_VehicleTransportation_MakeFieldsMandatory.php');
//OT3771 - Setting mandatory fields in Orders Module
require_once("one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Orders_MakeFieldsMandatory.php");
//OT3763 - Set mandatory fields in Accounts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Accounts_MakeFieldsMandatory.php');
//OT3790 - Make fields mandatory in Participating Agents
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ParticipatingAgents_MakeFieldsMandatory.php');
// OT 17832 - Calendar relation to OrdersTask for To-Do
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddOrdersTaskCalendarRelation.php');
// OT 17479 -- estimates refactor
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_EstimatesRefactor.php');
// OT 3792 - make fields in actuals mandatory
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_SetActualsFieldsMandatory3792.php');
// OT 3921 - Payment terms to integer
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_PaymentTermsToInteger3921.php');
// OT 17479
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_LocalTariffsAddBusinessLine.php');

// OT 3521 - Contract Effective Date field on Vendors
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_AddVendorContractEffectiveDate3521.php');
//OT3969 - Conversion of field type to picklist for Driver's License Class in Associates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Associates_MakeLicenseTypeAPicklist.php');
// OT 3984 - Field for Terminations reason in Associates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Employees_AddTerminationBlock.php');
// OT 3980 - 400DOE tariff
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Add400DOETariff.php');
// OT 4027 - Add Qualification Radius field in the driver qualification module
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddDriverQualificationMiles.php');
// OT 4043 - make coordinator for Leads not mandatory
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfixes_GVL_MakeCoordinatorNotMandatory4043.php');

// OT 4025 & 4026 - Add permissions fields for editing drivers and vehicles to users
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddExtraUserPermissions.php');
//OT4059 -- Allow user to set invoice packet include from order's related list view of documents.
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_documents_updateSummaryField.php');
// OT 4157 - Change Field In associates module
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVLAddSkillsLevelCompleted4157.php');
// OT 18013 - fix for some column types
require_once ('one-off scripts/master-scripts/Hotfixes/FixColumnTypes20170208.php');
//OT4156 -- Add value to Type of Reason picklist and capitalize first character of all picklist values
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_OutOfService_AddTypeofReasonPicklistVal.php');
//OT4164 -- Adding TEXT field to Leads
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Leads_AddMoreInformationField.php');
// OT 4150 - Convert MovingViolation vehicletype to picklist
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ConvertVehicleTypeInMovingViolation4150.php');
// OT 4152 - convert info source in MovingViolation to picklist
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ConvertInfoSourceToPicklist4152.php');
// OT 4153 - add status field to moving violation
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddMovingViolationStatus4153.php');
// OT 4179 - Change vehicle status options
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ChangeVehicleStatusOptions4179.php');
// OT4149 - Add fields to Accidents module
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Accidents_ModuleExpansion.php');
//OT4198 -- Make fields mandatory in OrdersTask
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_OrdersTask_MakeFieldsMandatory.php');
// OT 4155 - Add OOS Court Date type
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddOOSCourtDateType4155.php');
//OT4089 -- Change dropdown contents for leadsource in Leads
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_Leads_AddFieldsRearrangeFields.php');
// Fix for TariffReportSections list view
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddAllFilterToTariffReportSections.php');
// OT 4328 - add account to OrderTasks, populated from Order account
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddAccountToOrdersTask4328.php');
// Fix for TariffServices list view
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddAllFilterToTariffServices.php');
