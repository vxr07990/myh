<?php
require_once('libraries/nusoap/nusoap.php');
class TariffManager_Record_Model extends Vtiger_Record_Model
{
    public function getAssignedRecords()
    {
        $recordId = $this->getId();
        $records = array('Vanlines'=>array(), 'Agents'=>array(), 'ApplyToAll'=>array());
        if ($recordId == null) {
            return $records;
        }

        $db = PearDatabase::getInstance();
        $sql = "SELECT vanlineid, apply_to_all_agents FROM `vtiger_tariff2vanline` JOIN `vtiger_crmentity` ON crmid=vanlineid WHERE tariffid=? AND deleted=0";
        $result = $db->pquery($sql, array($recordId));

        while ($row =& $result->fetchRow()) {
            $records['Vanlines'][] = Vtiger_Record_Model::getInstanceById($row[0]);
            if ($row[1] == 1) {
                $records['ApplyToAll'][] = $row[0];
            }
        }

        $sql = "SELECT agentid FROM `vtiger_tariff2agent` WHERE tariffid=?";
        $result = $db->pquery($sql, array($recordId));

        while ($row =& $result->fetchRow()) {
            $records['Agents'][] = $row[0];
        }

        return $records;
    }

    public function getDetailViewUrl()
    {
        $module = $this->getModule();
        return 'index.php?module='.$this->getModuleName().'&view='.$module->getDetailViewName().'&record='.$this->getId().'&mode=showDetailViewByMode&requestMode=full';
    }

    /**
     * Function to get list of blocks to show/hide based on tariff
     * @param <String> $module - Name of module
     * @return <Array> - 2-dimensional array containing list of blocks to hide and list of blocks to show
     */
    public function getTariffBlocks($module)
    {
        $tariffId = $this->getId();
        file_put_contents('logs/TariffBlockEdit.log', date('Y-m-d H:i:s - ').$tariffId."\n", FILE_APPEND);
        $db = PearDatabase::getInstance();

        $currentTariffBlocks = array();
        $blocksToHide = array();

        if (isset($tariffId) && !empty($tariffId)) {
            $sql = "SELECT blocklabel, show_block FROM `vtiger_tariff_blockrel` JOIN `vtiger_blocks` ON vtiger_tariff_blockrel.blockid=vtiger_blocks.blockid WHERE tariffid=?";

            file_put_contents('logs/TariffBlockEdit.log', date('Y-m-d H:i:s - ')."Preparing to run SQL query: $sql\n", FILE_APPEND);
            $result = $db->pquery($sql, array($tariffId));

            while ($row =& $result->fetchRow()) {
                $currentTariffBlocks[$row[0]] = $row[1];
            }
        }

        $sql = "SELECT vtiger_blocks.blocklabel FROM `vtiger_tariff_blockrel` JOIN `vtiger_blocks` ON vtiger_tariff_blockrel.blockid=vtiger_blocks.blockid JOIN `vtiger_tab` ON vtiger_blocks.tabid=vtiger_tab.tabid WHERE name=? AND tariffid!=?";

        file_put_contents('logs/TariffBlockEdit.log', date('Y-m-d H:i:s - ')."Preparing to run SQL query: $sql\n", FILE_APPEND);
        $result = $db->pquery($sql, array($module, $tariffId));

        while ($row =& $result->fetchRow()) {
            $blocksToHide[] = $row[0];
        }

        $info = array();
        $info['currentTariffBlocks'] = $currentTariffBlocks;
        $info['inactiveTariffBlocks'] = $blocksToHide;

        return $info;
    }

