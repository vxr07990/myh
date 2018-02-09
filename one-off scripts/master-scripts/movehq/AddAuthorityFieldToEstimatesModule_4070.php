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
$Vtiger_Utils_Log = true;
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
global $adb;

$moduleInstance = Vtiger_Module::getInstance('Estimates');
if ($moduleInstance) {
    $blockInstance = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $moduleInstance);
    if($blockInstance){
        $field1 = Vtiger_Field::getInstance('authority', $moduleInstance);
        if ($field1) {
            echo "<br> The authority field already exists <br>";
        } else {
            $field1 = new Vtiger_Field();
            $field1->label = 'Authority';
            $field1->name = 'authority';
            $field1->table = 'vtiger_quotes';
            $field1->column = 'authority';
            $field1->columntype = 'varchar(100)';
            $field1->uitype = 16;
            $field1->typeofdata = 'V~O';
            $field1->quickcreate = 0;
            $blockInstance->addField($field1);
            // Remove existed values
            $adb->pquery("DELETE FROM vtiger_authority");
            $field1->setPicklistValues(['Van Line', 'Own Authority', 'Other Agent Authority']);
        }
    }

    $data = array(
        1=>'subject',
        2=>'business_line_est2',
        3=>'potential_id',
        4=>'quotestage',
        5=>'validtill',
        6=>'contact_id',
        7=>'account_id',
        8=>'assigned_user_id',
        9=>'business_line_est',
        10=>'is_primary',
        11=>'orders_id',
        12=>'load_date',
        13=>'contract',
        14=>'billing_type',
        15=>'authority',
        16=>'agentid',
        17=>'quotation_type',
        18=>'estimate_type',
        19=>'effective_tariff',
    );
    foreach ($data as $key=>$value)
    {
        $adb->pquery("UPDATE `vtiger_field` SET `sequence`=? WHERE `tabid`=? AND `fieldname`=?",
            array($key,$moduleInstance->id,$value));
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";