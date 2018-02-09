<?php
/*

    Sirva initialization script. Modifies the instance with Sirva specific updates

*/
//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');

//hot fix for adding shipper type fields
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_ShipperType.php');
//Hot fix to change lead status to lead disposition with Sirva specific picklist values
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaJobStatus.php');
//Hotfix for the stuff needed to add the LeadType field
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LeadType.php');
//Hotfix for adding TPG/Pricelock Tariffs
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_TPGPricelock.php');
//Hot fix to change leads primary email field to mandatory
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaRequireEmail.php');
//Hot fix to add/modify SIRVA specific fields for leads
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaLeadsFieldsMod.php');
//Hot fix to add/modify SIRVA specific fields for opps
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaOppsFieldsMod.php');
//Hot fix to add/modify SIRVA specific fields for estimates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaEstFieldsMod.php');
//Hot fix to add/modify SIRVA specific fields for contracts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaContractsFieldsMod.php');
//Hot fix to add tables for annual rate increases
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaAnnualRateTables.php');
//Hot fix to add a place to save custom packing rates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddCustomRate_to_Packing_Items.php');
//Add the Language field to Opportunities - must be run before lead conversion hotfix
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Opportunities_AddLanguage.php');
//Hot fix to configure SIRVA specific lead conversion settings
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaLeadConversion.php');
//Hot fix for CWT rate by weight
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CWTbyWeight.php');
//Hot fix to require email
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaRequireEmail.php');
//Hot fix turns the funded field into a text field from it's previous checkbox type
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_FundedTextField.php');
//Moves Special Terms and Comments from Employer Assisting to a more logical place
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaTermsCommentsRelo.php');
//Adds in the Disposition Lost fields and reorders the fields in leads to make more sense
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_DispositionLost.php');
//Adds in the fields needed for LMP to moveCRM and reorders everything all pretty like.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LMPFieldsToLeads.php');
//Adds in the fields needed for LMP to ModComments so the data can be retreived easier
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LMPModComments.php');
//Add Lead Type to Estimates so Tariffs can be filtered based on Lead Type, this field is hidden and auto populated from Opps
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddOpportunityTypeToEstimates.php');
//Add grr field to estimates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_GRR_field.php');
//Add FVP for National accounts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Free_FVP_Fields.php');
//Add Corporate Vehicles table so that we can save those
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddCorporateVehiclesTable.php');
//Add new move types (max 3 & max 4; maybe more later)
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_NewMoveTypes.php');
//Make move type mandatory for leads
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaMoveTypeMandatory.php');
//This does something. What? Who knows. Someone sloppy-pasta'd their comment and never changed it.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LocalTariffAdminOnly.php');
//Add lock military fields for sirva movestar opportunities
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Lock_Military_fields.php');
//Modify Sirva fields for military fields
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Modify_Sirva_Military_Fields.php');
//Hot fix to add tables for coordinators
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaCoordinatorTables.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Set_Smf_Default.php');
//Add ade_lead_id and acm_salesperson_id to Leads for catching data for LMP
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddAdeLeadId.php');
//Create OPList module and associated tables
require_once('one-off scripts/master-scripts/Create_OpList.php');
//Add some tables for getting rating line items to spit out in webservices
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddTableForRetrieveLineItems.php');
//Add a placeholder field for STS Vehicle info
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_STS_Vehicle_Field.php');
//Add estimate type to estimates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Estimate_type_field.php');
//hotfix for new sirva stop type column
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaStopTypes.php');
//Hot fix to configure SIRVA specific lead conversion settings TO add dest and origin phone 1/2
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaLeadConversion.addPhone.php');
//Hot fix to remove Fulfillment Date's mandatory flag:
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_ChangeFulfillment_date_to_nonmandatory.php');
//Add the nessecary stuff for moving from Pricing Color to Demand Color and Pricing Level
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_PricingLevelChanges.php');
//Hot fix to change the sales stage sort order
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_ChangeSalesStage.php');
//Hot fix to update the opportunity disposition and opportunity detail disposition
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaFixOppDisposition.php');
//Hot fix to add disposition lost block, field, picklist
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_OppDispositionLost.php');
//Updates the zip fields to be non integer dependent
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_RemoveIntegerOnlyZip.php');
//Adds options to the dwelling type options
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddOptionsDwellingType.php');
//Adds required to leads phone fields
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LeadsPhoneFieldRequired.php');
//Removes Converted from field from view
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_HideConvertedFromField.php');
// Makes the lead type required in the ui
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LeadTypeRequired.php');
//Adds all Countries to the origin_country and destination_country fields
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddNewCountriesList.php');
//Removes the lead fulfillment date from view
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SetDisplayTypeInputFields.php');
//updates requested for Contracts module
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaContractsUpdateDateAndDisplay.php');
//add countries and counties to the estimates seems to have gotten lost in merge updates.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Country_County_Fields.php');
//Add AMC Sales Person Id field to the add users section
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_AMC_Salesperson_Id.php');
//Add Agent Manager imagename field to the Agent Info block for logos.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaAddAgentManagerLogo.php');
//Add Agent Manager SelfHaul field to the Agent Info block for logos.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaAddAgentManagerSelfHaul.php');
//update order of billing APN display in contracts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_CorrectContractBillingOrder.php');
//Add in self haul to opportunities.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_Add_Opp_SelfHaul.php');
//Create Local Carrier module
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_CreateLocalCarrierInformation.php');
//Add Local Carrier to Estimates.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaEstimatesAddLocalCarrier.php');
//Add Lead sources module
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_CreateLeadSourceManager.php');
//Add Lead sources to Leads
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaLeadAddLeadSourceManager.php');
//because I didn't set agentid as a summary field or program term as text area. need to do that...
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_fixLeadSourceManagerAgentID_ProgTerm.php');
//Removes the extra SIT Fuel surcharges from the estimates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_RemoveSITFuelSurcharges.php');
//Add the 400NG tariff and update the picklist for tariffmanager.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaCorrectlyAdd400NG_Tariff.php');
//update accounts and contracts to have APN and to update nat_acct_no to uitype 10;
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaAddAPNToAccounts.php');
//add contract only checkboxes to limit tariffs
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaAddContractFlagsToTariffMan.php');
//Updates 400N/104G Rating URL
//require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva400NG_Tariff_URL.php');
// Removed this due to the rating urls having prod and qa urls
//require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaIntra400NTariffUrl.php');
//Add sts fields
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_STS_User_Fields.php');
//update database columns for the fuel surcharges.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_UpdateFuelSurchageDBCol.php');
//updates rating_url in tariff manager table with the correct rating engine url
//Add special services options to estimates and OT pack/unpack for fullpack and update OT load/unload to checkboxes.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaAddEstimateFields.php');
require_once('one-off scripts/master-scripts/Hotfixes/HotFix_Sirva_ImporterModule.php');
//Correct TPG GRR override to allow for a larger decimaled number.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaUpdateGRROverride.php');
//Add activity types to the calendar activity picklist
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddActivityTypeCalendarOptions.php');
//Change label for Sub-contracts to Sub-Agreements apparently it's databased not vtranslated.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaChangeSubContracts.php');
//Add the sts field Reg number - varchar 30 - CAMIS_REG_NBR - 5/4/2016
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_STSRegNumber.php');
// Remove max 3 and max 4 and Alaska and Hawaii move types
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_RemoveMoveTypes.php');
//Add the Per Cu Ft Row to the rate_types 5/5/2016
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaCuFt.php');
//Add auto spot quote
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Sirva_Auto_Spot_Quote.php');

