<?php
//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');


require_once('one-off scripts/master-scripts/windfall/20170501_000101_wflocations_create_module.php');
require_once('one-off scripts/master-scripts/windfall/20170501_000102_wfslotconfiguration_create_module.php');
require_once('one-off scripts/master-scripts/windfall/20170501_000103_wflocationhistory_create_module.php');
require_once('one-off scripts/master-scripts/windfall/20170501_000104_wfconfiguration_create_module.php');
require_once('one-off scripts/master-scripts/windfall/20170501_000105_wfarticles_create_module.php');
require_once('one-off scripts/master-scripts/windfall/20170501_000106_wfcostcenters_create_module.php');
require_once('one-off scripts/master-scripts/windfall/20170501_000107_wflocationtypes_create_module.php');
require_once('one-off scripts/master-scripts/windfall/20170501_000108_wfstatus_create_module.php');
require_once('one-off scripts/master-scripts/windfall/20170501_000109_wfconditions_create_module.php');
require_once('one-off scripts/master-scripts/windfall/20170501_000110_wfinventory_create_module.php');
require_once('one-off scripts/master-scripts/windfall/20170501_000111_wfinventorylocations_create_module.php');
require_once('one-off scripts/master-scripts/windfall/20170501_000112_wfinventoryhistory_create_module.php');
require_once('one-off scripts/master-scripts/windfall/20170501_000113_wfworkorders_create_module.php');
require_once('one-off scripts/master-scripts/windfall/20170501_000114_wflineitems_create_module.php');
require_once('one-off scripts/master-scripts/windfall/20170501_000115_wflocationtags_create_module.php');
require_once('one-off scripts/master-scripts/windfall/20170501_000116_wftransactions_create_module.php');
require_once('one-off scripts/master-scripts/windfall/20170501_000117_wfsynccenters_create_module.php');
require_once('one-off scripts/master-scripts/windfall/20170501_000118_wflocationorders_create_module.php');

// Creating the WFLocations filters
require_once('one-off scripts/master-scripts/windfall/20170510_105000_wflocations_create_initial-filters.php');

// OT3566 - Create WFSlotConfiguration Module and Fields
require_once('one-off scripts/master-scripts/windfall/20170501_000102_wfslotconfiguration_create_module.php');
// OT3600 - Create WFLocations Module and Fields and Filters
require_once('one-off scripts/master-scripts/windfall/20170501_000101_wflocations_create_module.php');
// OT3993 - Create WFWarehouse Module and Fields
require_once('one-off scripts/master-scripts/windfall/20170510_104800_wfwarehouse_create_initial.php');

// OT3577 - Create WFLocationHistory Module and Fields
// require_once('one-off scripts/master-scripts/windfall/20170501_000104_wfconfiguration_create_module.php');

// OT4472 - Create LocationTypes Module and Fields
// require_once('one-off scripts/master-scripts/windfall/20170510_104900_locationtypes_create_initial.php');

// OT4691 - Create WFCarriers Module and Fields
require_once('one-off scripts/master-scripts/windfall/20170510_105100_wfcarriers_create_initial.php');
// OT4454 - Create WFAccounts Module and Fields
require_once('one-off scripts/master-scripts/windfall/20170510_105200_wfaccounts_create_initial.php');
// OT4707 - Add related list from WFWarehouses to WFAccounts and vice versa
require_once('one-off scripts/master-scripts/windfall/20170510_105300_wfwarehouses-wfaccounts_create_related-list.php');
// OT4530 - Create WFWorkOrders Module and Fields
require_once('one-off scripts/master-scripts/windfall/20170510_105400_wfworkorders_create_initial.php');
// OT4459 - Create CostCenters Module that will be tied to Accounts & Inventory.
require_once('one-off scripts/master-scripts/windfall/20170510_105500_wfcostcenters_create_initial.php');
// OT4527 - Create WFOrders Module and Fields
require_once('one-off scripts/master-scripts/windfall/20170510_105600_wforders_create_initial.php');
// OT4460 - Create Locations Module and Fields
require_once('one-off scripts/master-scripts/windfall/20170510_105700_wflocations_update_fields-and-blocks.php');
// OT3992 - Create table of default entries for WFLocationTypes
require_once('one-off scripts/master-scripts/windfall/20170516_190218_wflocationtypes_add_default-rows.php');

