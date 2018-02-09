<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Opportunities_RelationListView_Model extends Vtiger_RelationListView_Model
{
    public function getCreateViewUrl()
    {
        $relationModel = $this->getRelationModel();
        $relatedModel = $relationModel->getRelationModuleModel();
        $parentRecordModule = $this->getParentRecordModel();
        $parentModule = $parentRecordModule->getModule();

        $createViewUrl = $relatedModel->getCreateRecordUrl().'&sourceModule='.$parentModule->get('name').
                         '&sourceRecord='.$parentRecordModule->getId().'&relationOperation=true&contact_id='.$parentRecordModule->get('contact_id').'&potential_id='.$parentRecordModule->getId();

        //To keep the reference fieldname and record value in the url if it is direct relation
        if ($relationModel->isDirectRelation()) {
            $relationField = $relationModel->getRelationField();
            $createViewUrl .='&'.$relationField->getName().'='.$parentRecordModule->getId();
        }
        return $createViewUrl;
    }
}
