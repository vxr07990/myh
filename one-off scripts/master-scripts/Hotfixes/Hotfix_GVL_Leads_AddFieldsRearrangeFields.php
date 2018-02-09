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
//*/
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
//*/
echo "<br><h1>Starting Hotfix Add Fields Leads for Nat Account</h1><br>\n";
$db = PearDatabase::getInstance();
$moduleName = 'Leads';
$picklistBlockName = 'LBL_LEADS_INFORMATION';
$module = Vtiger_Module::getInstance($moduleName);
$block = Vtiger_Block::getInstance($picklistBlockName, $module);
/********************* leadstatus Applied Field ***********************/
if ($block) {
    $picklistOptions = [
        'New',
        'Attempted to Contact',
        'Survey Scheduled',
        'Cancelled',
        'Duplicate',
    ];
    $field = Vtiger_Field::getInstance('leadstatus', $module);
    if ($field) {
        echo '<p>leadstatus Field already present</p>';
        Vtiger_Utils::ExecuteQuery('TRUNCATE TABLE `vtiger_leadstatus`');
        $i = 1;
        foreach ($picklistOptions as $val) {
            $params = [$i, $val, 1, $i, $i];
            $sql = 'INSERT INTO `vtiger_leadstatus` (leadstatusid, leadstatus, presence, picklist_valueid, sortorderid) VALUES (?, ?, ?, ?, ?)';
            $db->pquery($sql, $params);
            $i++;
        }
        $sql = 'UPDATE `vtiger_leadstatus_seq` SET id = ?';
        $db->pquery($sql, [$i]);
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_QUOTES_VALUATION_APPLIED';
        $field->name = 'leadstatus';
        $field->table = 'vtiger_leaddetails';
        $field->column = 'leadstatus';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~O';
        $block->addField($field);
        $field->setPicklistValues($picklistOptions);
        echo '<p>Added leadstatus Field</p>';
    }
    /*********************  Lead Source Field ***********************/
    $picklistOptions = [
        'Direct Mail',
        'E&I',
        'ERC',
        'Existing Customer Referral',
        'GCI-GRSW/MMI',
        'Graebelmoving.com',
        'GSA',
        'GVL Employee',
        'Networking Group',
        'Other',
        'Previous Customer',
        'Self Generated',
        'SHRM',
        'Trade Conference',
        'Self Generated'
    ];
    $field = Vtiger_Field::getInstance('leadsource', $module);
    if ($field) {
        echo '<p>leadsource Field already present</p>';
        Vtiger_Utils::ExecuteQuery('TRUNCATE TABLE `vtiger_leadsource`');
        $i = 1;
        foreach ($picklistOptions as $val) {
            $params = [$i, $val, 1, $i, $i];
            $sql = 'INSERT INTO `vtiger_leadsource` (leadsourceid, leadsource, presence, picklist_valueid, sortorderid) VALUES (?, ?, ?, ?, ?)';
            $db->pquery($sql, $params);
            $i++;
        }
        $sql = 'UPDATE `vtiger_leadsource_seq` SET id = ?';
        $db->pquery($sql, [$i]);
        $sql = 'UPDATE `vtiger_field` SET uitype = ? WHERE fieldid = ?';
        $db->pquery($sql, [16, $field->id]);
        echo '<p>leadsource field updated to a picklist</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_LEADS_LEADSOURCE';
        $field->name = 'leadsource';
        $field->table = 'vtiger_leaddetails';
        $field->column = 'leadsource';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~O';
        $block->addField($field);
        $field->setPicklistValues($picklistOptions);
        echo '<p>Added leadsource Field</p>';
    }
    /********************LEAD SOURCE WORKSPACE *************/
    $picklistOptions = [
        'AIM',
        'CBRE',
        'ConstructionWire',
        'CoreNet',
        'Co-Star',
        'Direct Mail',
        'DTZ/C&W',
        'E&I',
        'Existing Customer Referral',
        'GCI-GCS',
        'Graebelmoving.com',
        'GSA',
        'GVL Employee',
        'HelpMovingOffice',
        'IFMA',
        'JLL',
        'Lodging Development',
        'Networking Group',
        'NEWH',
        'Other',
        'Previous Customer',
        'Self Generated',
        'SHRM',
        'TMI',
        'Trade Conference',
        'Wendover',
    ];
    $field = Vtiger_Field::getInstance('leadsource_workspace', $module);
    if ($field) {
        echo '<p>leadsource_workspace Field already present</p>';
        Vtiger_Utils::ExecuteQuery('TRUNCATE TABLE `vtiger_leadsource_workspace`');
        $i = 1;
        foreach ($picklistOptions as $val) {
            $params = [$i, $val, 1, $i];
            $sql = 'INSERT INTO `vtiger_leadsource_workspace` (leadsource_workspaceid, leadsource_workspace, presence, sortorderid) VALUES (?, ?, ?, ?)';
            $db->pquery($sql, $params);
            $i++;
        }
        $sql = 'UPDATE `vtiger_leadsource_workspace_seq` SET id = ?';
        $db->pquery($sql, [$i]);
        $sql = 'UPDATE `vtiger_field` SET uitype = ? WHERE fieldid = ?';
        $db->pquery($sql, [16, $field->id]);
        echo '<p>leadsource_workspace field updated to a picklist</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_LEADS_LEADSOURCE';
        $field->name = 'leadsource_workspace';
        $field->table = 'vtiger_leaddetails';
        $field->column = 'leadsource_workspace';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~O';
        $block->addField($field);
        $field->setPicklistValues($picklistOptions);
        echo '<p>Added leadsource_workspace Field</p>';
    }
    /********************LEAD SOURCE NATIONAL *************/
    $picklistOptions = [
        'Direct Mail',
        'E&I',
        'ERC',
        'Existing Customer Referral',
        'GCI-GRSW/MMI',
        'Graebelmoving.com',
        'GSA',
        'GVL Employee',
        'Networking Group',
        'Other',
        'Previous Customer',
        'Self Generated',
        'SHRM',
        'Trade Conference',
    ];
    $field = Vtiger_Field::getInstance('leadsource_national', $module);
    if ($field) {
        echo '<p>leadsource_national Field already present</p>';
        Vtiger_Utils::ExecuteQuery('TRUNCATE TABLE `vtiger_leadsource_national`');
        $i = 1;
        foreach ($picklistOptions as $val) {
            $params = [$i, $val, 1, $i];
            $sql = 'INSERT INTO `vtiger_leadsource_national` (leadsource_nationalid, leadsource_national, presence, sortorderid) VALUES (?, ?, ?, ?)';
            $db->pquery($sql, $params);
            $i++;
        }
        $sql = 'UPDATE `vtiger_leadsource_national_seq` SET id = ?';
        $db->pquery($sql, [$i]);
        $sql = 'UPDATE `vtiger_field` SET uitype = ? WHERE fieldid = ?';
        $db->pquery($sql, [16, $field->id]);
        echo '<p>leadsource_national field updated to a picklist</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_LEADS_LEADSOURCE';
        $field->name = 'leadsource_national';
        $field->table = 'vtiger_leaddetails';
        $field->column = 'leadsource_national';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~O';
        $block->addField($field);
        $field->setPicklistValues($picklistOptions);
        echo '<p>Added leadsource_national Field</p>';
    }
    /********************LEAD SOURCE HHG *************/
    $picklistOptions = [
        'All My Sons',
        'Angie\'s List',
        'BBB',
        'Beneplace',
        'Branch Call-In',
        'Chamber of Commerce',
        'Corporate Affinity',
        'Direct Mail',
        'Facebook',
        'Friends or Relatives',
        'Graebelmoving.com',
        'Local Mover',
        'NASMM',
        'National Account Referral',
        'Networking Group',
        'Other',
        'Previous Customer',
        'Realtor - Local',
        'Retirement Community',
        'Trade Conference',
        'Walker Sands',
        'WPS Customer',
        'Yelp',
        'optim.com'
    ];
    $field = Vtiger_Field::getInstance('leadsource_hhg', $module);
    if ($field) {
        echo '<p>leadsource_hhg Field already present</p>';
        Vtiger_Utils::ExecuteQuery('TRUNCATE TABLE `vtiger_leadsource_hhg`');
        $i = 1;
        foreach ($picklistOptions as $val) {
            $params = [$i, $val, 1, $i];
            $sql = 'INSERT INTO `vtiger_leadsource_hhg` (leadsource_hhgid, leadsource_hhg, presence, sortorderid) VALUES (?, ?, ?, ?)';
            $db->pquery($sql, $params);
            $i++;
        }
        $sql = 'UPDATE `vtiger_leadsource_hhg_seq` SET id = ?';
        $db->pquery($sql, [$i]);
        $sql = 'UPDATE `vtiger_field` SET uitype = ? WHERE fieldid = ?';
        $db->pquery($sql, [16, $field->id]);
        echo '<p>leadsource_hhg field updated to a picklist</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_LEADS_LEADSOURCE';
        $field->name = 'leadsource_hhg';
        $field->table = 'vtiger_leaddetails';
        $field->column = 'leadsource_hhg';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '16';
        $field->typeofdata = 'V~O';
        $block->addField($field);
        $field->setPicklistValues($picklistOptions);
        echo '<p>Added leadsource_hhg Field</p>';
    }
    /********************LEAD BILLING TYPE *************/
    // No picklist values needed, billing type is already exisits in opps
    /*  $field = Vtiger_Field::getInstance('billing_type', $module);
      if($field) {
          echo '<p>leadsource_national Field already present</p>';
      } else {
          $field = new Vtiger_Field();
          $field->label = 'LBL_LEADS_BILLING_TYPE';
          $field->name = 'billing_type';
          $field->table = 'vtiger_leaddetails';
          $field->column = 'billing_type';
          $field->columntype = 'VARCHAR(100)';
          $field->uitype = '16';
          $field->typeofdata = 'V~O';
          $block->addField($field);
          echo '<p>Added billing_type Field</p>';
      }*/
    // REORDER THE FIELDS
    $fieldOrder = ['business_line', 'leadsource', 'leadstatus', 'sales_person', 'agentid', 'assigned_user_id', 'billing_type', 'leadsource_workspace', 'leadsource_national', 'leadsource_hhg'];
    $count = 1;
    foreach ($fieldOrder as $val) {
        $field = Vtiger_Field::getInstance($val, $module);
        if ($field) {
            $params = [$block->id, $count, $field->id];
            $sql = 'UPDATE `vtiger_field` SET block = ?, sequence = ? WHERE fieldid = ?';
            $db->pquery($sql, $params);
            echo '<p>Moved '.$val.' to the LBL_LEADS_CONTACT_INFORMATION block</p>';
            $count++;
        } else {
            echo '<p>'.$val.' Field don\'t exists present</p>';
        }
    }
} else {
    echo '<p>Couldn\'t find the LBL_LEADS_INFORMATION</p>';
}
/********************ADD NEW BLOCK ********************/
$blockName = 'LBL_LEADS_CONTACT_INFORMATION';
$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    echo '<p>Block Already Exists</p>';
} else {
    echo '<p>Block LBL_LEADS_CONTACT_INFORMATION Doesn\'t Exist Adding It...</p>';
    //Add Block
    $block = new Vtiger_Block();
    $block->label = $blockName;
    $block->sequence = 1;
    $module->addBlock($block);
}
/*************** MOVE FIELDS TO THE LBL_LEADS_CONTACT_INFORMATION  BLOCK *************************/
if ($block) {
    $count = 1;
    $fieldsToMove = ['firstname', 'lastname', 'phone', 'mobile', 'email', 'mobile', 'secondaryemail', 'company', 'emailoptout'];
    foreach ($fieldsToMove as $val) {
        $field = Vtiger_Field::getInstance($val, $module);
        if ($field) {
            $params = [$block->id, $count, $field->id];
            $sql = 'UPDATE `vtiger_field` SET block = ?, sequence = ? WHERE fieldid = ?';
            $db->pquery($sql, $params);
            echo '<p>Moved '.$val.'to the LBL_LEADS_CONTACT_INFORMATION block</p>';
            $count++;
        } else {
            echo '<p>'.$val.' Field don\'t exists present</p>';
        }
    }
} else {
    echo '<p>Couldn\'t find the LBL_LEADS_CONTACT_INFORMATION</p>';
}
/**************************************************************/
$block = Vtiger_Block::getInstance('LBL_LEADS_NATIONALACCOUNT', $module);
if ($block) {
    $field = Vtiger_Field::getInstance('account_address1', $module);
    if ($field) {
        echo '<p>account_address1 Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_LEADS_ACCOUNT_ADDRESS1';
        $field->name = 'account_address1';
        $field->table = 'vtiger_leaddetails';
        $field->column = 'account_address1';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '1';
        $field->sequence = '1';
        $field->typeofdata = 'V~O';
        $block->addField($field);
        echo '<p>Added account_address1 Field</p>';
    }
    ///////////////////////////////////
    $field = Vtiger_Field::getInstance('account_address2', $module);
    if ($field) {
        echo '<p>account_address2 Field already present</p>';
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_LEADS_ACCOUNT_ADDRESS2';
        $field->name = 'account_address2';
        $field->table = 'vtiger_leaddetails';
        $field->column = 'account_address2';
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '1';
        $field->sequence = '2';
        $field->typeofdata = 'V~O';
        $block->addField($field);
        echo '<p>Added account_address2 Field</p>';
    }
    $field = Vtiger_Field::getInstance('lane', $module);
    $field2 = Vtiger_Field::getInstance('pobox', $module);
    $sql = 'UPDATE `vtiger_field` SET presence = ? WHERE fieldid = ? OR fieldid = ?';
    $params = [1, $field->id, $field2->id];
    $db->pquery($sql, $params);
} else {
    echo '<p></p>';
}
echo "<br><h1>Ending Hotfix Add Fields Leads for Nat Account</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";