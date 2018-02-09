<?php

if (function_exists("call_ms_function_ver")) {
    $version = 3;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "RUNNING: " . __FILE__ . "<br />\n";

$db = PearDatabase::getInstance();
$docModule = Vtiger_Module::getInstance('Documents');
$foldersField = Vtiger_Field::getInstance('folderid', $docModule);

if($foldersField) {
    //Hide this folder and add a new one to replace it
    $db->pquery('UPDATE vtiger_field SET presence = 1 WHERE fieldid = ?',[$foldersField->id]);
}

$foldersField = Vtiger_Field::getInstance('foldername', $docModule);

if(!$foldersField) {
    $blockModel = Vtiger_Block_Model::getInstance('LBL_NOTE_INFORMATION', $docModule);
    
    $fieldFolderName                = new Vtiger_Field();
    $fieldFolderName->label         = 'LBL_FOLDERNAME';
    $fieldFolderName->name          = 'foldername';
    $fieldFolderName->table         = 'vtiger_notes';
    $fieldFolderName->column        = 'foldername';
    $fieldFolderName->columntype    = 'VARCHAR(250)';
    $fieldFolderName->uitype        = 1500;
    $fieldFolderName->defaultvalue  = 'Default';
    $fieldFolderName->sequence      = 2;
    $fieldFolderName->typeofdata    = 'V~O';
    $blockModel->addField($fieldFolderName);
   

    $result = $db->pquery('SELECT * FROM vtiger_attachmentsfolder WHERE createdby = 1');

    if($result && $db->num_rows($result) > 0){
        while ($row = $db->fetch_array($result)) {
            $picklistValues[]=$row['foldername'];
        }
    }

    $fieldFolderName->setPicklistValues($picklistValues);
    $blockModel->save();
    //Adding agent base picklist Values

    $result = $db->pquery('SELECT * FROM vtiger_attachmentsfolder WHERE createdby != 1');

    if($result && $db->num_rows($result) > 0){
        $fieldModel = Vtiger_Field_Model::getInstance('foldername', Vtiger_Module_Model::getInstance('Documents'));
        while ($row = $db->fetch_array($result)) {
            $folderName  =  $row['foldername'];
            $createdBy  =   $row['createdby'];

            $user = new Users();
            $userObject = $user->retrieveCurrentUserInfoFromFile($createdBy);
            $userModel = Users_Record_Model::getInstanceFromUserObject($userObject);
            $accessibleOwners = $userModel->getBothAccessibleOwnersIdsForUser();
            

            foreach ($accessibleOwners as $agentId) {
                
                $currentTime = date('Y-m-d H:i:s');
                //I've try using this: what did you work from cli PicklistCustomizer_Module_Model::addPickListValues($fieldModel, $folderName, $agentId);
                $db->startTransaction();
                $sql = "INSERT INTO `vtiger_picklistexceptions` (`agentid`, `fieldid`, `value`, `type`, `createdtime`, `modifiedtime`, `modifiedby`) VALUES (?,?,?,?,?,?,?)";
                $db->pquery($sql, [$agentId, $fieldModel->getId(), $folderName, 'ADDED', $currentTime, $currentTime, 1]);
                $db->completeTransaction();
                


            }
        }
    }
    
    //Need to update the info in vtiger notes table

    $db->pquery('UPDATE vtiger_notes, vtiger_attachmentsfolder SET vtiger_notes.foldername = vtiger_attachmentsfolder.foldername 
                    WHERE vtiger_notes.folderid = vtiger_attachmentsfolder.folderid');
}