//Make orders field visible in detail view
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Opp_Order_Visible_Detail.php');
//Add the International Quotes
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaIntlQuote.php');
//Updated the Opportunities so that the destination fields are required
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaOppsReqBlockMods.php');
//Updated the Opportunities so that the Leads fields are required
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaLeadsFixReq.php');
//Add ext for sirva
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_Phone_Field_Mod.php');
//Hot fix to add/modify SIRVA specific fields for opps
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaOppsFieldsMod_v2.php');
//Add CHG payment option
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_CHG_Payment.php');
//GRR Estimate maker
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_GRR_Field.php');
//Add missint NAT option
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_NAT_Payment_Type.php');
//Update the participant agent type list - and remove rows with the old value
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaUpdatePartipantDropDown.php');
//Turn account countries into a dropdown
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaAccounts_CountryFix.php');
//Remove mobile phone
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Remove_Mobile.php');
//Add Billing APN to Estimates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddBillingAPNToEstimates.php');
//New dates block for estimates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Date_Block_Estimates.php');
//Remove the data for the options that are no longer needed.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaUpdatePicklist.php');
//Several UI changes
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_Field_Modifications.php');
//Add primary phone type as a convert lead field
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Convert_Lead_Phone_Type.php');
//Move the TruckLoad
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaMoveTruckLoad.php');
//Adds primary phone type and secondary phone type to accounts and reorders the inputs
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_Accounts_AddPhoneTypeFields.php');
//Adds SIT Items to the Tariff Manager
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_LocalTariff_Add_SIT_Items.php');
//Hides the Weight and is_primary for extra stops
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_Hide_Stops_Fields.php');
//we need this because the Hotfix_Sirva_Accounts_AddPhoneTypeFields did not always run, so I made this IO
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_Accounts_Validate_Order.php');
//turns country picklist fields in opps/leads back into text fields that can handle the google autofill
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaCountryPicksRevertToText.php');
//add CBS contact too STS in Opps
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaOppsAddCBSContact.php');
//adds lead conversion field mapping for contact address fields
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaLeadsAddContactAddressMapping.php');
//
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaGRRCWTCurrencyField.php');
//adds location types to dropdown for extra stops
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Location_Type_DropDowns.php');
//adds primary estimate flag
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Primary_Estimate_Fields.php');
//adds a column to vtiger_packing_items to support containers
Vtiger_Utils::AddColumn('vtiger_packing_items', 'pack_cont_qty', 'INT(10)');
// Reorder Leads date block
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Leads_Reorder_Script.php');
// Add Send Email button to Opportunities Module
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Email_To_Opportunity_Summary.php');
// Add Local Weight to local move details on estimate module
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Local_Weight.php');
// Add Move Coordinator to the user set up screen.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Move_Coordinator_ID.php');
// Add Day Certain Pickup for estimates screen.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Day_Certain_Pickup.php');
// Move the fuel surcharge field
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Move_Fuel_Surcharge.php');
// Fix sequence numbers for the Accessorial Details block.
require_once('one-off scripts/master-scripts/Hotfixes/EstimatesReorderScript_20160915_203316.php');
// Set tariff services fields block numbers to zero.
//REMOVING THIS HOTFIX BECAUSE IT'S AN INSANELY STUPID WAY TO FIX THE ISSUE OF EXTRA FIELDS APPEARING
//require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Set_Tariff_Block_Numbers_Zero.php');
//ADDING A HOTFIX TO REVERT CHANGES FROM PREVIOUS HOTFIX THAT HAS BEEN COMMENTED OUT ABOVE
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Reset_TariffServices_Block_Numbers.php');
// Add Move Coordinator NAVL to the user set up screen.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Move_Coordinator_ID_NAVL.php');

