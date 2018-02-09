<?php
class ItemCodes_ListView_Model extends Vtiger_ListView_Model
{
    public function getListViewHeaders()
    {
        $listViewContoller = $this->get('listview_controller');
        $module = $this->getModule();
        $headerFieldModels = array();
        $headerFields = $this->getListViewHeaderFields();
        foreach ($headerFields as $fieldName => $webserviceField) {
            if ($webserviceField && !in_array($webserviceField->getPresence(), array(0, 2))) {
                continue;
            }

            //VGS - Add this new if to show the fields from related module.
            if ($webserviceField->getTabId() != $module->id) {
                $fieldModel=Vtiger_Field_Model::getInstance($fieldName, Vtiger_Module_Model::getInstance(getTabModuleName($webserviceField->getTabId())));
                $headerFieldModels[$fieldName] =$fieldModel;
            } else {
                $headerFieldModels[$fieldName] = Vtiger_Field_Model::getInstance($fieldName, $module);
            }
        }
        return $headerFieldModels;
    }

    public function getListViewHeaderFields()
    {
        $this->queryGenerator = $this->get('query_generator');
        $meta = $this->queryGenerator->getMeta($this->queryGenerator->getModule());
        $moduleFields = $this->queryGenerator->getModuleFields();
        $fields = $this->queryGenerator->getFields();
        $headerFields = array();
        foreach ($fields as $fieldName) {
            if (array_key_exists($fieldName, $moduleFields)) {
                $headerFields[$fieldName] = $moduleFields[$fieldName];
            }
        }
        return $headerFields;
    }
}
