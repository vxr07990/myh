<?php
/**
 *
 *	MasterScript will call scripts in (one-off scripts/master-scripts) and will create all modules of move CRM and move HQ in the database.
 *	Master Script is built to be run with a clean Source Code.
 *
 *	This file has been updated to create a new database if it does not alrady exist, using the database name in the .env file.
 *
 **/
ini_set('display_errors', 'on');


$Vtiger_Utils_Log = true;

require_once 'vtlib/Vtiger/Menu.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'modules/ModTracker/ModTracker.php';
require_once 'modules/ModComments/ModComments.php';
require_once 'includes/main/WebUI.php';
require_once 'include/Webservices/Create.php';
require_once 'modules/Users/Users.php';
require_once 'vendor/autoload.php';

$db = &PearDatabase::getInstance();

if (getenv('PHP_ENV') === 'dev') {
	ini_set('display_errors', 'on');

	$whoops = new \Whoops\Run;
	$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
	$whoops->register();
}

global $scriptVersionsToUpdate, $masterScriptVersionTable;
$scriptVersionsToUpdate = [];
$masterScriptVersionTable = 'master_script_versions';

function ensureScriptVersionTable($tableName) {
    if (!$tableName) {
        return false;
    }

    $db = &PearDatabase::getInstance();
    // check to make sure script version table exists
    $stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "'.$tableName.'" LIMIT 1';
    $res = $db->pquery($stmt);
    if ($db->num_rows($res) > 0) {
        // already exists
    } else {
        $stmt = 'CREATE TABLE `'.$tableName.'` (
                `filename` VARCHAR (250) NOT NULL,
                `run` INT (11) NOT NULL,
                PRIMARY KEY(filename)
                )';
        $db->pquery($stmt);
    }
    updateScriptVersionTable($tableName);
    return true;
}

function updateScriptVersionTable($tableName) {
    if (!$tableName) {
        return false;
    }

    $db = &PearDatabase::getInstance();
    $newColumns = [
        'run_count' => 'INT(11) NOT NULL DEFAULT 1',
        'initial_run_time' => 'TIMESTAMP DEFAULT 0',
        'last_check_time' => 'TIMESTAMP DEFAULT 0',
        'last_run_time' => 'TIMESTAMP DEFAULT 0'
    ];

    foreach ($newColumns as $columnName => $columnType) {
        if (!columnExists($columnName, $tableName)) {
            $stmt = 'ALTER TABLE '.$tableName.' ADD COLUMN '.$columnName.' '. $columnType;
            $db->query($stmt);

            //default just the run_count and not the times, since we don't know them
            if ($columnName == 'run_count') {
                $stmt = 'UPDATE '.$tableName.' SET run_count=1';
                $db->query($stmt);
            }
        }
    }
}

function call_ms_function_ver($fileName, $version)
{
    global $scriptVersionsToUpdate, $masterScriptVersionTable;
    //$masterScriptVersionTable here is unfortunate
    $tableName = $masterScriptVersionTable;

    if (!$tableName) {
        return false;
    }

    $fileName = standardizeFilenameForVersionLogs($fileName);

    if ($version == 'AlwaysRun') {
        // insert version 1 into the table, so that we can disable the script from always running later, without it having to run one last time
        $scriptVersionsToUpdate[$fileName] = '1';
        return false;
    }
    $db = &PearDatabase::getInstance();
    $res = $db->pquery('SELECT run FROM `'.$tableName.'` WHERE `filename`=?', [$fileName]);
    if ($res && ($row = $res->fetchRow())) {
        if ($version > $row['run']) {
            // higher version to run
            $scriptVersionsToUpdate[$fileName] = $version;
            return false;
        }
        $stmt = 'UPDATE `'.$tableName.'` SET last_check_time = CURRENT_TIMESTAMP() WHERE `filename`=?';
        $db->pquery($stmt, [$fileName]);
        return true;
    }
    // row doesn't exist in table
    $scriptVersionsToUpdate[$fileName] = $version;
    return false;
}

function standardizeFilenameForVersionLogs($fileName) {
    $fileName = str_replace(getcwd(), '', $fileName);
    $fileName = preg_replace('/\\\\/i','/',$fileName);

    return $fileName;
}

function removeScriptFromVersionLogs($fileName) {
    global $scriptVersionsToUpdate;
    $fileName = standardizeFilenameForVersionLogs($fileName);
    unset($scriptVersionsToUpdate[$fileName]);
    return true;
}

function updateVersionRunLog($tableName) {
    global $scriptVersionsToUpdate;

    if (!$tableName) {
        return false;
    }

    $db = &PearDatabase::getInstance();
    foreach ($scriptVersionsToUpdate as $fileName => $version) {
        $insertStmt = 'INSERT INTO `'.$tableName.'` (filename, run, initial_run_time, last_run_time, last_check_time) VALUES (?,?,CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP())'
                      . ' ON DUPLICATE KEY UPDATE run=?,run_count=(run_count+1),last_run_time=CURRENT_TIMESTAMP()';
        $db->pquery($insertStmt, [$fileName, $version, $version]);
    }
}

function getVersionInformation() {
    $db = &PearDatabase::getInstance();
    $movehq     = getenv('IGC_MOVEHQ');
    $envDbVersion     = getenv('DB_VERSION');
    $db_version = '0.0.0';
    $sql    = "SELECT movehq, db_version FROM `database_version`";
    $result = $db->pquery($sql, []);
    if ($result != NULL) {
        $row = $result->fetchRow();
        //$movehq = $row[0];
        $db_version = $row[1];
    }

    if (
        $envDbVersion &&
        //@TODO: This is limited
        preg_match('/^\d+\.\d+\.\d+$/', $envDbVersion)
    ) {
        list ($db_version, $higherVersion) = compareVersions($db_version, $envDbVersion);
    }

    $db_version_array = explode('.', $db_version);

    foreach ($db_version_array as $index => $value) {
        $db_version_array[$index] = intval($value);
    }

    return [$movehq, $db_version, $db_version_array];
}

function compareVersions($v1, $v2) {
    $v1_array = explode('.', $v1);
    $v2_array = explode('.', $v2);

    foreach ($v1_array as $index => $value) {
        if ($value > $v2_array[$index]) {
            return [$v2,$v1];
        } elseif ($value < $v2_array[$index]) {
            break;
        }
    }
    return [$v1,$v2];
}

if ($db->database->_errorMsg != "Unknown database '" . getenv('DB_NAME')."'") {
	echo 'Database "' . getenv('DB_NAME') . '" exists.';
} else {
	echo 'Database "' . getenv('DB_NAME') . '" does not exists. Creating new database named "' . getenv('DB_NAME') . '"';

	Install_Utils_Model::checkDbConnection(function_exists('mysqli_connect')?'mysqli':'mysql', getenv('DB_SERVER'),
			getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_NAME'),
			true, true, getenv('DB_USERNAME'), getenv('DB_PASSWORD'));

	$db->connect();

	$sqlFile = file_get_contents("sql/movecrm-empty.sql");

    foreach (explode(";\n", $sqlFile) as $query) {
        $query = trim($query);

        if (!empty($query) && $query != ";") {
            Vtiger_Utils::ExecuteQuery($query);
        }
    }
}

