<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 12/14/2016
 * Time: 2:56 PM
 */

class Estimates_ViewHandler
{
    public $view = false;
    public $instanceName = '';

    public function __construct(Vtiger_View_Controller $controller)
    {
        $this->instanceName = getenv('INSTANCE_NAME');
        $this->view = $controller;

        $commonBlocks = new Block_View_Handler('Estimates', $this, 'COMMON_INFO', 'CommonDetail.tpl', 'CommonEdit.tpl', 'viewCommonInfo',
                                                ['modules.Estimates.resources.MiscItems',
                                                 'modules.Estimates.resources.Contract']);
        if($this->instanceName == 'graebel')
        {
            $commonBlocks->addJS('modules.Estimates.resources.GVL');
            $commonBlocks->addSubBlocks(['LBL_QUOTE_INFORMATION', 'LBL_QUOTES_CONTACTDETAILS', 'LBL_ADDRESS_INFORMATION']);
        } else if($this->instanceName == 'sirva')
        {
            $commonBlocks->addJS('modules.Estimates.resources.SIRVA');
            $commonBlocks->addSubBlocks(['LBL_QUOTE_INFORMATION',
                                         'LBL_ESTIMATES_DATES',
                                         'LBL_QUOTES_CONTACTDETAILS',
                                         'LBL_ADDRESS_INFORMATION'
                                        ]);
        } else {
            $commonBlocks->addJS('modules.Estimates.resources.MoveCRM');
            $commonBlocks->addSubBlocks(['LBL_QUOTE_INFORMATION', 'LBL_QUOTES_CONTACTDETAILS', 'LBL_ADDRESS_INFORMATION']);
            if(getenv('IGC_MOVEHQ'))
            {
                $commonBlocks->addJS('modules.ExtraStops.resources.EditBlock');
                $commonBlocks->addSubBlocks(['ExtraStops']);
            }
        }

        $this->view->abstractBlocks[] = $commonBlocks;

        if(getenv('GOOGLE_ADDRESS_MILES_CALCULATOR'))
        {
            $this->view->abstractBlocks[] = (new Block_View_Handler('Estimates', $this, 'GOOGLE_CALCULATOR', 'GoogleMilesCalculator.tpl', 'GoogleMilesCalculator.tpl', 'viewGoogleMilesCalculator',
                                                                    'modules.Estimates.resources.GoogleCalculator'))
                ->addSubBlocks(['GOOGLE_CALCULATOR']);
        }

        if($this->instanceName == 'sirva')
        {
            $this->view->abstractBlocks[] = (new Block_View_Handler('AddressSegments', $this, 'ADDRESS_SEGMENTS_TABLE', 'AddressSegmentsDetail.tpl', 'AddressSegmentsEdit.tpl', 'viewAddressSegments',
                                                                    'modules.AddressSegments.resources.EditBlock'))
                ->addSubBlocks(['ADDRESS_SEGMENTS_TABLE']);
        }

        $interstateMoveDetails = (new Block_View_Handler('Estimates', $this, 'INTERSTATE_MOVE_DETAILS', 'CommonDetail.tpl', 'CommonEdit.tpl', 'viewInterstateMoveDetails', null))
            ->addSubBlocks(['LBL_QUOTES_INTERSTATEMOVEDETAILS']);

        if($this->instanceName == 'sirva')
        {
            $interstateMoveDetails->addSubBlocks(['LBL_QUOTES_TPGPRICELOCK']);
        }
        $this->view->abstractBlocks[] = $interstateMoveDetails;

        if($this->instanceName == 'graebel' || $this->instanceName == 'sirva')
        {
            $this->view->abstractBlocks[] = (new Block_View_Handler('Estimates', $this, 'INTERSTATE_SERVICE_CHARGES', 'InterstateServiceChargesDetail.tpl', 'InterstateServiceChargesEdit.tpl', 'viewInterstateServiceCharges', null))
                ->addSubBlocks(['INTERSTATE_SERVICE_CHARGES']);
        }
        $this->view->abstractBlocks[] = (new Block_View_Handler('Estimates', $this, 'VALUATION', 'CommonDetail.tpl', 'CommonEdit.tpl', 'viewValuationInfo', 'modules.Valuation.resources.Common'))
            ->addSubBlocks(['LBL_QUOTES_VALUATION']);
        $this->view->abstractBlocks[] = (new Block_View_Handler('Estimates', $this, 'INTERSTATE_MOVE_SERVICES', 'RateEstimateDetail.tpl', 'RateEstimateEdit.tpl', 'viewInterstateMoveServices', null))
            ->addSubBlocks(['INTERSTATE_SERVICES']);
        $this->view->abstractBlocks[] = (new Block_View_Handler('Estimates', $this, 'INTERSTATE_MISC_CHARGES', 'MiscChargesDetail.tpl', 'MiscChargesEdit.tpl', 'viewInterstateMiscCharges', null))
            ->addSubBlocks(['INTERSTATE_MISC_CHARGES']);

        if($this->instanceName == 'sirva')
        {
            $this->view->abstractBlocks[] = (new Block_View_Handler('Estimates', $this, 'SIRVA_VEHICLES', 'SirvaVehiclesDetail.tpl', 'SirvaVehiclesEdit.tpl', 'viewSirvaVehicles',
                                                                    null))
                ->addSubBlocks(['SIRVA_VEHICLES']);
        }

        $this->view->abstractBlocks[] = (new Block_View_Handler('Estimates', $this, 'LOCAL_MOVE_DETAILS', 'CommonDetail.tpl', 'CommonEdit.tpl', 'viewLocalMoveDetails', null))
            ->addSubBlocks(['LBL_QUOTES_LOCALMOVEDETAILS']);
        $this->view->abstractBlocks[] = (new Block_View_Handler('Estimates', $this, 'LOCAL_MOVE_CONTENTS', 'LocalBlockDetail.tpl', 'LocalBlockEdit.tpl', 'viewLocalMoveContents', null))
            ->addSubBlocks(['LOCAL_MOVE_CONTENTS']);
        $this->view->abstractBlocks[] = (new Block_View_Handler('Estimates', $this, 'CUSTOM_MISC_CHARGES', 'CustomChargesDetail.tpl', 'CustomChargesEdit.tpl', 'viewCustomMiscCharges', null))
            ->addSubBlocks(['CUSTOM_MISC_CHARGES']);
        if($this->instanceName == 'graebel') {
            $this->view->abstractBlocks[] = (new Block_View_Handler('Contracts', $this, 'FLAT_RATE_AUTO', 'AutoRateTableDetail.tpl', 'AutoRateTableEdit.tpl', 'viewAutoRateTable', NULL))
                ->addSubBlocks(['FLAT_RATE_AUTO']);
            $this->view->abstractBlocks[] = (new Block_View_Handler('Estimates', $this, 'GUEST_BLOCKS', 'CommonDetail.tpl', 'CommonEdit.tpl', 'viewGuestBlocks',
                                                                    ['modules.VehicleTransportation.resources.EditBlock',
                                                                     'modules.ExtraStops.resources.EditBlock']))
                ->addSubBlocks(['ExtraStops', 'UpholsteryFineFinish', 'VehicleTransportation']);
            $this->view->abstractBlocks[] = (new Block_View_Handler('Estimates', $this, 'TRANSPORTATION_PRICING', 'CommonDetail.tpl', 'CommonEdit.tpl', 'viewTransportationPricing', NULL))
                ->addSubBlocks(['LBL_QUOTES_TRANSPORTATIONPRICING']);
        } else
        {
            if(getenv('IGC_MOVEHQ'))
            {
                $this->view->abstractBlocks[] = (new Block_View_Handler('Estimates', $this, 'GUEST_BLOCKS', 'CommonDetail.tpl', 'CommonEdit.tpl', 'viewGuestBlocks',
                                                                        []))
                    ->addSubBlocks(['VehicleLookup']);
            } else {
                $this->view->abstractBlocks[] = (new Block_View_Handler('Estimates', $this, 'GUEST_BLOCKS', 'CommonDetail.tpl', 'CommonEdit.tpl', 'viewGuestBlocks',
                                                                        ['modules.ExtraStops.resources.EditBlock']))
                    ->addSubBlocks(['ExtraStops','VehicleLookup']);
            }
        }

        $this->view->abstractBlocks[] = (new Block_View_Handler('Estimates', $this, 'ADDITIONAL_INFO', 'CommonDetail.tpl', 'CommonEdit.tpl', 'viewAdditionalInfo', null))
            ->addSubBlocks(['LBL_TERMS_INFORMATION','LBL_DESCRIPTION_INFORMATION']);
        if(getenv('IGC_MOVEHQ')) {
            if ($this->instanceName == 'graebel') {
                $this->view->abstractBlocks[] =
                    (new Block_View_Handler('Estimates',
                                            $this,
                                            'DETAILED_LINE_ITEMS',
                                            'DetailLineItemDetail.tpl',
                                            'DetailLineItemEdit.tpl',
                                            'viewDetailedLineItems',
                                            'modules.Estimates.resources.LineItems'))
                        ->addSubBlocks(['DETAILED_LINE_ITEMS']);
            } else {
                $this->view->abstractBlocks[] =
                    (new Block_View_Handler('Estimates',
                                            $this,
                                            'DETAILED_LINE_ITEMS',
                                            'MoveHQLineItemDetail.tpl',
                                            'MoveHQLineItemDetail.tpl',
                                            'viewDetailedLineItems',
                                            'modules.Estimates.resources.LineItems'))
                        ->addSubBlocks(['DETAILED_LINE_ITEMS']);
            }
        }
        /* else if($this->instanceName == 'sirva') {
            $this->view->abstractBlocks[] =
                (new Block_View_Handler('Estimates',
                                        $this,
                                        'LINE_ITEMS',
                                        'LineItemsDetail.tpl',
                                        'LineItemsEdit.tpl',
                                        'viewLineItems',
                                        'modules.Estimates.resources.SimpleLineItems'))
                    ->addSubBlocks(['LINE_ITEMS']);
        }*/
        else {
            $this->view->abstractBlocks[] =
                (new Block_View_Handler('Estimates',
                                        $this,
                                        'DETAILED_LINE_ITEMS',
                                        'MoveCRMLineItemDetail.tpl',
                                        'MoveCRMLineItemDetail.tpl',
                                        'viewDetailedLineItems',
                                        'modules.Estimates.resources.LineItems'))
                    ->addSubBlocks(['DETAILED_LINE_ITEMS']);
        }
    }