// OT4066 - Add default slot configurations table
require_once('one-off scripts/master-scripts/windfall/20170518_205435_wfslotconfiguration_add_create-default-records.php');
// OT3566 - Adding the agentid field to SlotConfiguration
require_once('one-off scripts/master-scripts/windfall/20170511_131000_wfslotconfiguration_add_agentid.php');
// OT3600 - Reorder the blocks in Locations
require_once('one-off scripts/master-scripts/windfall/20170511_144000_wflocations_update_location-blocks.php');
// OT3600 - Make Name field mandatory in Locations
require_once('one-off scripts/master-scripts/windfall/20170511_152900_wflocations_update_name-to-mandatory.php');
// OT4066 - Add Record Information Block to WFSlotconfiguration
require_once('one-off scripts/master-scripts/windfall/20170530_165155_wfslotconfiguration_add_record-information-block.php');
// OT 4875 - Add Assigned To Field to all Windfall modules
require_once('one-off scripts/master-scripts/windfall/20170531_153337_all_add_assigned-to-field.php');
// OT4874 - Hiding Agent field in WFLocations
require_once('one-off scripts/master-scripts/windfall/20170601_162042_wflocations_hide_agent.php');
// OT4737 - Hide 'Keep Active' field in WFAccounts
require_once('one-off scripts/master-scripts/windfall/20170602_132447_wfaccounts_hide_field-keep_active.php');
// OT4740 - Update Account Type picklist
require_once('one-off scripts/master-scripts/windfall/20170609_122500_wfaccounts_update_picklist-wfaccounts_type.php');
// OT4741 - Add Customer Number field to Accounts
require_once('one-off scripts/master-scripts/windfall/20170609_143900_wfaccounts_add_field-customer_number.php');
// OT4895 - Hide active check box
require_once('one-off scripts/master-scripts/windfall/20170609_1528_wflocations_hide_field-active.php');
// OT4897 - Remove Company field in WFAccounts
require_once('one-off scripts/master-scripts/windfall/20170609_154300_wfaccounts_hide_field-company.php');
// OT4897 - Add Status field to WFLocations
require_once('one-off scripts/master-scripts/windfall/20170609_165400_wflocations_add_field-wflocations_status.php');

// OT4738 - Add Account Status Field in WFAccounts
require_once('one-off scripts/master-scripts/windfall/20170602_143728_wfaccounts_add_field-account_status.php');
// OT3599 - Reorder fields for All filter in WFSlotconfiguration
require_once('one-off scripts/master-scripts/windfall/20170602_165559_wfslotconfiguration_update_all-filter.php');
// OT4459 - Add the WFCostCenters Code field
require_once('one-off scripts/master-scripts/windfall/20170609_092700_wfcostcenters_add_field-code.php');
// OT4536
require_once('one-off scripts/master-scripts/windfall/20170612_123900_wfworkorders_add_field-wfcostcenter.php');
//4558	 Inventory Management - InventoryHistory Module
require_once('one-off scripts/master-scripts/windfall/20170531_171800_wfinventoryhistory_add_fields.php');

