<?php

class OrdersTask_ExportData_Action extends Vtiger_ExportData_Action
{
    public function ExportData(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $moduleName = $request->get('source_module');

        $this->moduleInstance = Vtiger_Module_Model::getInstance($moduleName);
        $this->moduleFieldInstances = $this->moduleInstance->getFields();
        $this->focus = CRMEntity::getInstance($moduleName);

        $orderInstance = Vtiger_Module_Model::getInstance("Orders");
        $orderFieldInstances = $orderInstance->getFields();
        
        $query = $this->getExportQuery($request);
        $result = $db->query($query);

        $headers = [];
        $refenceFields = [];
        if (!empty($this->accessibleFields)) {
            $accessiblePresenceValue = [0,2];
            foreach ($this->accessibleFields as $fieldName) {
                if ($fieldName !== "orderstaskid") {
                    $fieldModel = ($fieldName !== "orders_contacts") ? $this->moduleFieldInstances[$fieldName] : $orderFieldInstances[$fieldName];
                    $presence = $fieldModel->get('presence');
                    if (in_array($presence, $accessiblePresenceValue)) {
                        $headers[] = $fieldModel->get('label');
                    }
                    if ($fieldModel->isReferenceField()) {
                        $refenceFields[] = $fieldName;
                    }
                } else {
                    $headers[] = "ID";
                }
            }
        } else {
            foreach ($this->moduleFieldInstances as $field) {
                $headers[] = $field->get('label');
            }
        }
        $translatedHeaders = [];
        foreach ($headers as $header) {
            $translatedHeaders[] = vtranslate(html_entity_decode($header, ENT_QUOTES), $moduleName);
        }

        $entries = [];
        for ($j=0; $j<$db->num_rows($result); $j++) {
            $entries[] = $db->fetchByAssoc($result, $j);
        }
        for ($k=0; $k<count($entries); $k++) {
            if (!empty($entries[$k][assigned_vehicles]) && $entries[$k][assigned_vehicles] !== '') {
                $names = OrdersTask_Record_Model::getVehicles($entries[$k][assigned_vehicles]);
                $entries[$k][assigned_vehicles] = implode(', ', array_values($names));
            }
            if (!empty($entries[$k][assigned_employee]) && $entries[$k][assigned_employee] !== '') {
                $employeeArray = array_filter(explode(' |##| ', $entries[$k][assigned_employee]));
                $names = OrdersTask_Record_Model::getEmployeesNames('Employees', $employeeArray);
                $string = implode(', ', array_values($names));
                $entries[$k][assigned_employee] = $string;
            }
            foreach ($refenceFields as $rfield) {
                $name = Vtiger_Util_Helper::getLabel($entries[$k][$rfield]);
                if ($name && $name !== '') {
                    $entries[$k][$rfield] = $name;
                }
            }
        }
        $this->output($request, $translatedHeaders, $entries);
    }

    public function getExportQuery(Vtiger_Request $request)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $moduleName = $request->get('source_module');
        $cvID = $request->get('viewname');
        $taskArr = $request->get('selected_ids');
        
        $queryGenerator = new QueryGenerator($moduleName, $currentUser);
        $queryGenerator->initForCustomViewById($cvID);
        
        $this->accessibleFields = $queryGenerator->getFields();
        array_splice($this->accessibleFields, -1, 1); // Remove element "id"
        array_push($this->accessibleFields, "orderstaskid"); // Add element "orderstaskid"
        $query = $queryGenerator->getQuery();

        $aux = explode('INNER JOIN', $query);
        if (strpos($aux[2], 'vtiger_orders')) {
            $anotherAux = explode("WHERE", $aux[2]);
            $anotherAux[0] = " vtiger_orders ON vtiger_orderstask.ordersid = vtiger_orders.ordersid ";
            $aux[2] = $anotherAux[0] . " WHERE vtiger_orderstask.orderstaskid IN (" . $taskArr . ")";
            $query = $aux[0] . " INNER JOIN " . $aux[1] . " INNER JOIN " . $aux[2];
        }
        
        return $query;
    }
}