    public function viewCommonInfo(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $viewer->assign('IS_LOCAL_RATING', !$request->get('is_interstate'));
            if(getenv('INSTANCE_NAME') != 'graebel')
            {
                $data = Estimates_Record_Model::getAllowedTariffsForUser($this->view->recordModel->get('agentid'));
            } else {
                $data = Estimates_Record_Model::getAllowedTariffsForUser();
            }
            $viewer->assign('AVAILABLE_TARIFFS_DATA', $data);
            $viewer->assign('AVAILABLE_TARIFFS', Vtiger_Util_Helper::toSafeHTML(json_encode($data)));

        if($request->isEditView())
        {
            if(getenv('INSTANCE_NAME') == 'sirva') {
                $amount = $this->view->recordModel->get('valuation_amount');
                $viewer->assign('VALUATION_AMOUNT', number_format($amount, 2, '.', ''));
                if($amount > 250000)
                {
                    $values = [
                        'Over 250000' => 'Over 250,000.00'
                    ];
                    $viewer->assign('SELECT_VALUE', 'Over 250000');
                } else {
                    $values = [
                        $amount => number_format($amount, 2)
                    ];
                    $viewer->assign('SELECT_VALUE', number_format($amount,0,'.',''));
                }
                $viewer->assign('VALUATION_AMOUNT_VALUES', $values);
            }
        }
        $viewer->assign('IS_HIDDEN', 0);
        $viewer->assign('CONTENT_DIV_CLASS', '');
        //$viewer->assign('ALWAYS_SHOW_CONTENT_DIV', 1);
        $this->assignSubBlocks($blockHandler, $viewer, 1);
    }