// build the run script versions
ensureScriptVersionTable($masterScriptVersionTable);

//Just in case these files don't exist make them.
require_once('updateTabData.php');

list ($movehq, $db_version, $db_version_array) = getVersionInformation();

if ($db_version_array[0] <= 0 && $db_version_array[1] < 1 && $db_version_array[2] <= 0) {
	echo "<br><h1>UPDATING TO 0.1.0</h1><br>";
	// Scripts to add moveCRM and moveHQ modules. Scripts are created to run in this order
	require_once('one-off scripts/master-scripts/Create_Version_Table.php');
	require_once('one-off scripts/master-scripts/deleteDefaultProfilesAndGroups.php');
	//removed for new securities
	//require_once('one-off scripts/master-scripts/makeDefaultProfiles.php');
	require_once('one-off scripts/master-scripts/removeDefaultTandC.php');
	require_once('one-off scripts/master-scripts/changeDefaultEmailTemplates.php');
	require_once('one-off scripts/master-scripts/UI15to16.php');
	require_once('one-off scripts/master-scripts/vanlineGroupHotfix.php');

	Vtiger_Utils::ExecuteQuery("INSERT INTO `database_version` (`movehq`, `db_version`) VALUES ($movehq, '0.1.0')");
	$db_version = '0.1.0';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
} if ($db_version_array[0] == 0 && $db_version_array[1] == 1 && $db_version_array[2] == 0) {
	echo "<br><h1>UPDATING TO 0.2.0</h1><br>";
	require_once('one-off scripts/master-scripts/Remove_Products_from_Help_Desk.php');
	require_once('one-off scripts/master-scripts/Modify_Leads.php');
	require_once('one-off scripts/master-scripts/update_contacts.php');
	require_once('one-off scripts/master-scripts/Update_quotes_20150608.php');
	require_once('one-off scripts/master-scripts/Update_potentials_20150609.php');
	require_once('one-off scripts/master-scripts/Update_users_20150729.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_AgentId_to_CRMEntity.php'); //Fix for Conrodo's missing CRMEntity fields
	require_once('one-off scripts/master-scripts/Create_Service_Lineitems.php');
	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.2.0'");
	$db_version = '0.2.0';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
} if ($db_version_array[0] == 0 && $db_version_array[1] == 2 && $db_version_array[2] == 0) {
	echo "<br><h1>UPDATING TO 0.3.0</h1><br>";
	require_once('one-off scripts/master-scripts/Vanlines_20150514.php');
	require_once('one-off scripts/master-scripts/Agents_20150514.php');
	require_once('one-off scripts/master-scripts/VanlineManager_AgentManager_20150604.php');
	require_once('one-off scripts/master-scripts/Users2Vanline20150624.php');
	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.3.0'");
	$db_version = '0.3.0';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
} if ($db_version_array[0] == 0 && $db_version_array[1] == 3 && $db_version_array[2] == 0) {
	echo "<br><h1>UPDATING TO 0.4.0</h1><br>";
	require_once('one-off scripts/master-scripts/Create_Counties_and_States.php');
	require_once('one-off scripts/master-scripts/Tariffmanager_20150603.php');
	require_once('one-off scripts/master-scripts/LocalServices20150601.php');
	require_once('one-off scripts/master-scripts/Tariffs_20150513.php');
	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.4.0'");
	$db_version = '0.4.0';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
} if ($db_version_array[0] == 0 && $db_version_array[1] == 4 && $db_version_array[2] == 0) {
	echo "<br><h1>UPDATING TO 0.5.0</h1><br>";
	require_once('one-off scripts/master-scripts/create_estimates_module.php');
	require_once('one-off scripts/master-scripts/Estimates_EntityName.php'); //adds estimates to entity name since
																			 //decoupled modules don't do this on their own
	require_once('one-off scripts/master-scripts/Remove-is_discountable.php');
	require_once('one-off scripts/master-scripts/create_opportunities_20150609.php');
	require_once('one-off scripts/master-scripts/Opp_EntityName.php'); //adds estimates to entity name since
																	   //decoupled modules don't do this on their own
	require_once('one-off scripts/master-scripts/Stops_20150514.php');
	require_once('one-off scripts/master-scripts/Surveys_20150603.php');
	require_once('one-off scripts/master-scripts/Cubesheets_20150603.php');
	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.5.0'");
	$db_version = '0.5.0';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
} if ($db_version_array[0] == 0 && $db_version_array[1] == 5 && $db_version_array[2] == 0) {
	echo "<br><h1>UPDATING TO 0.6.0</h1><br>";
	require_once('one-off scripts/master-scripts/Employees_20150515.php');
	require_once('one-off scripts/master-scripts/TimeOff_20150605.php');
	require_once('one-off scripts/master-scripts/MoveRoles_20150605.php');
	require_once('one-off scripts/master-scripts/Vehicles_20150518.php');
	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.6.0'");
	$db_version = '0.6.0';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
} if ($db_version_array[0] == 0 && $db_version_array[1] == 6 && $db_version_array[2] == 0) {
	echo "<br><h1>UPDATING TO 0.7.0</h1><br>";
	require_once('one-off scripts/master-scripts/create_orders.php');
	require_once('one-off scripts/master-scripts/Claims_20150515.php');
	require_once('one-off scripts/master-scripts/Equipment_20150518.php');
	require_once('one-off scripts/master-scripts/Accidents_20150605.php');
	require_once('one-off scripts/master-scripts/Storage_20150605.php');
	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.7.0'");
	$db_version = '0.7.0';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
} if ($db_version_array[0] == 0 && $db_version_array[1] == 7 && $db_version_array[2] == 0) {
	echo "<br><h1>UPDATING TO 0.8.0</h1><br>";
	require_once('one-off scripts/master-scripts/Timesheets_20150605.php');
	require_once('one-off scripts/master-scripts/A_Create_Trips.php');
	require_once('one-off scripts/master-scripts/A_Create_WeeklyTimeSheets.php');
	require_once('one-off scripts/master-scripts/A_Create_ZoneAdmin.php');
	require_once('one-off scripts/master-scripts/Update_vendors.php');
	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.8.0'");
	$db_version = '0.8.0';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
	die("<h1>Database version updated to 0.8.0. Please re-run the script to continue.</h1>");
} if ($db_version_array[0] == 0 && $db_version_array[1] == 8 && $db_version_array[2] == 0) { //Hot fixes for Alpha release,run hotfixes even if they have already been run.
	echo "<br><h1>HOT FIXING TO 0.8.1</h1><br>";
	require_once('one-off scripts/master-scripts/Contracts_20150818.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_contractsAssignVanline.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_createContract2Agent.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_vanlineGroup.php');
    //hot fix for lead conversion
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LeadConversion.php');
	//hot fix for Product ID being optional for createEstimates WS
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_ProductId_Optional.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CrateId.php');
	//hot fix for Contracts fields being added to Estimates to allow for Contract Enforcement in Estimates
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddContractsFieldsToEstimates.php');
	//hot fix to add custom reports password field to agentmanager table
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_reports_AgentManager_password.php');
	//hot fix to add enforce field to `vtiger_misc_accessorials`
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_misc_accessorials_contract_enforce.php');
	//hot fix to add a missing table for cost_service_total saving.
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Quotes_ServiceCost_Table.php');
	//hotfix for sales person fields
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_salesPerson.php');
	//hotfix to add sales person field to opps filter
	//require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_OppFilter.php');
	//hot fix specifically for demo
	//require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Demo_Updates.php');
	//hot fix for decoupling the widgets from Potentials to Opportunities
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_DecouplingWidgets.php');
	//hot fix for adding the opportunity  sales stage filters
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddFilters_to_Opportunities.php');
	//hot fix for adding interstate effective date
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddEffectiveDateToInterstate.php');
	//hot fix for adding agency admin
	//removed until it's a complete thing
	//require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_createAgencyAdmin.php');
	//add the customJS field to tariff manager
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CustomJSFields.php');
	//add the PackItemId field to tariff services for default items
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddPackItemId_to_Local.php');
	//hot fix for terms & conditions to local tariffs
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddLocalReportsTermsAndConditions.php');

	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.8.1'");

	$db_version = '0.8.1';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
} if ($db_version_array[0] == 0 && $db_version_array[1] == 8 && $db_version_array[2] == 1) {
	echo "<br><h1>UPDATING TO 0.9.0</h1><br>";
	require_once('one-off scripts/master-scripts/add_modifiedtime_column_to_helpdesk_list_view.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_NewStops.php');
	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.9.0'");
	$db_version = '0.9.0';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
} if ($db_version_array[0] == 0 && $db_version_array[1] == 9 && $db_version_array[2] == 0) {
	echo "<br><h1>UPDATING TO 0.9.1</h1><br>";
	//Fix limit to column names. Set 30 to 50
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_update_column_name_limit.php');
	//UVLC estimate fields
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Additional_Estimate_Fields.php');
	//Adding vehicles to estimates
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_vehicles_to_Estimates.php');
	//Adding vehicles to estimates
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Local_Url_to_Vanlines.php');
	//adding CWT by Weight
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CWTbyWeight.php');
	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.9.1'");
	$db_version = '0.9.1';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
} if ($db_version_array[0] == 0 && $db_version_array[1] == 9 && $db_version_array[2] == 1) {
	echo "<br><h1>UPDATING TO 0.9.2</h1><br>";

	//Opportunity to Lead
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Opportunity_to_Lead_field.php');
	//Add fields to mod comments, not used by all instances but can cause errors if they aren't there
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LMPModComments.php');
	//Correct field-module relation for parent_id field in Activities
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CalendarReferenceModules.php');
	//Add custom tariff types to Tariff Manager
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_TariffType.php');
	//fix column type accessorial fuel surcharge field from estimates
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AccsFuelColumnType.php');

	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.9.2'");
	$db_version = '0.9.2';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
} if ($db_version_array[0] == 0 && $db_version_array[1] <= 10 && $db_version_array[2] <= 2) {
	echo "<br><h1>UPDATING TO 0.10.2</h1><br>";
	require_once('one-off scripts/master-scripts/add_vtiger_colorsettings_table.php');
	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.10.2'");
	$db_version = '0.10.2';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
} if ($db_version_array[0] == 0 && $db_version_array[1] == 10 && $db_version_array[2] <= 3) {
	echo "<br><h1>UPDATING TO 0.10.3</h1><br>";
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddBillingType.php');
	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.10.3'");
	$db_version = '0.10.3';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
}