// Create Custom field "Type" on Comments module
require_once('one-off scripts/master-scripts/sirva/CreateCommentType.php');
// Create custom field "Record ID"
require_once('one-off scripts/master-scripts/sirva/CreateCustomRecordIdFields.php');
// Add New column for packing items tables
require_once('one-off scripts/master-scripts/sirva/AddNewColumnForPackingItems.php');
// Create custom field "OA/DA Coordinator" on Users module
require_once('one-off scripts/master-scripts/sirva/CreateUsersOADACoordinatorField.php');
// Add rate capture field for sirva
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Rate_Capture_Field.php');
// Add missing LMP fields to the opportunity module
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Sirva_LMP_Opportunity_Fields.php');
// Update Blocks And Fields For #16625
require_once('one-off scripts/master-scripts/sirva/UpdateBlocksAndFieldsFor_16625.php');
// Update Create Address Segments module #16625
require_once('one-off scripts/master-scripts/sirva/AddressSegments16625.php');
// Change the identifier for lead source manager
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Lead_Source_Identifier.php');
// Add appointment type
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Appointment_Type_Sirva.php');
// Sync name of vtiger_tariffpackingitems table with PackingLabels on module
require_once('one-off scripts/master-scripts/sirva/AddNewColumnForPackingItems.php');