    public function viewInterstateMoveDetails(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $active = $request->get('is_interstate');
        $viewer->assign('CONTENT_DIV_CLASS', 'interstateContent');
        $viewer->assign('IS_HIDDEN', 0);
        $this->assignSubBlocks($blockHandler, $viewer, $active);
    }

    public function viewLocalMoveDetails(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $tariffId = $request->get('effective_tariff_id');
        $active = $tariffId && !$request->get('is_interstate');
        $viewer->assign('CONTENT_DIV_CLASS', 'localMoveContent');
        $viewer->assign('IS_HIDDEN', 0);
        $this->assignSubBlocks($blockHandler, $viewer, $active);
    }

    public function viewValuationInfo(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $active = $request->get('is_interstate');
        $viewer->assign('CONTENT_DIV_CLASS', 'interstateContent');
        $viewer->assign('IS_HIDDEN', 0);
        $this->assignSubBlocks($blockHandler, $viewer, $active);
    }

    public function viewInterstateMoveServices(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $active = $request->get('is_interstate');
        $viewer->assign('CONTENT_DIV_CLASS', 'interstateContent');
        $viewer->assign('IS_HIDDEN', 1);

        if($this->instanceName == 'graebel')
        {
            $viewer->assign('ALLOWED_BLOCKS', [
                'LBL_QUOTES_SITDETAILS',
                'LBL_QUOTES_ACCESSORIALDETAILS',
            ]);
        } else if($this->instanceName == 'sirva')
        {
            $viewer->assign('ALLOWED_BLOCKS', [
                'LBL_QUOTES_SITDETAILS',
                'LBL_QUOTES_ACCESSORIALDETAILS',
                //'LBL_ESTIMATES_APPLIANCE',
                //'LBL_SPACE_RESERVATION',
            ]);
        } else if (getenv('IGC_MOVEHQ'))
        {
            $viewer->assign('ALLOWED_BLOCKS', [
                //                'LBL_QUOTES_VALUATIONDETAILS',
                'LBL_QUOTES_SITDETAILS',
                'LBL_QUOTES_ACCESSORIALDETAILS',
                'LBL_ESTIMATES_APPLIANCE',
                //'LBL_QUOTES_LONGCARRY',
                //'LBL_QUOTES_STAIR',
                //'LBL_QUOTES_ELEVATOR',
                'LBL_SPACE_RESERVATION',
                //                'LBL_SIT_DETAILS2',
            ]);
        }
        else {
            $viewer->assign('ALLOWED_BLOCKS', [
//                'LBL_QUOTES_VALUATIONDETAILS',
                'LBL_QUOTES_SITDETAILS',
                'LBL_QUOTES_ACCESSORIALDETAILS',
                'LBL_ESTIMATES_APPLIANCE',
                'LBL_QUOTES_LONGCARRY',
                'LBL_QUOTES_STAIR',
                'LBL_QUOTES_ELEVATOR',
                'LBL_SPACE_RESERVATION',
//                'LBL_SIT_DETAILS2',
            ]);
        }

        $this->assignSubBlocks($blockHandler, $viewer, $active);
    }

