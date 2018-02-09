<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
include_once 'vtlib/Vtiger/Field.php';

/**
 * Vtiger Field Model Class
 */
class MoveRoles_Field_Model extends Vtiger_Field_Model
{
    public static function getFieldModelFromName($fieldName){


        $newFieldModel = new Vtiger_Field_Model;
        $newFieldModel->set('label',$fieldName);
        $newFieldModel->set('table','vtiger_moveroles');
        $newFieldModel->set('column','moveroles_employees');
        $newFieldModel->set('typeofdata','V~O');
        $newFieldModel->set('presence',2);
        $newFieldModel->set('module',Vtiger_Module_Model::getInstance('MoveRoles'));
        $newFieldModel->set('uitype',2);
        $newFieldModel->getWebserviceFieldObject();

        $name = "(guest_blocks ; (MoveRoles) $fieldName)";
        $newFieldModel->set('name', $name);
        $newFieldModel->set('reference_fieldname', $name);

        return $newFieldModel;
    }
    
    public static function getInstance($value, $module = false)
    {
        $fieldObject = null;
        if ($module) {
            $fieldObject = Vtiger_Cache::get('field-'.$module->getId(), $value);
        }
        if (!$fieldObject) {
            $fieldObject = parent::getInstance($value, $module);
            if ($module) {
                Vtiger_Cache::set('field-'.$module->getId(), $value, $fieldObject);
            }
        }
        if ($fieldObject) {
            return self::getInstanceFromFieldObject($fieldObject);
        }

        return false;
    }

    public function getCustomViewColumnName(){
        parent::getCustomViewColumnName();
    }
}
