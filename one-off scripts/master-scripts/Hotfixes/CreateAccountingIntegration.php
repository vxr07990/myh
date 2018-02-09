<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 3/17/2017
 * Time: 11:32 AM
 */

require_once ('libraries/MoveCrm/AccountingIntegration.php');

if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "SKIPPING: " . __FILE__ . "<br />\n";
        return;
    }
}
print "RUNNING: " . __FILE__ . "<br />\n";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db = &PearDatabase::getInstance();

// TODO: remove this: for testing only

$res = $db->pquery('SELECT TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
                       FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                       WHERE REFERENCED_TABLE_SCHEMA=? AND REFERENCED_TABLE_NAME=?',
                   [getenv('DB_NAME'), 'vtiger_accountingintegration_entity']);
while($res && $row = $res->fetchRow())
{
    $tableName = $row['TABLE_NAME'];
    $db->pquery('ALTER TABLE `'.$tableName.'` DROP FOREIGN KEY `'.$row['CONSTRAINT_NAME'].'`');
}

$db->pquery('DROP TABLE IF EXISTS vtiger_accountingintegration_fieldrel');
$db->pquery('DROP TABLE IF EXISTS vtiger_accountingintegration_log');
$db->pquery('DROP TABLE IF EXISTS vtiger_accountingintegration_entity');
$db->pquery('DROP TABLE IF EXISTS vtiger_accountingintegration_configinfo');
$db->pquery('DROP TABLE IF EXISTS vtiger_accountingintegration_config');
$db->pquery('DROP TABLE IF EXISTS vtiger_accountingintegration_agents');
$db->pquery('DROP TABLE IF EXISTS vtiger_accountingintegration_vanlines');
$db->pquery('DROP TABLE IF EXISTS vtiger_accountingintegration');

$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "vtiger_accountingintegration" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'vtiger_accountingintegration already exists'.PHP_EOL;
} else {
    echo 'Creating table vtiger_accountingintegration'.PHP_EOL;
    $stmt = 'CREATE TABLE `vtiger_accountingintegration` 
            (`id` INT(11) NOT NULL AUTO_INCREMENT,
            `auth_userid` INT(11),
            `createdtime` DATETIME DEFAULT CURRENT_TIMESTAMP ,
            `refreshtime` DATETIME DEFAULT CURRENT_TIMESTAMP ,
            `modifiedtime` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `remote_system` VARCHAR(100),
            
            `app_token` VARCHAR(100),
            `oauth_consumer_key` VARCHAR(100),
            `oauth_consumer_secret` VARCHAR(100),
            `realmid` VARCHAR(100),
            `oauth_token` VARCHAR(100),
            `oauth_token_secret` VARCHAR(100),
            
            PRIMARY KEY (`id`)
            )';
    $db->pquery($stmt);
}

$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "vtiger_accountingintegration_agents" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'vtiger_accountingintegration_agents already exists'.PHP_EOL;
} else {
    echo 'Creating table vtiger_accountingintegration_agents'.PHP_EOL;
    $stmt = 'CREATE TABLE `vtiger_accountingintegration_agents` 
            (`id` INT(11),
            `agentid` INT(11),
            
            KEY (`id`),
            UNIQUE KEY (`agentid`),
            FOREIGN KEY (id) REFERENCES vtiger_accountingintegration(id) ON DELETE CASCADE,
            FOREIGN KEY (agentid) REFERENCES vtiger_agentmanager(agentmanagerid)
            )';
    $db->pquery($stmt);
}

$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "vtiger_accountingintegration_vanlines" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'vtiger_accountingintegration_vanlines already exists'.PHP_EOL;
} else {
    echo 'Creating table vtiger_accountingintegration_vanlines'.PHP_EOL;
    $stmt = 'CREATE TABLE `vtiger_accountingintegration_vanlines` 
            (`id` INT(11),
            `vanlineid` INT(11),
            
            KEY (`id`),
            UNIQUE KEY (`vanlineid`),
            FOREIGN KEY (id) REFERENCES vtiger_accountingintegration(id) ON DELETE CASCADE,
            FOREIGN KEY (vanlineid) REFERENCES vtiger_vanlinemanager(vanlinemanagerid)
            )';
    $db->pquery($stmt);
}

$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "vtiger_accountingintegration_config" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'vtiger_accountingintegration_config already exists'.PHP_EOL;
} else {
    echo 'Creating table vtiger_accountingintegration_config'.PHP_EOL;
    $stmt = 'CREATE TABLE `vtiger_accountingintegration_config` 
            (`id` INT(11) NOT NULL AUTO_INCREMENT,
            `integrationid` INT(11),
            
            KEY (`id`),
            UNIQUE KEY (`integrationid`),
            FOREIGN KEY (integrationid) REFERENCES vtiger_accountingintegration(id)
            )';
    $db->pquery($stmt);
}

