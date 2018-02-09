<?php

class PicklistCustomizer_ActionAjax_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    function __construct()
    {
        parent::__construct();
        $this->exposeMethod('getPickListFieldsForModule');
        $this->exposeMethod('checkCyclicDependency');
        $this->exposeMethod('savePicklistDependency');
        $this->exposeMethod('getCustomPicklistValues');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);

            return;
        }
    }

    public function getPickListFieldsForModule(Vtiger_Request $request){
        $moduleName = $request->get('selectedModule');
        $options = PicklistCustomizer_Module_Model::getFieldsForModule($moduleName,true);
        $response = new Vtiger_Response();
        $response->setResult($options);
        $response->emit();
    }

    public function checkCyclicDependency(Vtiger_Request $request)
    {
        $module = $request->get('sourceModule');
        $sourceField = $request->get('sourcefield');
        $targetField = $request->get('targetfield');
        $result = Vtiger_DependencyPicklist::checkCyclicDependency($module, $sourceField, $targetField);
        $response = new Vtiger_Response();
        $response->setResult(array('result'=>$result));
        $response->emit();
    }

    public function savePicklistDependency(Vtiger_Request $request){

        global $adb;
        $tabId = getTabid($request->get('selectedmodule'));
        $sourceField = $request->get('sourcefield');
        $targetFieldModel = Vtiger_Field_Model::getInstance($request->get('target_field_select'), Vtiger_Module_Model::getInstance($request->get('selectedmodule')));
        $targetField = $targetFieldModel->get('name');
        $agentId = $request->get('agentid');

        $valueMapping = $request->get('mapping');
        for ($i=0; $i<count($valueMapping); ++$i) {
            $mapping = $valueMapping[$i];
            $sourceValue = $mapping['sourcevalue'];
            $targetValues = $mapping['targetvalues'];
            $serializedTargetValues = Zend_Json::encode($targetValues);

            $optionalsourcefield = $mapping['optionalsourcefield'];
            $optionalsourcevalues = $mapping['optionalsourcevalues'];

            if (!empty($optionalsourcefield)) {
                $criteria = array();
                $criteria["fieldname"] = $optionalsourcefield;
                $criteria["fieldvalues"] = $optionalsourcevalues;
                $serializedCriteria = Zend_Json::encode($criteria);
            } else {
                $serializedCriteria = null;
            }
            //to handle Accent Sensitive search in MySql
            //reference Links http://dev.mysql.com/doc/refman/5.0/en/charset-convert.html , http://stackoverflow.com/questions/500826/how-to-conduct-an-accent-sensitive-search-in-mysql
            $checkForExistenceResult = $adb->pquery("SELECT vtiger_picklist_dependency.id FROM vtiger_picklist_dependency "
                    . " INNER JOIN vtiger_custom_picklist_dependency ON vtiger_custom_picklist_dependency.picklistdependencyid = vtiger_picklist_dependency.id"
                    . " WHERE agentid=? AND tabid=? AND sourcefield=? AND targetfield=? AND sourcevalue=CAST(? AS CHAR CHARACTER SET utf8) COLLATE utf8_bin",
                    array($agentId,$tabId, $sourceField, $targetField, $sourceValue));
            if ($adb->num_rows($checkForExistenceResult) > 0) {
                $dependencyId = $adb->query_result($checkForExistenceResult, 0, 'id');
                $adb->pquery("UPDATE vtiger_picklist_dependency SET targetvalues=?, criteria=? WHERE id=?",
                        array($serializedTargetValues, $serializedCriteria, $dependencyId));
            } else {
                $picklistNewId = $adb->getUniqueID('vtiger_picklist_dependency');
                $adb->pquery("INSERT INTO vtiger_picklist_dependency (id, tabid, sourcefield, targetfield, sourcevalue, targetvalues, criteria)
								VALUES (?,?,?,?,?,?,?)",
                        array($picklistNewId, $tabId, $sourceField, $targetField, $sourceValue,
                        $serializedTargetValues, $serializedCriteria));
                if($agentId && $agentId != ''){
                    //insert into custom table the picklist id and the owner id
                    $adb->pquery("INSERT INTO vtiger_custom_picklist_dependency (picklistdependencyid,agentid) VALUES (?,?)",
                            array($picklistNewId,$agentId));
                }
            }
        }
        $response = new Vtiger_Response();
        $response->setResult(array('success'=>true));
        $response->emit();
    }

    public function getCustomPicklistValues(Vtiger_Request $request){
        $db = PearDatabase::getInstance();
        $agentid = $request->get('agentid');
        $module  = $request->get('targetModule');

        $sql = "SELECT `vtiger_field`.`fieldname`, `vtiger_field`.fieldid FROM `vtiger_field` JOIN `vtiger_tab` ON `vtiger_field`.`tabid`=`vtiger_tab`.`tabid` WHERE `vtiger_tab`.`name`=? AND `vtiger_field`.`uitype`=1500";
        $result = $db->pquery($sql, [$module]);

        $newPicklistValues = [];
        while($row =& $result->fetchRow()) {
            $options = Vtiger_Util_Helper::getCustomPicklistValues($row['fieldname'], $row['fieldid'], $agentid);

            $sortedOptions = [];
            foreach($options as $value) {
                $sortedOptions[$value] = vtranslate($value, $module);
            }
            asort($sortedOptions);

            $newPicklistValues[$row['fieldname']] = $sortedOptions;
        }
//        $options = Vtiger_Util_Helper::getPicklistValues($fieldName,true,$agentid);
        $response = new Vtiger_Response();
        $response->setResult($newPicklistValues);
        $response->emit();
    }
}