if ($db_version_array[0] == 0 && $db_version_array[1] == 10 && $db_version_array[2] < 4) {
		//creating a inbox/notification system and adding a user setting for only receiving messages of a specific priority
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Notification.php');
	require_once('one-off scripts/master-scripts/Inbox_20151023.php');
	print("HERE");
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Participating_Agent.php');
	print("HERE2");
	//require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddVanlineManagerParent.php');
	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.10.4'");
	$db_version = '0.10.4';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
}

if ($db_version_array[0] == 0 && $db_version_array[1] == 10 && $db_version_array[2] <= 5) {
		//creating a inbox/notification system and adding a user setting for only receiving messages of a specific priority
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddLDDModule.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LDDEmployeeFields.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LDDOrdersFields.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LDDTrips.php');
	require_once('one-off scripts/master-scripts/Hotfixes/HotFix_Claims.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Move_Opportunity_fields.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Pricing_Type_to_Estimates.php');
	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.10.5'");
	$db_version = '0.10.5';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
}

if ($db_version_array[0] == 0 && $db_version_array[1] == 10 && $db_version_array[2] <= 6) {
	//creating a inbox/notification system and adding a user setting for only receiving messages of a specific priority
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Vehicles.php');
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_VehiclesInspections.php');
    /* [MISSING!!!] */ # require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Trips.php');
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_VehiclesMantenance.php');
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_ServiceHours.php');

	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.10.6'");
	$db_version = '0.10.6';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
}

if ($db_version_array[0] == 0 && $db_version_array[1] == 10 && $db_version_array[2] <= 7) {
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_IsParentFieldDuplicatePurge.php');
	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.10.7'");
	$db_version = '0.10.7';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
}

if ($db_version_array[0] == 0 && $db_version_array[1] == 10 && $db_version_array[2] <= 8) {
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Move_Opportunity_fields.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Pricing_Type_to_Estimates.php');
	//Fix for google address autofill not liking textareas anymore
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GoogleAddressFix.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_RemoveCalendarTypes.php');
	//Adding summary fields to AgentManager so Participating Agents is useful
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddSummaryFieldsToAgentManager.php');
	//Fix the participating agents table to not have a primary key
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_FixParticipatingAgents.php');
	//add row association columns for tabled valuation fix
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_TabledValuationUpdate.php');
	//repair script for ancient module relations between Opps, Contacts, & Accounts
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_RelationOppContactAccount.php');
	//add group column to agentman and tariffman
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_addAgentVanlineGroupColumn.php');
	//repair script for ancient module relations between Estimates, Contacts, & Accounts
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_RelationEstimateContactAccount.php');
	//Hot fix to add tables for annual rate increases
	//TODO change the name of this script to not include SIRVA (turns out this isn't SIRVA specific)
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaAnnualRateTables.php');
	//Fix export and import permissions
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Export_Import_Permission.php');
	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.10.8'");
	$db_version = '0.10.8';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
}

if ($db_version_array[0] == 0 && $db_version_array[1] == 10 && $db_version_array[2] <= 9) {
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_BulkyIntRate.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CubeSheetModTrack.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddTableForRetrieveLineItems.php');
	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.10.9'");
	$db_version = '0.10.9';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
}

