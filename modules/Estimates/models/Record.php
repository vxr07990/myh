<?php
/**
 * @author             Louis Robinson
 * @file               GetDetailedRate.php
 * @description        Extended functionality from the Quotes module so we can add to
 *                  it without having to deal with changing the core vtiger code.
 * @overwrites         Functionality of the respective file in the Quotes module
 * @contact        lrobinson@igcsoftware.com
 * @company            IGC Software
 */

/**
 * Quotes Record Model Class extended
 */
require_once('libraries/nusoap/nusoap.php');
include_once('libraries/MoveCrm/ValuationUtils.php');

class Estimates_Record_Model extends Quotes_Record_Model
{

    /**
     * @param call method from parent class w/ scope resolution
     */
    public function getCreateInvoiceUrl()
    {
        parent::getCreateInvoiceUrl();
    }

    /**
     * @param call method from parent class w/ scope resolution
     */
    public function getCreateSalesOrderUrl()
    {
        parent::getCreateSalesOrderUrl();
    }

    /**
     * @param call method from parent class w/ scope resolution
     */
    public function getPDF()
    {
        parent::getPDF();
    }


    public function getAndUpdatePackingLabels($vanline, $tariffType, $caller, $originalTariffType)
    {
            $db = &PearDatabase::getInstance();
            $params               = [];
            $params['caller']     = $caller;
            $params['vanlineID']  = $vanline;
            $params['tariffType'] = $tariffType;
            $url = getenv('CARTON_WEBSERVICE_URL');

            foreach ($params as $key => $value) {
                $url.=$key.'='.\MoveCrm\InputUtils::encodeURIComponent($value).'&';
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $result = curl_exec($ch);
            curl_close($ch);
            $decodedResult = json_decode($result, true);
            if ($decodedResult['Message'] == 'The request is invalid.' && $tariffType != 'Default') {
                // tariff type is not supported or something, so try Default
            return self::getAndUpdatePackingLabels($vanline, 'Default', $caller, $tariffType);
            }
            $cartonArray = [];
        if ($vanline && $tariffType && count($decodedResult['CartonItems']) > 0) {
                $db->pquery('DELETE FROM `packing_label_cache` WHERE vanline=? AND tariff=?',
                            [$vanline, $tariffType]);
            }
            foreach ($decodedResult['CartonItems'] as $carton) {
                $cartonID               = $carton['CartonItemID'];
                $cartonDesc             = $carton['Description'];
                $cartonArray[$cartonID] = $cartonDesc;
                if ($vanline && $tariffType) {
                    $db->pquery('INSERT INTO `packing_label_cache` (vanline, tariff, item_id, label) VALUES (?,?,?,?)',
                                [$vanline, $tariffType, $cartonID, $cartonDesc]);
                }
            }
            if ($vanline && $tariffType) {
                $db->pquery('DELETE FROM `packing_label_cachetime` WHERE vanline=? AND tariff=?',
                            [$vanline, $tariffType]);
                $db->pquery('INSERT INTO `packing_label_cachetime` (vanline, tariff, updated_time) VALUES (?,?,NOW())',
                            [$vanline, $tariffType]);
            }

            return $cartonArray;
    }

    public function getPackingLabels($vanline, $tariffType = 'Default', $caller = "VnbZ1BjT4xtFyCKj21Xr", $originalTariffType = null)
    {
        if (getenv('INSTANCE_NAME') == 'graebel') {
            $db  = &PearDatabase::getInstance();
            $res = $db->pquery('SELECT * FROM `packing_label_cachetime` WHERE vanline=? AND tariff=? ORDER BY updated_time DESC LIMIT 1',
                               [$vanline, $tariffType]);
            if ($db->num_rows($res)) {
                $lastUpdated = strtotime($res->fetchRow()['updated_time']);
                $res         = $db->pquery('SELECT item_id, label FROM `packing_label_cache` WHERE vanline=? AND tariff=?',
                                           [$vanline, $tariffType]);
                $labels      = [];
                while ($row = $res->fetchRow()) {
                    $labels[$row['item_id']] = $row['label'];
                }
                if (count($labels) > 0 && $tariffType != $originalTariffType && $originalTariffType) {
                    // copy the labels to the original tariff type
                    $db->pquery('CREATE TEMPORARY TABLE cachetemp SELECT vanline,tariff,item_id,label FROM `packing_label_cache` WHERE vanline=? and tariff=?',
                                [$vanline, $tariffType]);
                    $db->pquery('UPDATE cachetemp SET tariff=?',
                                [$originalTariffType]);
                    $db->pquery('DELETE FROM `packing_label_cache` WHERE vanline=? AND tariff=?',
                                [$vanline, $originalTariffType]);
                    $db->pquery('INSERT INTO `packing_label_cache` (vanline,tariff,item_id,label) SELECT * FROM cachetemp');
                    $db->pquery('DROP TEMPORARY TABLE cachetemp');
                    $db->pquery('DELETE FROM `packing_label_cachetime` WHERE vanline=? AND tariff=?',
                                [$vanline, $originalTariffType]);
                    $db->pquery('INSERT INTO `packing_label_cachetime` (vanline, tariff, updated_time) VALUES (?,?,NOW())',
                                [$vanline, $originalTariffType]);
                }
                if ($lastUpdated < time() - 60 * 15)
                {
                    $newLabels = self::getAndUpdatePackingLabels($vanline, $tariffType, $caller, $originalTariffType);
                    if (count($newLabels) > 0)
                    {

                        return $newLabels;
                    }
                }

                return $labels;
            }

            return self::getAndUpdatePackingLabels($vanline, $tariffType, $caller, $originalTariffType);
        }

        $labels = [
            '1' => '1.5', // - CP',
            '2' => '3.0', // - CP',
            '3' => '4.5', // - CP',
            '4' => '6.0', // - CP',
            '16' => '6.5', // - CP',
            //The one true book carton!  Let there be no other Book Carton before the CP carton id 5 Book Carton.
            '5' => 'Book Carton', // - CP',
            '8' => 'Dish Pack', // - CP',
            '6' => 'Matt. Crib', // - CP',
            '7' => 'Matt. Double', // - CP',
            '13' => 'Matt. Q/K', // - CP',
            '10' => 'Matt. Q/K Split', // - CP',
            '14' => 'Matt. Single', // - CP',
            '17' => 'Mattress Cover', // - CP',
            '12' => 'Mirror Crt.', // - CP',
            '15' => 'Wardrobe', // - CP',
            '102' => 'TV Carton', // - CP', //Same as: TV Flat 30 - 59
            '9' => 'Grandfather Clock',
            '11' => 'Lamp',
            '18' => 'Office Tote',
            '21' => 'Paper',
            '22' => 'Tape',
            '23' => 'Bubble Wrap',
            '24' => 'Pads',
            // Book Carton ID 50.
            //  If you want to add it back in please take this note and say no, please fix rating/cubesheet/somebody else.
            //'50' => 'Book Carton',
            // I am serious if it's added back I will probably revert your entire branch.
            '103' => 'TV Carton',
            '112' => 'Wardrobe flat',
            '500' => 'Wardrobe Speedpack',
            '501' => 'Tote Box',
            '502' => 'Small Mirror',
            '503' => 'TV Flat 50',
            '504' => 'TV Flat 60',
            '505' => 'Pillow Top Single',
            '506' => 'Pillow Top Double',
            '507' => 'Pillow Top King/Queen',
            '509' => 'Other',
            '510' => 'Heavy Duty',
            '511' => 'TV 42',
            '512' => 'TV 43 To 60',
            '513' => 'TV 60 Plus',
        ];

        //@TODO: Find a better way to handle this
        //@NOTE: this was in getPackingItems, moved up here to be in one place at least.
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $estimatesLabelIds = ['1', '2', '3', '4', '16', '5', '8', '6', '7', '13', '10', '14', '17', '12', '15', '102'];
            foreach ($labels as $id => $label) {
                if (!in_array($id, $estimatesLabelIds)) {
                    unset($labels[$id]);
                }
            }
            // Begin - Filter by Custom_Tariff_type

            //@NOTE: For some reason this removed TV Carton from a not list of tariff types.
            //changed to remove if the tariffs is on a list to remove, so anything not considered will have the TV Carton
            $tariffTypesForCarton = [
                                        '/ALLV-2A/i',
                                        '/NAVL-12A/i',
                                        '/400/i',  //@TODO: regexp matches ALL 400 tariffs. not wrong from the previous code, but could be.
                                    ];
            foreach ($tariffTypesForCarton as $testTariff) {
                if (preg_match($testTariff, $tariffType)) {
                    unset($labels['102']);
                }
            }
        } else {
            //Oh my god, this is a hot mess. Adding another override to the labels array because every few months I get asked to pare it down to this list
            $labels = [
                '1'   => '1.5',
                '2'   => '3.0',
                '3'   => '4.5',
                '4'   => '6.0',
                '16'  => '6.5',
                '5'   => 'Book Carton',
                '6'   => 'Crib Matt.',
                '7'   => 'Dbl. Matt.',
                '8'   => 'Dish Pack',
                '9'   => 'GF Clock Carton',
                '10'  => 'K/Q Split Matt.',
                '11'  => 'Lamp Crt.',
                '12'  => 'Mirror Crt.',
                '13'  => 'Qn/Kn Matt.',
                '14'  => 'Single Matt.',
                '15'  => 'Wardrobe',
                '102' => 'TV Flat 30 - 59',
                '509' => 'Other'
            ];
        }

        return $labels;
    }

    public static function getPackingLabelsStatic($vanline='', $tariffType='')
    {
        return self::getPackingLabels($vanline, $tariffType);
    }

    public static function getDynamicPackingLabels()
    {
		$wsdlURL = getenv('PACKING_LIST_URL');

		$soapClient = new soapclient2($wsdlURL, 'wsdl');
		$soapClient->setDefaultRpcParams(true);
		$soapProxy = $soapClient->getProxy();

		$soapResult = $soapProxy->GetPackItemsForTariff([
			'tariffID' => 1,
			'caller' => 'VnbZ1BjT4xtFyCKj21Xr',
			'effectiveDate' => date('Y-m-d')
		]);

		$rawPackItems = $soapResult['GetPackItemsForTariffResult']['PackDescriptiveItem'];

		$formatPackItems = [];

        foreach ($rawPackItems as $field) {
			$formatPackItems[$field['PackItemID']] = $field['Description'];
		}

		return $formatPackItems;
	}

    public function getPackingItems($tariffOverride = null)
    {
        $recordId   = $this->getId();
        if ($recordId) {
            $vanlineId = Estimates_Record_Model::getVanlineIdStatic($recordId);
        } else {
            $vanlineId = $this->getVanlineIdForNewRecord();
        }
        if ($tariffOverride) {
            $effectiveTariff = $tariffOverride;
        } else {
            $effectiveTariff = $this->getCurrentAssignedTariff();
        }
        if ($effectiveTariff) {
            $tariffForWebService = $this->getAssignedTariffName($effectiveTariff);
        } else {
            $tariffForWebService = 'Default';
        }
        $defaultLabels     = $this->getPackingLabels($vanlineId, $tariffForWebService);
        $pack       = [];
        $unpack     = [];
        $customRate = [];
        $otpack     = [];
        $otunpack   = [];
        $bulkyItems = [];
        $db         = PearDatabase::getInstance();
        $sql        = "SELECT * FROM `vtiger_packing_items` WHERE quoteid=?";
        $params[]   = $recordId;
        $result     = $db->pquery($sql, $params);
        unset($params);
        $packingItems = [];

		while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            //$quantities[$row['bulkyid']] = $row['ship_qty'];

            if(getenv('INSTANCE_NAME') == 'sirva') {
                $packingItems[$row['itemid']] = [
                    'label' => $row['label'],
                    'cont' => (array_key_exists('pack_cont_qty', $row) ? $row['pack_cont_qty'] : $row['pack_qty']),
                    'pack' => $row['pack_qty'],
                    'unpack' => $row['unpack_qty'],
                    'customRate' => $row['custom_rate'],
                    'packRate' => $row['pack_rate'],
                    'otpack' => $row['ot_pack_qty'],
                    'otunpack' => $row['ot_unpack_qty']
                ];
            } else {
                $packingItems[$row['itemid']] = ['label' => $row['label'], 'pack' => $row['pack_qty'], 'unpack' => $row['unpack_qty'], 'customRate' => $row['custom_rate'], 'otpack' => $row['ot_pack_qty'], 'otunpack' => $row['ot_unpack_qty'], 'containers' => $row['containers']];
        }
        }
		foreach($defaultLabels as $itemId => $defaultLabel){
			if(!$packingItems[$itemId]){
				$packingItems[$itemId] = ['label' => $defaultLabel, 'pack' => 0, 'unpack' => 0, 'customRate' => '0.00', 'otpack' => 0, 'otunpack' => 0];
				$arrKey = getenv('INSTANCE_NAME') == 'sirva' ? 'cont' : 'containers';
				$packingItems[$itemId][$arrKey] = 0;
			} else {
			    $packingItems[$itemId]['label'] = $defaultLabel;
            }
		}