require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddTableForRetrieveLineItemDetails.php');
// Update Fuel Surcharge Estimates block number to the Move Details Block
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Update_Fuel_Surcharge_Block.php');
// Add table for interstate service charges
require_once('one-off scripts/master-scripts/sirva/AddServiceChargesTable.php');
// Add MC id field to users.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_User_MCID.php');
// Update AutoSpotQuote with new sirva requests.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_Auto_Spot_Quote_Updates.php');
// Added a Primary Phone entry to the Users module
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Primary_Phone_Dropdown.php');

// TFS 26904 - allow custom crate rate
require_once ('one-off scripts/master-scripts/sirva/AddCustomCrateRateColumn.php');

// add in Auto Spot Quotes to the services for it to be a line item.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_sirva_add_AutoSpotService.php');
// add in extra services based on the available list from Rating.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_sirva_add_missingServicesForLineItems.php');
// Update Estimates Total field to have two decimals.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_sirva_update_Estimates_TotalField.php');

//Adding sts response field
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Sirva_Auto_Spot_Quote_STS.php');
Vtiger_Utils::ExecuteQuery("DELETE FROM `vtiger_move_type` WHERE move_type = 'Local US'");
// Moves Space Reservation and Exclusive Vehicle Use to Special Services
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Move_Estimates_Special_Services.php');
// Changes custom packing rates to decimals instead of integers
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Change_Custom_Pack_Rates_Types.php');
// Add autos only tariff info
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Sirva_Auto_Spot_Quote_Tariff.php');
// Add customer LMP sections
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Opportunities_UserSection.php');
// Add a local estimate type
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Local_Estimate_type_field.php');
// OT 17479 -- estimates refactor
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_EstimatesRefactor.php');
//Add AMC Sales Person Id field to the add users section (dual brand)
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_AMC_Salesperson_Id_NVL.php');
// Add dual branded logins for STS
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_STS_User_Fields_Dual.php');
// Add a key to estimate_id on vtiger_rating_line_item_details
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Line_Item_Detail_Key.php');
//Sirva importer table
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_Importer_CreateTable.php');

// Add express truckload tariff
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Sirva_Express_Truckload_Tariff.php');
// Add Local Origin Acc field.
require_once('one-off scripts/master-scripts/sirva/AddLocalOriginAccField.php');
// Hide Order Id from Quick Create by setting presence to 1.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_HideOrderId.php');
// Change Express Truckload to Truckload Express.
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Rename_ExpressTruckload_TruckloadExpress.php');

//3429: National -  Lead Module Changes because it wasn't here and it's breaking everything.
require_once('one-off scripts/master-scripts/movehq/NationalLeadModuleChanges_3429.php');

// Add Move Type to sub agreements because ¯\_(ツ)_/¯
require('one-off scripts/master-scripts/sirva/Add_MoveType_To_Contracts.php');
// Update Opportunities fields layout
require_once('one-off scripts/master-scripts/sirva/Sirva_UI_Hotfixes.php');
// Reorder Survey Appointments
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Reorder_Survey_Appointments.php');
// hide use current rates checkbox
require_once ('one-off scripts/master-scripts/sirva/RemoveUseCurrentRates28423.php');

