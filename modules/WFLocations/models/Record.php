<?php

class WFLocations_Record_Model extends Vtiger_Record_Model
{
  /**
   * Updates the base location of a record object
   *
   * @param     WFLocations Object $location
   * @return    void
   */
  public function updateBaseLocation($location,$request) {
    $this->set('mode','edit');
    $this->set('wflocation_base',$location->get('id'));
    $request->set('current_location',$this->get('id'));
    $this->save($request,$location);
  }

  /**
   * Returns true/false if the location is/is not a base location
   *
   * @return    Boolean
   */
  public function isBaseLocation() {
    $db = PearDatabase::getInstance();
    $result = $db->pquery("SELECT `base` FROM `vtiger_wflocationtypes` WHERE wflocationtypesid = ?",[$this->get('wflocation_type')]);
    return $result->fetchRow()['base'] ? true : false;
  }
}