        uasort($packingItems, function($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        return $packingItems;
    }

    public static function getPackingItemsStatic($recordId, $tablePrefix = '')
    {
        $vanlineId = Estimates_Record_Model::getVanlineIdStatic($recordId, $tablePrefix);
        $tariffId = Estimates_Record_Model::getCurrentAssignedTariffStatic($recordId, $tablePrefix);
        $tariffName = Estimates_Record_Model::getAssignedTariffName($tariffId);
        $labels     = self::getPackingLabelsStatic($vanlineId, $tariffName);

        $cont       = [];
        $pack       = [];
        $unpack     = [];
        $customRate = [];
        $otpack     = [];
        $otunpack   = [];
        $containersPack   = [];
        $bulkyItems = [];
        $db         = PearDatabase::getInstance();
        $sql        = "SELECT * FROM `".$tablePrefix."vtiger_packing_items` WHERE quoteid=?";
        $params[]   = $recordId;
        $result     = $db->pquery($sql, $params);
        unset($params);
        while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $cont[$row['itemid']]       = array_key_exists('pack_cont_qty', $row) ? $row['pack_cont_qty'] : $row['pack_qty'];
            $pack[$row['itemid']]       = $row['pack_qty'];
            $unpack[$row['itemid']]     = $row['unpack_qty'];
            $customRate[$row['itemid']] = $row['custom_rate'];
            $otpack[$row['itemid']]     = $row['ot_pack_qty'];
            $otunpack[$row['itemid']]   = $row['ot_unpack_qty'];
            $containersPack[$row['itemid']]   = $row['containers'];
            if (getenv('INSTANCE_NAME') == 'sirva') {
                $containersPack[$row['itemid']] = $row['pack_cont_qty'];
            }
        }
        foreach ($labels as $itemId => $itemLabel) {
            $contQty               = (array_key_exists($itemId, $cont))?$cont[$itemId]:'0';
            $packQty               = (array_key_exists($itemId, $pack))?$pack[$itemId]:'0';
            $unpackQty             = (array_key_exists($itemId, $unpack))?$unpack[$itemId]:'0';
            $rate                  = (array_key_exists($itemId, $customRate))?$customRate[$itemId]:'0.00';
            $otpackQty             = (array_key_exists($itemId, $otpack))?$otpack[$itemId]:'0';
            $otunpackQty           = (array_key_exists($itemId, $otunpack))?$otunpack[$itemId]:'0';
            $containersQty     = $containersPack[$itemId]?:'0';
            $packingItems[$itemId] = ['label' => $itemLabel, 'pack' => $packQty, 'unpack' => $unpackQty, 'customRate' => $rate, 'otpack' => $otpackQty, 'otunpack' => $otunpackQty,
                                      'containers' => $containersQty, 'cont' => $contQty];
        }

        return $packingItems;
    }


    /*
     *
     * FROM dyost's survey 2017-04-13 (this is THE list for all cartons and bulky cartonBulkyID
     *
     public enum CARTON_IDS
   {
       NONE = 0,
       CARTON_15 = 1,
       CARTON_30 = 2,
       CARTON_45 = 3,
       CARTON_60 = 4,
       BOOK = 5,
       CRIB = 6,
       DOUBLE = 7,
       DISH = 8,
       GF_CLOCK = 9,
       KQ_SPLIT = 10,
       LAMP = 11,
       MIRROR = 12,
       QN_KN_MATT = 13,
       SINGLE = 14,
       WARDROBE = 15,
       CARTON_65 = 16,
       MATT_COVER = 17,
       OFFICE_TOTE = 18,
       PAPER = 21,
       TAPE = 22,
       BUBBLE_WRAP = 23,
       PADS = 24,
       TV = 102,
       TV_PLAIN = 103,
       WARDROBE_FLAT = 112,
       WARDROBE_SPEEDPACK = 500,
       TOTE_BOX = 501,
       SMALL_MIRROR = 502,
       TV_FLAT_50 = 503,
       TV_FLAT_60 = 504,
       PILLOW_TOP_SINGLE = 505,
       PILLOW_TOP_DOUBLE = 506,
       PILLOW_TOP_KQ = 507,
       OTHER = 509,
       HEAVY_DUTY = 510,
       TV_42 = 511,
       TV_43_TO_60 = 512,
       TV_60_PLUS = 513,
       TV_FLAT_70 = 514,
   }
        public enum BULKY_IDS
    {
        VEHICLE_4x4 = 1,
        AirplanesGliders = 2,
        AllTerrainCycle = 3,
        AnimalHouse = 4,
        Automobile = 5,
        Bath = 6,
        BathGreater65CuFt = 7,
        BoatTrailers = 8,
        BoatsLess14ft = 9,
        BoatsGreater14ft = 10,
        CamperTruckless = 11,
        CamperShell = 12,
        CamperTrailers = 13,
        CanoeLess14ft = 14,
        CanoeGreater14ft = 15,
        DinghyLess14ft = 16,
        DinghyGreater14ft = 17,
        DollHouse = 18,
        FarmEquipment = 19,
        FarmImplement = 20,
        FarmTrailer = 21,
        GoCart = 22,
        GolfCart = 23,
        HorseTrailers = 24,
        HotTub = 25,
        HotTubGreater65CuFt = 26,
        Jacuzzi = 27,
        JacuzziGreater65CuFt = 28,
        JetSki = 29,
        JetSkiGreater14ft = 30,
        KayakLess14ft = 31,
        KayakGreater14ft = 32,
        Kennel = 33,
        LargeTVGreater40 = 34,
        LightBulky = 35,
        Limousine = 36,
        MiniMobileHomes = 37,
        Motorbike = 38,
        Motorcycle = 39,
        Piano = 40,
        PianoConcert = 41,
        PianoGrand = 42,
        PianoSpinet = 43,
        PianoUpright = 44,
        PianoBabyGrand = 45,
        PickupandCamper = 46,
        PickupTruck = 47,
        Playhouse = 48,
        RidingMowerLess25hp = 49,
        RowboatLess14ft = 50,
        RowboatGreater14ft = 51,
        SatelliteDish = 52,
        ScullsGreater14ft = 53,
        SkiffLess14ft = 54,
        SkiffGreater14ft = 55,
        SnowMobile = 56,
        Spa = 57,
        SpaGreater65CuFt = 58,
        ToolShed = 59,
        TractorLess25hp = 60,
        TractorGreater25hp = 61,
        TrailerLess14ft = 62,
        TrailerGreater14ft = 63,
        TVRadioDish = 64,
        UtilityShed = 65,
        UtilityTruck = 66,
        Van = 67,
        WhirlpoolBath = 68,
        WhirlpoolBathGreater65Cu = 69,
        Windsurfer = 70,
        WindsurferGreater14ft = 71,
        BulkyArticleGreater400lbs = 72,
        HomeGymEquipment = 73,
        SailboatGreater14ft = 74,
        AnimalKennel = 75,
        BathTubLess65CuFt = 76,
        BathTubGreaterEqual65CuFt = 77,
        BoatLess14Ft = 78,
        BoatGreaterEqual14Ft = 79,
        BoatTrailerAdd = 80,
        CamperShellUnmounted = 81,
        CamperUnmounted = 82,
        CanoeLess14Ft = 83,
        CanoeGreaterEqual14Ft = 84,
        DinghieLess14Ft = 85,
        DinghieGreaterEqual14Ft = 86,
        DuneBuggie = 87,
        HorseTrailer = 88,
        HotTubLess65CuFt = 89,
        HotTubGreaterEqual65CuFt = 90,
        JacuzziLess65CuFt = 91,
        JacuzziGreaterEqual65CuFt = 92,
        JetSkiLess14Ft = 93,
        JetSkiGreaterEqual14Ft = 94,
        KayakLess14Ft = 95,
        KayakGreaterEqual14Ft = 96,
        LrgScreenProjTVGreaterEqual40IN = 97,
        MiniMobileHome = 98,
        PianoGrandGreater6Ft = 99,
        PianoGrand5TO6FtLong = 100,
        PianoOrganHarpshichordLessEqual45IN = 101,
        PianoOrganHarpshichordGreater45IN = 102,
        RidingMower = 103,
        RowboatLess14Ft = 104,
        RowboatGreaterEqual14 = 105,
        SailboatLess14Ft = 106,
        SailboatGreaterEqual14Ft = 107,
        SailboatTrailer = 108,
        ScullLess14Ft = 109,
        ScullGreaterEqual14Ft = 110,
        SkiffLess14Ft = 111,
        SkiffGreaterEqual14 = 112,
        Snowmobile = 113,
        SpaLess65CuFt = 114,
        SpaGreaterEqual65CuFt = 115,
        SpecialtyMotorVehicle = 116,
        SUV = 117,
        Tractor = 118,
        TrailerNEC = 119,
        TrailerLessEqual14Ft = 120,
        TravelCamperTrailer = 121,
        UtilityTrailer = 122,
        WhirlpoolBathLess65CuFt = 123,
        WhirlpoolBathGreaterEqual65CuFt = 124,
        WindsurferLess14Ft = 125,
        WindsurferGreaterEqual14Ft = 126,
        GunSafe = 127,
        TV45BigScreen = 128,
        TV55BigScreen = 129,
        PianoLess45IN = 130,
        PianoGreater45IN = 131,
        PianoGreater6FtLong = 132,
        Piano5TO6FtLong = 133,
        PianoConcertLess45IN = 134,
        PianoConcertGreater45IN = 135,
        PianoConcertGreater6FtLong = 136,
        PianoConcert5TO6FtLong = 137,
        PianoGrandLess45IN = 138,
        PianoGrandGreater45IN = 139,
        WHEATONPianoGrandGreater6FtLong = 140,
        WHEATONPianoGrand5TO6FtLong = 141,
        PianoSpinetLess45IN = 142,
        PianoSpinetGreater45IN = 143,
        PianoSpinetGreater6FtLong = 144,
        PianoSpinet5TO6FtLong = 145,
        PianoUprightLess45IN = 146,
        PianoUprightGreater45IN = 147,
        PianoUprightGreater6FtLong = 148,
        PianoUpright5TO6FtLong = 149,
        PianoBabyGrandLess45IN = 150,
        PianoBabyGrandGreater45IN = 151,
        PianoBabyGrandGreater6FtLong = 152,
        PianoBabyGrand5TO6FtLong = 153,
        Stevens_Paddleboard = 159,
        Stevens_Gun_Safe = 160,
        Stevens_Mower = 161,
    }
     */

    private function getSirvaBulkyLabel() {
        return ['1'  => '4x4 Vehicle',
                       '2'  => 'Airplane, Glider',
                       '3'  => 'All Terrain Cycle',
                       '4'  => 'Animal House',
                       '5'  => 'Automobile',
                       '6'  => 'Bath',
                       '7'  => 'Bath > 65 Cu Ft',
                       '8'  => 'Boat Trailer',
                       '9'  => 'Boat < 14 Ft',
                       '10' => 'Boat > 14 Ft',
                       '72' => 'Bulky > 400lbs',
                       '11' => 'Camper, Truckless',
                       '12' => 'Camper Shell',
                       '13' => 'Camper Trailer',
                       '14' => 'Canoe < 14 Ft',
                       '15' => 'Canoe > 14 Ft',
                       '16' => 'Dinghy < 14 Ft',
                       '17' => 'Dinghy > 14 Ft',
                       '18' => 'Doll House',
                       '19' => 'Farm Equipment',
                       '20' => 'Farm Implement',
                       '21' => 'Farm Trailer',
                       '22' => 'Go-Cart',
                       '23' => 'Golf Cart',
                       '73' => 'Gym Equipment',
                       '24' => 'Horse Trailer',
                       '25' => 'Hot Tub',
                       '26' => 'Hot Tub > 65 Cu Ft',
                       '27' => 'Jacuzzi',
                       '28' => 'Jacuzzi > 65 Cu Ft',
                       '29' => 'Jet Ski',
                       '30' => 'Jet Ski > 14 Ft',
                       '31' => 'Kayak < 14 Ft',
                       '32' => 'Kayak > 14 Ft',
                       '33' => 'Kennel',
                       '34' => 'Large Tv > 40',
                       '35' => 'Light/Bulky',
                       '36' => 'Limousine',
                       '37' => 'Mini Mobile Home',
                       '38' => 'Motorbike',
                       '39' => 'Motorcycle',
                       '40' => 'Piano',
                       '41' => 'Piano, Concert',
                       '42' => 'Piano, Grand',
                       '43' => 'Piano, Spinet',
                       '44' => 'Piano, Upright',
                       '45' => 'Piano, Baby Grand',
                       '46' => 'Pickup & Camper',
                       '47' => 'Pickup Truck',
                       '48' => 'Playhouse',
                       '49' => 'Riding Mower',
                       '50' => 'Rowboat < 14 Ft',
                       '51' => 'Rowboat > 14 Ft',
                       '106' => 'Sailboat < 14ft',
                       '74' => 'Sailboat > 14ft',
                       '52' => 'Satellite Dish',
                       '53' => 'Scull > 14 Ft',
                       '54' => 'Skiff < 14 Ft',
                       '55' => 'Skiff > 14 Ft',
                       '56' => 'Snow Mobile',
                       '57' => 'Spa',
                       '58' => 'Spa > 65 Cu Ft',
                       '59' => 'Tool Shed',
                       '60' => 'Tractor < 25HP',
                       '61' => 'Tractor > 25HP',
                       '62' => 'Trailer < 14 Ft',
                       '63' => 'Trailer > 14 Ft',
                       '64' => 'TV/Radio Dish',
                       '65' => 'Utility Shed',
                       '66' => 'Utility Truck',
                       '67' => 'Van',
                       '68' => 'Whirlpool Bath',
                       '69' => 'Whirlpool > 65 Cu',
                       '70' => 'Windsurfer < 14 Ft',
                       '71' => 'Windsurfer > 14 Ft',
        ];
    }