// Update Local Estimate Type options
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Rename_Local_Estimate_Type.php');

require_once('one-off scripts/master-scripts/sirva/Add_LeadType_To_Opportunities.php');

// Update Max3
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Change_Max3_Service_Types.php');
// update opp filters
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddFilters_to_Opportunities.php');
// fix modcomments type
require_once ('one-off scripts/master-scripts/sirva/FixModCommentTypes26898.php');

// transfer line items to new table
require_once ('one-off scripts/master-scripts/sirva/TransferLineItems.php');
// reorder MAX tariff sections
require_once ('one-off scripts/master-scripts/sirva/ReorderMAXTariffSections.php');

// Just hide the flag please
require_once('one-off scripts/master-scripts/sirva/TruckloadExpress_FlagToPresence1.php');

// hide apparently useless fields
require_once ('one-off scripts/master-scripts/sirva/RemoveApparentlyUselessFields28720.php');
// Add local mileage field
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddMilesFieldToLocalMoveDetails.php');

// Re-order fields for sts
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Order_STS_Fields.php');
//TFS28929 -- update contracts national account number field: Agreements nat_account_no
require_once ('one-off scripts/master-scripts/Hotfix_Sirva_Contracts_NationalAcctNumber.php');

//TFS28859 -- move_type on Contracts (Agreements) should not be mandatory.
require_once ('one-off scripts/master-scripts/sirva/sirva_Contracts_MoveType_NotMandatory.php');
// update opp filters
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Update_Cus_Ref_Types.php');

// Update Max3
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Remove_Dup_Conform_Fields.php');

//TFS22828 - Change billing APN UI type and update existing values
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_Billing_APN_Changed.php');

// Get rid of extra OP Lists link in Opp sidebar.
require_once('one-off scripts/master-scripts/sirva/RemoveRedundantOPListEntry.php');

// Adding Effective Date/Effective Tariff to Surveys
require_once('one-off scripts/master-scripts/sirva/Add_EffectiveDate_Tariff_to_Surveys.php');
// Add Vehicles block to Opps.
require_once('one-off scripts/master-scripts/AddOppVehiclesBlock.php');
// Re-order fields
require_once('one-off scripts/master-scripts/Hotfixes/OpportunitiesReorderScript_20170323_132456.php');

// Removing duplicate disposition field
require_once('one-off scripts/master-scripts/sirva/RemoveDuplicateOpportunityDispositionField.php');

// TFS 29332 - update convert lead mapping
require_once ('one-off scripts/master-scripts/sirva/UpdateLeadConvert29332.php');
//TFS29630 -- Correct disposition_lost_reasons field for both lead and opportunities.
require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_OppDispositionLostPicklistUpdate.php');

// Add booked option for quotes
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Booked_Quotes.php');

// Fix the keys on the Sirva Calendar Metadata table
require_once ('one-off scripts/master-scripts/sirva/Hotfix_Fix_Exchange_Meta_Key.php');

// Fixing something that should've ever existed. (Sirva Military => Military)
require_once('one-off scripts/master-scripts/sirva/FixOppBusinessLine.php');

