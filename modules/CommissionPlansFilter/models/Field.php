<?php

/**
 * CommissionPlanFilter Field Model Class
 */
class CommissionPlansFilter_Field_Model extends Vtiger_Field_Model
{
    public function getFieldInfo()
    {
        $this->fieldInfo = parent::getFieldInfo();
        if ($this->getName() == 'related_tariff') {
            $referenceList = $this->getReferenceList();
            $this->fieldInfo['reference_module'] = $referenceList;
        }

        return $this->fieldInfo;
    }
    public function getReferenceValues($referenceModule, $seachValue ='')
    {
        $fieldName = $this->getName();
        if(!is_array($referenceModule) && $referenceModule == 'Contracts') {
            $referenceModule=array($referenceModule);
        }
        if (is_array($referenceModule)) {
            global $list_max_entries_per_page;
            $values = array();
            $fieldValue = $this->get('fieldvalue');

            $cache = Vtiger_Cache::getInstance();
            if ($cache->getPicklistValues($fieldName)) {
                return $cache->getPicklistValues($fieldName);
            }

            $db = PearDatabase::getInstance();
            $seachConditionsParams=array();
            if (!empty($seachValue)) {
                $seachValue = "%$seachValue%";
                $seachConditions = " AND label LIKE ? ";
                $seachConditionsParams[]=$seachValue;
            } else {
                $seachConditions = '';
            }

            if(!getenv('DISABLE_REFERENCE_FIELD_LIMIT_BY_OWNER')) {
                if($_REQUEST['agentid']) {
                    $agentId = $_REQUEST['agentid'];
                    $userRecordModel = new Users_Record_Model();
                    $agentIdsRecord = $userRecordModel->getAgentParent($agentId, array());
                    $agentIdsRecord[] = $agentId;
                    $countAgentIdsRecord = count($agentIdsRecord);
                    if ($countAgentIdsRecord>0){
                        $InValuesSql = str_repeat( '?,' , $countAgentIdsRecord-1).'?';

                        $seachConditions .= " AND (vtiger_crmentity.agentid IN ($InValuesSql) OR IFNULL(vtiger_crmentity.agentid,'') = '') ";

                        foreach ($agentIdsRecord as $agent){
                            $seachConditionsParams[]= $agent;
                        }
                    }
                }
            }

            if (!empty($fieldValue)) {
                $fieldValueList = explode(',', $fieldValue);

                $query = "SELECT crmid,label FROM
                          ((SELECT crmid , label,createdtime,2 AS seq  FROM vtiger_crmentity
                          WHERE label IS NOT NULL AND TRIM(label) != '' AND  crmid in (".generateQuestionMarks($fieldValueList).") AND deleted = 0
                          ORDER BY createdtime DESC
                          LIMIT $list_max_entries_per_page)
                          UNION
                          (SELECT crmid , label, createdtime,1 AS seq  FROM vtiger_crmentity
                          WHERE label IS NOT NULL AND TRIM(label) != '' AND setype in (".generateQuestionMarks($referenceModule).") AND deleted = 0 $seachConditions
                          ORDER BY createdtime DESC
                          LIMIT $list_max_entries_per_page )) AS E
                      GROUP BY E.crmid
                      ORDER BY E.seq DESC, E.createdtime DESC
                      LIMIT $list_max_entries_per_page";
                $params = array($fieldValueList,$referenceModule);
            } elseif ($referenceModule[0] == 'Tariffs') {
                $query = "SELECT crmid, label FROM `vtiger_crmentity` JOIN `vtiger_tariffs` ON `vtiger_crmentity`.crmid=`vtiger_tariffs`.tariffsid
                            WHERE setype IN (".generateQuestionMarks($referenceModule).") AND deleted = 0 AND (vtiger_tariffs.`tariff_status` != 'Inactive' OR vtiger_tariffs.`tariff_status` IS NULL) $seachConditions
                            ORDER BY createdtime DESC
                            LIMIT $list_max_entries_per_page";
                $params = $referenceModule;
            } else {
                $query = "SELECT crmid , label FROM vtiger_crmentity
                      WHERE setype in (".generateQuestionMarks($referenceModule).") AND deleted = 0 $seachConditions
                      ORDER BY createdtime DESC
                      LIMIT $list_max_entries_per_page";
                $params = $referenceModule;
            }

            if($seachConditions !='') {
                foreach($seachConditionsParams as $conditionParam) {
                    $params[]=$conditionParam;
                }
            }

            $result = $db->pquery($query, $params);

            $num_rows = $db->num_rows($result);
            for ($i=0; $i<$num_rows; $i++) {
                //                Need to decode the picklist values twice which are saved from old ui
                $values[$db->query_result($result, $i, 'crmid')] = decode_html(decode_html($db->query_result($result, $i, 'label')));
            }
            $cache->setPicklistValues($fieldName, $values);
            return $values;
        } else {
            return parent::getReferenceValues($referenceModule, $seachValue);
        }
    }
}