    private function getDefaultBulkyLabel () {
        return ['1'  => '4x4 Vehicle',
                '2'  => 'Airplane, Glider',
                '3'  => 'All Terrain Cycle',
                '4'  => 'Animal House',
                '5'  => 'Automobile',
                '6'  => 'Bath',
                '7'  => 'Bath > 65 Cu Ft',
                '8'  => 'Boat Trailer',
                '9'  => 'Boat < 14 Ft',
                '10' => 'Boat > 14 Ft',
                '11' => 'Camper (Truckless)',
                '12' => 'Camper Shell',
                '13' => 'Camper Trailer',
                '14' => 'Canoe < 14 Ft',
                '15' => 'Canoe > 14 Ft',
                '16' => 'Dinghy < 14 Ft',
                '17' => 'Dinghy > 14 Ft',
                '18' => 'Doll House',
                '19' => 'Farm Equipment',
                '20' => 'Farm Implement',
                '21' => 'Farm Trailer',
                '22' => 'Go-Cart',
                '23' => 'Golf Cart',
                '24' => 'Horse Trailer',
                '25' => 'Hot Tub',
                '26' => 'Hot Tub > 65 Cu Ft',
                '27' => 'Jacuzzi',
                '28' => 'Jacuzzi > 65 Cu Ft',
                '29' => 'Jet Ski',
                '30' => 'Jet Ski > 14 Ft',
                '31' => 'Kayak < 14 Ft',
                '32' => 'Kayak > 14 Ft',
                '33' => 'Kennel',
                '34' => 'Large Tv > 40',
                '35' => 'Light/Bulky',
                '36' => 'Limousine',
                '37' => 'Mini Mobile Home',
                '38' => 'Motorbike',
                '39' => 'Motorcycle',
                '40' => 'Piano',
                '41' => 'Piano, Concert',
                '42' => 'Piano, Grand',
                '43' => 'Piano, Spinet',
                '44' => 'Piano, Upright',
                '45' => 'Piano, Baby Grand',
                '46' => 'Pickup & Camper',
                '47' => 'Pickup Truck',
                '48' => 'Playhouse',
                '49' => 'Riding Mower < 25 HP',
                '50' => 'Rowboat < 14 Ft',
                '51' => 'Rowboat > 14 Ft',
                '52' => 'Satellite Dish',
                '53' => 'Sculls > 14 Ft',
                '54' => 'Skiff < 14 Ft',
                '55' => 'Skiff > 14 Ft',
                '56' => 'Snow Mobile',
                '57' => 'Spa',
                '58' => 'Spa > 65 Cu Ft',
                '59' => 'Tool Shed',
                '60' => 'Tractor < 25HP',
                '61' => 'Tractor > 25HP',
                '62' => 'Trailer < 14 Ft',
                '63' => 'Trailer > 14 Ft',
                '64' => 'TV/Radio Dish',
                '65' => 'Utility Shed',
                '66' => 'Utility Truck',
                '67' => 'Van',
                '68' => 'Whirlpool Bath',
                '69' => 'Whirlpool > 65 Cu',
                '70' => 'Windsurfer < 14 Ft',
                '71' => 'Windsurfer > 14 Ft',
        ];
    }

    public function getAndUpdateBulkyLabels($vanline, $tariffType, $caller, $originalTariffType)
    {
        $db = &PearDatabase::getInstance();
        $params = array();
        $params['caller'] = $caller;
        $params['vanlineID'] = $vanline;
        $params['tariffType'] = $tariffType;
        $url = getenv('BULKY_WEBSERVICE_URL');

        foreach ($params as $key=>$value) {
            $url.=$key.'='.\MoveCrm\InputUtils::encodeURIComponent($value).'&';
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result = curl_exec($ch);
        curl_close($ch);
        $decodedResult=json_decode($result, true);
        if ($decodedResult['Message'] == 'The request is invalid.' && $tariffType != 'Default') {
            // tariff type is not supported or something, so try Default
            return self::getAndUpdateBulkyLabels($vanline, 'Default', $caller, $tariffType);
        }

        $bulkyArray = array();

        if ($vanline && $tariffType && count($decodedResult['Bulkies']) > 0) {
            $db->pquery('DELETE FROM `bulky_label_cache` WHERE vanline=? AND tariff=?',
                        [$vanline, $tariffType]);
        }
        foreach ($decodedResult['Bulkies'] as $bulky) {
            $bulkyID = $bulky['BulkyItemID'];
            $bulkyDesc = $bulky['Description'];
            $bulkyArray[$bulkyID] = $bulkyDesc;
            if ($vanline && $tariffType) {
                $db->pquery('INSERT INTO `bulky_label_cache` (vanline, tariff, item_id, label) VALUES (?,?,?,?)',
                            [$vanline, $tariffType, $bulkyID, $bulkyDesc]);
            }
        }
        if ($vanline && $tariffType) {
            $db->pquery('DELETE FROM `bulky_label_cachetime` WHERE vanline=? AND tariff=?',
                        [$vanline, $tariffType]);
            $db->pquery('INSERT INTO `bulky_label_cachetime` (vanline, tariff, updated_time) VALUES (?,?,NOW())',
                        [$vanline, $tariffType]);
        }

        return $bulkyArray;
    }

    public function getBulkyLabels($vanline = false, $tariffType = 'Default', $caller = "VnbZ1BjT4xtFyCKj21Xr", $originalTariffType=null) {
        if (getenv('INSTANCE_NAME') == 'sirva') {
            return self::getSirvaBulkyLabel();
        }

        if(getenv('INSTANCE_NAME') != 'graebel') {
            return self::getDefaultBulkyLabel();
        }

        $db = &PearDatabase::getInstance();
        $res = $db->pquery('SELECT * FROM `bulky_label_cachetime` WHERE vanline=? AND tariff=? ORDER BY updated_time DESC LIMIT 1',
                           [$vanline, $tariffType]);
        if ($db->num_rows($res)) {
            $lastUpdated = strtotime($res->fetchRow()['updated_time']);
            $res = $db->pquery('SELECT item_id, label FROM `bulky_label_cache` WHERE vanline=? AND tariff=?',
                               [$vanline, $tariffType]);
            $labels = [];
            while ($row = $res->fetchRow()) {
                $labels[$row['item_id']] = $row['label'];
            }
            if (count($labels) > 0 && $tariffType != $originalTariffType && $originalTariffType) {
                // copy the labels to the original tariff type
                $db->pquery('CREATE TEMPORARY TABLE cachetemp SELECT vanline,tariff,item_id,label FROM `bulky_label_cache` WHERE vanline=? and tariff=?',
                            [$vanline, $tariffType]);
                $db->pquery('UPDATE cachetemp SET tariff=?',
                            [$originalTariffType]);
                $db->pquery('DELETE FROM `bulky_label_cache` WHERE vanline=? AND tariff=?',
                            [$vanline, $originalTariffType]);
                $db->pquery('INSERT INTO `bulky_label_cache` (vanline,tariff,item_id,label) SELECT * FROM cachetemp');
                $db->pquery('DROP TEMPORARY TABLE cachetemp');
                $db->pquery('DELETE FROM `bulky_label_cachetime` WHERE vanline=? AND tariff=?',
                            [$vanline, $originalTariffType]);
                $db->pquery('INSERT INTO `bulky_label_cachetime` (vanline, tariff, updated_time) VALUES (?,?,NOW())',
                            [$vanline, $originalTariffType]);
            }
            if($lastUpdated < time() - 60*15)
            {
                $newLabels = self::getAndUpdateBulkyLabels($vanline, $tariffType, $caller, $originalTariffType);
                if(count($newLabels) > 0)
                {
                    return $newLabels;
                }
            }
            return $labels;
        }

        return self::getAndUpdateBulkyLabels($vanline, $tariffType, $caller, $originalTariffType);
//        return ['1'  => '4x4 Vehicle',
//                '2'  => 'Airplane, Glider',
//                '3'  => 'All Terrain Cycle',
//                '4'  => 'Animal House',
//                '5'  => 'Automobile',
//                '6'  => 'Bath',
//                '7'  => 'Bath > 65 Cu Ft',
//                '8'  => 'Boat Trailer',
//                '9'  => 'Boat < 14 Ft',
//                '10' => 'Boat > 14 Ft',
//                '11' => 'Camper, Truckless',
//                '12' => 'Camper Shell',
//                '13' => 'Camper Trailer',
//                '14' => 'Canoe < 14 Ft',
//                '15' => 'Canoe > 14 Ft',
//                '16' => 'Dinghy < 14 Ft',
//                '17' => 'Dinghy > 14 Ft',
//                '18' => 'Doll House',
//                '19' => 'Farm Equipment',
//                '20' => 'Farm Implement',
//                '21' => 'Farm Trailer',
//                '22' => 'Go-Cart',
//                '23' => 'Golf Cart',
//                '24' => 'Horse Trailer',
//                '25' => 'Hot Tub',
//                '26' => 'Hot Tub > 65 Cu Ft',
//                '27' => 'Jacuzzi',
//                '28' => 'Jacuzzi > 65 Cu Ft',
//                '29' => 'Jet Ski',
//                '30' => 'Jet Ski > 14 Ft',
//                '31' => 'Kayak < 14 Ft',
//                '32' => 'Kayak > 14 Ft',
//                '33' => 'Kennel',
//                '34' => 'Large Tv > 40',
//                '35' => 'Light/Bulky',
//                '36' => 'Limousine',
//                '37' => 'Mini Mobile Home',
//                '38' => 'Motorbike',
//                '39' => 'Motorcycle',
//                '40' => 'Piano',
//                '41' => 'Piano, Concert',
//                '42' => 'Piano, Grand',
//                '43' => 'Piano, Spinet',
//                '44' => 'Piano, Upright',
//                '45' => 'Piano, Baby Grand',
//                '46' => 'Pickup & Camper',
//                '47' => 'Pickup Truck',
//                '48' => 'Playhouse',
//                '49' => 'Riding Mower',
//                '50' => 'Rowboat < 14 Ft',
//                '51' => 'Rowboat > 14 Ft',
//                '52' => 'Satellite Dish',
//                '53' => 'Scull > 14 Ft',
//                '54' => 'Skiff < 14 Ft',
//                '55' => 'Skiff > 14 Ft',
//                '56' => 'Snow Mobile',
//                '57' => 'Spa',
//                '58' => 'Spa > 65 Cu Ft',
//                '59' => 'Tool Shed',
//                '60' => 'Tractor < 25HP',
//                '61' => 'Tractor > 25HP',
//                '62' => 'Trailer < 14 Ft',
//                '63' => 'Trailer > 14 Ft',
//                '64' => 'TV/Radio Dish',
//                '65' => 'Utility Shed',
//                '66' => 'Utility Truck',
//                '67' => 'Van',
//                '68' => 'Whirlpool Bath',
//                '69' => 'Whirlpool > 65 Cu',
//                '70' => 'Windsurfer < 14 Ft',
//                '71' => 'Windsurfer > 14 Ft',
//               '72' => 'Bulky > 400lbs',
//               '73' => 'Gym Equipment',
//               '74' => 'Sailboat > 14ft',
//               '75' => 'Animal Kennel',
//               '76' => 'Bathub < 65CuFt',
//               '77' => 'Bathtub > 65CuFt',
//               '78' => 'Boat < 14Ft',
//               '79' => 'Boat > 14Ft',
//               '80' => 'Boat Trailer',
//               '81' => 'Camper Shell Unmounted',
//               '82' => 'Camper Unmounted',
//               '83' => 'Canoe < 14Ft',
//               '84' => 'Canoe > 14Ft',
//               '85' => 'Dinghie < 14Ft',
//               '86' => 'Dinghie > 14Ft',
//               '87' => 'Dune Buggie',
//               '88' => 'Horse Trailer',
//               '89' => 'HotTub < 65CuFt',
//               '90' => 'HotTub > 65CuFt',
//               '91' => 'Jacuzzi < 65CuFt',
//               '92' => 'Jacuzzi > 65CuFt',
//               '93' => 'JetSki < 14Ft',
//               '94' => 'JetSki > 14Ft',
//               '95' => 'Kayak < 14Ft',
//               '96' => 'Kayak > 14Ft',
//               '97' => 'Projection TV > 40in',
//               '98' => 'Mini Mobile-Home',
//               '99' => 'Grand Piano > 6Ft',
//               '100' => 'Grand Piano 5-6Ft',
//               '101' => 'Harpshichord < 45in',
//               '102' => 'Harpshichord > 45in',
//               '103' => 'Riding Mower',
//               '104' => 'Rowboat < 14ft',
//               '105' => 'Rowboat > 14ft',
//               '106' => 'Sailboat < 14ft',
//               '107' => 'Sailboat > 14ft',
//               '108' => 'Sailboat Trailer',
//               '109' => 'Scull < 14ft',
//               '110' => 'Scull > 14ft',
//               '111' => 'Skiff < 14ft',
//               '112' => 'Skiff > 14ft',
//               '113' => 'Snowmobile',
//               '114' => 'Spa < 65CuFt',
//               '115' => 'Spa > 65CuFt',
//               '116' => 'Specialty Vehicle',
//               '117' => 'SUV',
//               '118' => 'Tractor',
//               '119' => 'Trailer NEC',
//               '120' => 'Trailer < 14Ft',
//               '121' => 'Camper Trailer',
//               '122' => 'Utility Trailer',
//               '123' => 'Whirlpool Bath < 65CuFt',
//               '124' => 'WhirlpoolBath > 65CuFt',
//               '125' => 'Windsurfer < 14Ft',
//               '126' => 'Windsurfer > 14Ft',
//               '127' => 'Gun Safe',
//               '128' => 'Big Screen TV 45in',
//               '129' => 'Big Screen TV 55in',
//               '130' => 'Piano < 45in',
//               '131' => 'Piano > 45in',
//               '132' => 'Piano > 6ft',
//               '133' => 'Piano 5-6ft',
//               '134' => 'Concert Piano < 45in',
//               '135' => 'Concert Piano > 45in',
//               '136' => 'Concert Piano > 6ft',
//               '137' => 'Concert Piano 5-6ft',
//               '138' => 'Grand Piano < 45in',
//               '139' => 'Grand Piano > 45in',
//              // '140' => 'WHEATON Grand Piano > 6ft',
//              // '141' => 'WHEATON Grand Piano 5-6ft',
//               '142' => 'Spinet Piano < 45in',
//               '143' => 'Spinet Piano > 45in',
//               '144' => 'Spinet Piano > 6ft',
//               '145' => 'Spinet Piano 5-6ft',
//               '146' => 'Upright Piano < 45in',
//               '147' => 'Upright Piano > 45in',
//               '148' => 'Upright Piano > 6ft',
//               '149' => 'Upright Piano 5-6ft',
//               '150' => 'Baby Grand Piano < 45in',
//               '151' => 'Baby Grand Piano > 45in',
//               '152' => 'Baby Grand Piano > 6ft',
//               '153' => 'Baby Grand Piano 5-6ft',
//               //'159' => 'Stevens Paddleboard',
//               //'160' => 'Stevens Gun Safe',
//               //'161' => 'Stevens Mower',
//       ];

        /*
		return [
			'9' => '4x4 Vehicle',
			'16' => 'Airplanes, Gliders',
			'17' => 'All Terrain Cycle',
			'18' => 'Animal House',
			'20' => 'Automobile',
			'27' => 'Bath',
			'28' => 'Bath > 65 Cu Ft',
			'47' => 'Boat Trailers',
			'48' => 'Boats < 14 ft.',
			'49' => 'Boats > 14 ft.',
			'58' => 'Bulky Article > 400 lbs',
			'67' => 'Camper (Truckless)',
			'68' => 'Camper Shell',
			'69' => 'Camper Trailers',
			'70' => 'Canoe < 14 ft.',
			'71' => 'Canoe > 14 ft.',
			'121' => 'Dinghy < 14 ft.',
			'122' => 'Dinghy > 14 ft.',
			'126' => 'Doll House',
			'140' => 'Farm Equipment',
			'141' => 'Farm Implement',
			'142' => 'Farm Trailer',
			'182' => 'Go-Cart',
			'183' => 'Golf Cart',
			'188' => 'Home Gym Equipment',
			'189' => 'Horse Trailers',
			'190' => 'Hot Tub',
			'191' => 'Hot Tub > 65 Cu Ft',
			'196' => 'Jacuzzi',
			'197' => 'Jacuzzi > 65 Cu Ft',
			'198' => 'Jet Ski',
			'199' => 'Jet Ski > 14 ft.',
			'202' => 'Kayak < 14 ft.',
			'203' => 'Kayak > 14 ft.',
			'204' => 'Kennel',
			'212' => 'Large TV > 40',
			'216' => 'Light/Bulky',
			'217' => 'Limousine',
			'235' => 'Mini Mobile Homes',
			'263' => 'Motorbike',
			'264' => 'Motorcycle',
			'281' => 'Piano',
			'282' => 'Piano, Concert',
			'283' => 'Piano, Grand',
			'285' => 'Piano, Spinet', //piano spinet
			'286' => 'Piano, Upright', //piano upright
			'287' => 'Piano, Baby Grand', //piano baby grand
			'288' => 'Pickup & Camper', // pickup and camper
			'289' => 'Pickup Truck', //pickup truck
			'308' => 'Playhouse', //playhouse
			'326' => 'Riding Mower < 25hp', //riding mower <25hp
			'327' => 'Rowboat < 14 ft.', //rowboat <14 ft
			'328' => 'Rowboat > 14 ft.', //rowboat >14 ft
			'333' => 'Sailboat > 14 ft.', //sailboat>14ft
			'334' => 'Satellite Dish', //satellitle dish
			'336' => 'Sculls > 14 ft.', //Sculls >14ft
			'348' => 'Skiff < 14 ft.', //Skiff <14 ft
			'349' => 'Skiff > 14 ft.', //Skiff >14 ft
			'353' => 'Snow Mobile', //snow mobile
			'362' => 'Spa', //spa
			'363' => 'Spa > 65 Cu Ft', //spa >65 cuFt
			'392' => 'Tool Shed', //tool shed
			'401' => 'Tractor < 25hp', //tractor <25 hp
			'402' => 'Tractor > 25hp', //tractor >25hp
			'403' => 'Trailer < 14 ft.', //trailer < 14hp
			'404' => 'Trailer > 14 ft.', //trailer >14ft
			'419' => 'TV/Radio Dish', //Tv/Radio Dish
			'423' => 'Utility Shed', //Utility Shed
			'424' => 'Utility Truck', //Utility Truck
			'426' => 'Van', //Van
			'435' => 'Whirlpool Bath', //Whirlpool Bath
			'436' => 'Whirlpool Bath > 65 Cu', //Whirlpool Bath > 65 Cu
			'438' => 'Windsurfer', //Windsurfer
			'439' => 'Windsurfer > 14 ft.', //Windsurfer
		];
       */
    }

    public static function getBulkyLabelsStatic($vanline="", $tariffName="")
    {
        return self::getBulkyLabels($vanline, $tariffName);
    }

	/*public function getCustomBulkyLabels(){
		$labels = [];
		$recordId = $this->getId();
		$db  = PearDatabase::getInstance();
		$sql = "SELECT DISTINCT label, bulkyid FROM `vtiger_bulky_items` WHERE quoteid=?";
		$result = $db->pquery($sql, [$recordId]);
		$row = $result->fetchRow();
		while($row != null){
			$labels[$row['bulkyid']] = $row['label'];
			$row = $result->fetchRow();
		}
		return $labels;
	}*/

    public function getBulkyItems($tariffIdOverride)
    {
        $recordId   = $this->getId();
        $tariffForWebService = 'Default';
        if ($recordId) {
            $vanlineId = self::getVanlineIdStatic($recordId);
        } else {
            $vanlineId = $this->getVanlineIdForNewRecord();
        }
        if ($tariffIdOverride) {
            $effectiveTariff = $tariffIdOverride;
        } else {
        $effectiveTariff = $this->getCurrentAssignedTariff();
        }
        if ($effectiveTariff) {
            $tariffForWebService = $this->getAssignedTariffName($effectiveTariff);
        }
        $defaultLabels     = $this->getBulkyLabels($vanlineId, $tariffForWebService);
		//$customLabels = $this->getCustomBulkyLabels();
		//$labels = array_merge($defaultLabels, $customLabels);
        $quantities = [];
        $bulkyItems = [];
        $db         = PearDatabase::getInstance();
        $sql        = "SELECT * FROM `vtiger_bulky_items` WHERE quoteid=?";
        $params[]   = $recordId;
        $result     = $db->pquery($sql, $params);
        unset($params);
        while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            //$quantities[$row['bulkyid']] = $row['ship_qty'];
			$bulkyItems[$row['bulkyid']] = ['label' => $row['label'], 'qty' => $row['ship_qty']];
        }
        foreach ($defaultLabels as $bulkyId => $defaultLabel) {
            if (!$bulkyItems[$bulkyId]) {
				$bulkyItems[$bulkyId] = ['label' => $defaultLabel, 'qty' => 0];
			} else {
                $bulkyItems[$bulkyId]['label'] = $defaultLabel;
            }
		}

        uasort($bulkyItems, function($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        return $bulkyItems;
    }

    public static function getBulkyItemsStatic($recordId, $tablePrefix = '')
    {
        $vanlineId = self::getVanlineIdStatic($recordId, $tablePrefix);
        $tariffId = self::getCurrentAssignedTariffStatic($recordId, $tablePrefix);
        $tariffName = self::getAssignedTariffName($tariffId);
        $labels     = self::getBulkyLabelsStatic($vanlineId, $tariffName);
        $quantities = [];
        $bulkyItems = [];
        $db         = PearDatabase::getInstance();
        $sql        = "SELECT * FROM `".$tablePrefix."vtiger_bulky_items` WHERE quoteid=?";
        $params[]   = $recordId;
        $result     = $db->pquery($sql, $params);
        unset($params);
        while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $quantities[$row['bulkyid']] = $row['ship_qty'];
        }
        foreach ($labels as $itemId => $itemLabel) {
            $quantity            = (array_key_exists($itemId, $quantities))?$quantities[$itemId]:'0';
            $bulkyItems[$itemId] = ['label' => $itemLabel, 'qty' => $quantity];
        }

        return $bulkyItems;
    }

    public function getVehicles()
    {
        $recordId = $this->getId();
        $db       = PearDatabase::getInstance();
        $sql      = "SELECT * FROM `vtiger_quotes_vehicles` WHERE estimateid=?";
        $result   = $db->pquery($sql, [$recordId]);
        unset($params);
        //$vehicles[];
        $i = 1;
        while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $vehicles[$i] = $row;
            $i++;
        }

        return $vehicles;
    }