    public function viewLocalMoveContents(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $tariffId = $request->get('effective_tariff_id');
        $active = $tariffId && !$request->get('is_interstate');
        $viewer->assign('CONTENT_DIV_CLASS', 'localMoveContent');
        $viewer->assign('IS_HIDDEN', 1);
        $this->assignSubBlocks($blockHandler, $viewer, $active);
        if($active)
        {
            $effDate = $request->get('effective_date') ?: $this->view->recordModel->get('effective_date');
            try {
                $tariffInstance = Tariffs_Record_Model::getInstanceById($tariffId);
                $data           = $tariffInstance->getTariffDetails($effDate);
                $viewer->assign('TARIFF_DETAILS', $data);
            } catch (Exception $e)
            {
            }
        }
    }

    public function viewInterstateMiscCharges(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $active = $request->get('is_interstate');
        $viewer->assign('CONTENT_DIV_CLASS', 'interstateContent');
        $viewer->assign('IS_HIDDEN', 1);
        $this->assignSubBlocks($blockHandler, $viewer, $active);
        if($active)
        {
            $recordModel = $this->view->recordModel;
            $viewer->assign('BULKY_ITEMS', $recordModel->getBulkyItems());
            if(getenv('INSTANCE_NAME')!='sirva') {
                $viewer->assign('PACKING_LABELS', $recordModel->getPackingLabels());
                $viewer->assign('PACKING_ITEMS', $recordModel->getPackingItems($this->view->tariffInfo['custom_type']));
                $customTariffType = $this->view->tariffInfo['custom_type'];
                if ($customTariffType != 'RMX400'
                    && $customTariffType != 'RMW400'
                    && $customTariffType != '09CapRelo'
                    && $customTariffType != '400N Base') {
                    $viewer->assign('HAS_CONTAINERS', true);
                }
            } else {
                $viewer->assign('PACKING_LABELS', $recordModel->getPackingLabels());
                $viewer->assign('PACKING_ITEMS', $recordModel->getPackingItems($request->get('effective_tariff_id')));
            }
            if (getenv('INSTANCE_NAME') == 'sirva') {
                $customTariffTypeForUseCustomRates = ['TPG GRR', 'TPG', 'Pricelock GRR', 'Pricelock', 'Blue Express', 'Allied Express'];
                $viewer->assign('CUSTOM_TARIFF_TYPE_FOR_USE_CUSTOM_RATES', $customTariffTypeForUseCustomRates);
                $viewer->assign('SIRVA_OT_PACKING',
                                \MoveCrm\InputUtils::CheckboxToBool($this->view->recordModel->get('accesorial_ot_packing'))
                                ?'Yes':'No');
                $viewer->assign('SIRVA_OT_UNPACKING',
                                \MoveCrm\InputUtils::CheckboxToBool($this->view->recordModel->get('accesorial_ot_unpacking'))
                                    ?'Yes':'No');
                $viewer->assign('CUSTOM_PACKING', $recordModel->getCustomPackingOverride());
                $viewer->assign('HIDE_PACKING_CONTAINERS', $this->view->tariffInfo['hide_packing_containers']);
                $viewer->assign('HIDE_PACKING_CUSTOM_RATE', $this->view->tariffInfo['hide_custom_rates']);
                $viewer->assign('HIDE_PACKING_PACK_RATE', $this->view->tariffInfo['hide_packing_rates']);
            }
            $viewer->assign('CRATES', $recordModel->getCrates($request));
        }
    }

