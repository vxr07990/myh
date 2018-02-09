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

echo "<br>Begin Sirva modifications to leads fields<br>";

$db = PearDatabase::getInstance();

function newLeadSourceEntry($name, $sequence)
{
    $db = PearDatabase::getInstance();
    Vtiger_Utils::ExecuteQuery('UPDATE vtiger_leadsource_seq SET id = id + 1');
    Vtiger_Utils::ExecuteQuery('UPDATE vtiger_picklistvalues_seq SET id = id + 1');
    echo "<br>updated sequence tables<br>";
    $result = $db->pquery('SELECT id FROM `vtiger_leadsource_seq`', array());
    $row = $result->fetchRow();
    $leadSourceId = $row[0];
    echo "<br>lead source id set: ".$leadSourceId."<br>";
    $result = $db->pquery('SELECT id FROM `vtiger_picklistvalues_seq`', array());
    $row = $result->fetchRow();
    $picklistValueId = $row[0];
    echo "<br>picklist value id set: ".$picklistValueId."<br>";
    $sql = 'INSERT INTO `vtiger_leadsource` (leadsourceid, leadsource, presence, picklist_valueid, sortorderid) VALUES (?, ?, 1, ?, ?)';
    $db->pquery($sql, array($leadSourceId, $name, $picklistValueId, $sequence));
}