    public function getCorporateVehicles()
    {
        if (getenv('INSTANCE_NAME') != 'sirva') {
            return [];
        }
        $recordId = $this->getId();
        $db       = PearDatabase::getInstance();
        $sql      = "SELECT * FROM `vtiger_corporate_vehicles` WHERE estimate_id=?";
        $result   = $db->pquery($sql, [$recordId]);
        unset($params);
        //$vehicles[];
        $i = 1;
        while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $vehicles[$i] = $row;
            $i++;
        }
        $sql    = "SELECT MAX(vehicle_id) FROM `vtiger_corporate_vehicles` WHERE estimate_id=?";
        $result = $db->pquery($sql, [$recordId]);
        $max    = $result->fetchRow()[0];

        return [$max, $vehicles];
    }

    public function getMiscCharges()
    {
        $recordId        = $this->getId();
        $charges         = [];
        $flatSequence    = 0;
        $qtyRateSequence = 0;
        $type            = '';
        $db              = PearDatabase::getInstance();
        $sql             = 'SELECT description, charge, qty, discounted, discount, charge_type, line_item_id, enforced, from_contract FROM vtiger_misc_accessorials WHERE quoteid=?';
        if(getenv('INSTANCE_NAME') == 'graebel') {
        $sql             = 'SELECT description, charge, qty, discounted, discount, charge_type, line_item_id, enforced, from_contract, included FROM vtiger_misc_accessorials WHERE quoteid=?';
	}
        $params[]        = $recordId;
        $result          = $db->pquery($sql, $params);
        while ($row =& $result->fetchRow()) {
            $type = $row[5];
            if ($type == 'flat') {
                $flatSequence++;
                $sequence = $flatSequence;
            } elseif ($type == 'qty') {
                $qtyRateSequence++;
                $sequence = $qtyRateSequence;
            }
            $charges[$type][$sequence]              = new StdClass();
            $charges[$type][$sequence]->description = $row[0];
            $charges[$type][$sequence]->charge      = $row[1];
            $charges[$type][$sequence]->qty         = $row[2];
            $charges[$type][$sequence]->discounted  = $row[3];
            $charges[$type][$sequence]->discount    = (float) $row[4];
            $charges[$type][$sequence]->lineItemId  = $row[6];
            $charges[$type][$sequence]->enforced    = $row[7];
            $charges[$type][$sequence]->fromContract  = $row[8];
            if(getenv('INSTANCE_NAME') == 'graebel') {
            $charges[$type][$sequence]->included  = $row[9];
        }
        }

        return $charges;
    }

    public static function getMiscChargesStatic($recordId, $tablePrefix = '')
    {
        $charges         = [];
        $flatSequence    = 0;
        $qtyRateSequence = 0;
        $type            = '';
        $db              = PearDatabase::getInstance();
        $sql             = 'SELECT description, charge, qty, discounted, discount, charge_type, line_item_id, enforced, from_contract FROM '.$tablePrefix.'vtiger_misc_accessorials WHERE quoteid=?';
        if(getenv('INSTANCE_NAME') == 'graebel') {
        $sql             = 'SELECT description, charge, qty, discounted, discount, charge_type, line_item_id, enforced, from_contract, included FROM `'.$tablePrefix.'vtiger_misc_accessorials` WHERE
        quoteid=?';
        }
        $params[]        = $recordId;
        $result          = $db->pquery($sql, $params);
        while ($row =& $result->fetchRow()) {
            $type = $row[5];
            if ($type == 'flat') {
                $flatSequence++;
                $sequence = $flatSequence;
            } elseif ($type == 'qty') {
                $qtyRateSequence++;
                $sequence = $qtyRateSequence;
            }
            $charges[$type][$sequence]              = new StdClass();
            $charges[$type][$sequence]->description = $row[0];
            $charges[$type][$sequence]->charge      = $row[1];
            $charges[$type][$sequence]->qty         = $row[2];
            $charges[$type][$sequence]->discounted  = $row[3];
            $charges[$type][$sequence]->discount    = (float) $row[4];
            $charges[$type][$sequence]->lineItemId  = $row[6];
            $charges[$type][$sequence]->enforced    = $row[7];
            $charges[$type][$sequence]->fromContract  = $row[8];
            if(getenv('INSTANCE_NAME') == 'graebel') {
            $charges[$type][$sequence]->included  = $row[9];
        }
        }

        return $charges;
    }

    public function getCrates()
    {
        $recordId = $this->getId();
        return self::getCratesStatic($recordId);
    }

    public static function getCratesStatic($recordId, $tablePrefix = '')
    {
        $crates   = [];
        $sequence = 0;
        $db       = PearDatabase::getInstance();
        $selectSQL = "SELECT crateid, description, length, width, height, pack, unpack, ot_pack, ot_unpack, discount, cube, line_item_id";
        if (getenv('IGC_MOVEHQ')) {
            $selectSQL .= ', apply_tariff, custom_rate_amount, custom_rate_amount_unpack';
        }
        $sql      = $selectSQL. " FROM `".$tablePrefix."vtiger_crates` WHERE quoteid=?";
        $params[] = $recordId;
        $result   = $db->pquery($sql, $params);
        while ($row =& $result->fetchRow()) {
            $sequence++;
            $crates[$sequence]              = new StdClass();
            $crates[$sequence]->crateid     = $row[0];
            $crates[$sequence]->description = $row[1];
            $crates[$sequence]->crateLength = $row[2];
            $crates[$sequence]->crateWidth  = $row[3];
            $crates[$sequence]->crateHeight = $row[4];
            $crates[$sequence]->pack        = $row[5];
            $crates[$sequence]->unpack      = $row[6];
            $crates[$sequence]->otpack      = $row[7];
            $crates[$sequence]->otunpack    = $row[8];
            $crates[$sequence]->discount    = $row[9];
            $crates[$sequence]->cube        = $row[10];
            $crates[$sequence]->lineItemId  = $row[11];
            if (getenv('IGC_MOVEHQ')) {
                $crates[$sequence]->apply_tariff        = $row[12];
                $crates[$sequence]->custom_rate_amount  = $row[13];
                $crates[$sequence]->custom_rate_amount_unpack  = $row[14];
            } else {
                $crates[$sequence]->apply_tariff = 1;
            }
        }

        return $crates;
    }

    public function getApplyCustomRates()
    {
        $db     = PearDatabase::getInstance();
        $sql    = "SELECT apply_custom_sit_rate_override, apply_custom_pack_rate_override,
                          apply_custom_sit_rate_override_dest, tpg_custom_crate_rate FROM `vtiger_quotes` WHERE quoteid=?";
        $result = $db->pquery($sql, [$this->getId()]);
        $row    = $result->fetchRow();

        return ['apply_custom_sit_rate_override' => $row['apply_custom_sit_rate_override'], 'apply_custom_pack_rate_override' => $row['apply_custom_pack_rate_override'],
                'apply_custom_sit_rate_override_dest' => $row['apply_custom_sit_rate_override_dest'],
                'tpg_custom_crate_rate' => $row['tpg_custom_crate_rate']];
    }

    /**
     *     Gets the tariffs for the current user, or a user based on the agent that it is given for local or interstate
     *
     * @param boolean local True to get Local Tariffs, False to get Interstate Tariffs
     * @param array   agents Optional array of agents to get tariffs for
     *
     * @return array Array of Tariff IDs for the given parameters
     */
    public function getCurrentUserTariffs($local = false, $agents = null, $intra = false)
    {
        $db = PearDatabase::getInstance();
        //file_put_contents('logs/devLog.log', "\n \$agents : ".print_r($agents,true), FILE_APPEND);
        if (empty($agents)) {
            $agents = Users_Record_Model::getCurrentUserModel()->getAccessibleAgentsForUser();
        }
        //file_put_contents('logs/devLog.log', "\n \$agents : ".print_r($agents,true), FILE_APPEND);
        if (count($agents) == 0) {
            return [];
        }
        $tariffs = [];
        // SELECT * FROM `vtiger_tariffs`
        // JOIN `vtiger_crmentity` ON `vtiger_tariffs`.`tariffsid` = `vtiger_crmentity`.`crmid`
        // JOIN `vtiger_groups` ON `vtiger_groups`.`groupid` = `vtiger_crmentity`.`smownerid` AND `vtiger_crmentity`.`setype` = 'Tariffs'
        // JOIN `vtiger_agentmanager` ON `vtiger_groups`.`groupname` = `vtiger_agentmanager`.`agency_name`
        // WHERE `vtiger_agentmanager`.`agentmanagerid` = 3
        $idcolumn   = $local?'`vtiger_tariffs`.tariffsid':'tariffid';
        $tablename  = $local
            ?"vtiger_tariffs`
		JOIN `vtiger_crmentity` ON `vtiger_tariffs`.`tariffsid` = `vtiger_crmentity`.`crmid`
		JOIN `vtiger_agentmanager` ON `vtiger_crmentity`.`agentid` = `vtiger_agentmanager`.`agentmanagerid"
            :'vtiger_tariff2agent`
		JOIN `vtiger_crmentity` AS tariffEntity ON `vtiger_tariff2agent`.`tariffid` = tariffEntity.`crmid`
		JOIN `vtiger_crmentity` AS agentEntity ON `vtiger_tariff2agent`.`agentid` = agentEntity.`crmid';
        $columnname = $local?'`vtiger_agentmanager`.`agentmanagerid`':'`vtiger_tariff2agent`.agentid';
        if(getenv('INSTANCE_NAME') == 'sirva') {
		$sql = "SELECT DISTINCT $idcolumn FROM `$tablename` WHERE $columnname IN (".generateQuestionMarks($agents).")";
		$sql .= $local?" AND deleted=0":" AND tariffEntity.deleted=0 AND agentEntity.deleted=0";
		//file_put_contents('logs/devLog.log', "\n agents sql : ".print_r($sql,true), FILE_APPEND);
		//file_put_contents('logs/devLog.log', "\n agencyId : ".print_r($agencyId,true), FILE_APPEND);
		$agentsId = array_keys($agents);
		$result = $db->pquery($sql, [$agentsId]);
		while ($row =& $result->fetchRow()) {
		    //added to prevent multiple of the same tariffs
		    $temp = $local?Tariffs_Record_Model::getInstanceById($row[0]):TariffManager_Record_Model::getInstanceById($row[0]);
		    if (!in_array($temp, $tariffs)) {
		        //exclude max 3 and max 4 from coming back from here.
		        //I could add it to the sql above, but....I want preg_match
		        //not sure if should be sirva specific.
		        //(getenv('INSTANCE_NAME') == 'sirva') &&
		        if (
		            $temp->get('tariff_name') &&
		            preg_match('/^max\s*[34]/i', $temp->get('tariff_name')) &&
		            (preg_match('/^max\s*[34]/i', $temp->get('tariff_name')) !== false)
		        ) {
		            continue;
		        }
		        //exclue interstate tariffs if you're searching for intra
		        if($intra && !$local && $temp->get('tariff_type') == 'Interstate'){
		            continue;
		        }
		        $temp->local = true;
		        if(!in_array($temp, $tariffs)) {
		            $tariffs[] = $temp;
		        }
		    }
		}
	} else {

        foreach ($agents as $agencyId => $agencyName) {
            $sql = "SELECT $idcolumn FROM `$tablename` WHERE $columnname=?";
            $sql .= $local?" AND deleted=0":" AND tariffEntity.deleted=0 AND agentEntity.deleted=0";
            //file_put_contents('logs/devLog.log', "\n agents sql : ".print_r($sql,true), FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n agencyId : ".print_r($agencyId,true), FILE_APPEND);
            $result = $db->pquery($sql, [$agencyId]);
            while ($row =& $result->fetchRow()) {
                //added to prevent multiple of the same tariffs
		        $temp = $local?Vtiger_Record_Model::getInstanceById($row[0], 'Tariffs'):Vtiger_Record_Model::getInstanceById($row[0], 'TariffManager');
                if (!in_array($temp, $tariffs)) {
                    //exclude max 3 and max 4 from coming back from here.
                    //I could add it to the sql above, but....I want preg_match
                    //not sure if should be sirva specific.
                    //(getenv('INSTANCE_NAME') == 'sirva') &&
                    if (
                        $temp->get('tariff_name') &&
                        preg_match('/^max\s*[34]/i', $temp->get('tariff_name')) &&
                        (preg_match('/^max\s*[34]/i', $temp->get('tariff_name')) !== false)
                        ) {
                        continue;
                    }
                    //exclue interstate tariffs if you're searching for intra
                    if ($intra && !$local && $temp->get('tariff_type') == 'Interstate') {
                        continue;
                    }
                    $temp->local = true;
                    if (!in_array($temp, $tariffs)) {
                        $tariffs[] = $temp;
                    }
                }
            }
        }
	}
/*        if($business_line == 'Intrastate Move') {
            $maxSql = 'SELECT tariffsid, tariff_name FROM `vtiger_tariffs`
                        JOIN `vtiger_crmentity` ON (`vtiger_tariffs`.`tariffsid` = `vtiger_crmentity`.`crmid`)
                        WHERE `vtiger_crmentity`.`deleted`=0
                        AND (tariff_type LIKE ? OR tariff_type LIKE ?)';
            //this was erroring out becuase it didnt have $db, this is a stop-gap to make sure estimates in trunk don't explode
            $result = $db->pquery($maxSql, ['Max%3', 'Max%4']);
            while ($row =& $result->fetchRow()) {
                $temp = $local?Tariffs_Record_Model::getInstanceById($row[0]):TariffManager_Record_Model::getInstanceById($row[0]);
                if (!in_array($temp, $tariffs)) {
                    $tariffs[] = $temp;
                }
            }
        }*/
        //file_put_contents('logs/devLog.log', "\n \$tariffs : ".print_r($tariffs,true), FILE_APPEND);

        return $tariffs;
    }

    public static function getAllowedTariffsForUser($owner = null, $filter = null)
    {
        $db = &PearDatabase::getInstance();
        if($owner)
        {
            //@TODO: FIX this.
            //I think I fixed it..
            try {
                $agents = [];
                $owners = explode(' |##| ', $owner);

                foreach ($owners as $agent) {
                    $recordModel = Vtiger_Record_Model::getInstanceById($agent);
                    if ($recordModel->getModuleName() == 'VanlineManager') {
                        array_merge($agents, array_keys(Users_Record_Model::getCurrentUserModel()->getAccessibleAgentsForUser($agent)));
                    } else {
                        $agents = Users_Record_Model::getCurrentUserModel()->getBothAccessibleOwnersIdsForUser();
                    }
                }
            } catch (Exception $e) {
                $agents = Users_Record_Model::getCurrentUserModel()->getBothAccessibleOwnersIdsForUser();
            }
        } else {
            $agents = Users_Record_Model::getCurrentUserModel()->getBothAccessibleOwnersIdsForUser();
        }
        if (!count($agents)) {
            return [];
        } else {
            $agents = implode(',', $agents);
        }
        $tariffs = [];
        $sql = 'SELECT * FROM vtiger_tariffmanager
                            INNER JOIN vtiger_crmentity AS crmTariff ON(crmTariff.crmid=tariffmanagerid)
                            WHERE crmTariff.deleted=0 AND EXISTS
                            (SELECT 1 FROM vtiger_tariff2agent INNER JOIN vtiger_crmentity AS crmAgent ON(crmAgent.crmid=vtiger_tariff2agent.agentid)
                              WHERE tariffid=tariffmanagerid AND crmAgent.deleted=0 AND vtiger_tariff2agent.agentid IN('.$agents.'))';
        $res = $db->pquery($sql,
                           []);
        while($row = $res->fetchRow())
        {
            $data = [
                'tariff_id' => $row['tariffmanagerid'],
                'tariff_name' => $row['tariffmanagername'],
                'is_managed_tariff' => 1,
                'custom_tariff_type' => $row['custom_tariff_type'],
                'custom_js' => $row['custom_javascript'],
                'is_intrastate' => $row['tariff_type'] == 'Intrastate',
            ];
            if($filter == null || in_array($row['custom_tariff_type'], $filter)) {
                $tariffs[$data['tariff_id']] = $data;
            }
        }

        $useLocalEstimateTypes = getenv('INSTANCE_NAME') == 'sirva';
        $sql = 'SELECT * FROM vtiger_tariffs
                            INNER JOIN vtiger_crmentity AS crmTariff ON(crmTariff.crmid=tariffsid)
                            LEFT JOIN vtiger_agentmanager ON(agentmanagerid=crmTariff.agentid)
                            LEFT JOIN vtiger_crmentity AS crmAgent ON(crmAgent.crmid=agentmanagerid)
                            WHERE crmTariff.deleted=0 AND tariff_status!="Inactive" AND COALESCE(crmAgent.deleted,0)=0 AND (crmTariff.agentid IS NULL OR crmTariff.agentid IN('.$agents.') OR agentmanagerid IN('.$agents.'))
                            AND (SELECT COUNT(tariffservicesid) FROM vtiger_tariffservices WHERE related_tariff = vtiger_tariffs.tariffsid) > 0';
        $res = $db->query($sql);
        while($row = $res->fetchRow())
        {
            $data = [
                'tariff_id' => $row['tariffsid'],
                'tariff_name' => $row['tariff_name'],
                'custom_tariff_type' => $row['tariff_type'],
                'is_managed_tariff' => 0,
                'tariff_state' => $row['tariff_state'],
                'admin_access' => \MoveCrm\InputUtils::CheckboxToBool($row['admin_access']),
                //'restricted_business_lines' => $row['business_line'] ? explode(' |##| ', $row['business_line']) : [],
				'restricted_business_lines' => $row['business_line'] ? $row['business_line'] == 'All' ? Vtiger_Util_Helper::getAllPicklistValuesAsString('business_line') : explode(' |##| ', $row['business_line']) : [],
				'restricted_commodities' => $row['commodities'] ? $row['commodities'] == 'All' ? Vtiger_Util_Helper::getAllPicklistValuesAsString('commodities') : explode(' |##| ', $row['commodities']) : [],
            ];
            if($useLocalEstimateTypes)
            {
                $data['local_estimate_types'] = self::getLocalEstimateTypes($db, $data['tariff_id']);
            }
            if($filter == null || in_array($row['custom_tariff_type'], $filter)) {
                $tariffs[$data['tariff_id']] = $data;
            }
        }

        return $tariffs;
    }

    public static function getAllowedTariffsForListView($owner = null) {
        $tariffs = Estimates_Record_Model::getAllowedTariffsForUser($owner);

        foreach($tariffs as $id => &$info) {
            $info = $info['tariff_name'];
        }

        return $tariffs;
    }
    private static function getLocalEstimateTypes($db, $localTariff){
        $localEstimateTypes = [];
        $sql = "SELECT DISTINCT tariff_orders_type FROM `vtiger_tariffreportsections` where tariff_orders_tariff = ?";
        $result = $db->pquery($sql, [$localTariff]);
        $row       = $result->fetchRow();
        while ($row != null) {
            $localEstimateTypes[] = $row['tariff_orders_type'];
            $row = $result->fetchRow();
        }
        return $localEstimateTypes;
    }

    public function getEffectiveDate()
    {
        $db     = PearDatabase::getInstance();
        $sql    = "SELECT effective_date FROM `vtiger_quotes` WHERE quoteid=?";
        $result = $db->pquery($sql, [$this->getId()]);
        $row    = $result->fetchRow();

        return $row[0];
    }

    public function getEffecitveDate()
    {
        return $this->getEffectiveDate();
    }

    public function getCountyChargePicklists($serviceid)
    {
        $db     = PearDatabase::getInstance();
        $sql    = "SELECT name FROM `vtiger_tariffcountycharge` WHERE serviceid=?";
        $result = $db->pquery($sql, [$serviceid]);
        //file_put_contents('C:/Apache22/htdocs/ryan/logs/CountyChargePicklist.log', 'Result: ' . print_r($results) . '/n', FILE_APPEND);
        while ($row =& $result->fecthRow()) {
            //file_put_contents('C:/Apache22/htdocs/ryan/logs/CountyChargePicklist.log', 'Row: '. $row . '/n', FILE_APPEND);
        }
    }

    public function getCurrentAssignedTariff()
    {
        $db     = PearDatabase::getInstance();
        $sql    = "SELECT effective_tariff,contract FROM `vtiger_quotes` INNER JOIN vtiger_crmentity ON (crmid=effective_tariff) WHERE quoteid=? AND deleted=0";
        $result = $db->pquery($sql, [$this->getId()]);
        $row    = $result->fetchRow();
        if ($row == null) {
            return null;
        }

        if($row[1] && getenv('INSTANCE_NAME') == 'graebel')
        {
            $res2 = $db->pquery('SELECT related_tariff FROM vtiger_contracts INNER JOIN vtiger_crmentity ON(related_tariff=crmid) WHERE contractsid=? AND deleted=0', [$row[1]]);
            if($res2)
            {
                $row2 = $res2->fetchRow();
                if($row2 && $row2['related_tariff'])
                {
                    return $row2['related_tariff'];
                }
            }
        }

        return $row[0];
    }

    public static function getCurrentAssignedTariffStatic($recordID, $tablePrefix = '')
    {
        if (!$recordID) {
            return null;
        }
        $db     = PearDatabase::getInstance();
        $sql    = "SELECT effective_tariff FROM `".$tablePrefix."vtiger_quotes` WHERE quoteid=?";
        $result = $db->pquery($sql, [$recordID]);
        $row    = $result->fetchRow();
        if ($row == null) {
            return null;
        }

        return $row[0];
    }

    public static function isLocalTariff($tariffid)
    {
        if(!$tariffid)
        {
            return null;
        }
        $db     = PearDatabase::getInstance();
        $sql    = "SELECT 1 FROM `vtiger_tariffs` WHERE tariffsid=?";
        $result = $db->pquery($sql, [$tariffid]);
        $row    = $result->fetchRow();
        if (empty($row)) {
            return false;
        }

        return true;
    }

    //OT 3189
    public function getAssignedTariffName($tariffid)
    {
        if (!$tariffid)  {
            return 'Default';
        }
        try {
        $effectiveTariffRecordModel = Vtiger_Record_Model::getInstanceById($tariffid);
        $customTariffType = $effectiveTariffRecordModel->get('custom_tariff_type');
        if ($customTariffType == '400N Base') {
            return $customTariffType;
           }
        $res = \MoveCrm\ValuationUtils::MapPricingMode($tariffid, 'Interstate Move');
        if ($res == 'Interstate') {
            $res = 'Default';
            }

        return $res;
        } catch(Exception $e)
        {
            return '--Deleted--';
        }

    }

    public function getCurrentAssignedTariffInfo() {
        return self::getTariffInfo($this->getCurrentAssignedTariff());
    }

    public static function getTariffInfo($tariffID)
    {
        if(!$tariffID)
        {
            return [
                'name' => '',
                'is_interstate' => 0
            ];
        }
        $db = &PearDatabase::getInstance();
        $sql = 'SELECT tariffmanagername,custom_tariff_type,custom_javascript FROM vtiger_tariffmanager
                  INNER JOIN vtiger_crmentity ON(tariffmanagerid=crmid)
                  WHERE tariffmanagerid=? AND deleted=0';
        $res = $db->pquery($sql, [$tariffID]);
        if($row = $res->fetchRow())
        {
            $outArray = [
                'name' => $row['tariffmanagername'],
                'custom_type' => $row['custom_tariff_type'],
                'custom_js' => $row['custom_javascript'],
                'is_interstate' => 1
            ];
            if (getenv('INSTANCE_NAME') == 'sirva') {
                //@TODO: pressed for time, this will need fixed in the future
                $customRates = ['TPG','Pricelock','TPG GRR','Pricelock GRR','Blue Express','Allied Express','Truckload Express'];
                $noContainers = ['UAS','Intra - 400N','400N Base','400N/104G','ALLV-2A','NAVL-12A'];

                $outArray['hide_packing_rates'] = 1;
                $outArray['hide_custom_rates'] = 1;
                $outArray['hide_packing_containers'] = 1;

                if (in_array($row['custom_tariff_type'], $customRates)) {
                    $outArray['hide_packing_rates'] = 0;
                }

                if (
                    in_array($row['custom_tariff_type'], $customRates) &&
                    !in_array($row['custom_tariff_type'], $noContainers)
                ) {
                    $outArray['hide_custom_rates'] = 0;
                }

                if (!in_array($row['custom_tariff_type'], $noContainers)) {
                    $outArray['hide_packing_containers'] = 0;
                }
            }
            return $outArray;
        }
        $sql = 'SELECT tariff_name,tariff_type FROM vtiger_tariffs
                  INNER JOIN vtiger_crmentity ON(tariffsid=crmid)
                  WHERE tariffsid=? AND deleted=0';
        $res = $db->pquery($sql, [$tariffID]);
        if($row = $res->fetchRow())
        {
            return [
                'name' => $row['tariff_name'],
                'custom_type' => $row['tariff_type'],
                'is_interstate' => 0
                ];
        }
        return ['name' => '-- Deleted Record --'];
    }

    public function pricingColorLock()
    {
        $db     = PearDatabase::getInstance();
        $sql    = "SHOW COLUMNS FROM `vtiger_quotes` LIKE 'pricing_color_lock'";
        $result = $db->pquery($sql, []);
        $row    = $result->fetchRow();
        if ($row == null) {
            return;
        }
        $sql    = "SELECT pricing_color_lock FROM `vtiger_quotes` WHERE quoteid=?";
        $result = $db->pquery($sql, [$this->getId()]);
        $row    = $result->fetchRow();
        if ($row == null) {
            return 0;
        }
        if ($row[0] == null) {
            return 0;
        }

        return $row[0];
    }

    public static function getVanlineIdForNewRecord()
    {
        $possVanlines = Users_Record_Model::getCurrentUserModel()->getAccessibleVanlinesForUser();
        reset($possVanlines);
        $first_key = key($possVanlines);
	//If vanline id = 0 this produce an error in the UI
	if($first_key){
        $recordModel = Vtiger_Record_Model::getInstanceById($first_key, 'VanlineManager');
        $vanlineId = $recordModel->get('vanline_id');
        return $vanlineId;
	} else {
	    return false;
	}


    }

    public function getVanlineId() {

        return self::getVanlineIdStatic($this->getId());
    }

    public static function getVanlineIdStatic($recordId, $tablePrefix = '', $userId = '') {
        $db = PearDatabase::getInstance();
        if ($userId == '') {
            $sql      = "SELECT agentid FROM `".$tablePrefix."vtiger_crmentity` WHERE crmid=?";
            $result   = $db->pquery($sql, [$recordId]);
            $ownerRow = $result->fetchRow();
            $ownerId  = $ownerRow[0];
        } else {
            $ownerRecordModel = Users_Record_Model::getInstanceById($userId, 'Users');
            $ownerId = key($ownerRecordModel->getAccessibleAgentsForUser());
        }
        $sql    = "SELECT vtiger_vanlinemanager.vanline_id FROM `vtiger_agentmanager`
				   JOIN `vtiger_crmentity` ON agentmanagerid=crmid
				   JOIN `vtiger_vanlinemanager` ON vanlinemanagerid=vtiger_agentmanager.vanline_id
				   WHERE `vtiger_agentmanager`.agentmanagerid=? AND deleted=0";
        $result = $db->pquery($sql, [$ownerId]);
        $row    = $result->fetchRow();
        return $row[0];
    }

    public static function getVanlineBrandStatic($recordId, $tablePrefix = '', $userId = '')
    {
        $db = PearDatabase::getInstance();
        if ($userId == '') {
            $sql      = "SELECT agentid FROM `".$tablePrefix."vtiger_crmentity` WHERE crmid=?";
            $result   = $db->pquery($sql, [$recordId]);
            $ownerRow = $result->fetchRow();
            $ownerId  = $ownerRow[0];
        } else {
            $ownerRecordModel = Users_Record_Model::getInstanceById($recordId, 'Users');
            $ownerId          = key($ownerRecordModel->getAccessibleAgentsForUser());
        }
        $sql = "SELECT vtiger_vanlinemanager.vanline_id, vtiger_vanlinemanager.vanline_name FROM `vtiger_agentmanager`
			   JOIN `vtiger_crmentity` ON agentmanagerid=crmid
			   JOIN `vtiger_vanlinemanager` ON vanlinemanagerid=vtiger_agentmanager.vanline_id
			   WHERE `vtiger_agentmanager`.agentmanagerid=? AND deleted=0";
        $result      = $db->pquery($sql, [$ownerId]);
        $row         = $result->fetchRow();
        $vanlineName = $row['vanline_name'];
        $vanlineId = $row['vanline_id'];
        //@TODO: this is a bold assumption...
        if ($vanlineName == 'Allied' || $vanlineId == 1) {
            return 'AVL';
        } elseif ($vanlineName == 'North American Van Lines' || $vanlineId == 9) {
            return 'NAVL';
        }
        return null;
    }

    public function getInterstateServiceCharges()
    {
        $recordId = $this->getId();
        global $adb;
        $chargesArray = [];
        if (Vtiger_Utils::CheckTable('vtiger_quotes_inter_servchg')) {
            $sql    = "SELECT * FROM `vtiger_quotes_inter_servchg` WHERE quoteid=? ORDER BY is_dest,serviceid";
        $result = $adb->pquery($sql, [$recordId]);
        while ($row =& $result->fetchRow()) {
            $chargesArray[$row['is_dest']][] = [
                    'serviceid'           => $row['serviceid'],
                    'service_description' => $row['service_description'],
                    'always_used'         => $row['always_used'],
                    'charge'              => $row['charge'],
                    'minimum'             => $row['minimum'],
                    'service_weight'      => $row['service_weight']? :'',
                    'applied'             => $row['applied']
            ];
            }
        }
        return $chargesArray;
    }

    public static function getAgents($recordId, $tablePrefix)
    {
//        file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Entering getAgents function\n", FILE_APPEND);
//        $db     = PearDatabase::getInstance();
//        $agents = [];
//        $sql    = "SELECT agency_code, agency_name, address1, address2, city, state, zip, country, phone1, phone2, fax, email
//				   FROM `".$tablePrefix."vtiger_crmentity`
//				   LEFT JOIN `vtiger_agentmanager`
//			       ON `vtiger_agentmanager`.`agentmanagerid` = `".$tablePrefix."vtiger_crmentity`.`agentid`
//				   WHERE crmid=?";
//        $result = $db->pquery($sql, [$recordId]);
//        file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Before row assignment\n", FILE_APPEND);
//        file_put_contents('logs/devLog.log', "\n Sql : ".print_r($sql, true), FILE_APPEND);
//        file_put_contents('logs/devLog.log', "\n crmId : ".print_r([$recordId], true), FILE_APPEND);
//        $row = $result->fetchRow();
//        file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."After booking agent SQL call\n", FILE_APPEND);
//        $agents['booking_agent']['code']    = $row[0];
//        $agents['booking_agent']['name']    = $row[1];
//        $agents['booking_agent']['add1']    = $row[2];
//        $agents['booking_agent']['add2']    = $row[3];
//        $agents['booking_agent']['city']    = $row[4];
//        $agents['booking_agent']['state']   = $row[5];
//        $agents['booking_agent']['zip']     = $row[6];
//        $agents['booking_agent']['country'] = $row[7];
//        $agents['booking_agent']['phone1']  = $row[8];
//        $agents['booking_agent']['phone2']  = $row[9];
//        $agents['booking_agent']['fax']     = $row[10];
//        $agents['booking_agent']['email']   = $row[11];
//        $sql = "SELECT `vtiger_agentmanager`.agency_code, `vtiger_agentmanager`.agency_name, `vtiger_agentmanager`.address1, `vtiger_agentmanager`.address2, `vtiger_agentmanager`.city, `vtiger_agentmanager`.state, `vtiger_agentmanager`.zip, `vtiger_agentmanager`.country, `vtiger_agentmanager`.phone1, `vtiger_agentmanager`.phone2, `vtiger_agentmanager`.fax, `vtiger_agentmanager`.email, agenttype
//                FROM `vtiger_orders_participatingagents`
//                LEFT JOIN `vtiger_agents`
//                    ON `vtiger_agents`.agentsid = `vtiger_orders_participatingagents`.`agentid`
//                LEFT JOIN `vtiger_agentmanager`
//                    ON `vtiger_agentmanager`.`agency_name` = `vtiger_agents`.`agentname`
//                WHERE `vtiger_orders_participatingagents`.`ordersid` = ?";
//        $result                             = $db->pquery($sql, [$recordId]);
//        file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."After other agents SQL call\n", FILE_APPEND);
//        while ($row =& $result->fetchRow()) {
//            switch ($row[12]) {
//                case 5;
//                    $agents['origin_agent']['code']    = $row[0];
//                    $agents['origin_agent']['name']    = $row[1];
//                    $agents['origin_agent']['add1']    = $row[2];
//                    $agents['origin_agent']['add2']    = $row[3];
//                    $agents['origin_agent']['city']    = $row[4];
//                    $agents['origin_agent']['state']   = $row[5];
//                    $agents['origin_agent']['zip']     = $row[6];
//                    $agents['origin_agent']['country'] = $row[7];
//                    $agents['origin_agent']['phone1']  = $row[8];
//                    $agents['origin_agent']['phone2']  = $row[9];
//                    $agents['origin_agent']['fax']     = $row[10];
//                    $agents['origin_agent']['email']   = $row[11];
//                    break;
//                case 1;
//                    $agents['dest_agent']['code']    = $row[0];
//                    $agents['dest_agent']['name']    = $row[1];
//                    $agents['dest_agent']['add1']    = $row[2];
//                    $agents['dest_agent']['add2']    = $row[3];
//                    $agents['dest_agent']['city']    = $row[4];
//                    $agents['dest_agent']['state']   = $row[5];
//                    $agents['dest_agent']['zip']     = $row[6];
//                    $agents['dest_agent']['country'] = $row[7];
//                    $agents['dest_agent']['phone1']  = $row[8];
//                    $agents['dest_agent']['phone2']  = $row[9];
//                    $agents['dest_agent']['fax']     = $row[10];
//                    $agents['dest_agent']['email']   = $row[11];
//                    break;
//                case 3;
//                    $agents['carrier_agent']['code']    = $row[0];
//                    $agents['carrier_agent']['name']    = $row[1];
//                    $agents['carrier_agent']['add1']    = $row[2];
//                    $agents['carrier_agent']['add2']    = $row[3];
//                    $agents['carrier_agent']['city']    = $row[4];
//                    $agents['carrier_agent']['state']   = $row[5];
//                    $agents['carrier_agent']['zip']     = $row[6];
//                    $agents['carrier_agent']['country'] = $row[7];
//                    $agents['carrier_agent']['phone1']  = $row[8];
//                    $agents['carrier_agent']['phone2']  = $row[9];
//                    $agents['carrier_agent']['fax']     = $row[10];
//                    $agents['carrier_agent']['email']   = $row[11];
//                    break;
//            }
//        }
//        file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Preparing to return \$agents:\n".print_r($agents, true)."\n", FILE_APPEND);
//
//        return $agents;
    }

    public function pseudoSave()
    {
        $recordModel = $this;
        //Contents of Vtiger/models/Module.php saveRecord function
        $moduleName = $this->getModuleName();
        $focus      = CRMEntity::getInstance($moduleName);
        $fields     = $focus->column_fields;
        foreach ($fields as $fieldName => $fieldValue) {
            $fieldValue = $recordModel->get($fieldName);
            if (is_array($fieldValue)) {
                $focus->column_fields[$fieldName] = $fieldValue;
            } elseif ($fieldValue !== null) {
                $focus->column_fields[$fieldName] = decode_html($fieldValue);
            }
        }
        $focus->mode = $recordModel->get('mode');
        $focus->id   = $recordModel->getId();
        $focus->pseudoSave($moduleName);

        return $recordModel->setId($focus->id);
    }

    public function getSirvaVanline($record = false)
    {
        $db = PearDatabase::getInstance();
        if ($record) {
            $recordId = $record;
        } else {
            $recordId = $this->getId();
        }
        $sql     = 'SELECT smownerid FROM `vtiger_crmentity` WHERE crmid = ?';
        $result  = $db->pquery($sql, [$recordId]);
        $row     = $result->fetchRow();
        $ownerId = $row[0];
        //file_put_contents('logs/devLog.log', "\n ownerId: ".$ownerId, FILE_APPEND);
        if ($ownerId) {
            $sql     =
                'SELECT `vtiger_agentmanager`.agentmanagerid FROM `vtiger_agentmanager` JOIN `vtiger_groups` ON  `vtiger_agentmanager`.agency_name = `vtiger_groups`.groupname WHERE `vtiger_groups`.groupid = ?';
            $result  = $db->pquery($sql, [$ownerId]);
            $row     = $result->fetchRow();
            $agentId = $row[0];
            //file_put_contents('logs/devLog.log', "\n agentId: ".$agentId, FILE_APPEND);
            if ($agentId) {
                $sql       = 'SELECT vanline_id FROM `vtiger_agentmanager` WHERE agentmanagerid = ?';
                $result    = $db->pquery($sql, [$agentId]);
                $row       = $result->fetchRow();
                $vanlineId = $row[0];
               // file_put_contents('logs/devLog.log', "\n vanlineId: ".$vanlineId, FILE_APPEND);
                if ($vanlineId) {
                    $sql         = 'SELECT vanline_name, vanline_id FROM `vtiger_vanlinemanager` WHERE vanlinemanagerid = ?';
                    $result      = $db->pquery($sql, [$vanlineId]);
                    $row         = $result->fetchRow();
                    $vanlineName = $row[0];
                    $vanlineCode = $row[1];
                    if ($vanlineName == 'Allied' || $vanlineCode == 1) {
                        return 'Allied';
                    } elseif ($vanlineName == 'North American Van Lines' || $vanlineCode == 9) {
                        return 'North American';
                    } else {
                        return null;
                    }
                }
            }
        }

        return null;
    }

    //this must be a function somewhere.
    public function getParticipatingAgentsInfoForDetailLineItems($agentsID)
    {
        if (!$agentsID) {
            return '';
        }
        $displayName = '';
        try {
            if ($agentsRecordModel = Vtiger_Record_Model::getInstanceById($agentsID, 'Agents')) {
                $displayName = $agentsRecordModel->getDisplayName();
                //$displayName = '('.$agentsRecordModel->get('agent_number').')';
            }
        } catch (Exception $ex) {
            //DON'T CARE!
        }
        return $displayName;
    }

    public static function getParticipatingAgentsForDetailLineItemsStatic($recordID, $agent_type = false)
    {
        if ($recordID) {
            try {
                $recordModel = Vtiger_Record_Model::getInstanceById($recordID);
                if (method_exists($recordModel, 'getParticipatingAgentsForDetailLineItems')) {
                    return $recordModel->getParticipatingAgentsForDetailLineItems($agent_type);
                }
            } catch (Exception $ex) {
            }
        }
        //return empty array.
        return [];
    }

    public static function getParticipatingAgentsForDetailLineItemsFromParentIdStatic($sourceId)
    {
        $participatingAgentInfo = ParticipatingAgents_Module_Model::getParticipants($sourceId);
        foreach ($participatingAgentInfo as $singlePA) {
            //$paInfo[$singlePA['agent_type']] = $singlePA['agentName'].' ('.$singlePA['agent_number'].')';
            //$paInfo[$singlePA['agent_type']]['name'] = $singlePA['agentName'].' ('.$singlePA['agent_number'].')';
            $paInfo[$singlePA['agent_type']]['name'] = $singlePA['agentName']. ' ('.$singlePA['agent_number'].')';
            $paInfo[$singlePA['agent_type']]['agents_id'] = $singlePA['agents_id'];
            $paInfo[$singlePA['agent_type']]['agent_type'] = $singlePA['agent_type'];
        }
        return $paInfo;
    }

    public function getParticipatingAgentsForDetailLineItems($agent_type = false)
    {
        $paInfo = false;

        if ($this->paInfo) {
            //pull already held information
            $paInfo = $this->paInfo;
        } elseif ($this->paInfoFail) {
            //don't bother if we already failed.
            $paInfo = false;
        } else {
            //try to pull this information
            $thingWithParticipants = false;
            if (!$thingWithParticipants && $this->get('orders_id')) {
                $thingWithParticipants = $this->get('orders_id');
            } elseif (!$thingWithParticipants && $this->get('potentialid')) {
                $thingWithParticipants = $this->get('potentialid');
            } else {
                $this->paInfoFail = true;
            }
            if ($thingWithParticipants) {
                $paInfo = self::getParticipatingAgentsForDetailLineItemsFromParentIdStatic($thingWithParticipants);
//                $participatingAgentInfo = ParticipatingAgents_Module_Model::getParticipants($thingWithParticipants);
//                foreach ($participatingAgentInfo as $singlePA) {
//                    //$paInfo[$singlePA['agent_type']] = $singlePA['agentName'].' ('.$singlePA['agent_number'].')';
//                    //$paInfo[$singlePA['agent_type']]['name'] = $singlePA['agentName'].' ('.$singlePA['agent_number'].')';
//                    $paInfo[$singlePA['agent_type']]['name'] = $singlePA['agentName']. ' ('.$singlePA['agent_number'].')';
//                    $paInfo[$singlePA['agent_type']]['agents_id'] = $singlePA['agents_id'];
//                    $paInfo[$singlePA['agent_type']]['agent_type'] = $singlePA['agent_type'];
//                }
                //store for later use.
                $this->paInfo = $paInfo;
            }
        }
        if ($agent_type) {
            return $paInfo[$agent_type];
        } else {
            return $paInfo;
        }
    }

    public function getMoveRolesInfoForDetailLineItems($vendorid)
    {
        if (!$vendorid) {
            return '';
        }
        $displayName = '';
        try {
            if ($vendorsRecordModel = Vtiger_Record_Model::getInstanceById($vendorid, 'Vendors')) {
                $displayName = $vendorsRecordModel->getDisplayName().' - '.$vendorsRecordModel->get('icode');
            }
        } catch (Exception $ex) {
            //DON'T CARE!
        }
        return $displayName;
    }

    public function getMoveRolesForDetailLineItemsStatic($recordID, $agent_type = false, $ordersID = false)
    {
        if ($recordID) {
            try {
                $recordModel = Vtiger_Record_Model::getInstanceById($recordID);
                if (method_exists($recordModel, 'getMoveRolesForDetailLineItems')) {
                    return $recordModel->getMoveRolesForDetailLineItems($agent_type);
                }
            } catch (Exception $ex) {
            }
        } else {
            try {
                $recordModel = new Estimates_Record_Model();
                if (method_exists($recordModel, 'getMoveRolesForDetailLineItems')) {
                    return $recordModel->getMoveRolesForDetailLineItems($agent_type, $ordersID);
                }
            } catch (Exception $ex) {
            }
        }

        //return empty array.
        return [];
    }

    //@TODO: Refactor to reflect @NOTE:
    //@NOTE: This NO LONGER gets ALL moveroles it only gets moveroles with a vendor.  So now it's a confusing misnomer
    public function getMoveRolesForDetailLineItems($role_type = false, $ordersID = false)
    {
        $mrInfo = false;
        if(getenv('INSTANCE_NAME') != 'graebel')
        {
            return $mrInfo;
        }
        //try to pull this information
        $thingWithMoveRoles = $ordersID;
        if (!$thingWithMoveRoles) {
            $thingWithMoveRoles = $this->get('orders_id');
        }
        if ($thingWithMoveRoles) {
            $db = PearDatabase::getInstance();
            //$stmt = 'SELECT * FROM `vtiger_moveroles`
            $stmt = 'SELECT `service_provider`, `icode`, `vendorname`, `vendorid` FROM `vtiger_moveroles`
                      JOIN `vtiger_vendor` ON (`vtiger_moveroles`.`service_provider` = `vtiger_vendor`.`vendorid`)
                      WHERE `moveroles_orders` = ? AND `icode` IS NOT NULL AND `icode` != ""';
            $result = $db->pquery($stmt, [$thingWithMoveRoles]);
            if (method_exists($result, 'fetchRow')) {
            while ($row = $result->fetchRow()) {
                $mrInfo[$row['vendorname']]['name'] = $row['vendorname'];
                $mrInfo[$row['vendorname']]['icode'] = $row['icode'];
                $mrInfo[$row['vendorname']]['id'] = $row['vendorid'];
                }
            } else {
                $mrInfo[$row['vendorname']]['name']  = '';
                $mrInfo[$row['vendorname']]['icode'] = '';
                $mrInfo[$row['vendorname']]['id']    = '';
            }
        }
        if ($role_type) {
            return $mrInfo[$role_type];
        } else {
            return $mrInfo;
        }
    }

    public function getParticipantTypeFromID($participantRoleID, $allPA)
    {
        $rv = '';
        if ($allPA) {
            if ($this->paReverseInfo) {
                //pull already held information
                $paReverseInfo = $this->paReverseInfo;
            } else {
                //reverse the thing... because sigh.
                foreach ($allPA as $roleType => $roleInfo) {
                    $paReverseInfo[$roleInfo['agents_id']] = $roleType;
                }
                $this->paReverseInfo = $paReverseInfo;
            }
            if ($paReverseInfo) {
                $rv = $paReverseInfo[$participantRoleID];
            }
        }
        return $rv;
    }

    //@TODO: This should be a database for the field normal uitype=16, this is temporary.
    public static function getDetailLineItemApprovalList()
    {
        return ['Client Approved', 'Transferee Approved', 'Chargeback', 'Payment', 'Refund'];
    }

    public function getCustomPackingOverride() {
        $db = PearDatabase::getInstance();
        $sql = "SELECT apply_custom_pack_rate_override FROM vtiger_quotes WHERE quoteid=?";
        $res = $db->pquery($sql, [$this->getID()]);

        if($res) {
            return $res->fetchRow()[0];
        }
        return 0;
    }

    public function allowPackingContainers() {

    }

    public function allowPackingCustomRate() {

    }

    public function allowPackingPackRate() {

    }
    public function getCustomSITOverride() {
        $db = PearDatabase::getInstance();
        $customSit = ['origin' => 0, 'destination' => 0];
        $sql = "SELECT apply_custom_sit_rate_override, apply_custom_sit_rate_override_dest FROM vtiger_quotes WHERE quoteid=?";
        $res = $db->pquery($sql, [$this->getID()]);

        if($res) {
            $row = $res->fetchRow();
            $customSit['origin'] = $row[0];
            $customSit['destination'] = $row[1];
        }
        return $customSit;
    }

    public function getServiceProviders($lineItemID)
    {
        $db = PearDatabase::getInstance();
        $sql = 'SELECT dli_service_providers_id, vendor_id, split_amount, split_miles, split_weight, split_percent FROM `dli_service_providers` WHERE dli_id=?';
        $res = $db->pquery($sql, [$lineItemID]);
        $providers = [];
        while ($row = $res->fetchRow()) {
            $providers[] = [
                'dli_service_providers_id' => htmlspecialchars($row['dli_service_providers_id']),
                'vendor_id' => htmlspecialchars($row['vendor_id']),
                'split_amount' => htmlspecialchars($row['split_amount']),
                'split_miles' => htmlspecialchars($row['split_miles']),
                'split_weight' => htmlspecialchars($row['split_weight']),
                'split_percent' => htmlspecialchars($row['split_percent']),
                'name' => $this->getMoveRolesInfoForDetailLineItems($row['vendor_id'])
            ];
        }
        return $providers;
    }

    public function getDetailLineItems($record, $tablePrefix = '', $parseForReports = false)
    {
        $record = $record ?: $this->getId();
        if (!$record) {
            return;
        }

        $lineItems = [];
        $db = PearDatabase::getInstance();
        $sql = 'SELECT * FROM `'.$tablePrefix.'vtiger_detailed_lineitems` WHERE `dli_relcrmid`=?';
        $result = $db->pquery($sql, [$record]);
        if (method_exists($result, 'fetchRow')) {
            //Sigh... I gave up doing magick one liners...
            $allPA = $this->getParticipatingAgentsForDetailLineItems();
            $firstPA = array_shift($allPA);
            $allMR = $this->getMoveRolesForDetailLineItems();
            $firstMR = array_shift($allMR);
            while ($row = $result->fetchRow()) {
                $dli_participant_role_id = $row['dli_participant_role_id'] ? $row['dli_participant_role_id'] : $firstPA['agents_id'];
                //$dli_service_provider = $row['dli_service_provider'] ? $row['dli_service_provider'] : $firstMR['id'];
                $dli_service_provider = $this->getServiceProviders($row['detaillineitemsid']);
                // OT 3343 - default to blank
//                if(empty($dli_service_provider))
//                {
//                    $dli_service_provider = [[
//                        'vendor_id' => $firstMR['id'],
//                        'name' => $this->getMoveRolesInfoForDetailLineItems($firstMR['id'])
//                                             ]];
//                }

                $tempArray = [
                    'TariffItemCode'           => $row['dli_tariff_item_number'],
                    'TariffItem'               => $row['dli_tariff_item_name'],
                    'Section'                  => $row['dli_tariff_schedule_section'],
                    'TariffSection'            => $row['dli_tariff_schedule_section'],
                    'ServiceDescription'       => $row['dli_description'],
                    'Description'              => $row['dli_description'],
                    'Quantity'                 => $row['dli_quantity'],
                    'UnitOfMeasurement'        => $row['dli_unit_of_measurement'],
                    'Cost'                     => $row['dli_gross'] ? CurrencyField::convertToUserFormat($row['dli_gross'], $current_user) : '',
                    'Gross'                    => $row['dli_gross'] ? CurrencyField::convertToUserFormat($row['dli_gross'], $current_user) : '',
                    'DiscountPct'              => $row['dli_invoice_discount'],
                    'CostNet'                  => $row['dli_invoice_net'] ? CurrencyField::convertToUserFormat($row['dli_invoice_net'], $current_user) : '',
                    'InvoiceDiscountPct'       => $row['dli_invoice_discount'],
                    'InvoiceCostNet'           => $row['dli_invoice_net'] ? CurrencyField::convertToUserFormat($row['dli_invoice_net'], $current_user) : '',
                    'BaseRate'                 => $row['dli_base_rate'] ? CurrencyField::convertToUserFormat($row['dli_base_rate'], $current_user) : '',
                    'UnitRate'                 => $row['dli_unit_rate'] ? CurrencyField::convertToUserFormat($row['dli_unit_rate'], $current_user, false, false, 4) : '',
                    'DistributableDiscountPct' => $row['dli_distribution_discount'],
                    'DistributableCostNet'     => $row['dli_distribution_net'] ? CurrencyField::convertToUserFormat($row['dli_distribution_net'], $current_user) : '',
                    'MovePolicy'               => $row['dli_tariff_move_policy'],
                    'Approval'                 => $row['dli_approval'],
                    //'Role'                     => $row['dli_provider_role'],
                    'Role'                     => $row['dli_participant_role'] ?
                        $row['dli_participant_role'] :
                        $this->getParticipantTypeFromID($dli_participant_role_id, $allPA),
                    'RoleNameID'               => $row['dli_participant_role_id'] ? $row['dli_participant_role_id'] : $firstPA['agents_id'],
                    'RoleName'                 => $this->getParticipatingAgentsInfoForDetailLineItems($dli_participant_role_id),
                        //: $this->getParticipatingAgentsInfoForDetailLineItems(key($this->getParticipatingAgentsForDetailLineItems())),
                    //'ServiceProvider'          => $row['dli_service_provider'],
                    //'ServiceProviderID'        => $row['dli_service_provider'] ? $row['dli_service_provider'] : $firstMR['id'],
                    //'ServiceProviderName'      => $this->getMoveRolesInfoForDetailLineItems($dli_service_provider),
                    'ServiceProviders'         => $dli_service_provider,
                    'Invoiceable'              => $row['dli_invoiceable'],
                    'Distributable'            => $row['dli_distributable'],
                    'Invoiced'                 => $row['dli_invoiced'],
                    'Distributed'              => $row['dli_distributed'],
                    'InvoiceNumber'            => $row['dli_invoice_number'],
                    'InvoicePhase'             => $row['dli_phase'],
                    'InvoiceEvent'             => $row['dli_event'],
                    'InvoiceSequence'          => $row['dli_invoice_sequence'],
                    'DistributionSequence'     => $row['dli_distribution_sequence'],
                    'DetailLineItemId'         => $row['detaillineitemsid'],
                    'ReadyToInvoice'           => $row['dli_ready_to_invoice'],
                    'ReadyToDistribute'        => $row['dli_ready_to_distribute'],
                    'Location'                 => $row['dli_location'],
                    'GCS_Flag'                 => $row['dli_gcs_flag']?:'N',
                    'DatePerformed'            => $row['dli_date_performed'] ? DateTimeField::convertToUserFormat($row['dli_date_performed']) : '',
                    'Metro_Flag'               => $row['dli_metro_flag'],
                    'Item_Weight'              => $row['dli_item_weight'],
                    'Rate_Net'                 => $row['dli_rate_net'] ? CurrencyField::convertToUserFormat($row['dli_rate_net'], $current_user) : '',
                ];
                if(in_array($row['dli_unit_of_measurement'], ['CF', 'CUFT'])) {
                    $tempArray['Cube'] = $row['dli_gross'] / $row['dli_unit_rate'] / $row['dli_quantity'];
                    $tempArray['IntegerCube'] = ceil($tempArray['Cube']);
                }
                if($parseForReports && in_array($row['dli_tariff_schedule_section'], ['Packing', 'Unpacking', 'Crating', 'Uncrating']) || in_array($row['dli_return_section_name'], ['Crating', 'Uncrating'])) {
                    if(strpos($tempArray['ServiceDescription'], ' - ') !== false) {
                        $tempArray['ServiceDescription'] = ltrim(strstr($tempArray['ServiceDescription'], ' - '), ' - ');
                        $tempArray['Description']        = ltrim(strstr($tempArray['Description'], ' - '), ' - ');
                    }
                }
                $sectionName = $row['dli_return_section_name'];
                if (!$sectionName) {
                    //@TODO: Evaluate if this is a good solution, the issue is the section name can't be empty string.
                    $sectionName = ($row['dli_tariff_item_name'] ? $row['dli_tariff_item_name'] : 'NO_SECTION');
                }
                if($row['dli_tariff_schedule_section'] == 'Accessorials' && $row['dli_unit_of_measurement'] == 'EA') {
                    $tempArray['ServiceDescription'] = preg_replace('/\s\d$/', '', $tempArray['ServiceDescription']);
                    $tempArray['Description'] = preg_replace('/\s\d$/', '', $tempArray['Description']);
                }
                //to group by sections:
                $lineItems[$sectionName][] = $tempArray;
            }
        }
        return $lineItems;
    }

    public function getLineItemResultsByType($sectionName, $description, $record, $tablePrefix = '')
    {
        $record = $record ?: $this->getId();
        if (!$record) {
            return;
        }
        $lineItemValues = [];
        $db = PearDatabase::getInstance();
        $sql = "SELECT * FROM `".$tablePrefix."vtiger_detailed_lineitems` WHERE dli_relcrmid =? AND dli_return_section_name =? AND dli_description =? LIMIT 1";
        $result = $db->pquery($sql, [$record, $sectionName, $description]);
        if (method_exists($result, 'fetchRow')) {
            while($row = $result->fetchRow()){
                $tempArray = [
                    'TariffItemCode'           => $row['dli_tariff_item_number'],
                    'TariffItem'               => $row['dli_tariff_item_name'],
                    'Section'                  => $row['dli_tariff_schedule_section'],
                    'TariffSection'            => $row['dli_tariff_schedule_section'],
                    'ServiceDescription'       => $row['dli_description'],
                    'Description'              => $row['dli_description'],
                    'Quantity'                 => $row['dli_quantity'],
                    'UnitOfMeasurement'        => $row['dli_unit_of_measurement'],
                    'Cost'                     => $row['dli_gross'],
                    'Gross'                    => $row['dli_gross'],
                    'DiscountPct'              => $row['dli_invoice_discount'],
                    'CostNet'                  => $row['dli_invoice_net'],
                    'InvoiceDiscountPct'       => $row['dli_invoice_discount'],
                    'InvoiceCostNet'           => $row['dli_invoice_net'],
                    'BaseRate'                 => $row['dli_base_rate'],
                    'UnitRate'                 => $row['dli_unit_rate'],
                    'DistributableDiscountPct' => $row['dli_distribution_discount'],
                    'DistributableCostNet'     => $row['dli_distribution_net'],
                    'MovePolicy'               => $row['dli_tariff_move_policy'],
                    'Approval'                 => $row['dli_approval'],
                    'Invoiceable'              => $row['dli_invoiceable'],
                    'Distributable'            => $row['dli_distributable'],
                    'Invoiced'                 => $row['dli_invoiced'],
                    'Distributed'              => $row['dli_distributed'],
                    'InvoiceNumber'            => $row['dli_invoice_number'],
                    'InvoicePhase'             => $row['dli_phase'],
                    'InvoiceEvent'             => $row['dli_event'],
                    'InvoiceSequence'          => $row['dli_invoice_sequence'],
                    'DistributionSequence'     => $row['dli_distribution_sequence'],
                    'DetailLineItemId'         => $row['detaillineitemsid'],
                    'ReadyToInvoice'           => $row['dli_ready_to_invoice'],
                    'ReadyToDistribute'        => $row['dli_ready_to_distribute'],
                    'Location'                 => $row['dli_location'],
                    'GCS_Flag'                 => $row['dli_gcs_flag']?:'N',
                    'DatePerformed'            => $row['dli_date_performed'],
                    'Metro_Flag'               => $row['dli_metro_flag'],
                    'Item_Weight'              => $row['dli_item_weight'],
                    'Rate_Net'                 => $row['dli_rate_net'],
                ];
                $lineItemValues = $tempArray;
            }
        }
        return $lineItemValues;
    }

    public static function getEffectiveTariff($quoteid, $tablePrefix='')
    {
        $db =& PearDatabase::getInstance();
        if(getenv('INSTANCE_NAME') == 'graebel') {
            $sql =
                "SELECT * FROM `".
                $tablePrefix.
                "vtiger_quotes` LEFT JOIN vtiger_contracts ON(contract=contractsid AND (related_tariff<>0 OR local_tariff<>0))
                LEFT JOIN `vtiger_tariffmanager` ON COALESCE(related_tariff,effective_tariff) = `vtiger_tariffmanager`.tariffmanagerid
                LEFT JOIN `vtiger_tariffs` ON COALESCE(local_tariff,effective_tariff) = `vtiger_tariffs`.tariffsid
                WHERE quoteid = ?";
        } else {
            $sql =
                "SELECT * FROM `".
                $tablePrefix.
                "vtiger_quotes` LEFT JOIN `vtiger_tariffmanager` ON effective_tariff = `vtiger_tariffmanager`.tariffmanagerid
                WHERE quoteid = ?";
        }
        $res = $db->pquery($sql, [$quoteid]);
        if($res)
        {
            $res = $res->fetchRow();
            $res['effective_tariff'] = ($res['tariffmanagerid'] ?: $res['tariffsid']) ?: $res['effective_tariff'];
            return $res;
        }
        return [];
    }

    public function delete()
    {
        if(getenv('INSTANCE_NAME') == 'graebel') {
            // prevent deleting records that have invoiced or distributed line items
            // requested for Actuals, but implemented on Estimates just in case
            $db     =& PearDatabase::getInstance();
            $sql    = 'SELECT * FROM `vtiger_detailed_lineitems` WHERE `dli_relcrmid`=?';
            $result = $db->pquery($sql, [$this->getId()]);
            while ($row = $result->fetchRow())
            {
                if(\MoveCrm\InputUtils::CheckboxToBool($row['dli_invoiced']))
                {
                    return false;
                }
                if(\MoveCrm\InputUtils::CheckboxToBool($row['dli_distributed']))
                {
                    return false;
                }
            }
        }
        return parent::delete();
    }

    public function getPricing($column, $description = false) {
        if (Vtiger_Utils::CheckTable('vtiger_detailed_lineitems')) {
            $fieldName = 'dli_invoice_net';
            if (preg_match('/gross/i', $column)) {
                $fieldName = 'dli_gross';
            }
            $db  = &PearDatabase::getInstance();
            $sql = 'SELECT dli_description, dli_invoice_net, dli_gross FROM `vtiger_detailed_lineitems`'
                   .' WHERE dli_relcrmid=?';
            $params = [$this->getId()];
            if ($description) {
                $sql      .= ' AND dli_description=?';
                $params[] = $description;
            }
            $result      = $db->pquery($sql, $params);
            $returnValue = 0;
            while ($row =& $result->fetchRow()) {
                $returnValue += $row[$fieldName];
            }
        } else {
            //HOPE for the best!
            $returnValue = $this->get('hdnGrandTotal');
        }

        return $returnValue;
    }
}