// OT4922 - Hide Details block in WFWorkOrders
require_once('one-off scripts/master-scripts/windfall/20170614_125500_wfworkorders_hide_fields-wfaccount-comment.php');
// OT4925 - Add minimum value limit to weight field
require_once('one-off scripts/master-scripts/windfall/20170614_171800_wfworkorders_update_field-wfworkorder_weight-set-minimum-value.php');
// OT4924 - Update picklist for wfworkorders_priority
require_once('one-off scripts/master-scripts/windfall/20170614_114400_wfworkorders_update_picklist-workorder_priority.php');
// OT4754 - Make Location Type Defaults non-editable
require_once('one-off scripts/master-scripts/windfall/20170612_101900_wflocationtypes_add_field-is_default.php');
// OT4531 - Add WFWorkOrders RelatedList To WFAccounts
require_once('one-off scripts/master-scripts/windfall/20170602_164000_wfaccounts_relate_wfworkorders.php');
// OT4009 - Add status field to WFWarehouse
require_once('one-off scripts/master-scripts/windfall/20170626_120700_wfwarehouses_add_field-wfwarehouses_status.php');
// OT4788 - Add License Levels picklist
require_once('one-off scripts/master-scripts/windfall/20170615_104100_wfwarehouses_add_field-license_level.php');
// OT4944 - Make fields mandatory in WFCostCenter
require_once('one-off scripts/master-scripts/windfall/20170621_104500_wfcostcenters_update_fields-code-accounts-to-mandatory.php');
// OT18877 - Update the entity identifier
require_once('one-off scripts/master-scripts/windfall/20170628_152600_wfworkorders_add_entity-identifier.php');
// OT18877 - Add columns to the grid view
require_once('one-off scripts/master-scripts/windfall/20170629_151400_wfworkorders_add_fields-to-list-view.php');
// OT4457 - Add WFAddress module
require_once('one-off scripts/master-scripts/windfall/20170525_183802_wfaddress_create_module.php');
// OT5024 - Add fields to WFArticles
require_once('one-off scripts/master-scripts/windfall/20170703_121200_wfarticles_create_fields-for-wfarticles.php');
// OT5034 - WFAccounts module layout update
require_once('one-off scripts/master-scripts/windfall/20170706_101600_wfaccounts_fields_reorder.php');
// OT5034 - WFAddress module field hiding and reorder
require_once('one-off scripts/master-scripts/windfall/20170710_102000_wfaddresses_update_reorder_fields.php');
// OT4952 - Number fields set minimum to zero
require_once('one-off scripts/master-scripts/windfall/20170622_091000_wflocations-wforders_update-fields_add-minimum-value-to-number-fields.php');
// OT4984 - Hide Box Label field in WFCostCenters
require_once('one-off scripts/master-scripts/windfall/20170628_163300_wfcostcenters_hide_field-boxlabelnumber.php');
// OT4995 - Add zero as minimum value for square footage
require_once('one-off scripts/master-scripts/windfall/20170629_101400_wfwarehouses_update_field-minimum-value-for-square-footage.php');
// OT4985 - Remove percentused and percentusedoverride
require_once('one-off scripts/master-scripts/windfall/20170627_103400_wflocations_delete_fields-percentused-percentusedoverride.php');
// OT5070 - Inventory Status module creation
require_once('one-off scripts/master-scripts/windfall/20170814_103400_wfstatus_create_initial.php');
// OT5196 - Default Rows for Inventory Status
require_once('one-off scripts/master-scripts/windfall/20170814_164800_wfstatus_add_default-rows.php');
// OT5196 - Default records for Inventory Status
require_once('one-off scripts/master-scripts/windfall/20170815_090600_wfstatus_add_default-records.php');
// OT4988 - Location Types - Remove Base Location check box
require_once('one-off scripts/master-scripts/windfall/20170630_115000_wflocationtypes_hide_fixed.php');
// OT4947 - Change to all filter in Cost Centers
require_once ('one-off scripts/master-scripts/windfall/20170703_102500_wfcostcenters_update_all-filter.php');
// OT4998 - Updating default filter for Location Types
require_once ('one-off scripts/master-scripts/windfall/20170703_120500_wflocationtypes_update_filter-default-list-view.php');
// OT4526 - Add field to wfwarehouses
require_once('one-off scripts/master-scripts/windfall/20170703_154200_wfwarehouses_add_field-translation.php');
// OT4997 - Change Agent field to picklist in WFWarehouses
require_once('one-off scripts/master-scripts/windfall/20170705_092300_wfwarehouses_update_field-agent.php');
// OT5016 - Adding fields for creating multiple locations, and reordering all the fields per the mockup
require_once('one-off scripts/master-scripts/windfall/20170705_171000_wflocations_add_fields-for-create-multiples.php');
require_once('one-off scripts/master-scripts/windfall/20170705_171100_wflocations_update_fields-reorder.php');
// OT5005 - Accounts Related list
require_once('one-off scripts/master-scripts/windfall/20170706_124300_wfaccounts_update_related-modules-list.php');
// OT5097 - Remove Owner and CreatedTime from Grid
require_once('one-off scripts/master-scripts/windfall/20170724_103400_wflocationtypes_update_filter-remove-create-and-owner.php');
// OT5098 - Redo the WFWarehouses Filters
require_once('one-off scripts/master-scripts/windfall/20170724_105900_wfwarehouses_create_active-and-inactive-warehouse-filters.php');
// OT19064 - WFArticles adding in missed fields// OT5000 - Updating related list for WFWarehouses
require_once('one-off scripts/master-scripts/windfall/20170725_114800_wfwarehouses_update_related-list.php');
// OT5001 - Adding Related Lists to Slot Configuration
require_once ('one-off scripts/master-scripts/windfall/20170725_124300_wfslotconfiguration_add_related-list.php');

