<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";



//HOTFIX Create ExtraStopsModule

//$Vtiger_Utils_Log = true;
require_once('vtlib/Vtiger/Menu.php');
require_once('vtlib/Vtiger/Module.php');
require_once('includes/main/WebUI.php');
require_once('includes/runtime/LanguageHandler.php');

//needs these
require_once('include/Webservices/Create.php');
require_once('modules/Vtiger/uitypes/Date.php');

echo "<br>BEGINNING Hotfix: Create ExtraStops Module<br>";

echo "<br>BEGINNING Hotfix: Creating Module<br>";

$tableConversion = false;
$oldStops = [];
$db = PearDatabase::getInstance();

$moduleInstance = Vtiger_Module::getInstance('ExtraStops');
if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'ExtraStops';
    $moduleInstance->save();
    $tableConversion = true;
    $result= $db->pquery('SELECT * FROM `vtiger_extrastops`', []);
    while ($row =& $result->fetchRow()) {
        $oldStops[] = $row;
    }
    Vtiger_Utils::ExecuteQuery("DROP TABLE IF EXISTS `vtiger_extrastops`");
    $moduleInstance->initTables();
}

$blockInstance = Vtiger_Block::getInstance('LBL_EXTRASTOPS_INFORMATION', $moduleInstance);
if (!$blockInstance) {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_EXTRASTOPS_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}

$blockInstance2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);
if (!$blockInstance2) {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_CUSTOM_INFORMATION';
    $moduleInstance->addBlock($blockInstance2);
}

$field1 = Vtiger_Field::getInstance('extrastops_name', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_EXTRASTOPS_NAME';
    $field1->name = 'extrastops_name';
    $field1->table = 'vtiger_extrastops';
    $field1->column = 'extrastops_name';
    $field1->columntype = 'VARCHAR(75)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~M';
    $field1->summaryfield = 1;

    $blockInstance->addField($field1);
    $moduleInstance->setEntityIdentifier($field1);
}

$field2 = Vtiger_Field::getInstance('extrastops_sequence', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_EXTRASTOPS_SEQUENCE';
    $field2->name = 'extrastops_sequence';
    $field2->table = 'vtiger_extrastops';
    $field2->column = 'extrastops_sequence';
    $field2->columntype = 'VARCHAR(40)';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~M';
    $field2->summaryfield = 1;

    $blockInstance->addField($field2);
}

$field3 = Vtiger_Field::getInstance('extrastops_weight', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_EXTRASTOPS_WEIGHT';
    $field3->name = 'extrastops_weight';
    $field3->table = 'vtiger_extrastops';
    $field3->column = 'extrastops_weight';
    $field3->columntype = 'INT(10)';
    $field3->uitype = 1;
    $field3->typeofdata = 'I~O';

    $blockInstance->addField($field3);
}

$field4 = Vtiger_Field::getInstance('extrastops_isprimary', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_EXTRASTOPS_ISPRIMARY';
    $field4->name = 'extrastops_isprimary';
    $field4->table = 'vtiger_extrastops';
    $field4->column = 'extrastops_isprimary';
    $field4->columntype = 'VARCHAR(3)';
    $field4->uitype = 56;
    $field4->typeofdata = 'V~O';

    $blockInstance->addField($field4);
}

$field5 = Vtiger_Field::getInstance('extrastops_address1', $moduleInstance);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_EXTRASTOPS_ADDRESS1';
    $field5->name = 'extrastops_address1';
    $field5->table = 'vtiger_extrastops';
    $field5->column = 'extrastops_address1';
    $field5->columntype = 'VARCHAR(100)';
    $field5->uitype = 1;
    $field5->typeofdata = 'V~O';

    $blockInstance->addField($field5);
} else {
    echo "<br>extrastops_address1 exists converting to mandatory<br>";
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'V~M', defaultvalue = 'Will Advise' WHERE fieldlabel = 'LBL_EXTRASTOPS_ADDRESS1'");
    echo "<br>extrastops_address1 mandatory swap done<br>";
}

