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



//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

echo "<br>Begin Sirva modifications to estimates fields<br>";

function newEstLineEntry($name)
{
    $db = PearDatabase::getInstance();
    Vtiger_Utils::ExecuteQuery('UPDATE vtiger_business_line_est_seq SET id = id + 1');
    // Vtiger_Utils::ExecuteQuery('UPDATE vtiger_picklistvalues_seq SET id = id + 1');
    echo "<br>updated sequence tables<br>";
    $result = $db->pquery('SELECT id FROM `vtiger_business_line_est_seq`', array());
    $row = $result->fetchRow();
    $businessLineId = $row[0];
    echo "<br>est biz line id set: ".$businessLineId."<br>";
    $result = $db->pquery('SELECT COUNT(*) FROM `vtiger_business_line_est`', array());
    $row = $result->fetchRow();
    $sequence = $row[0]++;
    echo "<br>new sequence set: ".$sequence."<br>";
    $sql = 'INSERT INTO `vtiger_business_line_est` (business_line_estid, business_line_est, sortorderid, presence) VALUES (?, ?, ?, 1)';
    $db->pquery($sql, array($businessLineId, $name, $sequence));
}

$db = PearDatabase::getInstance();

$estModule = Vtiger_Module::getInstance('Estimates');

if ($estModule) {
    echo "<br>Est module exists! Modifying fields.<br>";
    $estInfo = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $estModule);
    if ($estInfo) {
        //create field for move type
        $moveType = Vtiger_Field::getInstance('move_type', $estModule);
        if ($moveType) {
            echo "<br> Field 'move_type' is already present. <br>";
        } else {
            echo "<br> Field 'move_type' not present. Creating it now<br>";
            $moveType = new Vtiger_Field();
            $moveType->label = 'LBL_QUOTES_MOVETYPE';
            $moveType->name = 'move_type';
            $moveType->table = 'vtiger_quotes';
            $moveType->column = 'move_type';
            $moveType->columntype = 'VARCHAR(255)';
            $moveType->uitype = 16;
            $moveType->typeofdata = 'V~M';
            $moveType->quickcreate = 0;

            $estInfo->addField($moveType);
            $moveType->setPicklistValues(array('Interstate', 'Intrastate', 'O&I', 'Local Canada', 'Local US', 'Interstate', 'Inter-Provincial', 'Intra-Provincial', 'Cross Border', 'Alaska', 'Hawaii', 'International'));
            echo "<br> Field 'move_type' added.<br>";
        }
    } else {
        echo "<br>LBL_QUOTE_INFORMATION NOT FOUND! NO ACTION TAKEN!<br>";
    }
    
    $estAcs = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $estModule);
    if ($estAcs) {
        //create field for move type
        $truckload = Vtiger_Field::getInstance('express_truckload', $estModule);
        if ($truckload) {
            echo "<br> Field 'express_truckload' is already present. <br>";
        } else {
            echo "<br> Field 'express_truckload' not present. Creating it now<br>";
            $truckload = new Vtiger_Field();
            $truckload->label = 'LBL_QUOTES_EXPRESSTRUCKLOAD';
            $truckload->name = 'express_truckload';
            $truckload->table = 'vtiger_quotes';
            $truckload->column = 'express_truckload';
            $truckload->columntype = 'VARCHAR(3)';
            $truckload->uitype = 56;
            $truckload->typeofdata = 'V~O';
            $truckload->quickcreate = 0;

            $estAcs->addField($truckload);
            echo "<br> Field 'express_truckload' added.<br>";
        }
        //create field for consumption fuel
        $consumptionFuel = Vtiger_Field::getInstance('consumption_fuel', $estModule);
        if ($consumptionFuel) {
            echo "<br> Field 'consumption_fuel' is already present. <br>";
        } else {
            echo "<br> Field 'consumption_fuel' not present. Creating it now<br>";
            $consumptionFuel = new Vtiger_Field();
            $consumptionFuel->label = 'LBL_QUOTES_CONSUMPTIONFUEL';
            $consumptionFuel->name = 'consumption_fuel';
            $consumptionFuel->table = 'vtiger_quotes';
            $consumptionFuel->column = 'consumption_fuel';
            $consumptionFuel->columntype = 'VARCHAR(3)';
            $consumptionFuel->uitype = 56;
            $consumptionFuel->typeofdata = 'V~O';
            $consumptionFuel->quickcreate = 0;

            $estAcs->addField($consumptionFuel);
            echo "<br> Field 'consumption_fuel' added.<br>";
        }
    } else {
        echo "<br>LBL_QUOTES_ACCESSORIALDETAILS NOT FOUND! NO ACTION TAKEN!<br>";
    }
} else {
    echo "<br>EST MODULE DOESN'T EXIST! FIELDS NOT MODIFIED<br>";
}

