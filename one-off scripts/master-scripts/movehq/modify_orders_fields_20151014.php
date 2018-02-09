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



require_once 'include/database/PearDatabase.php';

echo '<h2>Invoking `' . basename(__FILE__) . '`</h2>' . \PHP_EOL;
echo '<ul>' . \PHP_EOL;

$db = \PearDatabase::getInstance();

// --------------------------------------------------------------------------

echo '<li>Setting the `presence` for the "OPERATIONS_TAB" `parent` items.' . \PHP_EOL;

$sql = 'UPDATE vtiger_tab SET presence = 0
		WHERE name = "Trips" AND parent = "OPERATIONS_TAB"';

$result = $db->query($sql);

if (!($result instanceof \ADORecordSet_empty)) {
    throw new \UnexpectedValueException;
}

echo '<li>Updated "Trips"', \PHP_EOL;

// --------------------------------------------------------------------------

echo '<li>Setting the `presence` for the "CUSTOMER_SERVICE_TAB" `parent` items.' . \PHP_EOL;

$parent = 'CUSTOMER_SERVICE_TAB';
$names = ['Orders', 'Claims'];

$sql = 'UPDATE vtiger_tab SET presence = 0
		WHERE name = ? AND parent = ?';

$result = $db->query($sql);

foreach ($names as $name) {
    $result = $db->pquery($sql, [$name, $parent]);

    if (!($result instanceof \ADORecordSet_empty)) {
        throw new \UnexpectedValueException;
    }

    echo "<li>Updated \"${name}\"", \PHP_EOL;
}

// --------------------------------------------------------------------------

echo '<li>Setting the `presence` for the "COMPANY_ADMIN_TAB" `parent` items.' . \PHP_EOL;

$name = 'TimeSheets';
$parent = 'COMPANY_ADMIN_TAB';

$sql = 'UPDATE vtiger_tab SET parent = ?
		WHERE name = ?';

$result = $db->pquery($sql, [$parent, $name]);

if (!($result instanceof \ADORecordSet_empty)) { /* || !$result */
    throw new \UnexpectedValueException;
}

echo '<li>Inserted "TimeSheets" into the "COMPANY_ADMIN_TAB" `parent`.', \PHP_EOL;

// --------------------------------------------------------------------------

echo '<li>Setting the `sequence` for the "Orders" field items.' . \PHP_EOL;

$sql = 'SELECT tabid FROM vtiger_tab WHERE name = "Orders"';
$tabid = $db->getOne($sql);

if ($tabid === false || !is_numeric($tabid)) {
    throw new \UnexpectedValueException;
}

$fields = json_decode('{
	"ordersname": {
		"fieldlabel": "Orders Name",
		"presence": 1,
		"sequence": 1,
		"uitype": "2"
	},
	"targetenddate": {
		"fieldlabel": "Target End Date",
		"presence": 2,
		"sequence": 18,
		"uitype": "23"
	},
	"actualenddate": {
		"fieldlabel": "Actual End Date",
		"presence": 2,
		"sequence": 16,
		"uitype": "23"
	},
	"orderstype": {
		"fieldlabel": "Order Type",
		"presence": 1,
		"sequence": 7,
		"uitype": "16"
	},
	"assigned_user_id": {
		"fieldlabel": "Assigned To",
		"presence": 2,
		"sequence": 20,
		"uitype": "53"
	},
	"orders_no": {
		"fieldlabel": "Orders No",
		"presence": 0,
		"sequence": 6,
		"uitype": "1001"
	},
	"orderspriority": {
		"fieldlabel": "Priority",
		"presence": 2,
		"sequence": 10,
		"uitype": "16"
	},
	"progress": {
		"fieldlabel": "Progress",
		"presence": 1,
		"sequence": 27,
		"uitype": "16"
	},
	"orders_fname": {
		"fieldlabel": "LBL_ORDERS_FNAME",
		"presence": 1,
		"sequence": 2,
		"uitype": "1"
	},
	"orders_account": {
		"fieldlabel": "LBL_ORDERS_ACCOUNT",
		"presence": 2,
		"sequence": 5,
		"uitype": "10"
	},
	"orders_contacts": {
		"fieldlabel": "LBL_ORDERS_CONTACTS",
		"presence": 2,
		"sequence": 1,
		"uitype": "10"
	},
	"orders_accounttype": {
		"fieldlabel": "LBL_ORDERS_ACCOUNTTYPE",
		"presence": 2,
		"sequence": 3,
		"uitype": "16"
	},
	"orders_vanlineregnum": {
		"fieldlabel": "LBL_ORDERS_VANLINEREGNUM",
		"presence": 2,
		"sequence": 2,
		"uitype": "1"
	},
	"orders_bolnumber": {
		"fieldlabel": "LBL_ORDERS_BOLNUMBER",
		"presence": 2,
		"sequence": 4,
		"uitype": "1"
	},
	"orders_gblnumber": {
		"fieldlabel": "LBL_ORDERS_GBLNUMBER",
		"presence": 2,
		"sequence": 8,
		"uitype": "1"
	},
	"orders_ponumber": {
		"fieldlabel": "LBL_ORDERS_PONUMBER",
		"presence": 2,
		"sequence": 11,
		"uitype": "1"
	},
	"orders_commodity": {
		"fieldlabel": "LBL_ORDERS_COMMODITY",
		"presence": 2,
		"sequence": 13,
		"uitype": "16"
	},
	"orders_elinehaul": {
		"fieldlabel": "LBL_ORDERS_ELINEHAUL",
		"presence": 2,
		"sequence": 15,
		"uitype": "71"
	},
	"orders_etype": {
		"fieldlabel": "LBL_ORDERS_ETYPE",
		"presence": 2,
		"sequence": 12,
		"uitype": "16"
	},
	"orders_miles": {
		"fieldlabel": "LBL_ORDERS_MILES",
		"presence": 2,
		"sequence": 14,
		"uitype": "7"
	},
	"orders_opportunities": {
		"fieldlabel": "LBL_ORDERS_OPPORTUNITIES",
		"presence": 2,
		"sequence": 22,
		"uitype": "10"
	},
	"sales_person": {
		"fieldlabel": "Sales Person",
		"presence": 2,
		"sequence": 30,
		"uitype": "16"
	}
}');

$sql = 'UPDATE vtiger_field SET fieldlabel = ?, presence = ?, sequence = ?, uitype = ?
		WHERE tabid = ? AND fieldname = ?';

foreach ($fields as $name => $field) {
    $result = $db->pquery($sql, [$field->fieldlabel, $field->presence, $field->sequence, $field->uitype, $tabid, $name]);

    if (!($result instanceof \ADORecordSet_empty)) {
        dump($result);
        throw new \UnexpectedValueException;
    }

    echo "<li>Updated \"${name}\" field parameters.", \PHP_EOL;
}

// --------------------------------------------------------------------------

echo '</ul>' . \PHP_EOL;


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";