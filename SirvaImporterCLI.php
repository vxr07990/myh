<?php

/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */

require_once 'include/Webservices/Relation.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'includes/main/WebUI.php';
require_once 'vendor/autoload.php';
require_once 'config/database.php';
require_once 'modules/SirvaImporter/actions/ImportStep2.php';

global $root_directory;

define('WORKING_DIRECTORY', $root_directory);


function sirvaimporter_shutdown()
{
            chdir(WORKING_DIRECTORY);
            $file = 'logs/SirvaImporter.log';

            file_put_contents($file, '\r\n', FILE_APPEND);
	    file_put_contents($file, 'IMPORTING ERROR:', FILE_APPEND);
	    file_put_contents($file, 'FILE INFO: ' . print_r($_FILES, true), FILE_APPEND);
            file_put_contents($file, 'ERROR INFO: ' . serialize(error_get_last()), FILE_APPEND);
}

register_shutdown_function ('sirvaimporter_shutdown');



error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
//go ahead be awesome!
{

    //
    // IMPORTANT NOTE: IF IMPORTING LEADS, YOU MUST ADD A COLUMN AT THE END OF THE DOCUMENT
    // CALLED 'AgencyCode' WITH THE AGENCY CODE OF THE LEADS!
    // THIS SHOULD NOT CAUSE ANY ISSUES SINCE EVERYTHING WILL BE IMPORTED ON A
    // PER AGENCY BASIS ANYWAYS!
    //
    
    /**
     	*** Notes on building the database ***
   	
	Please add a new row in Excel / Libreoffice on top of the header row with this function

		=LOWER(SUBSTITUTE(TRIM(SUBSTITUTE(SUBSTITUTE(A2,"#",""),"/","_"))," ","_"))

	That will make the excel columns match to the database columns names and make easy the import
	
	Also in Lead Data sheet there is a column called UQ Status that is duplicated please remove the duplicated
	column before importing the CSV into the database

	Tables names are:

		* sirva_importer_extrastops
		* sirva_importer_leadactivity
		* sirva_importer_leaddata
		* sirva_importer_leadnotes
      
     */
    
    
    $importedLinesPerBatch = 1001; //How many lines will import in each run (cli only setting)
    
    $emailNotifications = ['jstout@igcsoftware.com'];

    $inputFiles = [
	[
	    'agency_code' => '0383002',
	    'agency_name' => 'DunMar Moving Systems',
	    'type' => 'Lead Data',
	    'agency_brand' => 'AVL',
	    'version' => '1'
	],
	[
	    'agency_code' => '0383002',
	    'agency_name' => 'DunMar Moving Systems',
	    'type' => 'Lead Notes',
	    'version' => '1'  //Increase this to keep track of multiple import process for same agency
	],
	/*
	  '/var/www/public/Scrum_9/go_live/sirva/imports/ExtraStopsExtract.csv'  => [
	  'agency_code' => '0383002',
	  'agency_name' => 'DunMar Moving Systems',
	  'type'        => 'Extra Stops Data',
	  'version'   => '1'
	  ], */
	[
	    'agency_code' => '0383002',
	    'agency_name' => 'DunMar Moving Systems',
	    'type' => 'Qualified Lead Activity',
	    'version' => '1'
	],
	    /*
	      '/var/www/public/sirva/go_live/sirva/historical/imports/userSpreadsheet.csv'  => [
	      'agency_code' => '',
	      'agency_name' => '',
	      'type'        => 'Users',
	      ],
	     */
    ];


    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());

    if (empty($inputFiles)) {
	//failed no input file.
	print "Error: No input files to load \n";
	exit();
    }
    foreach ($inputFiles as $data) {

	if (!isset($data['type'])) {
	    //failed no type.
	    print "Error: No type : (" . $data['type'] . ") \n";
	    exit();
	}

	if(getCount($data['type']) == 0){
	    sirvaImporter_sendNotificationEmail($emailNotifications, $data);
	    continue;
	}

	print "START! Importing file: $inputFile \n";
	print "START! Importing Data Type: " . $data['type'] . "\n";

	unset($_REQUEST);
	$_REQUEST = [
	    'module' => 'SirvaImporter',
	    'action' => 'ImportStep2',
	    'next' => 'Next',
	    'module1' => $data['type'],
	    'emailnotifications' => $emailNotifications,
	    'max_imports_lines'=>$importedLinesPerBatch
	];

	if (isset($data['agency_brand']) && $data['agency_brand'] != '') {
	    $_REQUEST['agency_brand'] = $data['agency_brand'];
	}


	if ($data['type'] == 'Users') {
	    $importer = new SirvaImporter_ImportStep2_Action;
	    $importer->process(new Vtiger_Request($_REQUEST, $_REQUEST));
	} elseif (preg_match('/^[0-9]+$/', $data['agency_code'])) {
	    if ($agentID = getAgentManagerId($data['agency_code'])) {
		$_REQUEST['agent_id'] = $agentID;
		$importer = new SirvaImporter_ImportStep2_Action;
		$importer->process(new Vtiger_Request($_REQUEST, $_REQUEST));
	    } else {
		print "Error: failed to find agentmanagerid for ($agency_code)\n";
	    }
	} else {
	    print "Error: in input data (agency code): " . print_r($data) . " for ($agency_code)\n";
	}


	print "DONE! Importing file: $inputFile \n";
	print "DONE! Importing Data Type:  " . $data['type'] . "\n";
    }
}

