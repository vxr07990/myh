<?php
//if (function_exists("call_ms_function_ver")) {
//    $version = 2;
//    if (call_ms_function_ver(__FILE__, $version)) {
//        //already ran
//        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
//        return;
//    }
//}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";
$mediaModule = Vtiger_Module::getInstance('Media');
$mediaModuleIsNew = false;
if ($mediaModule) {
    echo "<h2>Media already exists, updating fields</h2><br>\n";
} else {
    $mediaModule       = new Vtiger_Module();
    $mediaModule->name = 'Media';
    $mediaModule->save();
    echo "<h2>Creating module Media and updating fields</h2><br>\n";
    $mediaModule->initTables();
    $mediaModuleIsNew = true;
}
$mediaBlock = Vtiger_Block::getInstance('LBL_MEDIA_INFORMATION', $mediaModule);
if ($mediaBlock) {
    echo "<h3>The LBL_MEDIA_INFORMATION block already exists</h3><br> \n";
} else {
    $mediaBlock        = new Vtiger_Block();
    $mediaBlock->label = 'LBL_MEDIA_INFORMATION';
    $mediaModule->addBlock($mediaBlock);
}
$fieldMedia1 = Vtiger_Field::getInstance('title', $mediaModule);
if ($fieldMedia1) {
    echo "The title field already exists<br>\n";
} else {
    $fieldMedia1             = new Vtiger_Field();
    $fieldMedia1->label      = 'Title';
    $fieldMedia1->name       = 'title';
    $fieldMedia1->table      = 'vtiger_media';
    $fieldMedia1->column     = 'title';
    $fieldMedia1->columntype = 'VARCHAR(255)';
    $fieldMedia1->uitype     = 2;
    $fieldMedia1->typeofdata = 'V~M';
    $mediaBlock->addField($fieldMedia1);

    $mediaModule->setEntityIdentifier($fieldMedia1);
}
$fieldMedia2 = Vtiger_Field::getInstance('is_video', $mediaModule);
if ($fieldMedia2) {
    echo "The is_video field already exists<br>\n";
} else {
    $fieldMedia2             = new Vtiger_Field();
    $fieldMedia2->label      = 'LBL_IS_VIDEO';
    $fieldMedia2->name       = 'is_video';
    $fieldMedia2->table      = 'vtiger_media';
    $fieldMedia2->column     = 'is_video';
    $fieldMedia2->columntype = 'TINYINT(1)';
    $fieldMedia2->uitype     = 56;
    $fieldMedia2->typeofdata = 'C~O';
    $mediaBlock->addField($fieldMedia2);
}
$fieldMedia3 = Vtiger_Field::getInstance('assigned_user_id', $mediaModule);
if ($fieldMedia3) {
    echo "The assigned_user_id field already exists<br>\n";
} else {
    $fieldMedia3             = new Vtiger_Field();
    $fieldMedia3->label      = 'Assigned To';
    $fieldMedia3->name       = 'assigned_user_id';
    $fieldMedia3->table      = 'vtiger_crmentity';
    $fieldMedia3->column     = 'smownerid';
    $fieldMedia3->uitype     = 53;
    $fieldMedia3->typeofdata = 'V~M';
    $mediaBlock->addField($fieldMedia3);
}
$fieldMedia4 = Vtiger_Field::getInstance('createdtime', $mediaModule);
if ($fieldMedia4) {
    echo "The createdtime field already exists<br>\n";
} else {
    $fieldMedia4             = new Vtiger_Field();
    $fieldMedia4->label      = 'Created Time';
    $fieldMedia4->name       = 'createdtime';
    $fieldMedia4->table      = 'vtiger_crmentity';
    $fieldMedia4->column     = 'createdtime';
    $fieldMedia4->uitype     = 70;
    $fieldMedia4->typeofdata = 'DT~O';
    $fieldMedia4->displaytype = 2;
    $mediaBlock->addField($fieldMedia4);
}
$fieldMedia5 = Vtiger_Field::getInstance('modifiedtime', $mediaModule);
if ($fieldMedia5) {
    echo "The modifiedtime field already exists<br>\n";
} else {
    $fieldMedia5             = new Vtiger_Field();
    $fieldMedia5->label      = 'Modified Time';
    $fieldMedia5->name       = 'modifiedtime';
    $fieldMedia5->table      = 'vtiger_crmentity';
    $fieldMedia5->column     = 'modifiedtime';
    $fieldMedia5->uitype     = 70;
    $fieldMedia5->typeofdata = 'DT~O';
    $fieldMedia5->displaytype = 2;
    $mediaBlock->addField($fieldMedia5);
}
$fieldMedia6 = Vtiger_Field::getInstance('agentid', $mediaModule);
if ($fieldMedia6) {
    echo "The agentid field already exists<br>\n";
} else {
    $fieldMedia6             = new Vtiger_Field();
    $fieldMedia6->label      = 'Owner Agent';
    $fieldMedia6->name       = 'agentid';
    $fieldMedia6->table      = 'vtiger_crmentity';
    $fieldMedia6->column     = 'agentid';
    $fieldMedia6->uitype     = 1002;
    $fieldMedia6->typeofdata = 'I~M';
    $mediaBlock->addField($fieldMedia6);
}
$fieldMedia7 = Vtiger_Field::getInstance('file_name', $mediaModule);
if ($fieldMedia7) {
    echo "The file_name field already exists<br>\n";
} else {
    $fieldMedia7             = new Vtiger_Field();
    $fieldMedia7->label      = 'LBL_FILE_NAME';
    $fieldMedia7->name       = 'file_name';
    $fieldMedia7->table      = 'vtiger_media';
    $fieldMedia7->column     = 'file_name';
    $fieldMedia7->columntype = 'VARCHAR(255)';
    $fieldMedia7->uitype     = 1;
    $fieldMedia7->typeofdata = 'V~O';
    $mediaBlock->addField($fieldMedia7);
}
$fieldMedia8 = Vtiger_Field::getInstance('thumb_file_name', $mediaModule);
if ($fieldMedia8) {
    echo "The thumb_file_name field already exists<br>\n";
} else {
    $fieldMedia8             = new Vtiger_Field();
    $fieldMedia8->label      = 'LBL_THUMB_FILE_NAME';
    $fieldMedia8->name       = 'thumb_file_name';
    $fieldMedia8->table      = 'vtiger_media';
    $fieldMedia8->column     = 'thumb_file_name';
    $fieldMedia8->columntype = 'VARCHAR(255)';
    $fieldMedia8->uitype     = 1;
    $fieldMedia8->typeofdata = 'V~O';
    $mediaBlock->addField($fieldMedia8);
}
$fieldMedia9 = Vtiger_Field::getInstance('archiveid', $mediaModule);
if ($fieldMedia9) {
    echo "The archiveid field already exists<br>\n";
} else {
    $fieldMedia9             = new Vtiger_Field();
    $fieldMedia9->label      = 'LBL_ARCHIVEID';
    $fieldMedia9->name       = 'archiveid';
    $fieldMedia9->table      = 'vtiger_media';
    $fieldMedia9->column     = 'archiveid';
    $fieldMedia9->columntype = 'VARCHAR(100)';
    $fieldMedia9->uitype     = 1;
    $fieldMedia9->typeofdata = 'V~O';
    $mediaBlock->addField($fieldMedia9);
}

