<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vehicles_ListView_Model extends Vtiger_ListView_Model
{
    public function getQuery()
    {
        $queryGenerator = $this->get('query_generator');
        $listQuery = $queryGenerator->getQuery();

        $request = new Vtiger_Request($_REQUEST, $_REQUEST);
        if (($request->get('popup_type') != '' && $request->get('popup_type') == 'get_vehicles') || $request->get('src_module') == 'Trips') {
            $recordId = $request->get('src_record');
            
            $newQuery = explode('GROUP BY', $listQuery);
            $groupBy = $newQuery[1];

            $newQuery = explode('WHERE', $newQuery[0]);
            $newQuerySelect = $newQuery[0];
            $newQueryWhere = $newQuery[1];
            $newQueryWhere .= " AND vtiger_vehicles.vehiclesid 
			NOT IN (
				SELECT outofservice_vehicle 
				FROM vtiger_vehicleoutofservice 
                		WHERE outofservice_status = 'Out of Service' AND outofservice_reinstated_date IS NULL AND outofservice_vehicle IS NOT NULL
			)";
            
            $listQuery = $newQuerySelect . ' WHERE ' . $newQueryWhere . ' GROUP BY ' . $groupBy;
        }
        return $listQuery;
    }
}