if ($db_version_array[0] == 0 && $db_version_array[1] == 10 && $db_version_array[2] <= 10) {
	require_once('one-off scripts/master-scripts/Hotfixes/GeneratedScript_20160118_194915.php'); //Leads blocks added by Brian
	require_once('one-off scripts/master-scripts/Hotfixes/GeneratedScript_20160121_194512.php'); //Opportunities blocks added by Brian
	//require_once('one-off scripts/master-scripts/Hotfixes/Add_fields_users.php'); //Conrado securities script
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CWTWeightSeq.php');
	//Hotfix to switch Tariffs related lists to get_dependents_list instead of get_related_list
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LocalTariffsRelatedList.php');
	//Hotfix to change the account's bill_street and ship_street to UIType 1 to allow google address populator
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AccountChangeAddressUIType.php');
	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.10.10'");
	$db_version = '0.10.10';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
}

if ($db_version_array[0] == 0 && $db_version_array[1] == 10 && $db_version_array[2] <= 11) {
	require_once('one-off scripts/master-scripts/Hotfixes/HotFix_UserProfilesAndRoles.php'); //Conrado securities script
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_AgentId_to_CRMEntity.php'); //Fix for Conrodo's missing CRMEntity fields
	//this must be not run because we are setting the column to a text running this will cut off the column
	//require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_RepairAgentIdsColumn.php'); //Fix for Conrodo's varchar(10)
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Fix_Estimates_Sharing.php'); //Fix for Estimate sharing
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Fix_TariffManager_Sharing.php'); //Fix for TariffManager sharing
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_MakeOwnerAgentMandatory.php'); //Make Owner Agent fields mandatory globally
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_RemoveAddSurveyApptFromContacts.php'); //Remove Add button from Surveys related list in Contacts
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Remove_Surveys_From_Accounts.php'); //Fix/remove surveys from accounts/contacts
	require_once('one-off scripts/master-scripts/Create_OpList.php');//Create OPList module and associated tables
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CorrectDigitalGroupingSeparatorDefault.php'); //Sets the default value of digital grouping separator to a , character
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_DynamicPackingBulky.php'); //adds label column to packing and bulky lists
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_NewPackingBulkies.php'); //hotfix to update old to new packing/bulky items
	//hot fix to add from_contract field to `vtiger_misc_accessorials`
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_misc_accessorials_contract_fromContract.php');
	require_once('one-off scripts/master-scripts/ParticipatingAgents_20160211.php'); //Script to add ParticipatingAgents module
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_OASurveyRequest.php'); //Script to add the OASurveyRequests module
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Fix_Orders_Order_Number.php');//Make sure that orders has unique order numbers
	require_once('one-off scripts/master-scripts/AddExchangeModule.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Service_Base_Charge.php'); //Add service based charge
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Update_Service_Base_Charge.php'); //update service based charge to have matrix option.
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LocalTariffAddType.php'); //add a "tariff_type" to the local tariffs only for admin to set selectable flags.
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CorrectHourlyAvg.php'); //correct lb/man/hour field.
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddParticipantRequestColumn.php'); //add requests ID column to participating agents.
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CustomViewAssignedToAgent.php'); //adds flag and agentman id column for filters
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_TariffServiceNotRequiredFields.php'); //ensure TariffService fields are not required.
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_MakeOwnerAgentOptionalTariffs.php');//makes agentid not mandatory for tariffs
    //require_once('one-off scripts/master-scripts/PushNotifications_20160427.php'); // Adds PushNotifications module as a related list to VanlineManager
	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.10.11'");
	$db_version = '0.10.11';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
}

if ($db_version_array[0] == 0 && $db_version_array[1] == 10 && $db_version_array[2] <= 12) {
	//Remove 'Invoke Custom Function' from Workflow tasks
	Vtiger_Utils::ExecuteQuery("DELETE FROM `com_vtiger_workflow_tasktypes` WHERE id=2 AND label='Invoke Custom Function'");
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Calendar_RelateEventsToOpps.php'); // Add Relation to allow Events to be created in Workflows for Opportunities and Orders and Documents
	//moved down because it wasn't always firing with the .11 release.
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Vehicle_Entity_Identifier.php'); //Add entity identifier to Vehicles module
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AgentTypePicklistValuesParticipants.php'); //update agent_type picklist in participating agents to use db based values

	//Remove fixed_fuel field from Contracts and add picklist for fuel_surcharge_type
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_FuelSurchargeType.php');
	require_once('one-off scripts/master-scripts/PushNotifications_20160427.php'); // Adds PushNotifications module as a related list to VanlineManager
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CreateExtraStopsModule.php'); //converts extrastops block into a customizable module
	//hot fix for lead conversion (updated and moved to current release because it truncates the table)
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LeadConversion.php');
	//hotfix to update vtiger_users' agent_ids to be text so it doesn't limit adding agencies.
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Users_Agent_ids.php');
	//hotfix to update com_vtiger_workflowtasks to set task to LONGTEXT because user emails are massive in size.
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_workflow_event_tasks.php');
	//adds table to relate guest module blocks to their host modules
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddGuestModuleRelTable.php');
	//remove moveroles from orders related list and set as a guest block
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SetGuestMoveRolesOrders.php');
	//adds related orders to account related list
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddOrdersToAccountsRelatedList.php');
	//renaming the related list we just made
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_RenameOrdersToAccountsRelatedList.php');

	//Add billing address to Orders. this is CORE not gvl
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_core_Add_BillingAddress_to_Orders.php');
    //reordering accounts fields
    require_once('one-off scripts/master-scripts/Hotfixes/AccountsReorderScript_20160712_180059.php');
    require_once('one-off scripts/master-scripts/Hotfixes/GeneratedScript_20160712_180100.php');
	//Add cubesheet link to estimates if they are converted
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Cubesheet_Link_Estimates.php');

	//Add Out Of Service block to Employees module - OT2890 - Core
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_EmployeesProdAssociateOOS.php');

	//add agentid to OPList
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AgentidForOPList.php');

    //Add modified time to users
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_User_Modified.php');

	//@TODO: have a discussion about this because changing contracts will seriously mess up sirva's special setup.
    //Ryan and Alf said to use this:
    if (getenv('INSTANCE_NAME') != 'sirva') {
		//Update to Contracts based on Kim's Mockup.
		// Add fields to the Contract module for contact information (NOT linked to account/contact)
		require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_core_Add_ContractInfo_to_Contracts.php');
		require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_core_Contracts_update_information_block.php');
		require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_core_Contracts_update_tariff_info.php');
		require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_core_Contracts_update_sit_block.php');
		require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_core_Contracts_update_IntraTariff_block.php');
		require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_core_Contracts_update_valuation_block.php');
		require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_core_Contracts_update_AdditionalServices_block.php');
		require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_core_Contracts_update_international_block.php');
		require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_core_Contracts_update_addtl_flat_rate_auto_block.php');
		require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_core_Contracts_update_flat_rate_auto_block.php');
		require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_core_Contracts_reorder_blocks.php');
	}
    //OT 16667 OT3312 add invoice and distribution sequence fields.
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_detaillineitem_addFields.php');

	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Vendors_Add_OOS_Block.php');

	//Add account to the lead for when they may wish to relate
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_core_Leads_add_account.php');
	//set oplist question column to longtext to prevent question truncation
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_OPListQuestionLongtext.php');

	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Vendors_Add_OOS_Block.php');

	//VTExperts shared calendar table creation:
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_core_RelateCalendarTables.php');

    // OT 16408 - Flat item rating selection box - database changes are core, UI is graebel only right now
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddContractMiscIncludedColumn.php');
	//adds admin only field to local tariffs
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LocalTariffAdminOnly.php');

	//This will remove the extra unique keys in the vtiger_users table
	//@TODO: uncomment and NOTE the code is commented OUT until it's decided to include.
	//require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_core_Users_RemoveExtraUniqueKeys.php');

    //Because this Vehicles Thing is confusing, moving this down here.
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Vehicle_Entity_Identifier.php'); //Add entity identifier to Vehicles module

    // OT 16357 - Rework overtime packing/unpacking to match device
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_OvertimePackingUnpackingToMatchDevice.php');

    //OT 16563 - Driver's not appearing to assign in Trips
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Employee_IsDriver.php');

    // OT 16731 - Storage Pickup Inspection Fee for 400 NG
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_AddStoragePickupInspectionFeeField.php');

    // OT 2955 - (Convert Extra Stops to guest module so that) extra stops can be usable in workflows
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_ConvertExtraStopsToGuestModule.php');

    // OT 16721 - Add primary key to module's base table id column
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddIndexToModuleIDColumns.php');

    //OT16508 - Need multiple entries for driver check in
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_TripsDriverCheckIn.php');

    //Reorders fields in Surveys module so that addresses line up correctly if Orders is disabled.
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Reorder_SurveyFields.php');

    if (getenv('INSTANCE_NAME') == 'arpin') {
        require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Arpin_HideUnneededDiscounts.php');
    }

    //OT16826 Added primary key to the table: vtiger_packing_items
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_AddKeysToPackingItems.php');

    //OT3338 add Vanline specific ID to tariff manager that can be utilized for API's to vanline.
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_TariffManager_AddIDfield.php');

	Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.10.12'");
	$db_version = '0.10.12';
	$db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
		$db_version_array[$index] = intval($value);
	}
}

