<?php
include_once 'vtlib/Vtiger/Field.php';

class WFLineItems_Field_Model extends Vtiger_Field_Model {

  public function isReadOnly()
  {

    if($this->getName() == 'onhand' || $this->getName() == 'processed') {
      return true;
    }

    parent::isReadOnly();
  }

  public function isEmptyPicklistOptionAllowed()
  {
    return false;
  }
}
