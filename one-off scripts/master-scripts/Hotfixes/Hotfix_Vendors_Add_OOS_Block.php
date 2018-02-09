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



$module = Vtiger_Module::getInstance('Vendors');

$blockOOS = Vtiger_Block::getInstance('LBL_VENDORS_OUTOFSERVICE', $module);
if ($blockOOS) {
    echo "<h3>The LBL_VENDORS_OUTOFSERVICE block already exists</h3><br> \n";
} else {
    $blockOOS        = new Vtiger_Block();
    $blockOOS->label = 'LBL_VENDORS_OUTOFSERVICE';
    $module->addBlock($blockOOS);
}
$fieldDateOOS = Vtiger_Field::getInstance('date_out_of_service', $module);
if ($fieldDateOOS) {
    echo "The date_out_of_service field already exists<br>\n";
} else {
    $fieldDateOOS             = new Vtiger_Field();
    $fieldDateOOS->label      = 'LBL_DATE_OUTOFSERVICE';
    $fieldDateOOS->name       = 'date_out_of_service';
    $fieldDateOOS->table      = 'vtiger_vendor';
    $fieldDateOOS->column     = 'date_out_of_service';
    $fieldDateOOS->columntype = 'DATE';
    $fieldDateOOS->uitype     = 5;
    $fieldDateOOS->typeofdata = 'D~O';
    $blockOOS->addField($fieldDateOOS);
}
$fieldDateReinstated = Vtiger_Field::getInstance('date_reinstated', $module);
if ($fieldDateReinstated) {
    echo "The date_reinstated field already exists<br>\n";
} else {
    $fieldDateReinstated             = new Vtiger_Field();
    $fieldDateReinstated->label      = 'LBL_DATE_REINSTATED';
    $fieldDateReinstated->name       = 'date_reinstated';
    $fieldDateReinstated->table      = 'vtiger_vendor';
    $fieldDateReinstated->column     = 'date_reinstated';
    $fieldDateReinstated->columntype = 'DATE';
    $fieldDateReinstated->uitype     = 5;
    $fieldDateReinstated->typeofdata = 'D~O';
    $blockOOS->addField($fieldDateReinstated);
}
$fieldReason = Vtiger_Field::getInstance('oos_reason', $module);
if ($fieldReason) {
    echo "The oos_reason field already exists<br>\n";
} else {
    $fieldReason             = new Vtiger_Field();
    $fieldReason->label      = 'LBL_OOS_REASON';
    $fieldReason->name       = 'oos_reason';
    $fieldReason->table      = 'vtiger_vendor';
    $fieldReason->column     = 'oos_reason';
    $fieldReason->columntype = 'VARCHAR(255)';
    $fieldReason->uitype     = 16;
    $fieldReason->typeofdata = 'V~O';
    $blockOOS->addField($fieldReason);

    $fieldReason->setPicklistValues([
        'Insurance - All Reasons',
        'Insurance - Auto Liability Expired',
        'Insurance - Auto Liability Incomplete',
        'Insurance - General Liability Expired',
        'Insurance - General Liability Incomplete',
        'Insurance - Non-trucking Liability Expired',
        'Insurance - OCC/ACC Insurance Expired',
        'Insurance - Physical Damage Expired',
        'Insurance - Umbrella Expired',
        'Insurance - Worker\'s Comp Expired',
        'Insurance - Worker\'s Comp Incomplete',
        'Insurance - Cancel 90 Days',
        'Insurance - Cancel Over 90 Days Must Reapply',
        'Contract - All Reasons',
        'Contract - I/C 30 Day Notice To Cancel',
        'Contract - I/C Contract Cancelled - Call Safety To Clear',
        'Contract - TSC 30 Day Notice To Cancel',
        'Contract - Paperwork Incomplete'
                                    ]);
}
$fieldComments = Vtiger_Field::getInstance('oos_comments', $module);
if ($fieldComments) {
    echo "The oos_comments field already exists<br>\n";
} else {
    $fieldComments             = new Vtiger_Field();
    $fieldComments->label      = 'LBL_OOS_COMMENTS';
    $fieldComments->name       = 'oos_comments';
    $fieldComments->table      = 'vtiger_vendor';
    $fieldComments->column     = 'oos_comments';
    $fieldComments->columntype = 'TEXT';
    $fieldComments->uitype     = 19;
    $fieldComments->typeofdata = 'V~O';
    $blockOOS->addField($fieldComments);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";