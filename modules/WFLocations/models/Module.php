<?php

class WFLocations_Module_Model extends Vtiger_Module_Model {
    /**
     * Function to get list view query for popup window
     * @param <String> $sourceModule Parent module
     * @param <String> $field parent fieldname
     * @param <Integer> $record parent id
     * @param <String> $listQuery
     * @return <String> Listview Query
     */
    public function getQueryByModuleField($sourceModule, $field, $record, $listQuery)
    {
        if($record == ''){
            $record = 0;
        }
        if (($sourceModule == 'WFLocations' && $field == 'wflocation_base')) {
            $position = stripos($listQuery, 'where');
            if ($position) {
                $split = preg_split('/where/i', $listQuery);
                $overRideQuery = $split[0] . '
                                INNER JOIN vtiger_wflocationtypes ON vtiger_wflocationtypes.wflocationtypesid=vtiger_wflocations.wflocation_type
                                WHERE vtiger_wflocations.wflocationsid !='.$record.' AND vtiger_wflocationtypes.container=1 AND'.$split[1];
            } else {
                $overRideQuery = $listQuery. '
                                INNER JOIN vtiger_wflocationtypes ON vtiger_wflocationtypes.wflocationtypesid=vtiger_wflocations.wflocation_type
                                WHERE vtiger_wflocationtypes.container=1 AND vtiger_wflocations.wflocationsid !='. $record;
            }
            return $overRideQuery;
        }
    }
    function getDuplicateCheckFields()
    {
        return Zend_Json::encode(['tag', 'wflocation_warehouse']);
    }

    public function saveRecord($recordModel) {
      $request = new Vtiger_Request($_REQUEST);

      if($recordModel->getId()) {
        $request->set('record',$recordModel->getId());
        WFLocationHistory_CreateHistory_Action::process($request);
      }
      parent::saveRecord($recordModel);
    }
}
