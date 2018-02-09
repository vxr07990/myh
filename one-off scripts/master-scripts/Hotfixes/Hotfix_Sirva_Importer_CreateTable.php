<?php

if (function_exists("call_ms_function_ver")) {
    $version = 5;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$table_name = 'vtiger_sirvaimporter_log';
$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "' . $table_name . '" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    $db->pquery("ALTER TABLE $table_name ADD `emailsent` VARCHAR(3)  NULL  DEFAULT NULL  AFTER `time`");

    echo $table_name . ' already exists' . PHP_EOL;
} else {
    $stmt = 'CREATE TABLE `' . $table_name . '` 
            (`' . $table_name . '_id` INT(11) NOT NULL AUTO_INCREMENT, 
            `file` VARCHAR(250), `last_imported_line` int(11), `file_import_ended` int(1), `time` datetime,
            PRIMARY KEY (' . $table_name . '_id))';
    $db->pquery($stmt);
}


$table_name = 'vtiger_sirvaimporter_ids';
$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "' . $table_name . '" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    $db->pquery("ALTER TABLE $table_name ADD `qualifiedlead` VARCHAR(30)  NULL  DEFAULT NULL  AFTER `importid`");

    echo $table_name . ' already exists' . PHP_EOL;
}


//sirva_importer_extrastops table

Vtiger_Utils::ExecuteQuery("CREATE TABLE IF NOT EXISTS `sirva_importer_extrastops` (
  `importer_dataid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unqualified_lead` varchar(8) DEFAULT NULL,
  `stop_name` varchar(16) DEFAULT NULL,
  `contact_name` varchar(15) DEFAULT NULL,
  `address_line1` varchar(21) DEFAULT NULL,
  `address_line2` varchar(16) DEFAULT NULL,
  `city` varchar(11) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip_code` int(11) DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `mobile_phone` varchar(4) DEFAULT NULL,
  `home_phone` varchar(4) DEFAULT NULL,
  `work_phone` varchar(4) DEFAULT NULL,
  `parsed` int(11) DEFAULT '0',
  `imported` int(11) DEFAULT '0',
  `import_result` varchar(250) DEFAULT NULL,
  `modified_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`importer_dataid`),
  KEY `Unqualified_Lead` (`unqualified_lead`,`parsed`,`imported`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;");

//sirva_importer_leadactivity table

Vtiger_Utils::ExecuteQuery("CREATE TABLE IF NOT EXISTS `sirva_importer_leadactivity` (
  `importer_dataid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `qualified_lead` varchar(8) DEFAULT NULL,
  `google_sync_flag` varchar(1) DEFAULT NULL,
  `description` varchar(46) DEFAULT NULL,
  `assigned_to` varchar(11) DEFAULT NULL,
  `last_name` varchar(17) DEFAULT NULL,
  `location` varchar(78) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `display_in` varchar(23) DEFAULT NULL,
  `duration` varchar(10) DEFAULT NULL,
  `status` varchar(11) DEFAULT NULL,
  `comments` varchar(214) DEFAULT NULL,
  `parsed` int(11) DEFAULT '0',
  `imported` int(11) DEFAULT '0',
  `import_result` varchar(250) DEFAULT NULL,
  `modified_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`importer_dataid`)
) ENGINE=InnoDB AUTO_INCREMENT=631 DEFAULT CHARSET=utf8;");

// sirva_importer_leadnotes table

Vtiger_Utils::ExecuteQuery("CREATE TABLE IF NOT EXISTS `sirva_importer_leadnotes` (
  `importer_dataid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `unqualified_lead` varchar(8) DEFAULT NULL,
  `created_by` varchar(11) DEFAULT NULL,
  `note_create_datetime` varchar(7) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `source` varchar(26) DEFAULT NULL,
  `provider` varchar(21) DEFAULT NULL,
  `lmp_note_id` varchar(8) DEFAULT NULL,
  `parsed` int(11) DEFAULT '0',
  `imported` int(11) DEFAULT '0',
  `import_result` varchar(250) DEFAULT NULL,
  `modified_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`importer_dataid`),
  KEY `parsed` (`parsed`,`imported`)
) ENGINE=InnoDB AUTO_INCREMENT=1491 DEFAULT CHARSET=utf8;");

