<?php

require_once('includes/main/WebUI.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');
require_once('go_live/email_all_users_change_password.php');
require_once('go_live/sirva/vanlineManagerImport.php');
require_once('go_live/sirva/agentManagerImport.php');
require_once('go_live/sirva/userImport.php');
require_once('go_live/sirva/leadSourceManagerImport.php');
require_once('go_live/sirva/Contract_Importer.php');

echo "Start Agent Manager Import <br>";

//alright fine leave this off so it shows doing stff.
//error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

$db = PearDatabase::getInstance();

//Vanlines
//vanlineImport();

//Agencies
//agentImport();

//Users
//userImport();

//lead source import
//leadSourceImport();


//Contract import
contractImport();

//Reset all passwords
//emailAllUsers();
//require_once('go_live/sirva/Generate_SirvaTariffs.php');
;
