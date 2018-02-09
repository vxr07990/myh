<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";



ini_set('display_errors', 'on');
error_reporting(E_ERROR);
$Vtiger_Utils_Log = true;

require_once 'vtlib/Vtiger/Menu.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'vendor/autoload.php';

$users = Vtiger_Module::getInstance('Users');

if (Vtiger_Block::getInstance('LBL_USERS_EXCHANGE', $users)) {
    $exchange = Vtiger_Block::getInstance('LBL_USERS_EXCHANGE', $users);
} else {
    $exchange        = new Vtiger_Block;
    $exchange->label = 'LBL_USERS_EXCHANGE';

    $users->addBlock($exchange);
}

var_dump($exchange);

// -----

if (Vtiger_Field::getInstance('user_exchange_hostname', $users)) {
    dump("HOSTNAME FIELD EXISTS!");
} else {
    $hostname             = new Vtiger_Field;
    $hostname->label      = 'LBL_USERS_EXCHANGE_HOSTNAME';
    $hostname->name       = 'user_exchange_hostname';
    $hostname->table      = 'vtiger_users';
    $hostname->column     = 'exchange_hostname';
    $hostname->columntype = 'VARCHAR(100)';
    $hostname->uitype     = 1;
    $hostname->typeofdata = 'V~O';

    $exchange->addField($hostname);
    echo "\n";
    var_dump($hostname);
    echo "\n";
}

// -----

if (Vtiger_Field::getInstance('user_exchange_username', $users)) {
    dump("USERNAME FIELD EXISTS!");
} else {
    $username             = new Vtiger_Field;
    $username->label      = 'LBL_USERS_EXCHANGE_USERNAME';
    $username->name       = 'user_exchange_username';
    $username->table      = 'vtiger_users';
    $username->column     = 'exchange_username';
    $username->columntype = 'VARCHAR(100)';
    $username->uitype     = 1;
    $username->typeofdata = 'V~O';

    $exchange->addField($username);
    echo "\n";
    var_dump($username);
    echo "\n";
}

// -----

if (Vtiger_Field::getInstance('user_exchange_password', $users)) {
    dump("PASSWORD FIELD EXISTS!");
} else {
    $password              = new Vtiger_Field;
    $password->label       = 'LBL_USERS_EXCHANGE_PASSWORD';
    $password->name        = 'user_exchange_password';
    $password->table       = 'vtiger_users';
    $password->column      = 'exchange_password';
    $password->columntype  = 'VARCHAR(100)';
    $password->uitype      = 2;
    $password->typeofdata  = 'V~O';

    $exchange->addField($password);
    echo "\n";
    var_dump($password);
    echo "\n";
}


// -----

/*$exchange->addField($password);*/
//$exchange->save($users);

// -----

//$users->save();

echo "\n";
var_dump($users);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";