$field6 = Vtiger_Field::getInstance('extrastops_address2', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EXTRASTOPS_ADDRESS2';
    $field6->name = 'extrastops_address2';
    $field6->table = 'vtiger_extrastops';
    $field6->column = 'extrastops_address2';
    $field6->columntype = 'VARCHAR(100)';
    $field6->uitype = 1;
    $field6->typeofdata = 'V~O';

    $blockInstance->addField($field6);
}

//This is the exact moment I realized this would go faster if I didn't change the field# variables
//I apologize for any confusion

$field6 = Vtiger_Field::getInstance('extrastops_phone1', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EXTRASTOPS_PHONE1';
    $field6->name = 'extrastops_phone1';
    $field6->table = 'vtiger_extrastops';
    $field6->column = 'extrastops_phone1';
    $field6->columntype = 'VARCHAR(30)';
    $field6->uitype = 11;
    $field6->typeofdata = 'V~O';

    $blockInstance->addField($field6);
}

$field6 = Vtiger_Field::getInstance('extrastops_phone2', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EXTRASTOPS_PHONE2';
    $field6->name = 'extrastops_phone2';
    $field6->table = 'vtiger_extrastops';
    $field6->column = 'extrastops_phone2';
    $field6->columntype = 'VARCHAR(30)';
    $field6->uitype = 11;
    $field6->typeofdata = 'V~O';

    $blockInstance->addField($field6);
}

$field6 = Vtiger_Field::getInstance('extrastops_phonetype1', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EXTRASTOPS_PHONETYPE1';
    $field6->name = 'extrastops_phonetype1';
    $field6->table = 'vtiger_extrastops';
    $field6->column = 'extrastops_phonetype1';
    $field6->columntype = 'VARCHAR(50)';
    $field6->uitype = 16;
    $field6->typeofdata = 'V~O';

    $blockInstance->addField($field6);

    $field6->setPicklistValues(array('Home', 'Work', 'Cell'));
}

$field6 = Vtiger_Field::getInstance('extrastops_phonetype2', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EXTRASTOPS_PHONETYPE2';
    $field6->name = 'extrastops_phonetype2';
    $field6->table = 'vtiger_extrastops';
    $field6->column = 'extrastops_phonetype2';
    $field6->columntype = 'VARCHAR(50)';
    $field6->uitype = 16;
    $field6->typeofdata = 'V~O';

    $blockInstance->addField($field6);

    $field6->setPicklistValues(array('Home', 'Work', 'Cell'));
}

$field6 = Vtiger_Field::getInstance('extrastops_city', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EXTRASTOPS_CITY';
    $field6->name = 'extrastops_city';
    $field6->table = 'vtiger_extrastops';
    $field6->column = 'extrastops_city';
    $field6->columntype = 'VARCHAR(75)';
    $field6->uitype = 1;
    $field6->typeofdata = 'V~O';

    $blockInstance->addField($field6);
}

$field6 = Vtiger_Field::getInstance('extrastops_state', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EXTRASTOPS_STATE';
    $field6->name = 'extrastops_state';
    $field6->table = 'vtiger_extrastops';
    $field6->column = 'extrastops_state';
    $field6->columntype = 'VARCHAR(40)';
    $field6->uitype = 1;
    $field6->typeofdata = 'V~O';

    $blockInstance->addField($field6);
}

$field6 = Vtiger_Field::getInstance('extrastops_zip', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EXTRASTOPS_ZIP';
    $field6->name = 'extrastops_zip';
    $field6->table = 'vtiger_extrastops';
    $field6->column = 'extrastops_zip';
    $field6->columntype = 'VARCHAR(30)';
    $field6->uitype = 1;
    $field6->typeofdata = 'V~O';

    $blockInstance->addField($field6);
}

$field6 = Vtiger_Field::getInstance('extrastops_country', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EXTRASTOPS_COUNTRY';
    $field6->name = 'extrastops_country';
    $field6->table = 'vtiger_extrastops';
    $field6->column = 'extrastops_country';
    $field6->columntype = 'VARCHAR(75)';
    $field6->uitype = 1;
    $field6->typeofdata = 'V~O';

    $blockInstance->addField($field6);
}