$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "vtiger_accountingintegration_configinfo" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'vtiger_accountingintegration_configinfo already exists'.PHP_EOL;
} else {
    echo 'Creating table vtiger_accountingintegration_configinfo'.PHP_EOL;
    $stmt = 'CREATE TABLE `vtiger_accountingintegration_configinfo` 
            (`id` INT(11),
            `entitytype` VARCHAR(40),
            `localname` VARCHAR(50),
            `remotename` VARCHAR(250),
            `datatype` VARCHAR(50),
            `presence` INT(11),
            `queryable` VARCHAR(5),
            
            KEY (`id`),
            FOREIGN KEY (id) REFERENCES vtiger_accountingintegration_config(id) ON DELETE CASCADE
            )';
    $db->pquery($stmt);
}

$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "vtiger_accountingintegration_entity" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'vtiger_accountingintegration_entity already exists'.PHP_EOL;
} else {
    echo 'Creating table vtiger_accountingintegration_entity'.PHP_EOL;
    $stmt = 'CREATE TABLE `vtiger_accountingintegration_entity` 
            (`id` INT(11) NOT NULL AUTO_INCREMENT,
            `integrationid` INT(11),
            `remoteid` INT(11),
            `type` VARCHAR(40),
            `label` VARCHAR(100),
            `createdtime` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `modifiedtime` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `synctime` DATETIME,
            
            PRIMARY KEY (`id`),
            KEY (`integrationid`,`type`),
            KEY (`integrationid`,`remoteid`),
            UNIQUE KEY (`integrationid`,`remoteid`,`type`),
            FOREIGN KEY (integrationid) REFERENCES vtiger_accountingintegration(id)
            )';
    $db->pquery($stmt);
}

$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "vtiger_accountingintegration_log" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'vtiger_accountingintegration_log already exists'.PHP_EOL;
} else {
    echo 'Creating table vtiger_accountingintegration_log'.PHP_EOL;
    $stmt = 'CREATE TABLE `vtiger_accountingintegration_log` 
            (`id` INT(11) NOT NULL AUTO_INCREMENT,
            `transaction` INT(11),
            `userid` INT(11),
            `time` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `category` VARCHAR(100),
            `notes` TEXT,
            
            PRIMARY KEY (`id`)
            )';
    $db->pquery($stmt);
}

$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "vtiger_accountingintegration_fieldrel" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'vtiger_accountingintegration_fieldrel already exists'.PHP_EOL;
} else {
    echo 'Creating table vtiger_accountingintegration_fieldrel'.PHP_EOL;
    $stmt = 'CREATE TABLE `vtiger_accountingintegration_fieldrel` 
            (`id` INT(11) NOT NULL AUTO_INCREMENT,
            `fieldid` INT(11),
            `type` VARCHAR(50),
            `subtype` VARCHAR(50),
            
            PRIMARY KEY (`id`),
            UNIQUE KEY (`fieldid`),
            FOREIGN KEY (fieldid) REFERENCES vtiger_field(fieldid)
            )';
    $db->pquery($stmt);
}

$arrFields = [
    'ItemCodesMapping' => [
        //'itcmapping_glcode'                  => 'Item',
        'itcmapping_salesexpense'            => ['Account', 'Expense'],
        'itcmapping_owner_operatorexpense'   => ['Account', 'Expense'],
        'itcmapping_company_driverexpense'   => ['Account', 'Expense'],
        'itcmapping_lease_driverexpense'     => ['Account', 'Expense'],
        'itcmapping_packer_expense'          => ['Account', 'Expense'],
        'itcmapping_3rdparty_serviceexpense' => ['Account', 'Expense']
    ],
    'Accounts' => [
        'customer_number' => ['Customer', null]
    ],
//    'Agents' => [
//        'agents_custnum' => ['Customer', null]
//    ],
    'Orders' => [
        'orders_custnum' => ['Customer', null]
    ],
    'Employees' => [
        'employee_vendornumber' => ['Vendor', null]
    ],
    'Vendors' => [
        'vendors_vendornum' => ['Vendor', null]
    ],
    'AgentManager' => [
        'quickbooks_class' => ['Department', null]
    ],
];

foreach ($arrFields as $moduleName => $fieldDetails)
{
    $module = Vtiger_Module::getInstance($moduleName);
    if(!$moduleName)
    {
        echo $moduleName . ' not found!'.PHP_EOL;
        continue;
    }
    foreach ($fieldDetails as $fieldName => $refType)
    {
        $field = Vtiger_Field::getInstance($fieldName, $module);
        if(!$field)
        {
            echo $fieldName . ' not found in ' . $moduleName . '!'.PHP_EOL;
            continue;
        }
        \MoveCrm\AccountingIntegration::setFieldUIType($field->id, $refType[0], $refType[1]);
    }
}