// Add STS salesperson fields
require_once('one-off scripts/master-scripts/Hotfixes/20170522_151210_users_add_sts-sales-fields.php');
// ¯\_( ͡° ͜ʖ ͡°)_/¯ Got to add a MIN=1 to this field ¯\_( ͡° ͜ʖ ͡°)_/¯
require_once('one-off scripts/master-scripts/sirva/TariffSection_SortOrder_MinimumOfOne.php');
// Hiding a picklist that doesn't do anything good and just breaks stuff
require_once('one-off scripts/master-scripts/sirva/Hide_BulkyChargePer.php');
// Zero out default bulky list because why not.
require_once('one-off scripts/master-scripts/sirva/Zero_DefaultBulkyList.php');
// TFS30230 - Add Express Pickup fields.
require_once('one-off scripts/master-scripts/sirva/20170515_154048_estimates_add_extra-pickup-fields.php');
// TFS30313 - Update Accounts National Account Number to be mandatory.
require_once('one-off scripts/master-scripts/sirva/20170602_125339_Accounts_update_apn.php');
// Add list of values for marketing channel
require_once('one-off scripts/master-scripts/Hotfixes/20170523_132737_lead-source_modify_marketing-channel.php');
// TFS30022 - Adding a google identifier to Surveys
require_once('one-off scripts/master-scripts/sirva/20170612_171035_surveys_add_field-google_apt_id.php');
// TFS31011 - Update registration date UItype
require_once('one-off scripts/master-scripts/sirva/20170706_144120_update_opportunity_registration-date.php');
// TFS30748
require_once('one-off scripts/master-scripts/sirva/20170630_182220_estimates_update_extra-labor-hour-fields.php');
// TFS30929 - Adding survey complete option
require_once('one-off scripts/master-scripts/sirva/20170628_133125_opportunity_update_sales-stage.php');

// TFS30748: Change labor hour fields.
require_once('one-off scripts/master-scripts/sirva/20170705_195320_estimates_update_acc_exlabor_hours.php');

// TFS30923: Hide opp_type and replace it with lead_type.
require_once('one-off scripts/master-scripts/sirva/20170622_173539_Opportunities_hide-update_lead_type-opp_type.php');

// TFS31198: Remove unnecessary fields from quick create.
require_once('one-off scripts/master-scripts/sirva/20170720_193207_estimates_hide_unnecessary-quick-create-fields.php');

// Remove unused languages to match device
require_once('one-off scripts/master-scripts/sirva/20170724_1450_update_opportunity_language-field-update.php');

// TFS29908: Making agentid optional because come on.
require_once('one-off scripts/master-scripts/sirva/20170724_145319_tariffreportsections_update_agentid.php');

// TFS31878
require_once('one-off scripts/master-scripts/sirva/20170901_151910_surveys_hide_virtual-option.php');
// TFS31497: Reorder contact fields on quick create.
require_once('one-off scripts/master-scripts/sirva/20170822_154339_contacts_update_quick-create-ordering.php');

// TFS32066: Add MIL and GVT.
require_once('one-off scripts/master-scripts/sirva/20170920_152232_estimates_update_shipper_type-picklist-values.php');

// TFS32297: Make Origin/Destination cities optional.
require_once('one-off scripts/master-scripts/sirva/20171031_180244_leads_opportunities_update_city_fields.php');
// TFS32259 -  opportunity filter - no results for booker name or booker city
require_once('one-off scripts/master-scripts/sirva/20171106_212547_opportunities_update_agent-type.php');
// TFS32515: Fix Program Name field not converting from Lead to Opportunity
require_once('one-off scripts/master-scripts/sirva/20171107_150111_leads_update_mapping-source_name.php');
// TFS32590: QIO2 select 'sources' and 'access denied' is returned (Clell/Hall mvg)
require_once('one-off scripts/master-scripts/sirva/20171109_163650_leads_update_marketing-channel.php');
// TFS32282: Added Contact Email to Opportunities
require_once('one-off scripts/master-scripts/sirva/20171113_112001_opportunities_add_contact-email.php');
// TFS32557: Add agentid to AutoSpotQuotes and map Estimate owner agents over to the AutoSpotQuote.
require_once('one-off scripts/master-scripts/sirva/20171127_154256_autospotquotes_add_agentid.php');
// TFS32792 - Changing number of decimal places in "Fuel Surcharge Lookup Table"
require_once('one-off scripts/master-scripts/sirva/20171207_094330_contracts_update_fields-FuelTableToCost-FuelTableFromCost-FuelTableRate.php');

echo "<br><h1> Completed Initilization_Sirva!</h1><br>";