$leadsModule = Vtiger_Module::getInstance('Leads');
if ($leadsModule) {
    //modifications to information block
    $leadsInfo = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $leadsModule);
    if ($leadsInfo) {
        echo "<br> block 'LBL_LEADS_INFORMATION' exists<br>";
        $leadFirstName = Vtiger_Field::getInstance('firstname', $leadsModule);
        if ($leadFirstName) {
            echo "<br>lead firstname exists converting to mandatory<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'V~M' WHERE fieldlabel = 'LBL_LEADS_FIRSTNAME'");
            echo "<br>lead firstname mandatory swap done<br>";
        }

        $leadSource = Vtiger_Field::getInstance('leadsource', $leadsModule);
        if ($leadSource) {
            $newSources = ['Affinity', 'Interactive', 'Lead Buying', 'Other', 'Referrals', 'Telephone'];
            if (Vtiger_Utils::CheckTable('vtiger_leadsource')) {
                echo "<br>vtiger_leadsource exists! Truncating...<br>";
                Vtiger_Utils::ExecuteQuery('TRUNCATE TABLE `vtiger_leadsource`');
                echo "<br>completed truncating...adding Sirva specific lead sources<br>";
                foreach ($newSources as $index => $status) {
                    echo "<br> adding status: ".$status;
                    newLeadSourceEntry($status, $index+1);
                }
            } else {
                echo "<br>vtiger_leadsource not found! No action taken<br>";
            }
            echo "<br>leadsource exists continuing with label swap<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_MARKETINGCHANNEL' WHERE fieldlabel = 'LBL_LEADS_LEADSOURCE'");
            echo "<br>leadsource label swap done<br>";
        }

        //create field for move type
        $moveType = Vtiger_Field::getInstance('move_type', $leadsModule);
        if ($moveType) {
            echo "<br> Field 'move_type' is already present. <br>";
        } else {
            echo "<br> Field 'move_type' not present. Creating it now<br>";
            $moveType = new Vtiger_Field();
            $moveType->label = 'LBL_LEADS_MOVETYPE';
            $moveType->name = 'move_type';
            $moveType->table = 'vtiger_leadscf';
            $moveType->column = 'move_type';
            $moveType->columntype = 'VARCHAR(255)';
            $moveType->uitype = 16;
            $moveType->typeofdata = 'V~M';
            $moveType->quickcreate = 0;

            $leadsInfo->addField($moveType);
            $moveType->setPicklistValues(array('Interstate', 'Intrastate', 'O&I', 'Local Canada', 'Local US', 'Interstate', 'Inter-Provincial', 'Intra-Provincial', 'Cross Border', 'Alaska', 'Hawaii', 'International'));
            echo "<br> Field 'move_type' added.<br>";
        }

        //create field for business channel
        $businessChannel = Vtiger_Field::getInstance('business_channel', $leadsModule);
        if ($businessChannel) {
            echo "<br> Field 'business_channel' is already present. <br>";
        } else {
            echo "<br> Field 'business_channel' not present. Creating it now<br>";
            $businessChannel = new Vtiger_Field();
            $businessChannel->label = 'LBL_LEADS_BUSINESSCHANNEL';
            $businessChannel->name = 'business_channel';
            $businessChannel->table = 'vtiger_leadscf';
            $businessChannel->column = 'business_channel';
            $businessChannel->columntype = 'VARCHAR(255)';
            $businessChannel->uitype = 16;
            $businessChannel->typeofdata = 'V~O';
            $businessChannel->quickcreate = 0;

            $leadsInfo->addField($businessChannel);
            $businessChannel->setPicklistValues(array('Consumer', 'Corporate', 'Military', 'Government'));
            echo "<br> Field 'business_channel' added.<br>";
        }

        //create checkbox for funded
        $funded = Vtiger_Field::getInstance('funded', $leadsModule);
        if ($funded) {
            echo "<br> Field 'funded' is already present. <br>";
        } else {
            echo "<br> Field 'funded' not present. Creating it now<br>";
            $funded = new Vtiger_Field();
            $funded->label = 'LBL_LEADS_FUNDED';
            $funded->name = 'funded';
            $funded->table = 'vtiger_leadscf';
            $funded->column = 'funded';
            $funded->columntype = 'VARCHAR(255)';
            $funded->uitype = 1;
            $funded->typeofdata = 'V~O';
            $funded->quickcreate = 0;

            $leadsInfo->addField($funded);
            echo "<br> Field 'funded' added.<br>";
        }

        //create checkbox for out_of_area (non conforming field)
        $outOfArea = Vtiger_Field::getInstance('out_of_area', $leadsModule);
        if ($outOfArea) {
            echo "<br> Field 'out_of_area' is already present. <br>";
        } else {
            echo "<br> Field 'out_of_area' not present. Creating it now<br>";
            $outOfArea = new Vtiger_Field();
            $outOfArea->label = 'LBL_LEADS_OUTOFAREA';
            $outOfArea->name = 'out_of_area';
            $outOfArea->table = 'vtiger_leadscf';
            $outOfArea->column = 'out_of_area';
            $outOfArea->columntype = 'VARCHAR(3)';
            $outOfArea->uitype = 56;
            $outOfArea->typeofdata = 'V~O';
            $outOfArea->quickcreate = 0;

            $leadsInfo->addField($outOfArea);
            echo "<br> Field 'out_of_area' added.<br>";
        }

        //create checkbox for out_of_origin (non conforming field)
        $outOfOrigin = Vtiger_Field::getInstance('out_of_origin', $leadsModule);
        if ($outOfOrigin) {
            echo "<br> Field 'out_of_origin' is already present. <br>";
        } else {
            echo "<br> Field 'out_of_origin' not present. Creating it now<br>";
            $outOfOrigin = new Vtiger_Field();
            $outOfOrigin->label = 'LBL_LEADS_OUTOFORIGIN';
            $outOfOrigin->name = 'out_of_origin';
            $outOfOrigin->table = 'vtiger_leadscf';
            $outOfOrigin->column = 'out_of_origin';
            $outOfOrigin->columntype = 'VARCHAR(3)';
            $outOfOrigin->uitype = 56;
            $outOfOrigin->typeofdata = 'V~O';
            $outOfOrigin->quickcreate = 0;

            $leadsInfo->addField($outOfOrigin);
            echo "<br> Field 'out_of_origin' added.<br>";
        }

        //create checkbox for small_move (non conforming field)
        $smallMove = Vtiger_Field::getInstance('small_move', $leadsModule);
        if ($smallMove) {
            echo "<br> Field 'small_move' is already present. <br>";
        } else {
            echo "<br> Field 'small_move' not present. Creating it now<br>";
            $smallMove = new Vtiger_Field();
            $smallMove->label = 'LBL_LEADS_SMALLMOVE';
            $smallMove->name = 'small_move';
            $smallMove->table = 'vtiger_leadscf';
            $smallMove->column = 'small_move';
            $smallMove->columntype = 'VARCHAR(3)';
            $smallMove->uitype = 56;
            $smallMove->typeofdata = 'V~O';
            $smallMove->quickcreate = 0;

            $leadsInfo->addField($smallMove);
            echo "<br> Field 'small_move' added.<br>";
        }

        //create checkbox for phone_estimate (non conforming field)
        $phoneEstimate = Vtiger_Field::getInstance('phone_estimate', $leadsModule);
        if ($phoneEstimate) {
            echo "<br> Field 'phone_estimate' is already present. <br>";
        } else {
            echo "<br> Field 'phone_estimate' not present. Creating it now<br>";
            $phoneEstimate = new Vtiger_Field();
            $phoneEstimate->label = 'LBL_LEADS_PHONEESTIMATE';
            $phoneEstimate->name = 'phone_estimate';
            $phoneEstimate->table = 'vtiger_leadscf';
            $phoneEstimate->column = 'phone_estimate';
            $phoneEstimate->columntype = 'VARCHAR(3)';
            $phoneEstimate->uitype = 56;
            $phoneEstimate->typeofdata = 'V~O';
            $phoneEstimate->quickcreate = 0;

            $leadsInfo->addField($phoneEstimate);
            echo "<br> Field 'phone_estimate' added.<br>";
        }
    } else {
        echo "<br><h1>LBL_LEADS_INFORMATION NOT FOUND</h1><br>";
    }

    //modifications to address block
    $leadsAddr = Vtiger_Block::getInstance('LBL_LEADS_ADDRESSINFORMATION', $leadsModule);
    if ($leadsAddr) {
        $missingFields = 0;
        echo "<br> block 'LBL_LEADS_ADDRESSINFORMATION' exists<br>";
        //field for origin fax num
        $originFaxNum = Vtiger_Field::getInstance('origin_fax', $leadsModule);
        if ($originFaxNum) {
            echo "<br> Field destination_fax already exists<br>";
        } else {
            $missingFields++;
            echo "<br> Field 'origin_fax' not present. Creating it now<br>";
            $originFaxNum = new Vtiger_Field();
            $originFaxNum->label = 'LBL_LEADS_ORIGINFAX';
            $originFaxNum->name = 'origin_fax';
            $originFaxNum->table = 'vtiger_leadscf';
            $originFaxNum->column = 'origin_fax';
            $originFaxNum->columntype = 'VARCHAR(255)';
            $originFaxNum->uitype = 1;
            $originFaxNum->typeofdata = 'V~O';
            $originFaxNum->quickcreate = 0;
            $leadsAddr->addField($originFaxNum);
            echo "<br> Field 'origin_fax' added.<br>";
        }

        //field for destinaiton fax num
        $destFaxNum = Vtiger_Field::getInstance('destination_fax', $leadsModule);
        if ($destFaxNum) {
            echo "<br> Field destination_fax already exists<br>";
        } else {
            $missingFields++;
            echo "<br> Field 'destination_fax' not present. Creating it now<br>";
            $destFaxNum = new Vtiger_Field();
            $destFaxNum->label = 'LBL_LEADS_DESTINATIONFAX';
            $destFaxNum->name = 'destination_fax';
            $destFaxNum->table = 'vtiger_leadscf';
            $destFaxNum->column = 'destination_fax';
            $destFaxNum->columntype = 'VARCHAR(255)';
            $destFaxNum->uitype = 1;
            $destFaxNum->typeofdata = 'V~O';
            $destFaxNum->quickcreate = 0;
            $leadsAddr->addField($destFaxNum);
            echo "<br> Field 'destination_fax' added.<br>";
        }

        //phone type field for primary phone type
        $phoneTypePrimary = Vtiger_Field::getInstance('primary_phone_type', $leadsModule);
        if ($phoneTypePrimary) {
            echo "<br> Field 'primary_phone_type' is already present. <br>";
        } else {
            $missingFields++;
            echo "<br> Field 'primary_phone_type' not present. Creating it now<br>";
            $phoneTypePrimary = new Vtiger_Field();
            $phoneTypePrimary->label = 'LBL_LEADS_PRIMARYPHONETYPE';
            $phoneTypePrimary->name = 'primary_phone_type';
            $phoneTypePrimary->table = 'vtiger_leadscf';
            $phoneTypePrimary->column = 'primary_phone_type';
            $phoneTypePrimary->columntype = 'VARCHAR(255)';
            $phoneTypePrimary->uitype = 16;
            $phoneTypePrimary->typeofdata = 'V~O';
            $phoneTypePrimary->quickcreate = 0;

            $leadsInfo->addField($phoneTypePrimary);

            $phoneTypePrimary->setPicklistValues(array('Home', 'Work', 'Cell'));
            echo "<br> Field 'primary_phone_type' added.<br>";
        }

        //phone type field for origin phone 1
        $phoneType1 = Vtiger_Field::getInstance('origin_phone1_type', $leadsModule);
        if ($phoneType1) {
            echo "<br> Field 'origin_phone1_type' is already present. <br>";
        } else {
            $missingFields++;
            echo "<br> Field 'origin_phone1_type' not present. Creating it now<br>";
            $phoneType1 = new Vtiger_Field();
            $phoneType1->label = 'LBL_LEADS_ORIGINPHONE1TYPE';
            $phoneType1->name = 'origin_phone1_type';
            $phoneType1->table = 'vtiger_leadscf';
            $phoneType1->column = 'origin_phone1_type';
            $phoneType1->columntype = 'VARCHAR(255)';
            $phoneType1->uitype = 16;
            $phoneType1->typeofdata = 'V~O';
            $phoneType1->quickcreate = 0;

            $leadsAddr->addField($phoneType1);

            $phoneType1->setPicklistValues(array('Home', 'Work', 'Cell'));
            echo "<br> Field 'origin_phone1_type' added.<br>";
        }

        //phone type field for origin phone 2
        $phoneType2 = Vtiger_Field::getInstance('origin_phone2_type', $leadsModule);
        if ($phoneType2) {
            echo "<br> Field 'origin_phone2_type' is already present. <br>";
        } else {
            $missingFields++;
            echo "<br> Field 'origin_phone2_type' not present. Creating it now<br>";
            $phoneType2 = new Vtiger_Field();
            $phoneType2->label = 'LBL_LEADS_ORIGINPHONE2TYPE';
            $phoneType2->name = 'origin_phone2_type';
            $phoneType2->table = 'vtiger_leadscf';
            $phoneType2->column = 'origin_phone2_type';
            $phoneType2->columntype = 'VARCHAR(255)';
            $phoneType2->uitype = 16;
            $phoneType2->typeofdata = 'V~O';
            $phoneType2->quickcreate = 0;

            $leadsAddr->addField($phoneType2);
            $phoneType2->setPicklistValues(array('Home', 'Work', 'Cell'));
            echo "<br> Field 'origin_phone2_type' added.<br>";
        }

        //phone type field for destination phone 1
        $phoneType3 = Vtiger_Field::getInstance('destination_phone1_type', $leadsModule);
        if ($phoneType3) {
            echo "<br> Field 'destination_phone1_type' is already present. <br>";
        } else {
            $missingFields++;
            echo "<br> Field 'destination_phone1_type' not present. Creating it now<br>";
            $phoneType3 = new Vtiger_Field();
            $phoneType3->label = 'LBL_LEADS_DESTINATIONPHONE1TYPE';
            $phoneType3->name = 'destination_phone1_type';
            $phoneType3->table = 'vtiger_leadscf';
            $phoneType3->column = 'destination_phone1_type';
            $phoneType3->columntype = 'VARCHAR(255)';
            $phoneType3->uitype = 16;
            $phoneType3->typeofdata = 'V~O';
            $phoneType3->quickcreate = 0;

            $leadsAddr->addField($phoneType3);
            $phoneType3->setPicklistValues(array('Home', 'Work', 'Cell'));
            echo "<br> Field 'destination_phone1_type' added.<br>";
        }

        //phone type field for destination phone 2
        $phoneType4 = Vtiger_Field::getInstance('destination_phone2_type', $leadsModule);
        if ($phoneType4) {
            echo "<br> Field 'destination_phone2_type' is already present. <br>";
        } else {
            $missingFields++;
            echo "<br> Field 'destination_phone2_type' not present. Creating it now<br>";
            $phoneType4 = new Vtiger_Field();
            $phoneType4->label = 'LBL_LEADS_DESTINATIONPHONE2TYPE';
            $phoneType4->name = 'destination_phone2_type';
            $phoneType4->table = 'vtiger_leadscf';
            $phoneType4->column = 'destination_phone2_type';
            $phoneType4->columntype = 'VARCHAR(255)';
            $phoneType4->uitype = 16;
            $phoneType4->typeofdata = 'V~O';
            $phoneType4->quickcreate = 0;

            $leadsAddr->addField($phoneType4);
            $phoneType4->setPicklistValues(array('Home', 'Work', 'Cell'));
            echo "<br> Field 'destination_phone2_type' added.<br>";
        }

        echo "<br> Field creation complete, resequencing.<br>";
        //resequence fields for leads address
        //start by grabbing block ID
        $blockId = $leadsAddr->id;

        //then grab all the block's fields (except for the new ones)in order of sequence
        $addressFields = array();
        $sql = "SELECT fieldlabel, fieldname FROM vtiger_field WHERE block = ? AND fieldlabel != 'LBL_LEADS_ORIGINFAX' AND fieldlabel != 'LBL_LEADS_DESTINATIONFAX' AND fieldlabel != 'LBL_LEADS_ORIGINPHONE1TYPE' AND fieldlabel != 'LBL_LEADS_ORIGINPHONE2TYPE' AND fieldlabel != 'LBL_LEADS_DESTINATIONPHONE1TYPE' AND fieldlabel != 'LBL_LEADS_DESTINATIONPHONE2TYPE' ORDER BY sequence ASC";
        $result = $db->pquery($sql, array($blockId));
        $row = $result->fetchRow();

        while ($row != null) {
            $addressFields[] = array($row[0], $row[1]);
            $row = $result->fetchRow();
        }

        //this section inserts the new fields to their appropriate locations relative to the old fields
        $index = array_search(array('LBL_LEADS_ORIGINPHONE2', 'origin_phone2'), $addressFields);

        array_splice($addressFields, $index+2, 0, array(array('LBL_LEADS_ORIGINFAX', 'origin_fax')));

        $index = array_search(array('LBL_LEADS_DESTINATIONPHONE2', 'destination_phone2'), $addressFields);

        array_splice($addressFields, $index+2, 0, array(array('LBL_LEADS_DESTINATIONFAX', 'destination_fax')));

        $index = array_search(array('LBL_LEADS_ORIGINPHONE1', 'origin_phone1'), $addressFields);

        array_splice($addressFields, $index+2, 0, array(array('LBL_LEADS_ORIGINPHONE1TYPE', 'origin_phone1_type')));

        $index = array_search(array('LBL_LEADS_DESTINATIONPHONE1', 'destination_phone1'), $addressFields);

        array_splice($addressFields, $index+2, 0, array(array('LBL_LEADS_DESTINATIONPHONE1TYPE', 'destination_phone1_type')));

        $index = array_search(array('LBL_LEADS_ORIGINPHONE2', 'origin_phone2'), $addressFields);

        array_splice($addressFields, $index+2, 0, array(array('LBL_LEADS_ORIGINPHONE2TYPE', 'origin_phone2_type')));

        $index = array_search(array('LBL_LEADS_DESTINATIONPHONE2', 'destination_phone2'), $addressFields);

        array_splice($addressFields, $index+2, 0, array(array('LBL_LEADS_DESTINATIONPHONE2TYPE', 'destination_phone2_type')));

        $index = array_search(array('LBL_LEADS_PHONENUMBER', 'phone'), $addressFields);

        array_splice($addressFields, $index+2, 0, array(array('LBL_LEADS_PRIMARYPHONETYPE', 'primary_phone_type')));

        //now the finished array is used to resequence all the block's fields
        foreach ($addressFields as $key => $field) {
            $sql = "UPDATE `vtiger_field` SET sequence = ? WHERE fieldlabel = ? AND fieldname = ? AND (tablename='vtiger_leaddetails' OR tablename='vtiger_leadscf')";
            //file_put_contents('logs/devLog.log', "\n SQL: $sql KEY: $key LABEL: ".$field[0]." NAME: ".$field[1], FILE_APPEND);
            $result = $db->pquery($sql, array($key, $field[0], $field[1]));
        }

        //resequence fields for leads info
        //start by grabbing block ID
        $blockIdInfo = $leadsInfo->id;
        $infoFields = array();
        $sql = "SELECT fieldlabel, fieldname FROM vtiger_field WHERE block = ? AND fieldlabel != 'LBL_LEADS_PRIMARYPHONETYPE' ORDER BY sequence ASC";
        $result = $db->pquery($sql, array($blockIdInfo));
        $row = $result->fetchRow();

        while ($row != null) {
            $infoFields[] = array($row[0], $row[1]);
            $row = $result->fetchRow();
        }

        //this section inserts the new fields to their appropriate locations relative to the old fields
        $indexInfo = array_search(array('LBL_LEADS_PHONENUMBER', 'phone'), $infoFields);

        array_splice($infoFields, $indexInfo+2, 0, array(array('LBL_LEADS_PRIMARYPHONETYPE', 'primary_phone_type')));

        //echo "<!-- ".print_r($infoFields, true)." -->";

        foreach ($infoFields as $key => $field) {
            //echo "<br><h1>UPDATE `vtiger_field` SET sequence = {$key} WHERE fieldlabel = '{$field[0]}' AND fieldname = '{$field[1]}' AND block = {$blockIdInfo}</h1><br>";
            $sql = "UPDATE `vtiger_field` SET sequence = ? WHERE fieldlabel = ? AND fieldname = ? AND block = ?";
            $result = $db->pquery($sql, array($key, $field[0], $field[1], $blockIdInfo));
            //echo print_r($result, true);
        }

        //ext field for primary phone
        $primaryPhoneExt = Vtiger_Field::getInstance('primary_phone_ext', $leadsModule);
        if ($primaryPhoneExt) {
            echo "<br> Field 'primary_phone_ext' is already present. <br>";
        } else {
            echo "<br> Field 'primary_phone_ext' not present. Creating it now<br>";
            $primaryPhoneExt = new Vtiger_Field();
            $primaryPhoneExt->label = 'LBL_LEADS_PRIMARYPHONEEXT';
            $primaryPhoneExt->name = 'primary_phone_ext';
            $primaryPhoneExt->table = 'vtiger_leadscf';
            $primaryPhoneExt->column = 'primary_phone_ext';
            $primaryPhoneExt->columntype = 'VARCHAR(255)';
            $primaryPhoneExt->uitype = 1;
            $primaryPhoneExt->typeofdata = 'V~O';
            $primaryPhoneExt->quickcreate = 0;

            $leadsInfo->addField($primaryPhoneExt);

            echo "<br> Field 'primary_phone_ext' added.<br>";
        }

        //ext field for origin phone 1
        $phoneExt1 = Vtiger_Field::getInstance('origin_phone1_ext', $leadsModule);
        if ($phoneExt1) {
            echo "<br> Field 'origin_phone1_ext' is already present. <br>";
        } else {
            echo "<br> Field 'origin_phone1_ext' not present. Creating it now<br>";
            $phoneExt1 = new Vtiger_Field();
            $phoneExt1->label = 'LBL_LEADS_ORIGINPHONE1EXT';
            $phoneExt1->name = 'origin_phone1_ext';
            $phoneExt1->table = 'vtiger_leadscf';
            $phoneExt1->column = 'origin_phone1_ext';
            $phoneExt1->columntype = 'VARCHAR(255)';
            $phoneExt1->uitype = 1;
            $phoneExt1->typeofdata = 'V~O';
            $phoneExt1->quickcreate = 0;

            $leadsAddr->addField($phoneExt1);

            echo "<br> Field 'origin_phone1_ext' added.<br>";
        }

        //ext field for origin phone 2
        $phoneExt2 = Vtiger_Field::getInstance('origin_phone2_ext', $leadsModule);
        if ($phoneExt2) {
            echo "<br> Field 'origin_phone2_ext' is already present. <br>";
        } else {
            echo "<br> Field 'origin_phone2_ext' not present. Creating it now<br>";
            $phoneExt2 = new Vtiger_Field();
            $phoneExt2->label = 'LBL_LEADS_ORIGINPHONE2EXT';
            $phoneExt2->name = 'origin_phone2_ext';
            $phoneExt2->table = 'vtiger_leadscf';
            $phoneExt2->column = 'origin_phone2_ext';
            $phoneExt2->columntype = 'VARCHAR(255)';
            $phoneExt2->uitype = 1;
            $phoneExt2->typeofdata = 'V~O';
            $phoneExt2->quickcreate = 0;

            $leadsAddr->addField($phoneExt2);
            echo "<br> Field 'origin_phone2_ext' added.<br>";
        }

        //ext field for destination phone 1
        $phoneExt3 = Vtiger_Field::getInstance('destination_phone1_ext', $leadsModule);
        if ($phoneExt3) {
            echo "<br> Field 'destination_phone1_ext' is already present. <br>";
        } else {
            echo "<br> Field 'destination_phone1_ext' not present. Creating it now<br>";
            $phoneExt3 = new Vtiger_Field();
            $phoneExt3->label = 'LBL_LEADS_DESTINATIONPHONE1EXT';
            $phoneExt3->name = 'destination_phone1_ext';
            $phoneExt3->table = 'vtiger_leadscf';
            $phoneExt3->column = 'destination_phone1_ext';
            $phoneExt3->columntype = 'VARCHAR(255)';
            $phoneExt3->uitype = 1;
            $phoneExt3->typeofdata = 'V~O';
            $phoneExt3->quickcreate = 0;

            $leadsAddr->addField($phoneExt3);
            echo "<br> Field 'destination_phone1_ext' added.<br>";
        }

        //ext field for destination phone 2
        $phoneExt4 = Vtiger_Field::getInstance('destination_phone2_ext', $leadsModule);
        if ($phoneExt4) {
            echo "<br> Field 'destination_phone2_ext' is already present. <br>";
        } else {
            echo "<br> Field 'destination_phone2_ext' not present. Creating it now<br>";
            $phoneExt4 = new Vtiger_Field();
            $phoneExt4->label = 'LBL_LEADS_DESTINATIONPHONE2EXT';
            $phoneExt4->name = 'destination_phone2_ext';
            $phoneExt4->table = 'vtiger_leadscf';
            $phoneExt4->column = 'destination_phone2_ext';
            $phoneExt4->columntype = 'VARCHAR(255)';
            $phoneExt4->uitype = 1;
            $phoneExt4->typeofdata = 'V~O';
            $phoneExt4->quickcreate = 0;

            $leadsAddr->addField($phoneExt4);
            echo "<br> Field 'destination_phone2_ext' added.<br>";
        }

        $leadOriginAddress = Vtiger_Field::getInstance('origin_address1', $leadsModule);
        if ($leadOriginAddress) {
            echo "<br>lead origin_address1 exists converting to mandatory<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'V~M~LE~50' WHERE fieldlabel = 'LBL_LEADS_ORIGINADDRESS1'");
            echo "<br>lead origin_address1 mandatory swap done<br>";
        }

        $leadDestinationAddress = Vtiger_Field::getInstance('destination_address1', $leadsModule);
        if ($leadDestinationAddress) {
            echo "<br>lead destination_address1 exists converting to mandatory<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'V~M~LE~50', defaultvalue = 'Will Advise' WHERE fieldlabel = 'LBL_LEADS_DESTINATIONADDRESS1'");
            echo "<br>lead destination_address1 mandatory swap done<br>";
        }

        $leadOriginCountry = Vtiger_Field::getInstance('origin_country', $leadsModule);
        if ($leadOriginCountry) {
            echo "<br>lead origin_country exists converting to mandatory<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'V~M' WHERE fieldlabel = 'LBL_LEADS_ORIGINCOUNTRY'");
            echo "<br>lead origin_country mandatory swap done<br>";
        }

        $leadOriginCity = Vtiger_Field::getInstance('origin_city', $leadsModule);
        if ($leadOriginCity) {
            echo "<br>lead origin_city exists converting to mandatory<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'V~M' WHERE fieldlabel = 'LBL_LEADS_ORIGINCITY'");
            echo "<br>lead origin_city mandatory swap done<br>";
        }

        $leadOriginState = Vtiger_Field::getInstance('origin_state', $leadsModule);
        if ($leadOriginState) {
            echo "<br>lead origin_city exists converting to mandatory<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'V~M' WHERE fieldlabel = 'LBL_LEADS_ORIGINSTATE'");
            echo "<br>lead origin_city mandatory swap done<br>";
        }

        $leadOriginZip = Vtiger_Field::getInstance('origin_zip', $leadsModule);
        if ($leadOriginZip) {
            echo "<br>lead origin_zip exists converting to mandatory<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'V~M' WHERE fieldlabel = 'LBL_LEADS_ORIGINZIP'");
            echo "<br>lead origin_zip mandatory swap done<br>";
        }

        $result = $db->pquery("SELECT uitype FROM `vtiger_field` WHERE fieldlabel = 'LBL_LEADS_ORIGINCOUNTRY'", array());
        $row = $result->fetchRow();
        $originCountryUIType = $row[0];

        if ($originCountryUIType != 16) {
            $leadOriginCountry = Vtiger_Field::getInstance('origin_country', $leadsModule);
            if ($leadOriginCountry) {
                echo "<br>lead origin_country exists converting to picklist<br>";
                Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET uitype = '16' WHERE fieldlabel = 'LBL_LEADS_ORIGINCOUNTRY'");
                $leadOriginCountry->setPicklistValues(array('United States', 'Canada'));
                echo "<br>lead origin_country picklist conversion done<br>";
            }
        } else {
            echo "<br>lead origin_country already a picklist<br>";
        }


        $result = $db->pquery("SELECT uitype FROM `vtiger_field` WHERE fieldlabel = 'LBL_LEADS_DESTINATIONCOUNTRY'", array());
        $row = $result->fetchRow();
        $destinationCountryUIType = $row[0];

        if ($destinationCountryUIType != 16) {
            $leadDestinationCountry = Vtiger_Field::getInstance('destination_country', $leadsModule);
            if ($leadDestinationCountry) {
                echo "<br>lead destination_country exists converting to picklist<br>";
                Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET uitype = '16' WHERE fieldlabel = 'LBL_LEADS_DESTINATIONCOUNTRY'");
                $leadDestinationCountry->setPicklistValues(array('United States', 'Canada'));
                echo "<br>lead destination_country picklist conversion done<br>";
            }
        } else {
            echo "<br>lead destination_country already a picklist<br>";
        }
    } else {
        echo "<br><h1>LBL_LEADS_ADDRESSINFORMATION NOT FOUND</h1><br>";
    }

    //modifications to dates block
    $leadsDates = Vtiger_Block::getInstance('LBL_LEADS_DATES', $leadsModule);
    if ($leadsDates) {
        echo "<br> block 'LBL_LEADS_DATES' exists<br>";
        $leadLoadDate = Vtiger_Field::getInstance('preferred_pldate', $leadsModule);
        if ($leadLoadDate) {
            echo "<br>preferred_pldate exists continuing with label swap<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_REQUESTEDMOVEDATE' WHERE fieldlabel = 'LBL_LEADS_PLDATE'");
            echo "<br>preferred_pldate label swap done<br>";
        }
        $leadDeliveryDate = Vtiger_Field::getInstance('preferred_pddate', $leadsModule);
        if ($leadDeliveryDate) {
            echo "<br>preferred_pddate exists continuing with label swap<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET fieldlabel = 'LBL_LEADS_EXPECTEDDELIVERYDATE' WHERE fieldlabel = 'LBL_LEADS_PDDATE'");
            echo "<br>preferred_pddate label swap done<br>";
        }
        $leadDaysToMove = Vtiger_Field::getInstance('days_to_move', $leadsModule);
        if ($leadDaysToMove) {
            echo "<br> leads days_to_move field already exists.<br>";
        } else {
            echo "<br> leads days_to_move field doesn't exist, adding it now.<br>";
            $leadDaysToMove = new Vtiger_Field();
            $leadDaysToMove->label = 'LBL_LEADS_DAYSTOMOVE';
            $leadDaysToMove->name = 'days_to_move';
            $leadDaysToMove->table = 'vtiger_leadscf';
            $leadDaysToMove->column = 'days_to_move';
            $leadDaysToMove->columntype = 'VARCHAR(255)';
            $leadDaysToMove->uitype = 1;
            $leadDaysToMove->typeofdata = 'V~O';
            $leadDaysToMove->displaytype = 1;
            $leadDaysToMove->quickcreate = 0;

            $leadsDates->addField($leadDaysToMove);
            echo "<br> leads days_to_move field added.<br>";
        }
    } else {
        echo "<br><h1>LBL_LEADS_DATES NOT FOUND</h1><br>";
    }

    //create new employer assisting block
    $employerAssisting = Vtiger_Block::getInstance('LBL_LEADS_EMPLOYERASSISTING', $leadsModule);
    if ($employerAssisting) {
        echo "<br>The LBL_LEADS_EMPLOYERASSISTING block already exists<br>";
    } else {
        $employerAssisting = new Vtiger_Block();
        $employerAssisting->label = 'LBL_LEADS_EMPLOYERASSISTING';
        $leadsModule->addBlock($employerAssisting);
    }

    $enabled = Vtiger_Field::getInstance('enabled', $leadsModule);
    if ($enabled) {
        echo "<br> leads enabled field already exists.<br>";
    } else {
        echo "<br> leads enabled field doesn't exist, adding it now.<br>";
        $enabled = new Vtiger_Field();
        $enabled->label = 'LBL_LEADS_ENABLED';
        $enabled->name = 'enabled';
        $enabled->table = 'vtiger_leadscf';
        $enabled->column = 'enabled';
        $enabled->columntype = 'VARCHAR(3)';
        $enabled->uitype = 56;
        $enabled->typeofdata = 'V~O';
        $enabled->quickcreate = 0;

        $employerAssisting->addField($enabled);
        echo "<br> leads enabled field added.<br>";
    }

    $contactName = Vtiger_Field::getInstance('contact_name', $leadsModule);
    if ($contactName) {
        echo "<br> leads contact_name field already exists.<br>";
    } else {
        echo "<br> leads contact_name field doesn't exist, adding it now.<br>";
        $contactName = new Vtiger_Field();
        $contactName->label = 'LBL_LEADS_CONTACTNAME';
        $contactName->name = 'contact_name';
        $contactName->table = 'vtiger_leadscf';
        $contactName->column = 'contact_name';
        $contactName->columntype = 'VARCHAR(255)';
        $contactName->uitype = 1;
        $contactName->typeofdata = 'V~O';
        $contactName->quickcreate = 0;

        $employerAssisting->addField($contactName);
        echo "<br> leads contact_name field added.<br>";
    }

    $contactEmail = Vtiger_Field::getInstance('contact_email', $leadsModule);
    if ($contactEmail) {
        echo "<br> leads contact_email field already exists.<br>";
    } else {
        echo "<br> leads contact_email field doesn't exist, adding it now.<br>";
        $contactEmail = new Vtiger_Field();
        $contactEmail->label = 'LBL_LEADS_CONTACTEMAIL';
        $contactEmail->name = 'contact_email';
        $contactEmail->table = 'vtiger_leadscf';
        $contactEmail->column = 'contact_email';
        $contactEmail->columntype = 'VARCHAR(255)';
        $contactEmail->uitype = 1;
        $contactEmail->typeofdata = 'V~O';
        $contactEmail->quickcreate = 0;

        $employerAssisting->addField($contactEmail);
        echo "<br> leads contact_email field added.<br>";
    }

    $contactPhone = Vtiger_Field::getInstance('contact_phone', $leadsModule);
    if ($contactPhone) {
        echo "<br> leads contact_phone field already exists.<br>";
    } else {
        echo "<br> leads contact_phone field doesn't exist, adding it now.<br>";
        $contactPhone = new Vtiger_Field();
        $contactPhone->label = 'LBL_LEADS_CONTACTPHONE';
        $contactPhone->name = 'contact_phone';
        $contactPhone->table = 'vtiger_leadscf';
        $contactPhone->column = 'contact_phone';
        $contactPhone->columntype = 'VARCHAR(255)';
        $contactPhone->uitype = 1;
        $contactPhone->typeofdata = 'V~O';
        $contactPhone->quickcreate = 0;

        $employerAssisting->addField($contactPhone);
        echo "<br> leads contact_phone field added.<br>";
    }

    $companyName = Vtiger_Field::getInstance('company', $leadsModule);
    if ($companyName) {
        echo "<br>lead company field exists, setting blocks and label<br>";
        $blockId = $employerAssisting->id;
        $sql = "UPDATE `vtiger_field` SET block = ?, fieldlabel = 'LBL_LEADS_COMPANYNAME' WHERE fieldlabel = 'LBL_LEADS_COMPANY' AND fieldname = 'company'";
        $db->pquery($sql, array($blockId));
    } else {
        echo "<br> leads special_terms doesn't exist, no action taken.<br>";
    }

    $descriptionInfo = Vtiger_Block::getInstance('LBL_LEADS_DESCRIPTIONINFORMATION', $leadsModule);
    if ($descriptionInfo) {
        $comments = Vtiger_Field::getInstance('employer_comments', $leadsModule);
        if ($comments) {
            echo "<br> leads employer_comments field already exists.<br>";
        } else {
            echo "<br> leads employer_comments field doesn't exist, adding it now.<br>";
            $comments = new Vtiger_Field();
            $comments->label = 'LBL_LEADS_EMPLOYERCOMMENTS';
            $comments->name = 'employer_comments';
            $comments->table = 'vtiger_leadscf';
            $comments->column = 'employer_comments';
            $comments->columntype = 'VARCHAR(255)';
            $comments->uitype = 19;
            $comments->typeofdata = 'V~O';
            $comments->quickcreate = 0;

            $employerAssisting->addField($comments);
            echo "<br> leads employer_comments field added.<br>";
        }

        $specialTerms = Vtiger_Field::getInstance('special_terms', $leadsModule);
        if ($specialTerms) {
            echo "<br> leads special_terms field already exists.<br>";
        } else {
            echo "<br> leads special_terms field doesn't exist, adding it now.<br>";
            $specialTerms = new Vtiger_Field();
            $specialTerms->label = 'LBL_LEADS_SPECIALTERMS';
            $specialTerms->name = 'special_terms';
            $specialTerms->table = 'vtiger_leadscf';
            $specialTerms->column = 'special_terms';
            $specialTerms->columntype = 'VARCHAR(255)';
            $specialTerms->uitype = 19;
            $specialTerms->typeofdata = 'V~O';
            $specialTerms->quickcreate = 0;

            $employerAssisting->addField($specialTerms);
            echo "<br> leads special_terms field added.<br>";
        }
    } else {
        echo "LBL_LEADS_DESCRIPTIONINFORMATION NOT FOUND. NO ACTION TAKEN.";
    }
} else {
    echo "<br><h1>LEADS MODULE NOT FOUND</h1><br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";