function getAgentManagerId($agency_code) {
    $agentID = false;
    $db = PearDatabase::getInstance();

    $stmt = 'SELECT * FROM `vtiger_agentmanager` WHERE `agency_code`=? LIMIT 1';
    $result = $db->pquery($stmt, [$agency_code]);
    if ($result) {
	$row = $result->fetchRow();
	$agentID = $row['agentmanagerid'];
    } else {
	//failed to find agency
    }

    return $agentID;
}


function getCount($dataType){
    	switch ($dataType) {
	    case 'Lead Data':
		$sql = "SELECT importer_dataid as pending FROM sirva_importer_leaddata WHERE parsed=0 OR parsed IS NULL";
		
		break;
	    
	    case 'Lead Notes':
		$sql = "SELECT importer_dataid as pending FROM sirva_importer_leadnotes WHERE parsed=0 OR parsed IS NULL";
		
		break;
	    
	    case 'Qualified Lead Activity':
		$sql = "SELECT importer_dataid as pending FROM sirva_importer_leadactivity WHERE parsed=0 OR parsed IS NULL";
		
		break;
	    
	    case 'Extra Stops Data':
		$sql = "SELECT importer_dataid as pending FROM sirva_importer_extrastops WHERE parsed=0 OR parsed IS NULL";
		
		break;

	    default:
		return [];
		break;
	}
	
	$db = PearDatabase::getInstance();
	$result = $db->pquery($sql);
	
	$count =  $db->num_rows($result);
	
	return $count;
	
}


function sirvaImporter_sendNotificationEmail($toArray, $data){
	
	$db = PearDatabase::getInstance();
	
	$fileName = $data['type'] . '_' . $data['agency_code'] . '_' . $data['version']; //Just in case they keep importing the same data
	
	$result = $db->pquery('SELECT emailsent FROM vtiger_sirvaimporter_log SET WHERE file=?', [$fileName]);
	if($result && $db->num_rows($result) > 0 ){
	    if($db->query_result($result, 0, 'emailsent') == 1){
		//We already sent this email. No need to do it again
		return;
	    } 
	} 
	
	
	require_once 'vtlib/Vtiger/Mailer.php';

	$mailer = new Vtiger_Mailer();
	$mailer->IsHTML(true);
	$mailer->Subject = vtranslate('LBL_MAIL_SUBJECT', 'SirvaImporter');
	
	$description = vtranslate('LBL_MAIL_CONTENTS', 'SirvaImporter');
	
	if($_FILES['userfile']['is_last_file'] == 1){
	    $description .= '<br><br>' . vtranslate('LBL_MAIL_CONTENTS_ALL_FILES', 'SirvaImporter');
	} else {
	    $description .= '<br><br>' . vtranslate('LBL_MAIL_CONTENTS_FILE', 'SirvaImporter') . ' Data Type: ' . $data['type'] . ' Agency: ' . $data['agency_code'] . ' Version: ' . $data['version'];
	}
	
	$description .= '<br><br>' . vtranslate('LBL_MAIL_CONTENTS_SALUTATION', 'SirvaImporter');

	$mailer->Body = $description;
	
	foreach ($toArray as $to) {
	    $mailer->AddAddress($to);
	}
	
	//CC the admin - Just in case
	//$accountOwnerId = Users::getActiveAdminId();
	//$mailer->AddCC(getUserEmail($accountOwnerId), getUserFullName($accountOwnerId));

	//Send the email
	$status = $mailer->Send(true);
	
	if($status){
	    $db = PearDatabase::getInstance();
	    $db->pquery('INSERT INTO vtiger_sirvaimporter_log ( emailsent, file) VALUES (?,?) ', [1, $fileName]);
	}

    }