//sirva_importer_leaddata Table


Vtiger_Utils::ExecuteQuery("CREATE TABLE IF NOT EXISTS `sirva_importer_leaddata` (
  `importer_dataid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unqualified_lead` varchar(8) DEFAULT NULL,
  `qualified_lead` varchar(8) DEFAULT NULL,
  `lead_last_updated_by` varchar(11) DEFAULT NULL,
  `lead_created_by` varchar(6) DEFAULT NULL,
  `lmp_lead_id` varchar(7) DEFAULT NULL,
  `mosys_id` varchar(4) DEFAULT NULL,
  `assign_date_time` datetime DEFAULT NULL,
  `lmp_lead_created` varchar(23) DEFAULT NULL,
  `test` varchar(1) DEFAULT NULL,
  `uq_status` varchar(9) DEFAULT NULL,
  `uq_disposition` varchar(22) DEFAULT NULL,
  `business_channel` varchar(8) DEFAULT NULL,
  `move_type` varchar(13) DEFAULT NULL,
  `primary_booker_name` varchar(29) DEFAULT NULL,
  `bkr_code` int(11) DEFAULT NULL,
  `booker_brand` varchar(3) DEFAULT NULL,
  `ca_agency_code` varchar(4) DEFAULT NULL,
  `primary_sales_team_position_name` varchar(20) DEFAULT NULL,
  `primary_position_primary_employee_login` varchar(11) DEFAULT NULL,
  `last_name` varchar(12) DEFAULT NULL,
  `first_name` varchar(21) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `fax` varchar(4) DEFAULT NULL,
  `cell_phone` varchar(10) DEFAULT NULL,
  `work_phone` varchar(10) DEFAULT NULL,
  `home_phone` varchar(10) DEFAULT NULL,
  `language` varchar(7) DEFAULT NULL,
  `primary_phone_type` varchar(10) DEFAULT NULL,
  `preferred_time` varchar(4) DEFAULT NULL,
  `time_zone` varchar(4) DEFAULT NULL,
  `work_phone_ext` varchar(4) DEFAULT NULL,
  `date_flexible` varchar(1) DEFAULT NULL,
  `orig_addr_1` varchar(42) DEFAULT NULL,
  `orig_addr_2` varchar(5) DEFAULT NULL,
  `orig_city` varchar(12) DEFAULT NULL,
  `orig_ctry` varchar(2) DEFAULT NULL,
  `orig_st_prov` varchar(2) DEFAULT NULL,
  `orig_zip_postal` int(11) DEFAULT NULL,
  `dest_addr_1` varchar(22) DEFAULT NULL,
  `dest_addr_2` varchar(4) DEFAULT NULL,
  `dest_city` varchar(13) DEFAULT NULL,
  `dest_ctry` varchar(2) DEFAULT NULL,
  `lead_type` varchar(8) DEFAULT NULL,
  `dest_st_prov` varchar(2) DEFAULT NULL,
  `dest_zip_postal` int(11) DEFAULT NULL,
  `dwelling_type` varchar(15) DEFAULT NULL,
  `comments` varchar(30) DEFAULT NULL,
  `promotion_code` varchar(9) DEFAULT NULL,
  `employer_company_name` varchar(4) DEFAULT NULL,
  `employer_contact_email` varchar(4) DEFAULT NULL,
  `employer_contact_name` varchar(4) DEFAULT NULL,
  `employer_contact_phone` varchar(4) DEFAULT NULL,
  `employer_assisting` varchar(1) DEFAULT NULL,
  `expc_dlvr_date` varchar(4) DEFAULT NULL,
  `fulfillment_date` varchar(4) DEFAULT NULL,
  `funded` varchar(8) DEFAULT NULL,
  `furnish_level` varchar(6) DEFAULT NULL,
  `lead_receive_date` varchar(7) DEFAULT NULL,
  `lead_source` varchar(4) DEFAULT NULL,
  `mktg_channel` varchar(21) DEFAULT NULL,
  `moving_a_vehicle` varchar(1) DEFAULT NULL,
  `no_of_vehicles` varchar(4) DEFAULT NULL,
  `offer_valuation` varchar(1) DEFAULT NULL,
  `office_and_industrial` varchar(1) DEFAULT NULL,
  `out_of_area` varchar(1) DEFAULT NULL,
  `out_of_origin` varchar(1) DEFAULT NULL,
  `out_of_time` varchar(1) DEFAULT NULL,
  `own_current` varchar(4) DEFAULT NULL,
  `own_new` varchar(4) DEFAULT NULL,
  `program_name` varchar(19) DEFAULT NULL,
  `program_terms` varchar(363) DEFAULT NULL,
  `promotion_terms` varchar(4) DEFAULT NULL,
  `req_move_date` varchar(7) DEFAULT NULL,
  `small_move` varchar(1) DEFAULT NULL,
  `source_name` varchar(18) DEFAULT NULL,
  `special_items` varchar(18) DEFAULT NULL,
  `make_s` varchar(4) DEFAULT NULL,
  `model_s` varchar(4) DEFAULT NULL,
  `year_s` varchar(4) DEFAULT NULL,
  `pack_load_haul` varchar(4) DEFAULT NULL,
  `other_program_name` varchar(4) DEFAULT NULL,
  `phone_estimate` varchar(4) DEFAULT NULL,
  `nc` varchar(1) DEFAULT NULL,
  `aa` varchar(1) DEFAULT NULL,
  `lead_source_2012` varchar(4) DEFAULT NULL,
  `program_name_2012` varchar(4) DEFAULT NULL,
  `persona_title` varchar(4) DEFAULT NULL,
  `persona_description` varchar(4) DEFAULT NULL,
  `paper_work_owner` varchar(4) DEFAULT NULL,
  `mobile_sync` varchar(4) DEFAULT NULL,
  `mobile_sync_status` varchar(9) DEFAULT NULL,
  `status` varchar(7) DEFAULT NULL,
  `canadian_govt_move` varchar(4) DEFAULT NULL,
  `mosys_interface_message` varchar(4) DEFAULT NULL,
  `mobile_interface_message` varchar(19) DEFAULT NULL,
  `canadian_govt_registered_date` varchar(4) DEFAULT NULL,
  `appt` varchar(4) DEFAULT NULL,
  `lead_disposition` varchar(17) DEFAULT NULL,
  `detail_disposition` varchar(20) DEFAULT NULL,
  `order_number` varchar(6) DEFAULT NULL,
  `closed_in_home` varchar(4) DEFAULT NULL,
  `booked_date` varchar(7) DEFAULT NULL,
  `move_coordinator_last_name` varchar(4) DEFAULT NULL,
  `move_coordinator_first_name` varchar(4) DEFAULT NULL,
  `move_coordinator_work_phone` varchar(4) DEFAULT NULL,
  `move_coordinator_fax` varchar(4) DEFAULT NULL,
  `move_coordinator_cell_phone` varchar(4) DEFAULT NULL,
  `move_coordinator_email_addr` varchar(4) DEFAULT NULL,
  `parsed` int(11) DEFAULT '0',
  `imported` int(11) DEFAULT '0',
  `import_result` varchar(250) DEFAULT NULL,
   `modified_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`importer_dataid`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;");


$tableNames = array(
    'sirva_importer_leaddata',
    'sirva_importer_leadnotes',
    'sirva_importer_leadactivity',
    'sirva_importer_extrastops'
);

foreach ($tableNames as $table_name) {

    $stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "' . $table_name . '" LIMIT 1';
    $res = $db->pquery($stmt);
    if ($db->num_rows($res) > 0) {
	$db->pquery("ALTER TABLE $table_name ADD `modified_time` TIMESTAMP  NULL  ON UPDATE CURRENT_TIMESTAMP  AFTER `import_result`;");

	echo $table_name . ' already exists' . PHP_EOL;
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