if ($db_version_array[0] == 0 && $db_version_array[1] == 10 && $db_version_array[2] <= 13) {
    //VTExperts shared calendar table creation:
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_core_RelateCalendarTables.php');

    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Make_Model_fields.php');

    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Orders_Surveys_RelatedList.php');

    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Set_Packing_Defaults.php');

    // OT 2955 - (Convert Extra Stops to guest module so that) extra stops can be usable in workflows
    // Requires a re-run because of disconnected db's
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_ConvertExtraStopsToGuestModule.php');
    //OT16867 - Error seen in mysql fail log for OrdersTask Edit view.
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Fix_OrdersTaskField.php');

    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Containers_Service.php');
    //OT1904 - Re-design storage
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_RedesignOfStorageScreen.php');

    // OT 16408 - Flat item rating selection box - database changes are core, UI is graebel only right now
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddContractMiscIncludedColumn.php');
    //OT1763/64 Cancel/Uncancel Orders
    require_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Orders_Cancellation_Table.php';


    //OT1969 Add ratings fields to driver block for Employees and Trips modules.
    require_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_AddRattingsFieldsToDriverBlock_Employees.php';
    require_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_AddRattingsFieldsToDriverBlock_Trips.php';

    // Caching for packing/bulky labels
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_AddPackingBulkyLabelCache.php');

    //OT1737 Add Survey and fix Survey Appointment under Orders
    require_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_AddOrderIDModuleOrders.php';

    // OT 17011 - additional info for rating crates
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_MoveHQ_UpdateCrateOptionalTariff10242016.php');

    //OT3405 - Update insurance fields
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Update_InsuranceFields.php');

    //OT17004 - Updating vendors UIType for Local Dispatch
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Modify_Assigned_Vendors_LD.php');

    //OT17383 - orders_sequence field missing
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddSequenceColumnToOrdersTable.php');
    //OT17439 - 17439 Particpating agents field is not updating with the new agent name that was changed in agents, company admin
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Fix_OrdersTask_table.php');
	// Hopefully a fix for the Exchange nonsense
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Exchange_Sync_Table.php');

    // Temp conditionalization
    if(getenv('INSTANCE_NAME') != 'sirva') {
        //Hotfix for Claims, ClaimItems and ClaimsSummary
        require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_ClaimsV2.php');

        //OT1782
        require_once 'one-off scripts/master-scripts/Hotfixes/Hotfix_AddInternationalLandtToBusinessLine.php';

        //OT17174 - Add non-planned status to orders dispatch status
        require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Orders_DispatchStatusUpdates.php');

        //3518 - Add filters to local dispatch tables
        require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_LocalDispatch_FilterTable.php');
    } else {
        //adds location type relation table for origin or destination
        require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Location_Type_OrigDest.php');
    }

    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddTableForRetrieveLineItemDetails.php');
    //adds sequencing too & orders estimate's services
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaEstimatesOrderServices.php');

    //OT1946 - Cancell Storage
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Storage_UpdateFields20161121.php');

    //OT3314 - Add the related module, Documents under the Empployees module
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Employees_AddRelatedModule_Documents.php');


	// Changes NAT street address field from uitype 21 to 1.
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_NATStreetToUIType1.php');
    // Estimates refactor
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_EstimatesRefactor.php');

	//Fixes user profile images.
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Fix_User_Profile_Images.php');
    // Add keys for performance
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddIndexesOnOrdersIdAndSetype.php');
    // OT 16830 - Add keys to various tables
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddVariousTableKeys.php');
    // Adding yet more keys
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddingYetMoreKeys.php');
    // OT 3943
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_AddGoogleAddressTable.php');

    //OT17833 -- hide city and country for leads detail summary view.
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Leads_hide_2_fields_17833.php');
    //OT17835 -- delete the related_account field on leads, because it should not be there for any body ever.
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Leads_remove_relatedAccounts_17835.php');
    // OT 17792 - fix orders/estimates relations
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_FixOrdersEstimateRelations.php');

    //Copied down here as well because it was in .12 and hadn't ran.
    //OT 16667 OT3312 add invoice and distribution sequence fields.
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_detaillineitem_addFields.php');

    //adds agentid to lead conversion field mapping
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LeadConversion_agentid.php');

    //OT3595 - Udpates to timesheet module
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_TimeSheets_Updates20170118.php');


    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_ParticipatingAgents_AddDeletedColumn.php');

    //@NOTE: Created: 2016-10-11
    Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.10.13'");
    $db_version = '0.10.13';
    $db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
        $db_version_array[$index] = intval($value);
    }
}