// OT19064 - WFArticles adding in missed fields
// OT5002 - Added related list items to wflocationtypes
require_once('one-off scripts/master-scripts/windfall/20170725_141400_wflocationtypes_add_related-list.php');
// OT5003 - Added related lists to WFLocations
require_once ('one-off scripts/master-scripts/windfall/20170725_151500_wflocations_update_related-lists.php');
// OT5107 - Adding ModComments to WFCostCenters
require_once('one-off scripts/master-scripts/windfall/20170727_154100_wfcostcenters_add-modcomments-widget.php');

require_once('one-off scripts/master-scripts/windfall/20170727_155600_wfarticles_create_field-manufacturer-part-number_account.php');
require_once('one-off scripts/master-scripts/windfall/20170728_103000_wfarticles_update_field-types.php');
// OT19069 - Update field names to allow addresses to save
require_once('one-off scripts/master-scripts/windfall/20170728_145000_wfaddress_add_fields-street_address-secondary_address.php');
// OT5143 - Creating fields for Item Conditions
require_once('one-off scripts/master-scripts/windfall/20170728_102500_wfconditions_add_fields.php');

// OT19049 - Setting entity identifier and hiding an unused column
require_once('one-off scripts/master-scripts/windfall/20170801_125000_wfarticles_update_entity-identifier.php');
require_once('one-off scripts/master-scripts/windfall/20170801_130300_wfarticles_remove_field-account.php');
// OT19094 - Remove field and update field order
require_once('one-off scripts/master-scripts/windfall/20170802_092000_wfwarehouses_hide_field-agent.php');
// OT5220 - Remove WH field from location types
require_once('one-off scripts/master-scripts/windfall/20170816_152302_wflocationtypes_update_warehouse-field.php');
// OT5225 - Make warehouse field manditory
require_once('one-off scripts/master-scripts/windfall/20170816_150432_wflocations_update_warehouse-field.php');
// OT5206 - Set up pre-populated list
require_once('one-off scripts/master-scripts/windfall/20170731_144100_wfconditions_add_default-rows.php');
// OT5106 - Add Add Comment to Actions menu in WFConditions
require_once('one-off scripts/master-scripts/windfall/20170801_111400_wfconditions_update_related-list.php');
// OT5125 - Changes to Locations List view
require_once('one-off scripts/master-scripts/windfall/20170802_115300_wflocations_create_active-and-inactive-location-filters.php');
// OT4521 - Rewrite the Location History module
require_once('one-off scripts/master-scripts/windfall/20170810_111200_wflocationhistory_rewrite_module.php');
// OT4568 - Creating the WFActivityCodes module
require_once('one-off scripts/master-scripts/windfall/20170724_151100_wfactivitycodes_create_module.php');
// OT5067 - Adding Vault Capacity field and reordering
require_once('one-off scripts/master-scripts/windfall/20170818_114000_wflocations_add_field-vault_capacity.php');
require_once('one-off scripts/master-scripts/windfall/20170705_171100_wflocations_update_fields-reorder.php');