if($mediaModuleIsNew) {
    $mediaFilter1 = new Vtiger_Filter();
    $mediaFilter1->name = 'All';
    $mediaFilter1->isdefault = true;
    $mediaModule->addFilter($mediaFilter1);

    $mediaFilter1->addField($fieldMedia1)->addField($fieldMedia2, 1)->addField($fieldMedia3, 2)->addField($fieldMedia4, 3)->addField($fieldMedia5, 4)->addField($fieldMedia6, 5);

    $mediaModule->setDefaultSharing();
    $mediaModule->initWebservice();

    $cubesheetsModule = Vtiger_Module::getInstance('Cubesheets');
    $cubesheetsModule->setRelatedList($mediaModule, 'Media');

    $opportunitiesModule = Vtiger_Module::getInstance('Opportunities');
    $opportunitiesModule->setRelatedList($mediaModule, 'Media');

    $contactsModule = Vtiger_Module::getInstance('Contacts');
    $contactsModule->setRelatedList($mediaModule, 'Media');

    $cubesheetsModule->unsetRelatedList($cubesheetsModule, 'Archives');
}

if(!Vtiger_Utils::CheckTable('vtiger_mediarel')) {
    Vtiger_Utils::CreateTable('vtiger_mediarel', '(crmid INT(19) NOT NULL, mediaid INT(19) NOT NULL)', true);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";