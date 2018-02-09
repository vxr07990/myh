<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Orders_RelationListView_Model extends Vtiger_RelationListView_Model
{
    public function getCreateViewUrl()
    {
        $createViewUrl = parent::getCreateViewUrl();
        
        $relationModuleModel = $this->getRelationModel()->getRelationModuleModel();
        $relationModelName = $relationModuleModel->getName();
        if ($relationModelName == 'HelpDesk') {
            if ($relationModuleModel->getField('parent_id')->isViewable()) {
                $createViewUrl .='&parent_id='.$this->getParentRecordModel()->get('linktoaccountscontacts');
            }
        } elseif ($relationModelName == 'Cubesheets' || $relationModelName == 'Surveys') {
            $parentRecordModule = $this->getParentRecordModel();
            
            $contact_id = $parentRecordModule->get('orders_contacts');
            if ($contact_id != '') {
                $createViewUrl .='&contact_id='.$contact_id;
            }
            
            $opportunityId = $parentRecordModule->get('orders_opportunities');
            if ($opportunityId != '') {
                $createViewUrl .='&potential_id='.$opportunityId;
            
                if (empty($contact_id)) {
                    $opportunityRecord = Vtiger_Record_Model::getInstanceById($opportunityId, 'Opportunities');
                    $contact_id = $opportunityRecord->get('contact_id');
                    if ($contact_id != '') {
                        $createViewUrl .='&contact_id='.$contact_id;
                    }
                }
            }
        }

        return $createViewUrl;
    }

    public function getEntries($pagingModel) {

	$relationModuleModel = $this->getRelationModel()->getRelationModuleModel();
	$relationModelName = $relationModuleModel->getName();
	if ($relationModelName == 'Trips' && $this->parentRecordModel->get("orders_trip") != "") {
	    return parent::getEntries($pagingModel);
	} elseif ($relationModelName != 'Trips') {
	    return parent::getEntries($pagingModel);
	} else {
	    return array();
	}
    }

}