$defaultConfig = [
    'Customer' => [
        'id' => [
            'field' => 'Id',
            'type' => 'int',
            'presence' => 0,
        ],
        'label' => [
            'field' => 'DisplayName',
            'type' => 'string',
            'presence' => 0,
        ],
        'Name' => [
            'field' => 'DisplayName',
            'type' => 'string',
            'presence' => 2,
        ],
        'Address 1' => [
            'field' => 'BillAddr.Line1',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 2,
        ],
        'Address 2' => [
            'field' => 'BillAddr.Line2',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 0,
        ],
        'City' => [
            'field' => 'BillAddr.City',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 0,
        ],
        'State' => [
            'field' => 'BillAddr.CountrySubDivisionCode',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 0,
        ],
        'Zip' => [
            'field' => 'BillAddr.PostalCode',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 0,
        ],
        'Country' => [
            'field' => 'BillAddr.Country',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 0,
        ],
        'Primary Phone' => [
            'field' => 'PrimaryPhone.FreeFormNumber',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 2,
        ],
        'Secondary Phone' => [
            'field' => 'AlternatePhone.FreeFormNumber',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 0,
        ],
        'Fax' => [
            'field' => 'Fax.FreeFormNumber',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 0,
        ],
        'Primary Email' => [
            'field' => 'PrimaryEmailAddr.Address',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 2,
        ],
        'Website' => [
            'field' => 'WebAddr.URI',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 0,
        ],
    ],
    'Vendor' => [
        'id' => [
            'field' => 'Id',
            'type' => 'int',
            'presence' => 0,
        ],
        'label' => [
            'field' => '(##AcctNum##) ##DisplayName##',
            'type' => 'string',
            'presence' => 0,
        ],
        'Account Number' => [
            'field' => 'AcctNum',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 2,
        ],
        'Vendor Name' => [
            'field' => 'DisplayName',
            'type' => 'string',
            'presence' => 2,
        ],
        'First Name' => [
            'field' => 'GivenName',
            'type' => 'string',
            'presence' => 0,
        ],
        'Last Name' => [
            'field' => 'FamilyName',
            'type' => 'string',
            'presence' => 0,
        ],
        'Full Name' => [
            'field' => '##GivenName## ##FamilyName##',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 0,
        ],
        'Address 1' => [
            'field' => 'BillAddr.Line1',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 2,
        ],
        'Address 2' => [
            'field' => 'BillAddr.Line2',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 0,
        ],
        'City' => [
            'field' => 'BillAddr.City',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 0,
        ],
        'State' => [
            'field' => 'BillAddr.CountrySubDivisionCode',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 0,
        ],
        'Zip' => [
            'field' => 'BillAddr.PostalCode',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 0,
        ],
        'Country' => [
            'field' => 'BillAddr.Country',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 0,
        ],
        'Primary Phone' => [
            'field' => 'PrimaryPhone.FreeFormNumber',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 2,
        ],
        'Secondary Phone' => [
            'field' => 'AlternatePhone.FreeFormNumber',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 0,
        ],
        'Mobile Phone' => [
            'field' => 'Mobile.FreeFormNumber',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 0,
        ],
        'Primary Email' => [
            'field' => 'PrimaryEmailAddr.Address',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 2,
        ],
        'Website' => [
            'field' => 'WebAddr.URI',
            'type' => 'string',
            'queryable' => 'no',
            'presence' => 0,
        ],
    ],
    'Department' => [
        'id' => [
            'field' => 'Id',
            'type' => 'int',
            'presence' => 0,
        ],
        'label' => [
            'field' => 'Name',
            'type' => 'string',
            'presence' => 0,
        ],
        'Name' => [
            'field' => 'Name',
            'type' => 'string',
            'presence' => 2,
        ]
    ],
];

$db->pquery('INSERT INTO vtiger_accountingintegration_config () VALUES ()');
$id = $db->getLastInsertID();

foreach ($defaultConfig as $entityType => $fields)
{
    foreach($fields as $localName => $data)
    {
        $db->pquery('INSERT INTO vtiger_accountingintegration_configinfo (id,entitytype,localname,remotename,datatype,presence,queryable)
                      VALUES (?,?,?,?,?,?,?)',
                    [
                        $id,
                        $entityType,
                        $localName,
                        $data['field'],
                        $data['type'],
                        $data['presence'],
                        $data['queryable'] ?: 'yes',
                    ]);
    }
}