// OT4935 - Setting the WFActivityCodes Entity Identifier
require_once('one-off scripts/master-scripts/windfall/20170830_100500_wfactivitycodes_add_entity-identifier.php');
// OT4935 - Creating the default records
require_once('one-off scripts/master-scripts/windfall/20170830_100600_wfactivitycodes_add_create-default-records.php');
// OT5203 - WFOrders Warehouse Status Field
require_once('one-off scripts/master-scripts/windfall/20171004_163900_wforders_add_field-warehouse_status.php');
// OT5332 OT5333 - Add business line and commodities fields to WFOrders
require_once('one-off scripts/master-scripts/windfall/20171005_090100_wforders_add_fields-commodities-business_line.php');
// OT5203 - Add load date field to WFOrders
require_once('one-off scripts/master-scripts/windfall/20171005_172600_wforders_add_field-load_date.php');
// OT5203 - Add Contact field to WFOrders
require_once('one-off scripts/master-scripts/windfall/20171009_153800_wforders_add_field-contact.php');
// OT5203 - Hide Storage Type and Consignee fields
require_once('one-off scripts/master-scripts/windfall/20171009_165800_wforders_hide_fields-wforder_storagetype-consignee.php');
// OT5094 - Add Weight Date field to WFOrders
require_once('one-off scripts/master-scripts/windfall/20171018_123200_wforders_add_field-weight_date.php');
// OT5203 - Reorder fields for WFOrders
require_once('one-off scripts/master-scripts/windfall/20171009_143100_wforders_reorder_fields.php');
// OT5180 - Add filters for list view WFOrders
require_once('one-off scripts/master-scripts/windfall/20171010_111100_wforders_create_filters.php');
// OT5175 - Hide is_default field in WFLocationTypes
require_once('one-off scripts/master-scripts/windfall/20170816_121300_wflocations_hide_field-is_default.php');
// OT5141 - WFInventory Field creation
require_once('one-off scripts/master-scripts/windfall/20170814_115900_wfinventory_create_all-fields.php');
// OT5165 - WFInventory List View All filter
require_once('one-off scripts/master-scripts/windfall/20170818_153200_wfinventory_create_list-view-filters.php');
// OT5223 - Moving comments field
require_once('one-off scripts/master-scripts/windfall/20170824_101400_wforders_update_comments-field-and-block.php');