    public function viewSirvaVehicles(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $active = $request->get('is_interstate');
        $viewer->assign('CONTENT_DIV_CLASS', 'interstateContent');
        $viewer->assign('IS_HIDDEN', 1);
        $this->assignSubBlocks($blockHandler, $viewer, $active);
        if($active) {
            $sirvaVehicleTypes = [
                '1' => '4 X 4 Vehicle',
                '5' => 'Automobile',
                '47' => 'Pickup Truck',
                '36' => 'Limousine',
                '67' => 'Van',
                '46' => 'Pickup & Camper'
            ];
            $recordModel = $this->view->recordModel;
            $vehicles = $recordModel->getVehicles();
            $viewer->assign('VEHICLES', $vehicles);
            $viewer->assign('SIRVA_VEHICLE_TYPES', $sirvaVehicleTypes);
            $viewer->assign('NUM_VEHICLES', count($vehicles));
            $corp_vehicles = $recordModel->getCorporateVehicles();
            $viewer->assign('CORP_VEHICLES', $corp_vehicles[1]);
            $viewer->assign('NUM_CORP_VEHICLES', $corp_vehicles[0]);
        }
    }

    public function viewAutoTransportVehicles(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $active = $request->get('is_interstate');
        $viewer->assign('CONTENT_DIV_CLASS', 'interstateContent');
        $viewer->assign('IS_HIDDEN', 0);
        $this->assignSubBlocks($blockHandler, $viewer, $active);
        if($active) {
            //vehicles block
            $vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
            if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
                $viewer->assign('VEHICLE_LOOKUP', $vehicleLookupModel->isActive());
                $rec = $this->view->recordId ?: $request->get('sourceRecord');
                $viewer->assign('VEHICLE_LIST', $vehicleLookupModel::getVehicles($rec));
            }
        }
    }

    public function viewCustomMiscCharges(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $active = $request->get('effective_tariff_id');
        $viewer->assign('CONTENT_DIV_CLASS', 'interstateContent localMoveContent');
        $viewer->assign('IS_HIDDEN', 1);
        $this->assignSubBlocks($blockHandler, $viewer, $active);
        if($active)
        {
            $recordModel = $this->view->recordModel;
            $viewer->assign('MISC_CHARGES', $recordModel->getMiscCharges($request));
        } else {
        }
    }

    public function viewAutoRateTable(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $active = $request->get('is_interstate');
        $viewer->assign('CONTENT_DIV_CLASS', 'interstateContent');
        $viewer->assign('IS_HIDDEN', 1);
        $viewer->assign('HAS_FLAT_AUTO', 0);
        $this->assignSubBlocks($blockHandler, $viewer, $active);

        if ($this->view->recordModel->get('contract')) {
            $db           = PearDatabase::getInstance();
            $contractId = $this->view->recordModel->get('contract');
            //Flat Rate Auto table
            $sql    = "SELECT * FROM `vtiger_contract_flat_rate_auto` WHERE `contractid` =?";
            $result    = $db->pquery($sql, [$contractId]);
            $row       = $result->fetchRow();
            while ($row != null) {
                $flatRateAutoTable[] = $row;
                $row = $result->fetchRow();
            }
            if(count($flatRateAutoTable))
            {
                $viewer->assign('HAS_FLAT_AUTO', 1);
            }
            $viewer->assign('FLAT_RATE_AUTO_TABLE', $flatRateAutoTable);
        }
    }

    public function viewInterstateServiceCharges(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $viewer->assign('CONTENT_DIV_CLASS', 'interstateContent');
        $viewer->assign('BLOCK_LABEL', 'LBL_QUOTES_INTERSTATE_SERVICECHARGES');
        $viewer->assign('IS_HIDDEN', 0);
        if(getenv('INSTANCE_NAME') == 'sirva') {
            $viewer->assign('CUSTOM_SIT', $this->view->recordModel->getCustomSITOverride());
        }
        $active = $request->get('is_interstate');
        $this->assignSubBlocks($blockHandler, $viewer, $active);
        if($active) {
            $viewer->assign('INTERSTATE_SERVICECHARGES', $this->view->recordModel->getInterstateServiceCharges());
        }
    }

    public function viewTransportationPricing(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $active = $request->get('is_interstate');
        $viewer->assign('CONTENT_DIV_CLASS', 'interstateContent');
        $viewer->assign('IS_HIDDEN', 1);
        $this->assignSubBlocks($blockHandler, $viewer, $active);
    }

    public function viewAdditionalInfo(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $active = 1;
        $viewer->assign('IS_HIDDEN', 0);
        $this->assignSubBlocks($blockHandler, $viewer, $active);
    }

    public function viewDetailedLineItems(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $detailLineItems = $this->view->recordModel->getDetailLineItems();
        $viewer->assign('LINEITEMS', $detailLineItems);
        $viewer->assign('BUSINESS_LINE', $this->view->recordModel->get('business_line_est'));

        // TODO: fix this so it can pull from an attached order
        $roleParticipants = $this->view->recordModel->getParticipatingAgentsForDetailLineItems();
        $moveRoles        = $this->view->recordModel->getMoveRolesForDetailLineItems();
        $viewer->assign('ROLES', array_keys($roleParticipants));
        $viewer->assign('ROLESLIST', $roleParticipants);
        $viewer->assign('MOVEROLES', $moveRoles);
        $viewer->assign('APPROVAL', Estimates_Record_Model::getDetailLineItemApprovalList());
        $this->assignSubBlocks($blockHandler, $viewer, 1);
    }

    public function viewLineItems(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $record          = $request->get('record');
        $moduleName      = $request->getModule();
        if((int)$record) {
            $recordModel = Inventory_Record_Model::getInstanceById($record, 'Estimates');
        } else {
            $recordModel = Estimates_Record_Model::getCleanInstance('Estimates');
        }
        $relatedProducts = $recordModel->getProducts();
        if(!$request->isEditView()) {
            //##Final details convertion started
            $finalDetails = $relatedProducts[1]['final_details'];
            //Final tax details convertion started
            $taxtype = $finalDetails['taxtype'];
            if ($taxtype == 'group') {
                $taxDetails = $finalDetails['taxes'];
                $taxCount   = count($taxDetails);
                for ($i = 0; $i < $taxCount; $i++) {
                    $taxDetails[$i]['amount'] = Vtiger_Currency_UIType::transformDisplayValue($taxDetails[$i]['amount'], NULL, true);
                }
                $finalDetails['taxes'] = $taxDetails;
            }
            //Final tax details convertion ended
            //Final shipping tax details convertion started
            $shippingTaxDetails = $finalDetails['sh_taxes'];
            $taxCount           = count($shippingTaxDetails);
            for ($i = 0; $i < $taxCount; $i++) {
                $shippingTaxDetails[$i]['amount'] = Vtiger_Currency_UIType::transformDisplayValue($shippingTaxDetails[$i]['amount'], NULL, true);
            }
            $finalDetails['sh_taxes'] = $shippingTaxDetails;
            //Final shipping tax details convertion ended
            $currencyFieldsList = ['adjustment',
                                   'grandTotal',
                                   'hdnSubTotal',
                                   'preTaxTotal',
                                   'tax_totalamount',
                                   'shtax_totalamount',
                                   'discountTotal_final',
                                   'discount_amount_final',
                                   'shipping_handling_charge',
                                   'totalAfterDiscount'];
            foreach ($currencyFieldsList as $fieldName) {
                $finalDetails[$fieldName] = Vtiger_Currency_UIType::transformDisplayValue($finalDetails[$fieldName], NULL, true);
            }
            $relatedProducts[1]['final_details'] = $finalDetails;
            //##Final details convertion ended
            //file_put_contents('logs/devLog.log', "\n relatedProducts : ".print_r($relatedProducts,true), FILE_APPEND);
            //##Product details convertion started
            $productsCount = count($relatedProducts);
            for ($i = 1; $i <= $productsCount; $i++) {
                $product = $relatedProducts[$i];
                //Product tax details convertion started
                if ($taxtype == 'individual') {
                    $taxDetails = $product['taxes'];
                    $taxCount   = count($taxDetails);
                    for ($j = 0; $j < $taxCount; $j++) {
                        $taxDetails[$j]['amount'] = Vtiger_Currency_UIType::transformDisplayValue($taxDetails[$j]['amount'], NULL, true);
                    }
                    $product['taxes'] = $taxDetails;
                }
                //this function does not behave as intended commented out for now, should probably be made to work in the future
                //Product tax details convertion ended
                $currencyFieldsList = ['taxTotal',
                                       'netPrice',
                                       'listPrice',
                                       'unitPrice',
                                       'productTotal',
                                       'discountTotal',
                                       'discount_amount',
                                       'totalAfterDiscount'];
                foreach ($currencyFieldsList as $fieldName) {
                    $product[$fieldName.$i] = Vtiger_Currency_UIType::transformDisplayValue($product[$fieldName.$i], NULL, true);
                }
                $relatedProducts[$i] = $product;
            }
        }
        //##Product details convertion ended
        $viewer->assign('INSTANCE_NAME', getenv('INSTANCE_NAME'));
        $viewer->assign('RELATED_PRODUCTS', $relatedProducts);
        //$viewer->assign('RECORD', $recordModel);
        $viewer->assign('MODULE_NAME', $moduleName);
        $this->assignSubBlocks($blockHandler, $viewer, 1);
    }

    public function viewGuestBlocks(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $active = $request->get('is_interstate');
        $viewer->assign('CONTENT_DIV_CLASS', 'interstateContent');
        $viewer->assign('IS_HIDDEN', 1);
        $this->assignSubBlocks($blockHandler, $viewer, $active);
    }

    public function viewAddressSegments(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $active = 1;
        $viewer->assign('CONTENT_DIV_CLASS', '');
        $viewer->assign('IS_HIDDEN', 0);
        $this->assignSubBlocks($blockHandler, $viewer, $active);
        if($active)
        {
            // edit view has its own logic to include address segments
            if(!$request->isEditView()) {
                //logic to include Address Segments
                $AddressSegmentsModel = Vtiger_Module_Model::getInstance('AddressSegments');
                if ($AddressSegmentsModel && $AddressSegmentsModel->isActive()) {
                    $viewer->assign('ADDRESSSEGMENTS_LIST', $AddressSegmentsModel->getAddressSegments($this->view->recordId));
                    $viewer->assign('ADDRESSSEGMENTS_MODULE_MODEL', $AddressSegmentsModel);
                    $viewer->assign('ADDRESSSEGMENTS_BLOCK_FIELDS', $AddressSegmentsModel->getFields('LBL_ADDRESSSEGMENTS_INFORMATION'));
                }
            }
        }
    }

    public function viewGoogleMilesCalculator(Vtiger_Request $request, Block_View_Handler $blockHandler)
    {
        $viewer = $this->view->getViewer($request);
        $active = 1;
        $viewer->assign('CONTENT_DIV_CLASS', '');
        $viewer->assign('IS_HIDDEN', 0);
        $this->assignSubBlocks($blockHandler, $viewer, $active);
        if($active && $this->view->recordId) {
            $db = & PearDatabase::getInstance();
            $res = $db->pquery('SELECT `address`,`miles`,`time` FROM vtiger_google_addresscalc WHERE quoteid=? ORDER BY vtiger_google_addresscalc_id ASC',
                               [$this->view->recordId]);
            $addr = [];
            while($row = $res->fetchRow())
            {
                if($row['address'] == '_Total_')
                {
                    $viewer->assign('GOOGLE_TOTAL_MILES', $row['miles']);
                    $viewer->assign('GOOGLE_TOTAL_TIME', $row['time']);
                } else {
                    $addr[] = [
                        'address' => $row['address'],
                        'miles' => $row['miles'],
                        'time' => $row['time'],
                    ];
                }
            }
            $viewer->assign('GOOGLE_ADDRESSES', $addr);
        }
    }

    public function assignSubBlocks(Block_View_Handler $block, $viewer, $active)
    {
        $arr = [];
        foreach($block->subBlocks as $sub) {
            $arr[$sub] = $active;
        }
        $viewer->assign('BLOCK_SUBLIST', $arr);
    }

}
