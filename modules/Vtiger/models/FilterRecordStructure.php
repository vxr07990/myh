<?php
/*+***********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_FilterRecordStructure_Model extends Vtiger_RecordStructure_Model {
	
	/**
	* Function to get the fields & reference fields in stuctured format
		 * @return <array> - values in structure array('block'=>array(fieldinfo));
	*/
		public function getStructure() {
		if(!empty($this->structuredValues)) {
			return $this->structuredValues;
		}
		
		$values = array();
		$recordModel = $this->getRecord();
		$recordExists = !empty($recordModel);
		$baseModuleModel = $moduleModel = $this->getModule();
		$baseModuleName = $baseModuleModel->getName();
		$blockModelList = $moduleModel->getBlocks();
		foreach($blockModelList as $blockLabel=>$blockModel) {
			$fieldModelList = $blockModel->getFields();
			if (!empty ($fieldModelList)) {
				$values[vtranslate($blockLabel, $baseModuleName)] = array();
				foreach($fieldModelList as $fieldName=>$fieldModel) {
					if($fieldModel->isViewableInFilterView()) {
						$newFieldModel = clone $fieldModel;
						if($recordExists) {
							$newFieldModel->set('fieldvalue', $recordModel->get($fieldName));
						}
						$values[vtranslate($blockLabel, $baseModuleName)][$fieldName] = $newFieldModel;
					}
				}
			}
		}
		//All the reference fields should also be sent
		$fields = $moduleModel->getFieldsByType(array('reference'));


		//Disabeling this feature till further testing is possible
		$fields = [];

		foreach($fields as $parentFieldName => $field) {
			if ($field->isViewableInFilterView()){
				$referenceModules = $field->getReferenceList();
				foreach($referenceModules as $refModule) {
					if($refModule == 'Users') continue;
					$refModuleModel = Vtiger_Module_Model::getInstance($refModule);
					$blockModelList = $refModuleModel->getBlocks();
					$fieldModelList = null;
					foreach($blockModelList as $blockLabel=>$blockModel) {
						$fieldModelList = $blockModel->getFields();
						if (!empty ($fieldModelList)) {
							if(count($referenceModules) > 1) {
								//block label format : reference field label (modulename) - block label. Eg: Related To (Organization) Address Details
								$newblockLabel = vtranslate($field->get('label'), $baseModuleName).' ('.vtranslate($refModule, $refModule).') - '.
									vtranslate($blockLabel, $refModule);
							}
							else {
								$newblockLabel = vtranslate($refModule) . ' - ' .vtranslate($blockLabel, $refModule);
							}
							$values[$newblockLabel] = array();
							$fieldModel = $fieldName = null;
							foreach($fieldModelList as $fieldName=>$fieldModel) {
								if($fieldModel->isViewableInFilterView() && $fieldModel->getDisplayType() != '5') {
									$newFieldModel = clone $fieldModel;
									$name = "($parentFieldName ; ($refModule) $fieldName)";
									$label = vtranslate($field->get('label'), $baseModuleName).'-'.vtranslate($fieldModel->get('label'), $refModule);
									$newFieldModel->set('reference_fieldname', $name)->set('label', $label);
									$values[$newblockLabel][$name] = $newFieldModel;
								}
							}
						}
					}
				}
			}
		}
		
		//Adding GuestBlocks Fields
		
		
		$guestBlocksModules = $moduleModel->getGuestBlockForModule();
		foreach ($guestBlocksModules as $guestBlocksModuleName) {

			if($guestBlocksModuleName == 'MoveRoles'){
				$newblockLabel = vtranslate($guestBlocksModuleName, $guestBlocksModuleName);
				$values[$newblockLabel] = array();

				$personalRolesArray = EmployeeRoles_Module_Model::getMoveRolesForUser();
	

				foreach($personalRolesArray as $fieldName) {
					
						$newFieldModel = MoveRoles_Field_Model::getFieldModelFromName($fieldName);
						$label = explode('_',$newFieldModel->get('label'));
						array_shift($label);
						$label = implode(' ', $label);
						$newFieldModel->set('label',$label);
						$newFieldModel->set('reference_fieldname', $name);
						$values[$newblockLabel][$newFieldModel->get('name')] = $newFieldModel;
					
				}


			}else{
				//@TODO: Check with eric is need to keep this.
				$guestBlocksModuleModel = Vtiger_Module_Model::getInstance($guestBlocksModuleName);
				$blockModelList = $guestBlocksModuleModel->getBlocks();
				$fieldModelList = null;
				foreach($blockModelList as $blockLabel=>$blockModel) {
					$fieldModelList = $blockModel->getFields();
					$newblockLabel = vtranslate($guestBlocksModuleName) . ' - ' .vtranslate($blockLabel, $guestBlocksModuleName);
					if (!empty ($fieldModelList)) {
						$values[$newblockLabel] = array();
						$fieldModel = $fieldName = null;
						foreach($fieldModelList as $fieldName=>$fieldModel) {
							if($fieldModel->isViewableInFilterView() && $fieldModel->getDisplayType() != '5') {
								$newFieldModel = clone $fieldModel;
								$name = "(guest_blocks ; ($guestBlocksModuleName) $fieldName)";
								$label = vtranslate($guestBlocksModuleName) . ' - ' . vtranslate($fieldModel->get('label'), $guestBlocksModuleName);
								$newFieldModel->set('reference_fieldname', $name)->set('label', $label);
								$values[$newblockLabel][$name] = $newFieldModel;
							}
						}
					}
				}
			}

			
			
		}

		$this->structuredValues = $values;
		return $values;
	}
}