    /**
     * Function to get list of allowed services based on tariff
     * @return <Array> - array containing list of services and a boolean indicating if it is allowed
     */
    //@TODO: FIX ALL THIS
    public function getAllowedServices()
    {
        return array(
            'origin'=>array(
                'Extra Labor'=>1,
                'Extra Labor OT'=>1,
                'Wait Time'=>1,
                'Wait Time OT'=>1,
                'Shuttle'=>1,
                'Service Charges'=>1,
                'Mini Storage'=>1,
                'SIT First Day'=>1,
                'SIT Additional Days'=>1,
                'SIT Pu/Del'=>1,
                'SIT Fuel'=>1,
            ),
             'dest'=>array(
                 'Extra Labor'=>1,
                 'Extra Labor OT'=>1,
                 'Wait Time'=>1,
                 'Wait Time OT'=>1,
                 'Shuttle'=>1,
                 'Service Charges'=>1,
                 'Mini Storage'=>1,
                 'SIT First Day'=>1,
                 'SIT Additional Days'=>1,
                 'SIT Pu/Del'=>1,
                 'SIT Fuel'=>1,
             ),
             'general'=>array(
                 'Bulky Items'=>1,
                 'Expedited Service'=>1,
                 'Exclusive Use Of Vehicle'=>1,
                 'Space Reservation'=>1,
                 'Vehicle Weights (Standard)'=>1,
                 'Alaskan Section 6 (Sea Charges)'=>1,
                 'Alaskan Section 7 (Intra Charges)'=>1,
                 'Packing'=>1,
                 'Unpacking - 25%'=>1,
                 'Full Pack (per CWT)'=>1,
                 'Full Unpack (per CWT)'=>1,
                 'Overtime Loading'=>1,
                 'Overtime Unloading'=>1,
                 'Packing OT'=>1,
                 'Unpacking OT'=>1,
                 'Fuel Surcharge'=>1,
                 'IRR'=>1,
                 'Miscellaneous Items'=>1,
                 'Valuation'=>1,
                 'Extra Stops'=>1,
                 'Crating'=>1,
             )
        );
        if (empty($this->getId())) {
            return array();
        }

        //Retrieve list of allowed services for the tariff from webservice
        $wsdlURL = $this->get('rating_url');

        $soapclient = new soapclient2($wsdlURL, 'wsdl');
        $soapclient->setDefaultRpcParams(true);
        $soapProxy = $soapclient->getProxy();

        $wsdlParams = array();
        $wsdlParams['caller'] = 'VnbZ1BjT4xtFyCKj21Xr';
        if (getenv('INSTANCE_NAME') === 'uvlc') {
            $wsdlParams['tariffID'] = 1;
        } elseif (getenv('INSTANCE_NAME') === 'invan') {
            $wsdlParams['tariffID'] = -1;
        }
        /*if($this->getId()) {
            $wsdlParams['tariffID'] = $this->get('tariffId');
        }*/

        //added this error checking because the rating engine may not return this method and that fatal errors.
        if (method_exists($soapProxy, 'GetInterfaceItemsForTariff')) {
            $soapResult = $soapProxy->GetInterfaceItemsForTariff($wsdlParams);

            $tariffItems = $soapResult['GetInterfaceItemsForTariffResult']['TariffRatingItem'];
            $services = array('origin'=>array(), 'dest'=>array(), 'general'=>array());

            file_put_contents('logs/AllowedServices.log', date('Y-m-d H:i:s - ').print_r($tariffItems, true)."\n", FILE_APPEND);

            foreach ($tariffItems as $tariffArray) {
                $firstWord = strstr($tariffArray['Description'], ' ', true);
                $remainingString = substr(strstr($tariffArray['Description'], ' ', false), 1);

                if ($firstWord == 'Origin') {
                    $services['origin'][$remainingString] = $tariffArray['HasInterfaceItem'] === 'true';
                } elseif ($firstWord == 'Destination') {
                    $services['dest'][$remainingString] = $tariffArray['HasInterfaceItem'] === 'true';
                } else {
                    $services['general'][$tariffArray['Description']] = $tariffArray['HasInterfaceItem'] === 'true';
                }
            }

            //Construct and return array of services with boolean value indicating whether service is allowed
            /*$services = array('origin'=>array('SIT'=>1, 'Shuttle'=>0, 'OT Service'=>0, 'Self Stg'=>0,
                                              'Extra Labor'=>1, 'Wait Time'=>1),
                                'dest'=>array('SIT'=>1, 'Shuttle'=>0, 'OT Service'=>1, 'Self Stg'=>0,
                                              'Extra Labor'=>0, 'Wait Time'=>1),
                             'general'=>array('Pack'=>1, 'Unpack'=>0, 'OT Pack'=>1, 'OT Unpack'=>0, 'Bulky'=>0,
                                              'Flat Charge'=>0, 'Qty/Rate'=>1, 'Crates'=>1, 'Bulky Article Changes'=>1));*/

            file_put_contents('logs/AllowedServices.log', date('Y-m-d H:i:s - ').print_r($services, true)."\n", FILE_APPEND);
        }

        return $services;
    }