//@NOTE: Created: 2017-01-12
print "HERE: \n";
print_r($db_version_array);
print "HERE: \n";
if (
    ($db_version_array[0] == 0 && $db_version_array[1] < 11) ||
    ($db_version_array[0] == 0 && $db_version_array[1] == 11 && $db_version_array[2] <= 1)
) {
    //OT17838 -- add the agentmanager logo to all people!  (it's already for sirva and in the Initialization_MoveHQ)
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaAddAgentManagerLogo.php');
    // Adds move easy integration
    require_once('one-off scripts/add_moveeasy_integration.php');

    //Create detail line items for Estimates/Actuals.
    require_once('one-off scripts/master-scripts/create_detailLineItems.php');

    // update for detailed line item table required for non-GVL instances
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_detaillineitem_addPhaseEventFields.php');
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_detaillineitem_addLocGCSFields.php');
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_gvl_detaillineitem_addMetroField.php');
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddTableForDetailedLineItemsToActualsServiceProviders.php');
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_UpdateServiceProviderInfo092816.php');
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddKeysToDetailedLineItemsServiceProviderTable.php');

    // Add Do Not Exceed report section to Max4
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddNotToExceedToMax4.php');
    // TFS28639: Someone set the default views to private, causes issues.
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SetDefaultViewsToPublic.php');

    // Update values for Rate Type on Tariff Services
    require_once('one-off scripts/master-scripts/sirva/Sirva_Update_Rate_Type_Picklist_Values.php');
    // Create vtiger_quotes_storage_valution and add new value for Rate Type field in Tariff Services
    require_once('one-off scripts/master-scripts/sirva/Sirva_CreateStorageValuationTable.php');
    // Fix for TariffReportSections list view
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddAllFilterToTariffReportSections.php');
    // Fix for TariffServices list view
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddAllFilterToTariffServices.php');

    if (getenv('INSTANCE_NAME') != 'sirva') {
        // OT 16365 - Carton only column for packing <-- moved to master because arpin (moveCRM) sync needs this.
        require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddCartonOnlyOptionToPacking.php');
    } elseif (getenv('INSTANCE_NAME') == 'national') {
        //OT <> -- remove the description field from the summary view of leads.
        require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_Leads_hide_description_field.php');
    }

    require_once ('one-off scripts/master-scripts/Hotfixes/FixColumnTypes20170208.php');
    // Update to guest blocks to allow them to appear after any host block
    require_once ('one-off scripts/master-scripts/Hotfixes/UpdateGuestBlocks3726.php');
    // we don't know why this is in 0.10.5
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_LDDOrdersFields.php');
    // fix for arpin
    if(getenv('INSTANCE_NAME') == 'arpin') {
        require_once ('one-off scripts/master-scripts/Hotfixes/FixForArpinOpList20170210.php');
        require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Estimates_AddFuelSurcharge.php');
        require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Estimates_AddEstimateType.php');
        require_once ('one-off scripts/master-scripts/sirva/Add_EffectiveDate_Tariff_to_Surveys.php');
        require_once ('one-off scripts/master-scripts/Hotfixes/Arpin_Update_EffectiveDate_Tariff_to_Surveys.php');
    }
    //Create OPList module and associated tables
    require_once('one-off scripts/master-scripts/Create_OpList.php');
    //removes the related order field on Surveys and Cubesheets, this would be required.
    require_once('one-off scripts/master-scripts/Hotfixes/RemoveOrdersLinkFieldsForMoveCRM.php');
    // Session Table fix
    require_once('one-off scripts/master-scripts/Hotfixes/Session_table.php');
    //OT4406 Capacity Calendar - "Calendar Settings"
    require "one-off scripts/master-scripts/Hotfixes/Hotfix_CapacityCalendar_Settings_Update.php";

    // create AgentSequenceNumber module
    require_once ('one-off scripts/master-scripts/Hotfix_AddAgentSequenceNumberModule4225.php');
    // Update typeofdata on survey_date fields to tie them to survey_time
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_UpdateSurveyDateTypeOfData.php');
    // Update typeofdata on various time fields to tie them to date fields
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_RelateTimeFieldsToDates.php');
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_ParticipatingAgents_AddDeletedColumn.php');
    // Update typeofdata on survey_date fields to tie them to survey_time
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_UpdateSurveyDateTypeOfData.php');
    // update local tariff flat charge so it isn't always included
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_AddRateIncludedToFlatChargeTariffItem.php');

    // add sort order to local tariff sections
    require_once ('one-off scripts/master-scripts/Hotfixes/AddSortOrderTariffSections.php');

    // Add Vehicles block to Opps.
    require_once('one-off scripts/master-scripts/AddOppVehiclesBlock.php');
    // Add a webservice to pull related things
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_CreateRetrieveRelatedWebservice.php');
    if (getenv('INSTANCE_NAME') == 'suddath') {
        // Show the notes on the quickcreate of a survey Appointment.
        require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Suddath_Surveys_QC_Notes.php');
        //Make duration_hours of Calendar and Events an integer instead of time.
        require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_FixSurveyDurationHours.php');
        // Allow workflow emails to the creator of the surveys
        require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Suddath_SurveyCubesheets_Add_CreatedField.php');
        //Make the contact mandatory for survey appointments
        require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Suddath_SurveysAppt_ContactFieldChange.php');
    }

    if (getenv('INSTANCE_NAME') != 'sirva') {
        //Make duration_hours of Calendar and Events an integer instead of time.
        require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Core_FixSurveyDurationHours.php');
    }
    // Add Rating Engine location id
    require_once ('one-off scripts/master-scripts/Hotfixes/AddCustomTariffIDToTariffManager.php');
    //OT18144 -- add a standard item flag to differentiate custom vs standard local packing items.
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_TariffServicesPackingitemAddStandardFlag.php');

		// Add default bulky table
		require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Local_Default_Bulky_Table.php');
		//fix the default bulky table.
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_Fix_Local_Default_Bulky_Table.php');

		// Add a standard item flag for bulkies too
		require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_TariffServicesBulkyitemAddStandardFlag.php');

    // TFS 27551 - new Rate Type for CWT Per Quantity, core it
    require_once('one-off scripts/master-scripts/sirva/AddQuantityChargeByCWTRate.php');
    // Add weight field to quotes_cwtperqty table.
    require_once('one-off scripts/master-scripts/sirva/AddWeightToCWTPerQty.php');
    // Hotfix to make CWTPerQty table's data columns be nullable, because sync isn't having it.
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_MakeCWTPerQtyColumnsNullable.php');

    // Add Flat Rate by Weight rate type.
    require_once('one-off scripts/master-scripts/Add_FlatRateByWeightService.php');

    //Update tariff services to have the enforced fields Bulky Items:
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_EnforceBulkyLocalLists.php');
    // TFS29604: Add valuation multiplier.
    require_once('one-off scripts/master-scripts/AddValuationMultiplierToChargePerHundred.php');

    //THIS has to run BEFORE: Hotfix_EnforcePackingLocalLists.php
    //OT18496 -- tariff services packing and bulky id's get the cartonBulkyID value assigned to them (independently of the creator)
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_TariffServicesAddPackItemIDToPacking.php');
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_TariffServicesAddCartonIDtoBulkyLists.php');

    //Update tariff services to have the enforced fields Packing items:
    require_once ('one-off scripts/master-scripts/Hotfixes/Hotfix_EnforcePackingLocalLists.php');

    // TFS29703: Add flag to perunit table.
    require_once('one-off scripts/master-scripts/AddFlagColumnToPerUnit.php');
    // TFS29704: Add multiplier to Tabled Valuation.
    require_once('one-off scripts/master-scripts/Add_Multipler_TabledValuation.php');
    // OT4715 - Move Twilio SIDs into VanlineManager (subaccount SID) and AgentManager (messaging service SID)
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddTwilioSIDsToVanlineAgentManagers.php');

    // TFS29704: Add multiplier to Tabled Valuation.
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Fix_Rate_For_Decimals.php');
    //OT18292 - Ensure that there's only one related list to Documents inside of Vehicles
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Vehicles_EnsureOneDocumentsRelatedList.php');
    //OT18549 - Adding 400NG custom tariff type
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddCustomTariffType_400NG.php');

    if (getenv('INSTANCE_NAME') == 'arpin') {
        require_once ('one-off scripts/master-scripts/arpin/20170515_163235_Opportunities_add_registration_number.php');

        //OT4749 -- add sales number to users for registration
        require_once ('one-off scripts/master-scripts/arpin/20170606_162530_users_add_vanline_sales_number.php');
    }
    if (getenv('INSTANCE_NAME') != 'sirva') {
        // OT18532 -- Brand should be hidden from the Accounts display.
        require_once('one-off scripts/master-scripts/movehq/20170518_092135_accounts_hide_brand.php');
    }
    // TFS29892: Add step to Estimates bottom line discount.
    require_once('one-off scripts/master-scripts/Add_BLD_StepAttribute.php');
    // TFS29849: Change Crate table to use quantities instead of flags for packing.
    require_once('one-off scripts/master-scripts/Change_CratePackingToQuantities.php');
    // no clue what ticket this relates to.
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Fix_Rate_For_Decimals.php');

    // Make abbr state fields uppercase
    require_once('one-off scripts/master-scripts/Hotfixes/20170526_135758_update_states-fields-enforce-capitalization.php');
    // OT 16267 - leading zero being cut off in zipcode field
    // This is being moved because everyone needs this.
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_ConvertZipToVarchar.php');
    //OT18562 - Adding Documents related list to Cubesheets module
    require_once('one-off scripts/master-scripts/Hotfixes/20170511_140800_Cubesheets_add_Documents-relation.php');
    //OT3953 - Add Status field to Tariffs module
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Tariffs_AddStatus.php');
    // OT4757 survey appointments add self Survey to the survey type list and register a related event
    require_once('one-off scripts/master-scripts/movehq/Surveys_survey_type_add_SelfSurvey_4757.php');
    // OT18653 - Add commodites to Convert Lead field mapping
    require_once('one-off scripts/master-scripts/20170525_150000_leads_add_commodity-field-mapping.php');
    //OT4664 - 2-way SMS
    require_once('one-off scripts/master-scripts/20150531_120000_SMSResponder_create_two-way-sms-handler.php');
    //Tidy up phone numbers and make sure they are all the same UI type
    require_once('one-off scripts/master-scripts/20170628_191706_udpate_phone_uitypes.php');
    //OT18815 - Bulkies do not send weight.
    require_once('one-off scripts/master-scripts/movehq/20170630_110930_estimates-detailedlineitems_add_field-item_weight.php');

    //OT 16667 OT3312 add invoice and distribution sequence fields.
    require_once('one-off scripts/master-scripts/movehq/20170714_105730_estimates-detailedlineitems_add_fields-dli_invoice_sequence-dli_distribution_sequence.php');

    //OT4458 - setting up flags table for record protection
    require_once('one-off scripts/master-scripts/windfall/20170714_153600_create_table-crmentity-flags.php');

    //OT18812 - No Valuation Table Information
    require_once('one-off scripts/master-scripts/movehq/20170630_164030_estimates_add_field-valuation_options.php');
		
	// TFS31571 - Make member of mandatory, goes in line with JS changes.
    require_once('one-off scripts/master-scripts/20170815_125151_users_update_agent_ids_make_mandatory.php');
    // OT4458 - Setting up vtiger_crmentity_flags table
    require_once('one-off scripts/master-scripts/windfall/20170714_153600_create_table-crmentity-flags.php');

    //OT19160	Net Unit Rate for Detailed Line Items XML
    require_once('one-off scripts/master-scripts/movehq/20170829_163030_estimates-detailedlineitems_add_field-rate_net.php');
    // TFS31882 - Fix sales stage picklist presence.
    require_once('one-off scripts/master-scripts/20170907_155042_opportunities_update_sales_stage-presence.php');
    // Fix presence for quotestage.
    require_once('one-off scripts/master-scripts/20170908_182251_estimates_update_quotestage-presence.php');
    // TFS32098 - Fix eventstatus presence.
    require_once('one-off scripts/master-scripts/20170912_140212_calendar_update_eventstatus-presence.php');
    // TFS32098 - Fix eventstatus presence.
    require_once('one-off scripts/master-scripts/20170914_150834_calendar_update_taskstatus-presence.php');
    // OT19404 - Fix activitytype presence
    require_once('one-off scripts/master-scripts/20170919_172000_calendar_update_activitytype-presence.php');

    require_once('one-off scripts/master-scripts/20171030_203324_tariffservices_update_service_base_charge.php');

    // TFS31556 - Add sales tax column to estimates local packing.
    require_once('one-off scripts/master-scripts/20171122_155719_estimates_add_packing_sales_tax_override.php');
    require_once('one-off scripts/master-scripts/20171129_141928_workflows_update_agents-column-size.php');

    //@NOTE: Created: 2017-01-12
    Vtiger_Utils::ExecuteQuery("UPDATE `database_version` SET `movehq` = $movehq ,`db_version` = '0.11.1'");
    $db_version = '0.11.1';
    $db_version_array = explode('.', $db_version);
    foreach ($db_version_array as $index=>$value) {
        $db_version_array[$index] = intval($value);
    }
}