$field6 = Vtiger_Field::getInstance('extrastops_date', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EXTRASTOPS_DATE';
    $field6->name = 'extrastops_date';
    $field6->table = 'vtiger_extrastops';
    $field6->column = 'extrastops_date';
    $field6->columntype = 'DATE';
    $field6->uitype = 5;
    $field6->typeofdata = 'D~O';

    $blockInstance->addField($field6);
}

$field6 = Vtiger_Field::getInstance('extrastops_contact', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EXTRASTOPS_CONTACT';
    $field6->name = 'extrastops_contact';
    $field6->table = 'vtiger_extrastops';
    $field6->column = 'extrastops_contact';
    $field6->columntype = 'INT(19)';
    $field6->uitype = 10;
    $field6->typeofdata = 'I~O';

    $blockInstance->addField($field6);

    $field6->setRelatedModules(array('Contacts'));
}

$field6 = Vtiger_Field::getInstance('extrastops_type', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EXTRASTOPS_TYPE';
    $field6->name = 'extrastops_type';
    $field6->table = 'vtiger_extrastops';
    $field6->column = 'extrastops_type';
    $field6->columntype = 'VARCHAR(40)';
    $field6->uitype = 16;
    $field6->typeofdata = 'V~O';

    $blockInstance->addField($field6);
    $field6->setPicklistValues(array('Origin', 'Destination', 'Extra Pickup', 'Extra Delivery'));
}

$field6 = Vtiger_Field::getInstance('extrastops_description', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EXTRASTOPS_DESCRIPTION';
    $field6->name = 'extrastops_description';
    $field6->table = 'vtiger_extrastops';
    $field6->column = 'extrastops_description';
    $field6->columntype = 'VARCHAR(40)';
    $field6->uitype = 16;
    $field6->typeofdata = 'V~O';

    $blockInstance->addField($field6);
    $field6->setPicklistValues(array('Apartment', 'Home', 'Mini Storage', 'Office Building', 'Other'));
}


//TODO Sirva Stop Types, this'll need it's custom JS back too
$field6 = Vtiger_Field::getInstance('extrastops_sirvastoptype', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EXTRASTOPS_SIRVASTOPTYPE';
    $field6->name = 'extrastops_sirvastoptype';
    $field6->table = 'vtiger_extrastops';
    $field6->column = 'extrastops_sirvastoptype';
    $field6->columntype = 'VARCHAR(40)';
    $field6->uitype = 16;
    $field6->typeofdata = 'V~O';

    $blockInstance->addField($field6);
    $field6->setPicklistValues(['XP1', 'XP2', 'XP3', 'XP4', 'XP5', 'OSIT', 'OSTG', 'OPRM', 'XD1', 'XD2', 'XD3', 'XD4', 'XD5', 'DSIT', 'DSTG', 'DPRM']);
}

$field6 = Vtiger_Field::getInstance('extrastops_relcrmid', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EXTRASTOPS_RELCRMID';
    $field6->name = 'extrastops_relcrmid';
    $field6->table = 'vtiger_extrastops';
    $field6->column = 'extrastops_relcrmid';
    $field6->columntype = 'INT(11)';
    $field6->uitype = 10;
    $field6->typeofdata = 'V~O';

    $blockInstance->addField($field6);
    $field6->setRelatedModules(array('Opportunities', 'Orders', 'Estimates'));
}

$field6 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if ($field6) {
    echo "<br> Field 'assigned_user_id' is already present. <br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EXTRASTOPS_ASSIGNEDTO';
    $field6->name = 'assigned_user_id';
    $field6->table = 'vtiger_crmentity';
    $field6->column = 'smownerid';
    $field6->uitype = 53;
    $field6->typeofdata = 'V~M';

    $blockInstance->addField($field6);
}

$field6 = Vtiger_Field::getInstance('agentid', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'Owner Agent';
    $field6->name = 'agentid';
    $field6->table = 'vtiger_crmentity';
    $field6->column = 'agentid';
    $field6->columntype = 'INT(10)';
    $field6->uitype = 1002;
    $field6->typeofdata = 'I~M';

    $blockInstance->addField($field6);
}