    //@TODO: FIX ALL THIS
        public function getAllowedTariffItems()
        {
            return [
                'origin' => [
                    'Extra Labor'         => 1,
                    'Extra Labor OT'      => 1,
                    'Wait Time'           => 1,
                    'Wait Time OT'        => 1,
                    'Shuttle'             => 1,
                    'Service Charges'     => 1,
                    'Mini Storage'        => 1,
                    'SIT First Day'       => 1,
                    'SIT Additional Days' => 1,
                    'SIT Pu/Del'          => 1,
                    'SIT Fuel'            => 1,
                ],
                'dest'   => [
                    'Extra Labor'         => 1,
                    'Extra Labor OT'      => 1,
                    'Wait Time'           => 1,
                    'Wait Time OT'        => 1,
                    'Shuttle'             => 1,
                    'Service Charges'     => 1,
                    'Mini Storage'        => 1,
                    'SIT First Day'       => 1,
                    'SIT Additional Days' => 1,
                    'SIT Pu/Del'          => 1,
                    'SIT Fuel'            => 1,
                ],
                'general'   => [
                    'Bulky Items'                       => 1,
                    'Expedited Service'                 => 1,
                    'Exclusive Use Of Vehicle'          => 1,
                    'Space Reservation'                 => 1,
                    'Vehicle Weights (Standard)'        => 1,
                    'Alaskan Section 6 (Sea Charges)'   => 1,
                    'Alaskan Section 7 (Intra Charges)' => 1,
                    'Packing'                           => 1,
                    'Unpacking - 25%'                   => 1,
                    'Full Pack (per CWT)'               => 1,
                    'Full Unpack (per CWT)'             => 1,
                    'Overtime Loading'                  => 1,
                    'Overtime Unloading'                => 1,
                    'Packing OT'                        => 1,
                    'Unpacking OT'                      => 1,
                    'Fuel Surcharge'                    => 1,
                    'IRR'                               => 1,
                    'Miscellaneous Items'               => 1,
                    'Valuation'                         => 1,
                    'Extra Stops'                       => 1,
                    'Crating'                           => 1,
                ]
            ];
            $thisId = $this->getId();
            if (empty($thisId)) {
                return array();
            }

            $tariffItems = array();

            $wsdlURL = $this->get('rating_url');

            //this link returns the same information ... mostly.
            $wsdlURL = 'https://aws.igcsoftware.com/RatingEngine/RatingService.svc?wsdl';

            //@TODO: this does not work... but it should i think?  so it's based on the custom tariff itself.
            //$wsdlURL = $this->get('rating_url');
            //@TODO So the 400NG rating service doesn't return GetInterfaceItemsForTariff (yet?)
            //so for now it's fine that we can't call by rating_url ha
            //$wsdlURL = 'https://awsdev1.movecrm.com/RatingEngineDev/Base400NG/RatingService.svc?wsdl';

            $soapclient = new soapclient2($wsdlURL, 'wsdl');
            $soapclient->setDefaultRpcParams(true);
            $soapProxy = $soapclient->getProxy();

            $wsdlParams = array();
            $wsdlParams['caller'] = 'VnbZ1BjT4xtFyCKj21Xr';
            if (getenv('INSTANCE_NAME') === 'uvlc') {
                $wsdlParams['tariffID'] = 1;
            } elseif (getenv('INSTANCE_NAME') === 'invan') {
                $wsdlParams['tariffID'] = -1;
            }

            if (method_exists($soapProxy, 'GetInterfaceItemsForTariff')) {
                $soapResult = $soapProxy->GetInterfaceItemsForTariff($wsdlParams);
                $tariffItems = $soapResult['GetInterfaceItemsForTariffResult']['TariffRatingItem'];
            }

            return $tariffItems;
        }

    public static function getValuationSetting($id)
    {
        if (getenv('INSTANCE_NAME') != 'graebel') {
            return [];
        }
        $db = PearDatabase::getInstance();

        $sql = 'SELECT * FROM `vtiger_valuation_tariff_types` WHERE related_id = ? AND active = ?';
        $result = $db->pquery($sql, [$id, 'y']);
        $data = array();
        while ($row = $result->fetchRow()) {
            $data[] = array(
                'id'                        => $row['id'],
                'related_id'                => $row['related_id'],
                'valuation_name'            => $row['valuation_name'],
                'per_pound'                => number_format($row['per_pound'], 2),
                'max_amount'                => number_format($row['max_amount'], 2),
                'additional_price_per'        => number_format($row['additional_price_per'], 2),
                'free'                        => $row['free'],
                'additional_price_per_sit'    => number_format($row['additional_price_per_sit'], 2),
                'free_amount'                => number_format($row['free_amount'], 2),
            );
        }
        return $data;
    }

    public static function getCustomTariffTypeById($effectiveTariffId)
    {
        if($effectiveTariffId){
            $customTariffInstance = TariffManager_Record_Model::getInstanceById($effectiveTariffId, 'TariffManager');
            if ($customTariffInstance) {
                return $customTariffInstance->get('custom_tariff_type');
            }
            // return default tariff type based on instance
            if (getenv("INSTANCE_NAME") == 'graebel') {
                return '1950-B';
            }
        }
        // No idea what to do here
        return '___Default';
    }
}