require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_FixModTrackerSequenceTable.php');
//Create detail line items for Estimates/Actuals.
require_once('one-off scripts/master-scripts/create_detailLineItems.php');

if (getenv('INSTANCE_NAME') == 'sirva') {
	echo "<br><h1>Initializing Sirva</h1><br>";
	require_once('Initialization_Sirva.php');

	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Opportunities_SentToMobile.php'); //add sent_to_mobile field to Potentials & Opportunities

	//echo "<br><h2>Adding a row in fieldmodulerel that was breaking Contacts</h2><br>";
	//require_once('one-off scripts/master-scripts/Hotfixes/HotFix_Sirva_BugContacts.php');

	//Reorder scripts for SIRVA instance - should be run after any other hotfixes and such
	require_once('one-off scripts/master-scripts/Hotfixes/LeadsReorderScript_20160118_194921.php');
	require_once('one-off scripts/master-scripts/Hotfixes/OpportunitiesReorderScript_20160121_194517.php');

	/**
	 * @todo Re-implement this script without a hardcoded `relation_id`.
	 */
	//-- echo "<br><h2>Updating QuickCreate In Estimates</h2><br>";
	//-- require_once('one-off scripts/master-scripts/Hotfixes/HotFix_Sirva_QuickCreate.php');

	//STS updates
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_SirvaSTSAgmtId.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AddStsCodFields.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_FixSTSConsumerFieldUIType.php');
	//AgentManager to Agents
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CreateAgentsFromAgentManager.php');
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Related_Opp2Opp.php');

	//Fix Date order: In an order to avoid confusion, I'm making this note:
	// This was not just for Sirva. It is being done to all vanlines and the order is being stomped by another script, originally thought to be a sirva script.
	// That is why it has Sirva in the name, even though it is for all vanlines.

	//Talked with Jeremy about this one and we think this is wrong, its causing issues in estimate if the date block
	//is not in the estimate view
	//require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_Fix_Date_Order.php');
} elseif (getenv('INSTANCE_NAME') == 'uvlc') {
	echo "<br><h1>Initializing UVLC</h1><br>";
	require_once('uvl-c_initialization.php');
} elseif (getenv('INSTANCE_NAME') == 'national')
{
    // TODO: move this to its own initialization grouping script
    //OT4002 : add VTE's ListViewColors module.  @NOTE: update to profile Permissions default to accomodate
    require_once('one-off scripts/master-scripts/movehq/ExtensionsListviewcolors_3783.php');
    require_once('one-off scripts/master-scripts/national/20170627_133530_estimates_update_field-pricing_type.php');
} elseif (getenv('INSTANCE_NAME') == 'suddath') {
    require_once('one-off scripts/master-scripts/Media_20161222.php');
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Suddath_ContactsFields_Mandatory.php');
}
require_once('one-off scripts/master-scripts/movehq/NationalLeadModuleChanges_3429.php');
//Hotfix to adjust tariffbulky table to make rate column decimal type
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_TariffBulkyRateToDecimal.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_FixModTrackerSequenceTable.php');

