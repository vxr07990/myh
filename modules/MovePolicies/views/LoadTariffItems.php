<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class MovePolicies_LoadTariffItems_View extends Vtiger_IndexAjax_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('LoadtariffEdit');
        $this->exposeMethod('LoadTariff');
    }

    public function LoadtariffEdit(Vtiger_Request $request)
    {
        $tariffId = $request->get('tariff_id');
        $movePolicyId = $request->get('movepolicy_id');

        $tariffModel = Vtiger_Record_Model::getInstanceById($tariffId, 'TariffManager');
        $tariffItems = @$tariffModel->getAllowedTariffItems(); //Hide nsoup erros :(

        $i = 0;
        foreach ($tariffItems as $tariffItem) {
            $i++;
            $tariffItem['tmp_id'] = $i;
            $tariffItemsSec[$tariffItem['SectionID']][] = $tariffItem;
        }

        ksort($tariffItemsSec);
        
        $viewer = $this->getViewer($request);
        $viewer->assign('TARIFF_ITEMS', $tariffItemsSec);

        if ($movePolicyId == '') {
            $viewer->assign('IS_NEW', true);
            $viewer->assign('ITEMS_COUNT', $i);
        }
        
        echo $viewer->view('EditViewTariffItems.tpl', 'MovePolicies', true);
    }
    
    public function LoadTariff(Vtiger_Request $request)
    {
        $contractId = $request->get('contract_id');
        
        if ($contractId == '') {
            echo 'No';
        } else {
            $contractModel = Vtiger_Record_Model::getInstanceById($contractId, 'Contracts');
            $tariffId = $contractModel->get('related_tariff');
            if ($tariffId != '' && $tariffId != 0) {
                $tariffName = getEntityName('TariffManager', array($tariffId));
                $tariffName =  $tariffName[$tariffId];
                
                echo $tariffId . '::' . $tariffName;
            } else {
                echo 'No';
            }
        }
    }
}