if ($tableConversion) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field1)->addField($field2, 1);

    $moduleInstance->initWebservice();
    $moduleInstance->setDefaultSharing();

    echo "<br>converting old stops<br>";
    foreach ($oldStops as $oldStop) {
        $element = [
                        'extrastops_name' => $oldStop['stop_description'],
                        'extrastops_sequence' => $oldStop['stop_dsequence'],
                        'extrastops_weight' => $oldStop['stop_weight'],
                        'extrastops_date' => Vtiger_Date_UIType::getDBInsertValue($oldStop['stop_date']),
                        'extrastops_address1' => $oldStop['stop_address1'],
                        'extrastops_address2' => $oldStop['stop_address2'],
                        'extrastops_city' => $oldStop['stop_city'],
                        'extrastops_state' => $oldStop['stop_state'],
                        'extrastops_zip' => $oldStop['stop_zip'],
                        'extrastops_country' => $oldStop['stop_country'],
                        'extrastops_phone1' => $oldStop['stop_phone1'],
                        'extrastops_phone2' => $oldStop['stop_phone2'],
                        'extrastops_phonetype1' => $oldStop['stop_phonetype1'],
                        'extrastops_phonetype2' => $oldStop['stop_phonetype2'],
                        'extrastops_isprimary' => $oldStop['stop_isprimary'],
                        'extrastops_stoptype' => $oldStop['stop_type'],
                   ];
        //figure out related record
        if ($oldStop['stop_opp'] && $db->pquery("SELECT crmid FROM `vtiger_crmentity` WHERE crmid = ?", [$oldStop['stop_opp']])->fetchRow()['crmid']) {
            $element['extrastops_relcrmid'] = vtws_getWebserviceEntityId('Opportunities', $oldStop['stop_opp']);
        } elseif ($oldStop['stop_order'] && $db->pquery("SELECT crmid FROM `vtiger_crmentity` WHERE crmid = ?", [$oldStop['stop_order']])->fetchRow()['crmid']) {
            $element['extrastops_relcrmid'] = vtws_getWebserviceEntityId('Orders', $oldStop['stop_order']);
        } elseif ($oldStop['stop_estimate'] && $db->pquery("SELECT crmid FROM `vtiger_crmentity` WHERE crmid = ?", [$oldStop['stop_estimate']])->fetchRow()['crmid']) {
            $element['extrastops_relcrmid'] = vtws_getWebserviceEntityId('Estimates', $oldStop['stop_estimate']);
        }
        //figure out contact
        if ($oldStop['stop_contact']) {
            $element['extrastops_contact'] = vtws_getWebserviceEntityId('Contacts', $oldStop['stop_contact']);
        }
        if ($oldStop['sirva_stop_type']) {
            $element['extrastops_sirvastoptype'] = $oldStop['sirva_stop_type'];
        }
        if (empty($oldStop['stop_sequence'])) {
            $element['extrastops_sequence'] = '1';
        }
        //figure out owners
        $result = $db->pquery('SELECT smownerid, agentid FROM `vtiger_crmentity` WHERE crmid=?', [$element['extrastops_relcrmid']]);
        $row = $result->fetchRow();
        if ($row['smownerid']) {
            $element['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $row['smownerid']);
        } else {
            $element['assigned_user_id'] = vtws_getWebserviceEntityId('Users', 1);
        }
        $element['agentid'] = $row['agentid'];
        if (empty($element['agentid'])) {
            $element['agentid'] = $db->pquery('SELECT agentmanagerid FROM `vtiger_agentmanager`', [])->fetchRow()['agentmanagerid'];
        }
        //echo "OLD STOP ELEMENT: ".print_r($element, true)."<br>";
        try {
            $user = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
            //create extra stop
            vtws_create('ExtraStops', $element, $current_user);
        } catch (Exception $e) {
            file_put_contents('logs/devLog.log', "\n\n Failed Element : ".print_r($element, true), FILE_APPEND);
            file_put_contents('logs/devLog.log', "\n\n Exception : ".print_r($e, true), FILE_APPEND);
        }
    }
}

echo "<br>COMPLETED Hotfix: Create ExtraStops Module<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";