// OT5009 - Updated related lists
require_once('one-off scripts/master-scripts/windfall/20170824_150300_wforders_add_related-list.php');
// OT5009 - Adding related modules for WFArticles
require_once('one-off scripts/master-scripts/windfall/20170824_152700_wfarticles_add-related-lists.php');
// OT5218 - Update default value for WFArticles status field
require_once ('one-off scripts/master-scripts/windfall/20170825_170300_wfarticles_update_field-article_status.php');
// OT5185 - Adding Articles Filters
require_once('one-off scripts/master-scripts/windfall/20170825_095800_wfarticles_create_filters.php');
// OT5071 - WFStatus filter creation
require_once('one-off scripts/master-scripts/windfall/20170908_084600_wfstatus_create_filter.php');
// OT5159 - WFInventory make tag color not mandatory
require_once('one-off scripts/master-scripts/windfall/20170908_090300_wfinventory_update_field-tag_color.php');
// OT5365 - WFSlotconfiguration default record additions
require_once('one-off scripts/master-scripts/windfall/20170816_110200_wflocationtypes_add_default-records.php');
// OT5162 - Fixing WFArticles filter
require_once('one-off scripts/master-scripts/windfall/20170908_134400_wfarticles_fix_filters.php');
// OT5160 - WFInventory make costcenter not mandatory
require_once('one-off scripts/master-scripts/windfall/20170908_150900_wfinventory_update_field-costcenter_remove-mandatory.php');
// OT5157 - WFInventory Tie the Order field to the WFOrders module for now. I'm sorry for this.
require_once('one-off scripts/master-scripts/windfall/20170908_154700_wfinventory_update_fields-order.php');
// OT5167 - OT5170 - WFInventory - Add in user defined fields
require_once('one-off scripts/master-scripts/windfall/20170915_094100_wfinventory_create_fields_user-defined-fields.php');
// OT5165 - Fixing the WFInventory Filter Name
require_once('one-off scripts/master-scripts/windfall/20170920_120300_wfinventory_update_filter-name.php');
// OT5109 - WFInventory Add Comments to related lists
require_once ('one-off scripts/master-scripts/windfall/20170913_095200_wfinventory_add_related-list_comments.php');
// OT5354 Cost Centers: Modify Layout and Field Name
require_once('one-off scripts/master-scripts/windfall/20170913_193634_wfcostcenters_rearrange_fields.php');
// OT5358 - Swap two fields on WFConditions
require_once('one-off scripts/master-scripts/windfall/20170906_112200_wfconditions_update_fields-reorder-agent-and-owner.php');
// OT5222 - Adding notes field and block to WFAccounts
require_once('one-off scripts/master-scripts/windfall/20170905_164200_wfaccounts_add_notes_field_and_blocks.php');
// OT5335 - Accounts - Default fields
require_once('one-off scripts/master-scripts/windfall/20170830_185259_wfaccounts_update_fields-default-values.php');
// OT5221 - Update to field sequence for WFLocationTypes
require_once('one-off scripts/master-scripts/windfall/20170905_151100_wflocationtypes_reorder_fields.php');
// OT5226 - Location field reorder
require_once('one-off scripts/master-scripts/windfall/20170906_114300_wflocations_reorder_fields.php');
// OT5110 - Adding Add Comment to WFInventoryHistory
require_once('one-off scripts/master-scripts/windfall/20170907_113900_wfinventoryhistory_add-modcomments-widget.php');
// OT5355 - Removing a redundant assigned to field
require_once('one-off scripts/master-scripts/windfall/20170912_112400_wflocations_update_field_hide-smownerid.php');
// OT5313 - WFImages creating module
require_once('one-off scripts/master-scripts/windfall/20170912_165900_wfimages_create_module.php');
// OT19344 - WFActivityCodes reorder fields
require_once('one-off scripts/master-scripts/windfall/20170921_112900_wfconditions_reorder_fields.php');
// OT18952 - Correct table for existing field licence_levels
require_once('one-off scripts/master-scripts/windfall/20170926_122100_wfwarehouses_update_field-license_level.php');
// OT19335 - Adding is_default field to WFSlotConfiguration
require_once('one-off scripts/master-scripts/windfall/20170926_170400_wfslotconfiguration_add_field-is_default.php');
// OT19335 - Default rows for WFSlotConfiguration
require_once('one-off scripts/master-scripts/windfall/20170816_110300_wfslotconfiguration_add_default-records.php');
// Updating is_default field type for WFConditions
require_once('one-off scripts/master-scripts/windfall/20170927_112300_wfconditions_update_field-is_default.php');
// Adding default rows for WFConditions
require_once('one-off scripts/master-scripts/windfall/20170927_151100_wfconditions_add_default-records.php');
// Adding default rows for WFLocationTypes
require_once('one-off scripts/master-scripts/windfall/20170816_110200_wflocationtypes_add_default-records.php');
// Removing old default rows for multiple modules
require_once('one-off scripts/master-scripts/windfall/20171002_171300_multiple_modules_remove_default-records.php');
// OT5359 - Remove is_default field from Item Conditions listview default filter
require_once('one-off scripts/master-scripts/windfall/20170831_113500_wfconditions_remove_protection-from-filter.php');
// OT5170 - Updating step of UDF numerical fields
require_once('one-off scripts/master-scripts/windfall/20171023_112100_wfinventory_update_fields-UDF11-UDF12-UDF13.php');
// OT19551 - Update WFStatus Default Records
require_once('one-off scripts/master-scripts/windfall/20171024_190800_wfstatus_update_default-records.php');
// OT19551 - Set smownerid to 0 for the defaults
require_once('one-off scripts/master-scripts/windfall/20171026_082500_wfstatus_update_smownerid.php');
// OT5623 - Create Activity Fields to map input fields for an activity code.
require_once ('one-off scripts/master-scripts/windfall/20171024_145630_wfactivityfields_create_module.php');
// OT5623 - add activity fields as a related module to the activity codes
require_once ('one-off scripts/master-scripts/windfall/20171024_173130_wfactivitycodes_add_related-list-wfactivityfields.php');
// Should run after OT5623 (Activity Field module) is built
// OT5624 - Create activity field mapping guest module.
require_once ('one-off scripts/master-scripts/windfall/20171023_130930_wfactivityfieldmaps_create_module.php');
// OT5624 - Set activity field mapping module as a guest to the activity field module.
require_once ('one-off scripts/master-scripts/windfall/20171023_142930_wfactivityfields_add_guest_module-wfactivityfieldmaps.php');
// @NOTE: Didn't need this but it does exist:
// OT5624 - Set activity field mapping module as a RELATED LIST MODULE to the activity field module.
//require_once ('one-off scripts/master-scripts/windfall/20171024_173530_wfactivityfields_add_related-list-wfactivityfieldmaps.php');
// OT5625 - Create WFActivityFieldRules module to hold the activity code field rules
require_once ('one-off scripts/master-scripts/windfall/20171024_233230_wfactivityfieldrules_create_module.php');
// OT5625 - Link the Activity Field Rules to the Activity Fields
//require_once ('one-off scripts/master-scripts/windfall/20171024_234230_wfactivityfields_add_related-list-wfactivityfieldrules.php');
// OT5625 - Link the Activity Field Rules to the Activity Fields (through guest blocks)
require_once ('one-off scripts/master-scripts/windfall/20171024_234434_wfactivityfields_add_guest_module-wfactivityfieldrules.php');
// OT5590 - Add fields to the WFTransactions module
require_once ('one-off scripts/master-scripts/windfall/20171025_082632_wftransactions_create_module.php');
// OT5590 - Remove OLD fields in the WFTransactions module
require_once ('one-off scripts/master-scripts/windfall/20171025_125832_wftransactions_hide_fields.php');
// OT5590 - move assigned_user_id field to the right block in the WFTransactions module
require_once ('one-off scripts/master-scripts/windfall/20171025_125832_wftransactions_move_fields-assigned_user_id.php');
// OT5187 - WFInventoryLocations add fields
require_once('one-off scripts/master-scripts/windfall/20171024_115000_wfinventorylocations_add_all_fields.php');
// OT19601 - Make fields non-mandatory in WFInventory
require_once ('one-off scripts/master-scripts/windfall/20171026_171600_wfinventory_update_fields_make_non-mandatory.php');

