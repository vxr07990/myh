<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/9/2017
 * Time: 3:14 PM
 */

class Orders_ValidateAssociateEmail_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request) {
        $db = &PearDatabase::getInstance();
        $id = $request->get('id');
        $result = [];
        if($id)
        {
            $res = $db->pquery('SELECT employeesid, employee_email, `name`, employee_lastname FROM vtiger_employees WHERE employeesid IN(?)', [$id]);
            while ($row = $res->fetchRow()) {
                if (!filter_var($row['employee_email'], FILTER_VALIDATE_EMAIL)) {
                    $result[] = [
                        'id' => $row['employeesid'],
                        'name' => $row['name'] . ' ' . $row['employee_lastname'],
                    ];
                }
            }
        }

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}