//This is run at bottom
//require_once('one-off scripts/master-scripts/Fix_Tabs.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_FixRelatedLists.php');
//fix for comments not being able to save because of AgentId field
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_RemoveAgentIdField.php');
echo "<br><h2>Reordering Accessorials in Estimates</h2><br>";
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_AccsOrder.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_removeIncorrectFields.php');
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_UpdateBusinessLine.php');

if (getenv('INSTANCE_NAME') != 'graebel') {
    //Run on all non-graebel non-HQ instances
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Move_OTPacking_Checkboxes.php');
}

if (getenv('INSTANCE_NAME') == 'graebel') {
	echo "<br> <h1> Initializing Graebel </h1> <br>";
	require_once('Initialization_Graebel.php');
} elseif (getenv('INSTANCE_NAME') == 'mccollisters') {
    echo "<br><h1>Initializing McCollisters</h1><br>";
    require_once('Initialization_McCollisters.php');
} elseif (getenv('INSTANCE_NAME') == 'sirva') {
    // apparently this HAS to run last for sirva
    echo "<br><h2>Updating QuickCreate In Estimates</h2><br>";
    require_once('one-off scripts/master-scripts/Hotfixes/HotFix_Sirva_QuickCreate.php');
    echo "<br><h2>Adding a row in fieldmodulerel that was breaking  Contacts</h2><br>";
    require_once('one-off scripts/master-scripts/Hotfixes/HotFix_Sirva_BugContacts.php');
} else {
    echo "<br> <h1> Initializing MoveHQ </h1> <br>";
    require_once('Initialization_MoveHQ.php');
}

if (checkIsWindfallActive()) {
    echo "<br> <h1> Initializing Windfall </h1> <br>";
    require_once('Initialization_Windfall.php');
}
//This is run at bottom
//fix tabs RUN AT BOTTOM
//require_once('one-off scripts/master-scripts/Fix_Tabs.php');

//Hotfix to validate that all TariffSections records have a corresponding Services record for line items
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_TariffSections_ValidateServices.php');

//Hotfix to add an owner id to email templates
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Owner_to_EmailTemplates.php');

echo "<h1> Running profile permissions for " . getenv('INSTANCE_NAME') . "</h1>";
if ($movehq) {
	require_once('one-off scripts/master-scripts/Profile_Permissions_moveHQ.php');
} else {
	switch (getenv('INSTANCE_NAME')) {
		case 'sirva':
			require_once('one-off scripts/master-scripts/Profile_Permissions_Sirva.php');
			break;

	case 'uvlc':
		require_once('one-off scripts/master-scripts/Profile_Permissions_UVLC.php');
		break;

	case 'mccollisters':
		require_once('one-off scripts/master-scripts/Profile_Permissions_Mccollisters.php');
		break;

		default:
			require_once('one-off scripts/master-scripts/Profile_Permissions_Default.php');
			break;
	}
}
require_once('one-off scripts/master-scripts/Hotfixes/fix_comma_seperateor.php');
//fix so everyone can leave comments
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_CommentPermissions.php');
//Reorder dates for sirva. Needed to move it here, because somee other hotfix is messing with the estimate's field order.
if (getenv('INSTANCE_NAME') == 'sirva') {
	require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Sirva_Fix_Date_Order.php');
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Add_Max3_Max4_tariffs.php');
}
//run fix tabs at bottom!
//@TODO take a look at breaking this into multiple scripts for clarity.
require_once('one-off scripts/master-scripts/Fix_Tabs.php');
//OT 2550: Change Vehicle Type Picklist
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Update_Vehicle_Type_Picklist.php');
//fix for having HHG - Interstate and HHG - Intrastate in the business lines
require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_RemoveExtraBusinessLines.php');
//fix for the database having duplicate fields
//OT 3235 synchronizing estimates/actuals business line picklists with other business lines in other modules. Graebel only.
if (getenv('INSTANCE_NAME') == 'graebel') {
    require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_GVL_SynchronizeBusinessLinePicklists.php');
}
require_once('one-off scripts/master-scripts/Hotfixes/RemoveDuplicateFields.php');
// 3515: Time zone changes in moveCRM
require_once ('one-off scripts/master-scripts/movehq/CreateDateTimeZoneFields.php');

//Remove default workflows from database
Vtiger_Utils::ExecuteQuery("DELETE FROM `com_vtiger_workflows` WHERE defaultworkflow=1");

//Delete any existing TariffManager records for MAX tariffs
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_crmentity` SET deleted=1 WHERE setype='TariffManager' AND label LIKE '%MAX%'");

if (getenv('INSTANCE_NAME') != 'sirva') {
    //OT4248 This is required to run after the initializations that might add modules in order to update the "Default Menu" list.
    require_once('one-off scripts/master-scripts/movehq/Core_MenuUpdater.php');
}

require_once('one-off scripts/master-scripts/Hotfixes/Hotfix_Delete_Admin_Email_Templates.php');

//THIS SHOULD ALWAYS RUN LAST!!!
require('updateTabData.php');
echo "<h1> Master Script Completed Successfully Updated to $db_version </h1>";

// update run script versions
updateVersionRunLog($masterScriptVersionTable);

echo "<h1>Master Script Finished</h1>";
//@NOTE: we return 10 at exit so the calling program has something particular to look for.
exit(10);
