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


//include_once 'vtlib/Vtiger/Module.php';
//$Vtiger_Utils_Log = true;

$ModuleName = 'MovePolicy';
$MODULENAME = strtoupper($ModuleName);
$modulename = strtolower($ModuleName);
$moduleTLA  = 'MP';

$moduleInstance = Vtiger_Module::getInstance($ModuleName);
if ($moduleInstance) {
    echo "Module already present - choose a different name.";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $ModuleName;
    $moduleInstance->parent= 'Tools';
    $moduleInstance->save();

    // Schema Setup
    $moduleInstance->initTables();

    // Field Setup
    $block = new Vtiger_Block();
    $block->label = 'LBL_'. strtoupper($moduleInstance->name) . '_INFORMATION';
    $moduleInstance->addBlock($block);

    $blockcf = new Vtiger_Block();
    $blockcf->label = 'LBL_CUSTOM_INFORMATION';
    $moduleInstance->addBlock($blockcf);


    $field1 = Vtiger_Field::getInstance("{$modulename}_no", $moduleInstance);
    if ($field1) {
        echo "<br> Field '{$ModuleName}_no' is already present <br>";
    } else {
        $field1              = new Vtiger_Field();
        $field1->label       = "LBL_{$MODULENAME}_NO";
        $field1->name        = "{$modulename}_no";
        $field1->table       = "vtiger_{$modulename}";
        $field1->column      = "{$modulename}_no";
        $field1->columntype  = 'VARCHAR(32)';
        $field1->uitype      = 4;
        $field1->typeofdata  = 'V~M';
        $field1->displaytype = 3;

        $block->addField($field1);

        $entity = new CRMEntity();
        $entity->setModuleSeqNumber('configure', $moduleInstance->name, $moduleTLA, 1);
    }

    $moduleInstance->setEntityIdentifier($field1);

    // Recommended common fields every Entity module should have (linked to core table)
    $mfield1 = new Vtiger_Field();
    $mfield1->name = 'assigned_user_id';
    $mfield1->label = 'Assigned To';
    $mfield1->table = 'vtiger_crmentity';
    $mfield1->column = 'smownerid';
    $mfield1->uitype = 53;
    $mfield1->typeofdata = 'V~M';
    $block->addField($mfield1);

    $mfield2 = new Vtiger_Field();
    $mfield2->name = 'CreatedTime';
    $mfield2->label= 'Created Time';
    $mfield2->table = 'vtiger_crmentity';
    $mfield2->column = 'createdtime';
    $mfield2->uitype = 70;
    $mfield2->typeofdata = 'T~O';
    $mfield2->displaytype= 2;
    $block->addField($mfield2);

    $mfield3 = new Vtiger_Field();
    $mfield3->name = 'ModifiedTime';
    $mfield3->label= 'Modified Time';
    $mfield3->table = 'vtiger_crmentity';
    $mfield3->column = 'modifiedtime';
    $mfield3->uitype = 70;
    $mfield3->typeofdata = 'T~O';
    $mfield3->displaytype= 2;
    $block->addField($mfield3);

    // Filter Setup
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
    $filter1->addField($field1);

    // Sharing Access Setup
    $moduleInstance->setDefaultSharing();

    // Webservice Setup
    $moduleInstance->initWebservice();

    //Accounts
    $relationLabel   = 'Move Policy';
    //relate to Vanline Manager
    $employeesInstance = Vtiger_Module::getInstance('Accounts');
    $employeesInstance->setRelatedList($moduleInstance, $relationLabel, ['Add']);
    echo "OK\n";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";