$quotesModule = Vtiger_Module::getInstance('Quotes');

if ($quotesModule) {
    echo "<br>Quote module exists! Modifying fields.<br>";
    $quoteInfo = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $quotesModule);
    if ($quoteInfo) {
        //create field for move type
        $moveType = Vtiger_Field::getInstance('move_type', $quotesModule);
        if ($moveType) {
            echo "<br> Field 'move_type' is already present. <br>";
        } else {
            echo "<br> Field 'move_type' not present. Creating it now<br>";
            $moveType = new Vtiger_Field();
            $moveType->label = 'LBL_QUOTES_MOVETYPE';
            $moveType->name = 'move_type';
            $moveType->table = 'vtiger_quotes';
            $moveType->column = 'move_type';
            $moveType->columntype = 'VARCHAR(255)';
            $moveType->uitype = 16;
            $moveType->typeofdata = 'V~M';
            $moveType->quickcreate = 0;

            $quoteInfo->addField($moveType);
            $moveType->setPicklistValues(array('Interstate', 'Intrastate', 'O&I', 'Local Canada', 'Local US', 'Interstate', 'Inter-Provincial', 'Intra-Provincial', 'Cross Border', 'Alaska', 'Hawaii', 'International'));
            echo "<br> Field 'move_type' added.<br>";
        }
    } else {
        echo "<br>LBL_QUOTE_INFORMATION NOT FOUND! NO ACTION TAKEN!<br>";
    }
    
    $quoteAcs = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $quotesModule);
    if ($quoteAcs) {
        //create field for express trucking
        $truckload = Vtiger_Field::getInstance('express_truckload', $quotesModule);
        if ($truckload) {
            echo "<br> Field 'express_truckload' is already present. <br>";
        } else {
            echo "<br> Field 'express_truckload' not present. Creating it now<br>";
            $truckload = new Vtiger_Field();
            $truckload->label = 'LBL_QUOTES_EXPRESSTRUCKLOAD';
            $truckload->name = 'express_truckload';
            $truckload->table = 'vtiger_quotes';
            $truckload->column = 'express_truckload';
            $truckload->columntype = 'VARCHAR(3)';
            $truckload->uitype = 56;
            $truckload->typeofdata = 'V~O';
            $truckload->quickcreate = 0;

            $quoteAcs->addField($truckload);
            echo "<br> Field 'express_truckload' added.<br>";
        }
        //create field for consumption fuel
        $consumptionFuel = Vtiger_Field::getInstance('consumption_fuel', $quotesModule);
        if ($consumptionFuel) {
            echo "<br> Field 'consumption_fuel' is already present. <br>";
        } else {
            echo "<br> Field 'consumption_fuel' not present. Creating it now<br>";
            $consumptionFuel = new Vtiger_Field();
            $consumptionFuel->label = 'LBL_QUOTES_CONSUMPTIONFUEL';
            $consumptionFuel->name = 'consumption_fuel';
            $consumptionFuel->table = 'vtiger_quotes';
            $consumptionFuel->column = 'consumption_fuel';
            $consumptionFuel->columntype = 'VARCHAR(3)';
            $consumptionFuel->uitype = 56;
            $consumptionFuel->typeofdata = 'V~O';
            $consumptionFuel->quickcreate = 0;

            $quoteAcs->addField($consumptionFuel);
            echo "<br> Field 'consumption_fuel' added.<br>";
        }
    } else {
        echo "<br>LBL_QUOTES_ACCESSORIALDETAILS NOT FOUND! NO ACTION TAKEN!<br>";
    }
} else {
    echo "<br>Quotes MODULE DOESN'T EXIST! FIELDS NOT MODIFIED<br>";
}

$result = $db->pquery('SELECT * FROM `vtiger_business_line_est` WHERE business_line_est = ?', array('International Move'));
$row = $result->fetchRow();

if (!$row) {
    newEstLineEntry('International Move');
} else {
    echo "International picklist option already exists <br>";
}

$result = $db->pquery('SELECT * FROM `vtiger_business_line_est` WHERE business_line_est = ?', array('Intrastate Move'));
$row = $result->fetchRow();

if (!$row) {
    newEstLineEntry('Intrastate Move');
} else {
    echo "Intrastate picklist option already exists <br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";