// OT5491 - Create WFLineItems to be used as a guest module
require_once('one-off scripts/master-scripts/windfall/20171011_150000_wflineitems_create_module.php');
// OT5841 - Create the WFOperationsTasks module and filters
require_once('one-off scripts/master-scripts/windfall/20171012_084700_wfoperationstasks_create_module.php');

require_once('one-off scripts/master-scripts/windfall/20171027_145100_wfoperationstasks_update_number-sequencing.php');
require_once('one-off scripts/master-scripts/windfall/20171027_150200_wfoperationstask_update_field-sequence.php');
require_once('one-off scripts/master-scripts/windfall/20171027_151700_wfoperationstasks_update_fields-tag_color-warehouse_notes.php');
require_once('one-off scripts/master-scripts/windfall/20171027_154700_wfoperationstasks_add_missed-cost-center.php');
require_once('one-off scripts/master-scripts/windfall/20171027_164600_wfoperationstasks_update_filter_names.php');
// fix for number sequnece db table.
require_once('one-off scripts/master-scripts/windfall/20171027_173333_wfoperationstasks_update_number-sequencing_table-field.php');

require_once('one-off scripts/master-scripts/windfall/20171030_140800_wfoperationstasks_add_modcomments.php');

// fix : making field non-mandatory in Inventory Location
require_once('one-off scripts/master-scripts/windfall/20171030_171200_wfinventorylocations_update_field-warehouse_task-non-mandatory.php');
// OT19620 - Accounts - Related Modules Not Showing All Related Records
require_once ('one-off scripts/master-scripts/windfall/20171030_166340_wfaccounts_update_related_list_setup.php');
// OT5095 - WFWeightHistory module creation
require_once ('one-off scripts/master-scripts/windfall/20171019_123400_wfweighthistory_create_module.php');
// OT5095 - Adding reference to WFOrders to WFWeightHistory
require_once ('one-off scripts/master-scripts/windfall/20171024_083400_wfweighthistory_add_field-related_order.php');
// OT5164 - WFInventoryHistory module rewrite
require_once ('one-off scripts/master-scripts/windfall/20171106_092500_wfinventoryhistory_rewrite_module.php');
// OT4548 - Inventory - Warehouse Conditions guest block
require_once('one-off scripts/master-scripts/windfall/20171027_111600_wfwarehousecond_create_module.php');
// OT4548 - Warehouse Conditions - populating picklist values
require_once('one-off scripts/master-scripts/windfall/20171031_144100_wfwarehousecond_update_fields_picklist_values.php');
// OT4546 - Driver Conditions - create module
require_once('one-off scripts/master-scripts/windfall/20171112_164600_wfdrivercond_create_module.php');
// OT4525 - WFConfigurations set up module for actual use
require_once('one-off scripts/master-scripts/windfall/20171031_105700_wfconfigurations_create_module.php');
// PPDOT5012 - Add documents related record
require_once('one-off scripts/master-scripts/windfall/20171110_094400_wfoperationstasks_add_documents-related-list.php');
// OT4525 - WFConfigurations add a bunch of configuration fields
require_once('one-off scripts/master-scripts/windfall/20171113_091300_wfconfiguration_create_configurable-fields.php');
// OT4525 - WFConfigurations add EVEN more configuration fields
require_once('one-off scripts/master-scripts/windfall/20171116_104930_wfconfiguration_create_configurable-rest-of-fields.php');
// OT5008 - WFOrders add WFWeightHistory related module
require_once('one-off scripts/master-scripts/windfall/20171110_092500_wforders_add_related-list_wfweighthistory.php');
// OT5008 - WFOrders remove extra Documents related list
require_once('one-off scripts/master-scripts/windfall/20171116_091600_wforders_update_related_list_remove_extra_documents.php');
// OT4525 - WFConfiguration reorder blocks.
require_once('one-off scripts/master-scripts/windfall/20171121_082630_wfconfiguration_reorder_blocks.php');
// OT4525 = WFConfiguration update records to default
require_once('one-off scripts/master-scripts/windfall/20171121_1101200_wfconfiguration_update_existing-records-to-default.php');

