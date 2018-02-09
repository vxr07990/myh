<?PHP
​
// THIS VARIABLE NEEDS TO MATCH THE EXACT ENDING OF A SUCCESSFUL MASTER SCRIPT!
// 13 CHARACTERS FROM THE END!
$mscriptfinal = "0.10.11 </h1>";
​
// REQUIRE AND DOTENV
require_once 'vendor/autoload.php';
use Carbon\Carbon;
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();
​
//DB CONNECTION
$db = new mysqli(getenv('DB_SERVER'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_NAME'));
​
​
// COLORS FOR TERMINAL
function colorize($text, $status) {
    $out = "";
    switch($status) {
        case "SUCCESS":
            $out = "[42m"; //Green background
            break;
        case "FAILURE":
            $out = "[41m"; //Red background
            break;
        case "WARNING":
            $out = "[43m"; //Yellow background
            break;
        case "NOTE":
            $out = "[44m"; //Blue background
            break;
        default:
            throw new Exception("Invalid status: " . $status);
    }
    return chr(27) . "$out" . "$text" . chr(27) . "[0m";
}
​
​
​
// Prod Variables
if (strtolower($argv[1]) == 'prod' && isset($argv[2]) == True && $argv[3] != 'init') {
    $ready = 1;
    $env = "prod";
    $backup = strtolower($argv[3]);
    $composercommand = " --no-dev";
}
// Dev Variables
elseif (strtolower($argv[1]) == 'dev' && isset($argv[2]) == True && $argv[3] != 'init'){
    $ready = 1;
    $env = "dev";
    $backup = strtolower($argv[3]);
    $composercommand = "";
​
}
// init prod
elseif (strtolower($argv[1]) == 'prod' && isset($argv[2]) == True && strtolower($argv[3]) == 'init') {
        $ready = 2;
        $env = "prod";
        $backup = "false";
        $composercommand = " --no-dev";
​
}
// init dev
elseif (strtolower($argv[1]) == 'dev' && isset($argv[2]) == True && strtolower($argv[3]) == 'init') {
    $ready = 2;
    $env = "dev";
    $backup = "false";
    $composercommand = "";
​
} else {
    $ready = 0;
}
​
​
//
//
//
// Migration Starts Here
//
//
//
if($ready == 1 || $ready == 2){
    echo "\n" . colorize("Creating a $env instance!", "SUCCESS") . "\n";
​
    //CHECKS IF ENV HAS CORRECT INFORMATION
    if ($db->connect_errno > 0 && $ready == 1) {
        echo "\n" . colorize("Unable to connect to database [ $db->connect_error ]", "FAILURE") . "\n";
        echo "\n" . colorize("If this is a new instance, and not a migration, you must\nrun php _migrate.php dev||prod user init", "WARNING") . "\n";
        die;
​
​
    } else {
        //creates a database backup
        if($ready == 1 && $backup == "true" && file_exists("migration_script") == TRUE){
            shell_exec("mysqldump -P ".getenv('DB_PORT')." -h ".getenv('DB_SERVER')." -u ".getenv('DB_USERNAME')." -p".getenv('DB_PASSWORD')." ".getenv('DB_NAME')." > migration_script/databasebackup".Carbon::now()->format('m_d_Y') .".sql");
            echo "\n" . colorize("DATABASE BACKUP SUCCESSFUL!", "SUCCESS") . "\n";
        } elseif($ready == 1 && $backup == "true" && file_exists("migration_script") == FALSE) {
            mkdir('migration_script',0777);
            shell_exec("mysqldump -P ".getenv('DB_PORT')." -h ".getenv('DB_SERVER')." -u ".getenv('DB_USERNAME')." -p".getenv('DB_PASSWORD')." ".getenv('DB_NAME')." > migration_script/databasebackup".Carbon::now()->format('m_d_Y') .".sql");
            echo "\n" . colorize("DATABASE BACKUP SUCCESSFUL!", "SUCCESS") . "\n";
        } else {
            echo "\n" . colorize("NO DATABASE BACKUP WAS CREATED!", "WARNING") . "\n";
        }
        // RUNS COMPOSER
        echo "\n" . colorize("CONNECTED TO DATABASE! Running Composer!", "SUCCESS") . "\n";
        exec("composer install$composercommand 2>&1", $composer);
        shell_exec("composer install$composercommand");
​
        //COMPOSER ERROR HANDLING
        if (end($composer) == "Generating optimized autoload files") {
            echo "\n" . colorize("COMPOSER FINISHED SUCCESSFULLY, CONTINUING!", "SUCCESS") . "\n";
            echo "\n" . colorize("Running master_script.php", "SUCCESS") . "\n";
            echo colorize("WARNING!: Master_script may take a moment! Please be patient!", "WARNING") . "\n";
            exec("php master_script.php 2>&1", $mscript);
​
            // MASTER SCRIPT ERROR HANDLING
            if (substr(end($mscript), -13) == $mscriptfinal) {
                echo colorize("MASTER_SCRIPT COMPLETED SUCCESSFULLY!", "SUCCESS") . "\n";
​
                //SETS FILE PERMISSIONS
                echo "\n" . colorize("Setting file permissions!", "SUCCESS") . "\n";
                shell_exec('chmod -R 0644 .');
                shell_exec('find . -type d -exec chmod 0554 {} +');
                shell_exec('chmod -R 0775 logs');
                shell_exec('chmod -R 0775 vendor');
                shell_exec('chmod -R 0775 user_privileges');
                shell_exec('chmod -R 0775 storage');
                shell_exec('chmod -R 0775 test');
​
                // TEMPLATES_C FILE PERMISSIONS AND DIRECTORY CREATION
                if (file_exists("test/templates_c/vlayout") == TRUE) {
                    echo "\n" . colorize("test/templates_c/vlayout exists, setting permission.", "NOTE") . "\n";
                    shell_exec('chmod 775 test/templates_c/vlayout');
                } else {
                    echo "\n" . colorize("test/templates_c/vlayout does not exist, creating and setting permissions!", "WARNING") . "\n";
                    shell_exec('mkdir test/templates_c');
                    shell_exec('mkdir test/templates_c/vlayout');
                    shell_exec('chmod 775 test/templates_c/vlayout');
                }
                shell_exec("chown -R $argv[2]:www-data .");
​
                // Running _recreate_user_privilege_files.php
                echo "\n" . colorize("Recreating user privilege files", "SUCCESS") . "\n";
                shell_exec('php _recreate_user_privilege_files.php');
​
                // INSTANCE FINISHED
                echo "\n" . colorize("$env INSTANCE WAS CREATED SUCCESSFULLY!", "SUCCESS") . "\n";
​
            } else {
                // MASTER SCRIPT FAILS HERE
                echo "\n" . colorize("MASTER_SCRIPT FAILED! STOPPING! LOGGED!", "FAILURE") . "\n";
                if (file_exists("migration_script") == TRUE) {
                    file_put_contents('migration_script/master_script_' . Carbon::now() . '.html', array_values($mscript));
                } else {
                    mkdir('migration_script',0777);
                    file_put_contents('migration_script/master_script_' . Carbon::now() . '.html', array_values($mscript));
                }
                shell_exec('chmod -R 0775 migration_script');
                die;
            }
​
        } else {
            // COMPOSER FAILS HERE
            echo "\n" . colorize("COMPOSER FAILED, STOPPING!", "FAILURE") . "\n";
            die;
        }
    }
}
// THE COMMAND WAS NOT SETUP CORRECTLY
else {
    echo "\n" . colorize("Please set your environment with the following command php _migrate.php dev||prod user init||true!", "FAILURE") . "\n";
    die;
}