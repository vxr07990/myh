<?php

class MoveRoles_Record_Model extends Vtiger_Record_Model {

    public function getName() {
        $db = PearDatabase::getInstance();
        $sql = "SELECT `name`, `employee_lastname`, `emprole_desc`
                  FROM `vtiger_moveroles` 
                  JOIN `vtiger_employees`
                    ON `vtiger_moveroles`.moveroles_employees=`vtiger_employees`.employeesid 
                  JOIN `vtiger_employeeroles`
                    ON `vtiger_moveroles`.moveroles_role=`vtiger_employeeroles`.employeerolesid 
                 WHERE moverolesid=?";

        $result = $db->pquery($sql, [$this->getId()]);
        if($result) {
            return $result->fields['name'].' '.$result->fields['employee_lastname'].' as '.$result->fields['emprole_desc'];
        }

        return parent::getName();
    }
}
