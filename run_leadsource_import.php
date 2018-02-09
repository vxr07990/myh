<?php

require_once('includes/main/WebUI.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');
require_once('go_live/sirva/agentManagerImport.php');
require_once('go_live/sirva/leadSourceManagerImport.php');


echo "Start Lead Source Import <br>";

$db = PearDatabase::getInstance();

//Agencies
//agentImport();

//lead source import
leadSourceImport();
