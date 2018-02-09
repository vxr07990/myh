<?php

require_once 'modules/Users/Users.php';
error_reporting(0);

$db_set = getenv('DB_VERSION');
$app_key = "asdf12ASD3GASDF25d4fa54sdf46664sdf2g4A21G4A2DSG45sd45fgs2d3fg4";
$okayip = "75.118.43.122";

if (authenticate()) {
    switch ($_GET['fetch']) {
        case 'instance_state':
            getInstance();
            break;
        case 'env_var':
            getEnvVars();
            break;
        default:
            echo "Whoops! Somethings wrong!";
    }
} else {
    echo "Not Authorized!";
}

function authenticate()
{
    global $app_key, $okayip;
    if ($_SERVER['REMOTE_ADDR'] == $okayip && $_GET['app_key'] == $app_key) {
        return 1;
    } else {
        return 0;
    }
}


// checking instance state
function getInstance()
{
    global $db_set;
    // connect to database and find the version
    $db = PearDatabase::getInstance();
    $sql = "SELECT db_version FROM `database_version`";
    $result = $db->pquery($sql, []);

    if ($result != null) {
        $row = $result->fetchRow();
        $db_version = $row[db_version];
    }


    // checks if database exists
    if ($db->database->_errorMsg != "Unknown database '" . getenv('DB_NAME')."'") {

        //checks if database version is correct
        if ($db_version == $db_set) {

            //checks for update.zip
            if (file_exists('update.zip')) {

                //checks for wiki flag
                if (getenv('WIKI')) {
                    echo "UPDATING";
                } else {
                    echo "PREPARING FOR UPDATE";
                }

                // EVERYTHING IS A-OK
            } else {
                echo "UP";
            }

            // wrong database version
        } else {
            echo "DATABASE VERSION MISMATCH";
        }
        //database does not exist
    } else {
        echo "DOWN";
    }
}

// getting enviornment variables
function getEnvVars()
{
    echo "<pre>";
    echo file_get_contents(".env");
    echo "</pre>";
}
