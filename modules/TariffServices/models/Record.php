<?php
class TariffServices_Record_Model extends Vtiger_Record_Model
{
    protected $rateType = null;
	protected $costs = array();
    public function setParentRecordData(Vtiger_Record_Model $parentRecordModel)
    {
		$userModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$moduleName = $parentRecordModel->getModuleName();

		$data = array();
		$fieldMappingList = array(array('parentField'=>'related_tariff', 'serviceField'=>'related_tariff', 'defaultValue'=>''));

		foreach ($fieldMappingList as $fieldMapping) {
			$parentField = $fieldMapping['parentField'];
			$serviceField = $fieldMapping['serviceField'];
            $fieldModel = Vtiger_Field_Model::getInstance($parentField,  Vtiger_Module_Model::getInstance($moduleName));
			if ($fieldModel->getPermissions()) {
				$data[$serviceField] = $parentRecordModel->get($parentField);
			} else {
				$data[$serviceField] = $fieldMapping['defaultValue'];
			}
		}
		return $this->setData($data);
	}

    public function getDetailViewUrl()
    {
		$module = $this->getModule();
		$parentRecord = $this->getParentRecord();
		$viewURL = 'index.php?module='.$this->getModuleName().'&view='.$module->getDetailViewName().'&record='.$this->getId();
        if ($parentRecord == null) {
			return $viewURL;
		}

		return $viewURL.'&sourceModule=EffectiveDates&sourceRecord='.$parentRecord.'&relationOperation=true&effective_date='.$parentRecord;
	}

    /**
     * Function to get the Duplicate url for the record
     * @return String URL
     */
    public function getDuplicateUrl()
    {
        $module = $this->getModule();
        $effDate = $this->get('effective_date');

        return 'index.php?module='.$this->getModuleName().'&view=Edit&record='.$this->getId().'&isDuplicate=true&sourceModule=EffectiveDates&relationOperation=true&effective_date='.$effDate.'&sourceRecord='.$effDate;
    }

    public function getAppliedServices($services)
    {
		$tariffIds = str_ireplace(' |##| ', ', ', $tariffIds);
		$db = PearDatabase::getInstance();
		if ($tablesuffix == 'valuations') {
		}


		return $viewURL.'&sourceModule=EffectiveDates&sourceRecord='.$parentRecord.'&relationOperation=true&effective_date='.$parentRecord;
	}

    public function getParentRecord()
    {
		$db = PearDatabase::getInstance();
		$sql = "SELECT effective_date FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
		$params[] = $this->getId();

		$result = $db->pquery($sql, $params);
        if (!empty($result)) {
            $row = $result->fetchRow();
        }
        if ($row == null) {
            return null;
        }
		return $row[0];
	}

    public function getEntries($tablesuffix)
    {
		    $recordId = $this->getId();
        if (!$recordId) {
            return $this->defaultEntries($tablesuffix);
        }

        $db = &PearDatabase::getInstance();
    		if ($tablesuffix == 'valuations') {
    			$sql = "SELECT * FROM `vtiger_tariff$tablesuffix` WHERE serviceid=? ORDER BY amount,deductible ASC";
            } else {
    			$sql = "SELECT * FROM `vtiger_tariff$tablesuffix` WHERE serviceid=?";
    		}
        $result = $db->pquery($sql, [$recordId]);
        // Not all Services have an associated table.
        if(!$result) {
            return false;
        }
        $entries = [];

        while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
			       $entries[] = $row;
        }
        return $this->removeInvalidEntries($recordId, $entries, $tablesuffix);
	}

    protected function removeInvalidEntries($recordId, $entries, $tablesuffix) {
        if (!$tablesuffix) {
            return $entries;
        }

        if (!$recordId) {
            return $entries;
        }

        if ($tablesuffix == 'packingitems') {
            return $this->removePackingItemEntries($recordId, $entries);
        } else if ($tablesuffix == 'bulky') {
            return $this->removeBulkyEntries($recordId, $entries);
        }

        return $entries;
    }

    protected function removePackingItemEntries($recordId, $entries) {
        //@TODO: Chances are, this should be done better.
        $defaultPacking = $this->getDefaultPacking();
        foreach ($entries as $key => $entry) {
            if (isset($defaultPacking[$entry['pack_item_id']])) {
                $entries[$key]['name']         = $defaultPacking[$entry['pack_item_id']];
                $entries[$key]['standardItem'] = 1;
                unset($defaultPacking[$entry['pack_item_id']]);
            } else {
                if (getenv('DISALLOW_CUSTOM_LOCAL_PACKING')) {
                    //remove that your dirty custom packing item.
                    unset($entries[$key]);
                }
            }
          }
          if (getenv('ENFORCE_STANDARD_LOCAL_PACKING')) {
              foreach ($defaultPacking as $defaultPackID => $defaultPackingItem) {
                  $entries[] = [
                      'serviceid'      => $recordId,
                      'name'           => $defaultPackingItem,
                      'container_rate' => '0.00',
                      'packing_rate'   => '0.00',
                      'unpacking_rate' => '0.00',
                      'pack_item_id'   => $defaultPackID,
                      'standardItem'   => 1
                  ];
              }
          }
        return $entries;
    }

    protected function removeBulkyEntries($recordId, $entries) {
        //@TODO: Chances are, this should be done better.
        $defaultBulky = $this->getDefaultBulkies();
        foreach ($entries as $key => $entry) {
            if (isset($defaultBulky[$entry['CartonBulkyId']])) {
                $entries[$key]['description']         = $defaultBulky[$entry['CartonBulkyId']]['description'];
                $entries[$key]['standardItem'] = 1;
                $lineItems[$entry['CartonBulkyId']] = $entry['line_item_id'];
                unset($defaultBulky[$entry['CartonBulkyId']]);
            } else {
                if (getenv('DISALLOW_CUSTOM_LOCAL_BULKY_LIST')) {
                    //remove that your dirty custom bulky item.
                    unset($entries[$key]);
                }
            }
          }
          if (getenv('ENFORCE_STANDARD_LOCAL_BULKY_LIST')) {
              foreach ($defaultBulky as $defaultBulkyID => $defaultBulkyItem) {
                  $entries[] = [
                      'serviceid'      => $recordId,
                      'description'    => $defaultBulkyItem['description'],
                      'weight'         => $defaultBulkyItem['weight'],
                      'rate'           => $defaultBulkyItem['rate'],
                      'CartonBulkyId'  => $defaultBulkyID,
                      'line_item_id'   => $lineItems[$defaultBulkyID],
                      'standardItem'   => 1
                  ];
              }
          }
        return $entries;
    }

    protected function defaultEntries($tablesuffix) {
        if ($tablesuffix == 'packingitems') {
            return $this->defaultPackingItemEntries();
        } elseif ($tablesuffix == 'bulky') {
                return $this->defaultBulkyEntries();
        }
        return [];
    }

    protected function defaultPackingItemEntries() {
        $defaultEntries = [];
        if (
            getenv('DEFAULT_STANDARD_LOCAL_PACKING') ||
            getenv('ENFORCE_STANDARD_LOCAL_PACKING')
        ) {
            $defaultPacking = $this->getDefaultPacking();
            foreach ($defaultPacking as $defaultPackID => $defaultPackingItem) {
                $defaultEntries[] = [
                    'name'           => $defaultPackingItem,
                    'container_rate' => '0.00',
                    'packing_rate'   => '0.00',
                    'unpacking_rate' => '0.00',
                    'pack_item_id'   => $defaultPackID,
                    'standardItem'   => 1
                ];
            }
        }
        return $defaultEntries;
    }

    protected function defaultBulkyEntries() {
        $defaultEntries = [];
        if (
            getenv('DEFAULT_STANDARD_LOCAL_BULKY_LIST') ||
            getenv('ENFORCE_STANDARD_LOCAL_BULKY_LIST')
        ) {
            $defaultBulky = $this->getDefaultBulkies();
            foreach ($defaultBulky as $defaultBulkyID => $defaultBulkyItem) {
                //array('description'=>'4x4 Vehicle', 'weight'=>'0', 'rate'=>'90.28'),
                $defaultEntries[] = [
                    'description'    => $defaultBulkyItem['description'],
                    'rate'           => $defaultBulkyItem['rate'],
                    'weight'         => $defaultBulkyItem['weight'],
                    'CartonBulkyId'  => $defaultBulkyID,
                    'standardItem'   => 1
                ];
            }
        }
        return $defaultEntries;
    }

    public function setEntry($tablesuffix,$data)
    {
        $db = PearDatabase::getInstance();
        $sql = "INSERT INTO `vtiger_tariff$tablesuffix` VALUES('" . implode("','",$data) . "');";
        $result = $db->pquery($sql);
        return $result;
    }

    public function wipeEntries($tablesuffix, $idRow, $id = null) {
        $db = PearDatabase::getInstance();
        if(empty($id)) {
            $id = $this->getID();
        }
        $sql = "DELETE FROM vtiger_tariff$tablesuffix WHERE $idRow = ?";
        return $db->pquery($sql, [$id]);
    }
    public function hasCheck($string)
    {
		$recordId = $this->getId();
		$db = PearDatabase::getInstance();
		$sql = "SELECT $string FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
		$params[] = $recordId;
		$result = $db->pquery($sql, $params);
		$return = array();
        while ($row = $result->fetchRow()) {
			$return[] = $row[0];
		}
		return $return[0];
	}

    public function getDefaultCountiesExistingRecord()
    {
		$userModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$recordId = $this->getId();

		$db = PearDatabase::getInstance();
		$sql = "SELECT related_tariff FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
		$params[] = $recordId;

		$result = $db->pquery($sql, $params);
		unset($params);
        if (!empty($result)) {
            $row = $result->fetchRow();
        }
        if ($row == null) {
			return array();
		}

		return $this->getDefaultCounties($row[0]);
	}

    public function getDefaultCounties($tariffId)
    {
		$userModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();

		$db = PearDatabase::getInstance();
		$sql = "SELECT tariff_state FROM `vtiger_tariffs` WHERE tariffsid=?";
		$params[] = $tariffId;

		$result = $db->pquery($sql, $params);
		unset($params);
        if (!empty($result)) {
            $row = $result->fetchRow();
        }
        if ($row == null) {
			return array();
		}

		$countyList = array();
		$sql = "SELECT name FROM `vtiger_counties` JOIN `vtiger_states` ON stateid=id WHERE abbr=?";
		$params[] = $row[0];

		$result = $db->pquery($sql, $params);

        while ($row = $result->fetchRow()) {
			$countyList[] = $row[0];
		}

		return $countyList;
	}

    public function getCountyChargePicklists()
    {
		$db = PearDatabase::getInstance();
		$serviceid = $this->getId();
		$sql = "SELECT name FROM `vtiger_tariffcountycharge` WHERE serviceid=?";
		$result = $db->pquery($sql, array($serviceid));

		$countyList = array();

        while ($row =& $result->fetchRow()) {
			$countyList[] = $row[0];
		}
		return $countyList;
	}
    public function getDeductiblePicklists()
    {
		$db = PearDatabase::getInstance();
		$serviceid = $this->getId();
		$sql = "SELECT deductible FROM `vtiger_tariffchargeperhundred` WHERE serviceid=?";
		$result = $db->pquery($sql, array($serviceid));

		$return = array();

        while ($row =& $result->fetchRow()) {
			$return[] = $row[0];
		}

		return $return;
	}
    public function getDistinct($fieldName, $tablesuffix = 'valuations')
    {
		$db = PearDatabase::getInstance();
		$serviceid = $this->getId();
		$sql = "SELECT DISTINCT($fieldName) FROM `vtiger_tariff$tablesuffix` WHERE serviceid=?";
		$result = $db->pquery($sql, array($serviceid));
		if (empty($result)) {
            return false;
        } else {
            while ($row =& $result->fetchRow()) {
				$return[] = $row[0];
			}
		}
		return $return;
	}

    public function getDefaultBulkies()
    {
        $db = PearDatabase::getInstance();
        $localBulkyTable = 'vtiger_local_bulky_defaults';
        $defaultBulkyList = Estimates_Record_Model::getBulkyLabelsStatic();
        $returnBulkyList = [];
        $params = [];
        $where = '';
        foreach ($defaultBulkyList as $defaultBulkyId => $defaultBulkyDescription) {
            if ($where) {
                $where .= ' OR ';
            }
            $where .= ' `CartonBulkyId` = ?';
            $params[] = $defaultBulkyId;
        }
        if (!$where) {
            return [];
        }

        $stmt = 'SELECT * FROM `'. $localBulkyTable. '` WHERE ' . $where . " AND active = 1";
        $result = $db->pquery($stmt, $params);

        while ($row =& $result->fetchRow()) {
            $return[] = $row[0];
            foreach(['description','weight','rate'] as $key) {
              $returnBulkyList[$row['CartonBulkyId']][$key] = $row[$key];
            }
            $returnBulkyList[$row['CartonBulkyId']]['standardItem'] = 1;
        }

        return $returnBulkyList;
//			array('description'=>'Airplanes, Gliders', 'weight'=>'120', 'rate'=>'0.00'),
//
//			array('description'=>'Boat Trailers', 'weight'=>'1600', 'rate'=>'0.00'),
//
//			array('description'=>'Boats &gt; 14 Ft', 'weight'=>'2500', 'rate'=>'0.00'),
//
//			array('description'=>'Camper Trailers', 'weight'=>'7000', 'rate'=>'0.00'),
//			array('description'=>'Horse Trailers', 'weight'=>'7000', 'rate'=>'0.00'),
//			array('description'=>'Mini Mobile Homes', 'weight'=>'7000', 'rate'=>'0.00'),
//
//			array('description'=>'Bath &gt; 65 Cu Ft', 'weight'=>'700', 'rate'=>'0.00'),
//			array('description'=>'Boats &lt; 14 Ft', 'weight'=>'700', 'rate'=>'0.00'),
//			array('description'=>'Camper (Truckless)', 'weight'=>'700', 'rate'=>'0.00'),
//			array('description'=>'Camper Shell', 'weight'=>'700', 'rate'=>'0.00'),
//			array('description'=>'Canoe &gt; 14 Ft', 'weight'=>'700', 'rate'=>'0.00'),
//			array('description'=>'Dinghy &gt; 14 Ft', 'weight'=>'700', 'rate'=>'0.00'),
//			array('description'=>'Whirlpool Bath &gt; 65 Cu Ft', 'weight'=>'700', 'rate'=>'0.00'),
//			array('description'=>'Hot Tub &gt; 65 Cu Ft', 'weight'=>'700', 'rate'=>'0.00'),
//			array('description'=>'Jacuzzi &gt; 65 Cu Ft', 'weight'=>'700', 'rate'=>'0.00'),
//			array('description'=>'Jet Ski &gt; 14 Ft', 'weight'=>'700', 'rate'=>'0.00'),
//			array('description'=>'Kayak &gt; 14 Ft', 'weight'=>'700', 'rate'=>'0.00'),
//			array('description'=>'Rowboat &gt; 14 Ft', 'weight'=>'700', 'rate'=>'0.00'),
//			array('description'=>'Sculls &gt; 14 Ft', 'weight'=>'700', 'rate'=>'0.00'),
//			array('description'=>'Skiff &gt; 14 Ft', 'weight'=>'700', 'rate'=>'0.00'),
//			array('description'=>'Spa &gt; 65 Cu Ft', 'weight'=>'700', 'rate'=>'0.00'),
//			array('description'=>'Windsurfer &gt; 14 Ft', 'weight'=>'700', 'rate'=>'0.00')
	}

    public function getDefaultPacking()
    {
		return Estimates_Record_Model::getPackingLabelsStatic();

	}

    public function getRateType()
    {
        if (empty($this->rateType)) {
			$db = PearDatabase::getInstance();
			$sql = "SELECT rate_type FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
			$result = $db->pquery($sql, array($this->getId()));

            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row == null) {
                return null;
            }
			$this->rateType = $row[0];
		}

		return $this->rateType;
	}

    public function getCostTotals($estimateid)
    {
        if (empty($this->costs)) {
			$db = PearDatabase::getInstance();
			$sql = "SELECT cost_service_total, cost_container_total, cost_packing_total, cost_unpacking_total, cost_crating_total, cost_uncrating_total  FROM `vtiger_quotes_servicecost` WHERE serviceid=? AND estimateid = ?";
			$result = $db->pquery($sql, array($this->getId(), $estimateid));

            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row == null) {
                return null;
            }
			$this->costs = $row;
		}

		return $this->costs;
	}

    public function getServiceBaseChargeMatrix()
    {
		$db = PearDatabase::getInstance();
		$sql = 'SELECT * FROM `vtiger_tariffservicebasecharge` WHERE serviceid=?';
		$result = $db->pquery($sql, [$this->getId()]);

		$serviceBaseChargeMatrix = [];
		if ($result) {
			while ($row =& $result->fetchRow()) {
				$serviceBaseChargeMatrix[] = [
					'price_from' => $row['price_from'],
					'price_to'   => $row['price_to'],
					'factor'    => $row['factor']
				];
			}
		}

		return $serviceBaseChargeMatrix;
	}

    public function getServiceCharges($estimateid, $tablePrefix = '')
    {
        if (empty($this->serviceCharges)) {
			$db = PearDatabase::getInstance();
			$sql = "SELECT rate  FROM `" . $tablePrefix . "vtiger_quotes_servicecharge` WHERE serviceid=? AND estimateid = ?";
			$result = $db->pquery($sql, array($this->getId(), $estimateid));

            if (!empty($result)) {
                $row = $result->fetchRow();
            } else {
                return 0;
            }
            if ($row == null) {
                return 0;
            }
			$this->serviceCharges = $row;
		}

		return $this->serviceCharges;
	}
    public function getStorageValuation($estimateid)
    {
        if (empty($this->storageValuation)) {
            $db = PearDatabase::getInstance();
            $sql = "SELECT rate  FROM `vtiger_quotes_storage_valution` WHERE serviceid=? AND estimateid = ?";
            $result = $db->pquery($sql, array($this->getId(), $estimateid));

            if (!empty($result)) {
                $row = $result->fetchRow();
            } else {
                return 0;
            }
            if ($row == null) {
                return 0;
            }
            $this->storageValuation = $row;
        }

        return $this->storageValuation;
    }

    public function getStorageValuationMonths($estimateid)
    {
        if (empty($this->storageValuationMonths)) {
        $db = PearDatabase::getInstance();
            $sql = "SELECT months  FROM `vtiger_quotes_storage_valution` WHERE serviceid=? AND estimateid = ?";
            $result = $db->pquery($sql, array($this->getId(), $estimateid));

            if (!empty($result)) {
                $row = $result->fetchRow();
            } else {
                return 0;
            }
            if ($row == null) {
                return 0;
            }
            $this->storageValuationMonths = $row;
        }

        return $this->storageValuationMonths;
    }

    public function getServiceChargesApplies()
    {
        $db = PearDatabase::getInstance();
        if ($this->get('service_base_charge_applies')) {
            $ids = str_ireplace(' |##| ', ', ', $this->get('service_base_charge_applies'));
        $sql = "SELECT service_name FROM vtiger_tariffservices WHERE tariffservicesid IN ($ids)";
        $result = $db->pquery($sql, []);

        $serviceList = [];
        if ($result) {
			while ($row =& $result->fetchRow()) {
				$serviceList[] = $row['service_name'];
			}
		}

        return implode(', ', $serviceList);
    }
        return;
    }

    public function getDefaultTable() {
      $db = PearDatabase::getInstance();
      $rateType  = $this->getRateType();

      if($rateType == 'Flat Rate By Weight') {
        $frbw = [];
        $sql = "SELECT * FROM `vtiger_tariffflatratebyweight` WHERE serviceid = ?";
        $result = $db->pquery($sql, [$this->getId()]);
        while ($row =& $result->fetchRow()) {
           $frbw[] = $row;
         }
         return $frbw;
      }


    }

    public function getRecordDetails($estimateId,$view = 'edit')
    {
		$rateType = $this->getRateType();
		$db = PearDatabase::getInstance();
		$loadedDetails = array();
		$loadedDetails['rateType'] = $rateType;

        if ($rateType == 'Base Plus Trans.') {
			$sql = "SELECT mileage, weight, rate, excess FROM `vtiger_quotes_baseplus` WHERE estimateid=? AND serviceid=?";
			$result = $db->pquery($sql, array($estimateId, $this->getId()));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['mileage'] = $row[0];
				$loadedDetails['weight'] = $row[1];
				$loadedDetails['rate'] = $row[2];
				$loadedDetails['excess'] = $row[3];
			} else {
				$loadedDetails['mileage'] = '';
				$loadedDetails['weight'] = '';
				$loadedDetails['rate'] = '';
				$loadedDetails['excess'] = '';
			}
        } elseif ($rateType == 'Break Point Trans.') {
			$sql = "SELECT mileage, weight, rate, breakpoint FROM `vtiger_quotes_breakpoint` WHERE estimateid=? AND serviceid=?";
			$result = $db->pquery($sql, array($estimateId, $this->getId()));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['mileage'] = $row[0];
				$loadedDetails['weight'] = $row[1];
				$loadedDetails['rate'] = $row[2];
				$loadedDetails['breakpoint'] = $row[3];
			} else {
				$loadedDetails['mileage'] = '';
				$loadedDetails['weight'] = '';
				$loadedDetails['rate'] = '';
				$loadedDetails['breakpoint'] = '';
			}
        } elseif ($rateType == 'Weight/Mileage Trans.') {
			$sql = "SELECT mileage, weight, rate FROM `vtiger_quotes_weightmileage` WHERE estimateid=? AND serviceid=?";
			$result = $db->pquery($sql, array($estimateId, $this->getId()));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['mileage'] = $row[0];
				$loadedDetails['weight'] = $row[1];
				$loadedDetails['rate'] = $row[2];
			} else {
				$loadedDetails['mileage'] = '';
				$loadedDetails['weight'] = '';
				$loadedDetails['rate'] = '';
			}
        } elseif ($rateType == 'Bulky List') {
            $bulkyList = [];
			$sql = "SELECT *
					FROM  `vtiger_quotes_bulky`
					WHERE estimateid =?
					AND serviceid =?";
            $result    = $db->pquery($sql, [$estimateId, $this->getId()]);
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if (!empty($row)) {
              $sql = "SELECT `vtiger_quotes_bulky`.description AS description,
                              `vtiger_quotes_bulky`.qty AS qty,
                              `vtiger_quotes_bulky`.weight AS weight,
                              `vtiger_quotes_bulky`.rate AS rate,
                              `vtiger_quotes_bulky`.bulky_id AS id
                              FROM `vtiger_quotes_bulky`
                              WHERE `vtiger_quotes_bulky`.estimateid=?
                              AND `vtiger_quotes_bulky`.serviceid=?";
                $result  = $db->pquery($sql, [$estimateId, $this->getId()]);
                $bulkIds = [];
                while ($row =& $result->fetchRow()) {
        		          $bulkyList[] = $row;
                    $bulkIds[] = $row['id'];
                 }
                 $sql = "SELECT `vtiger_tariffbulky`.description AS description,
                      0 AS qty,
                      `vtiger_tariffbulky`.weight AS weight,
                      `vtiger_tariffbulky`.rate AS rate,
                      `vtiger_tariffbulky`.CartonBulkyId AS id
                      FROM `vtiger_tariffbulky`
                      WHERE `vtiger_tariffbulky`.serviceid=?";
                 if(!empty($bulkIds)){
                    $sql .= "AND `vtiger_tariffbulky`.CartonBulkyId NOT IN (".implode(',', $bulkIds).")";
                 }
                $result = $db->pquery($sql, [$this->getId()]);
                while ($row =& $result->fetchRow()) {
        					$bulkyList[] = $row;
        				}
            } else {
				$sql = "SELECT description, 0 AS qty, weight, rate, CartonBulkyId AS id FROM `vtiger_tariffbulky` WHERE serviceid=?";
                $result = $db->pquery($sql, [$this->getId()]);
                while ($row =& $result->fetchRow()) {
					$bulkyList[] = $row;
				}
			}
			$loadedDetails['bulkyList'] = $bulkyList;
        } elseif ($rateType == 'Charge Per $100 (Valuation)') {
			$sql = "SELECT qty1, qty2, rate, multiplier, flag FROM `vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=? AND ratetype=?";
            $result = $db->pquery($sql, array($estimateId, $this->getId(), $rateType));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
                $loadedDetails['released'] = $row['flag'];
				if($row['flag'] == 0) {
                    $loadedDetails['amount'] = $row['qty1'];
    				$loadedDetails['deductible'] = $row['qty2'];
    				$loadedDetails['rate'] = $row['rate'];
    				$loadedDetails['multiplier'] = $row['multiplier'];
                }else {
                    $loadedDetails['released_amount'] = $row['rate'];
                }
			} else {
				$loadedDetails['amount'] = '';
				$loadedDetails['deductible'] = '';
				$loadedDetails['rate'] = '';

                $sql = 'SELECT multiplier FROM `vtiger_tariffchargeperhundred` WHERE serviceid=?';
                $result = $db->pquery($sql, [$this->getId()]);
                if($result) {
                    $row = $result->fetchRow();
                    if($row != null) {
                        $loadedDetails['multiplier'] = $row['multiplier'];
                    }else {
                        $loadedDetails['multiplier'] = '';
                    }
                }
			}
			//file_put_contents('logs/loadsave.log', "\n \$packingList : \n". print_r($bulkyList,true));
        } elseif ($rateType == 'County Charge') {
			$sql = "SELECT county, rate FROM `vtiger_quotes_countycharge` WHERE estimateid=? AND serviceid=?";
			$result = $db->pquery($sql, array($estimateId, $this->getId()));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['county'] = $row[0];
				$loadedDetails['rate'] = $row[1];
			} else {
				$loadedDetails['county'] = '';
				$loadedDetails['rate'] = '';
			}
        } elseif ($rateType == 'Crating Item') {
			$sql = "SELECT crateid, description, crating_qty, crating_rate, uncrating_qty, uncrating_rate, length, width, height, inches_added, line_item_id, cost_crating, cost_uncrating FROM `vtiger_quotes_crating` WHERE estimateid=? AND serviceid=?";
			$result = $db->pquery($sql, array($estimateId, $this->getId()));
            while ($row =& $result->fetchRow()) {
				$cratingList[] = $row;
			}
			$loadedDetails['cratingList'] = $cratingList;

			$sql = "SELECT line_item_id FROM `vtiger_quotes_crating` WHERE estimateid=? AND serviceid=? ORDER BY line_item_id DESC LIMIT 1";
			$result = $db->pquery($sql, array($estimateId, $this->getId()));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }

			$loadedDetails['highestCrate'] = $row[0];

			//file_put_contents('logs/loadsave.log', "\n highestCrate : " . print_r($loadedDetails['highestCrate'], true), FILE_APPEND);
			//file_put_contents('logs/loadsave.log', "\n cratingList : " . print_r($cratingList, true), FILE_APPEND);
        } elseif ($rateType == 'Flat Charge') {
			$sql = "SELECT rate, rate_included FROM `vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=? AND ratetype=?";
            $result = $db->pquery($sql, array($estimateId, $this->getId(), $rateType));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['rate'] = $row['rate'];
				$loadedDetails['rate_included'] = $row['rate_included'];
			} else {
				$loadedDetails['rate'] = '';
				$loadedDetails['rate_included'] = 0;
			}
        } elseif ($rateType == 'Hourly Set') {
			$sql = "SELECT men, vans, hours, traveltime, rate FROM `vtiger_quotes_hourlyset` WHERE estimateid=? AND serviceid=?";
			$result = $db->pquery($sql, array($estimateId, $this->getId()));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['men'] = $row[0];
				$loadedDetails['vans'] = $row[1];
				$loadedDetails['hours'] = $row[2];
				$loadedDetails['traveltime'] = $row[3];
				$loadedDetails['rate'] = $row[4];
			} else {
				$loadedDetails['men'] = '';
				$loadedDetails['vans'] = '';
				$loadedDetails['hours'] = '';
				$loadedDetails['traveltime'] = '';
				$loadedDetails['rate'] = '';
			}
        } elseif ($rateType == 'SIT Item') {
			$sql = "SELECT cartage_cwt_rate, first_day_rate, additional_day_rate FROM `vtiger_quotes_sit` WHERE estimateid=? AND serviceid=?";
			$result = $db->pquery($sql, array($estimateId, $this->getId()));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['cartage_cwt_rate']      = $row[0];
				$loadedDetails['first_day_rate']        = $row[1];
				$loadedDetails['additional_day_rate']   = $row[2];
			} else {
				$loadedDetails['cartage_cwt_rate']      = '';
				$loadedDetails['first_day_rate']        = '';
				$loadedDetails['additional_day_rate']   = '';
			}
        } elseif ($rateType == 'Hourly Simple') {
			$id = $this->getId();
			//file_put_contents('logs/hideThingsForGlobalEdit.log', "\n \$estimateId: $estimateId \n \$this->getId(): $id \n \$rateType : $rateType \n", FILE_APPEND);
			$sql = "SELECT qty1, qty2, rate FROM `vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=? AND ratetype=?";
            $result = $db->pquery($sql, array($estimateId, $this->getId(), $rateType));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['quantity'] = $row[0];
				$loadedDetails['hours'] = $row[1];
				$loadedDetails['rate'] = $row[2];
			} else {
				$loadedDetails['quantity'] = '';
				$loadedDetails['hours'] = '';
				$loadedDetails['rate'] = '';
			}
        } elseif ($rateType == 'Packing Items') {
			$sql = "SELECT `vtiger_quotes_packing`.name AS name,
                     `vtiger_quotes_packing`.container_qty AS container_qty,
                     `vtiger_quotes_packing`.container_rate AS container_rate,
                     `vtiger_quotes_packing`.pack_qty AS pack_qty,
                     `vtiger_quotes_packing`.pack_rate AS pack_rate,
                     `vtiger_quotes_packing`.unpack_qty AS unpack_qty,
                     `vtiger_quotes_packing`.unpack_rate AS unpack_rate,
                     `vtiger_quotes_packing`.packing_id AS packing_id,
                     `vtiger_quotes_packing`.cost_container AS cost_container,
                     `vtiger_quotes_packing`.cost_packing AS cost_packing,
                     `vtiger_quotes_packing`.cost_unpacking AS cost_unpacking,
                     `vtiger_quotes_packing`.sales_tax,
                     `vtiger_tariffpackingitems`.container_rate AS container_rate,
                     `vtiger_tariffpackingitems`.packing_rate AS packing_rate,
                     `vtiger_tariffpackingitems`.unpacking_rate AS unpacking_rate,
                     `vtiger_tariffpackingitems`.pack_item_id AS CartonBulkyId,
                     `vtiger_tariffpackingitems`.line_item_id AS notCurrentlyUseful
                  	 FROM `vtiger_tariffpackingitems`
                     JOIN `vtiger_quotes_packing`
            ON  `vtiger_tariffpackingitems`.pack_item_id = `vtiger_quotes_packing`.packing_id
                		 WHERE `vtiger_tariffpackingitems`.serviceid=?
                     AND `vtiger_quotes_packing`.serviceid=?
                     AND `vtiger_quotes_packing`.estimateid=?";
            //@TODO HERE variation on this select for future consideration / check
//ON (`vtiger_tariffpackingitems`.pack_item_id = `vtiger_quotes_packing`.packing_id AND `vtiger_tariffpackingitems`.serviceid=`vtiger_quotes_packing`.serviceid)
//WHERE `vtiger_quotes_packing`.estimateid=5425
//AND `vtiger_quotes_packing`.serviceid=4405;
			//@TODO: PROBABLYT HERE
            //ON  `vtiger_tariffpackingitems`.line_item_id = `vtiger_quotes_packing`.packing_id
            //ON  `vtiger_tariffpackingitems`.pack_item_id = `vtiger_quotes_packing`.packing_id
            $result  = $db->pquery($sql, [$this->getId(), $this->getId(), $estimateId]);
            $packIds = [];
        $salesTax = $this->get('packing_salestax');
        while ($row =& $result->fetchRow()) {
              $packingList[] = $row;
              $packIds[] = $row['packing_id'];
              $salesTax = $row['sales_tax'] ?: $salesTax;
         }
         $sql = "SELECT `vtiger_tariffpackingitems`.name AS name,
              0 AS container_qty,
              `vtiger_tariffpackingitems`.container_rate AS container_rate,
              0 AS pack_qty,
              `vtiger_tariffpackingitems`.packing_rate AS packing_rate,
              0 AS unpack_qty,
              `vtiger_tariffpackingitems`.unpacking_rate AS unpacking_rate,
             `vtiger_tariffpackingitems`.pack_item_id AS CartonBulkyId,
             `vtiger_tariffpackingitems`.line_item_id AS packingId,
              0 AS cost_container,
              0 AS cost_packing,
              0 AS cost_unpacking,
              `vtiger_tariffpackingitems`.standardItem
              FROM `vtiger_tariffpackingitems`
              WHERE `vtiger_tariffpackingitems`.serviceid=?";
         if(!empty($packIds)){
             //@TODO: AND HERE
                $sql .= "AND `vtiger_tariffpackingitems`.pack_item_id NOT IN (" . implode(',',$packIds) . ")";
                //$sql .= "AND `vtiger_tariffpackingitems`.line_item_id NOT IN (".implode(',', $packIds).")";
         }
            $result = $db->pquery($sql, [$this->getId()]);
            //$defaultPacking = $this->getDefaultPacking();
         while ($row =& $result->fetchRow()) {
                if (
                    !$row['standardItem'] &&
                    getenv('DISALLOW_CUSTOM_LOCAL_PACKING')
                ) {
                    //This should only skip custom packing that isn't already set on an estimate.
                    continue;
                }
 		          $packingList[] = $row;
          }
            $loadedDetails['sales_tax']   = $salesTax;
			$loadedDetails['packingList'] = $packingList;
        } elseif ($rateType == 'Per Cu Ft') {
			$sql = "SELECT qty1,  rate FROM `vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=? AND ratetype=?";
            $result = $db->pquery($sql, array($estimateId, $this->getId(), $rateType));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['cubicfeet'] = $row[0];
				$loadedDetails['rate'] = $row[1];
			} else {
				$loadedDetails['cubicfeet'] = '';
				$loadedDetails['rate'] = '';
			}
        } elseif ($rateType == 'Per Cu Ft/Per Day') {
			$sql = "SELECT qty1, qty2, rate FROM `vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=? AND ratetype=?";
            $result = $db->pquery($sql, array($estimateId, $this->getId(), $rateType));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['cubicfeet'] = $row[0];
				$loadedDetails['days'] = $row[1];
				$loadedDetails['rate'] = $row[2];
			} else {
				$loadedDetails['cubicfeet'] = '';
				$loadedDetails['days'] = '';
				$loadedDetails['rate'] = '';
			}
        } elseif ($rateType == 'Per Cu Ft/Per Month') {
			$sql = "SELECT qty1, qty2, rate FROM `vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=? AND ratetype=?";
            $result = $db->pquery($sql, array($estimateId, $this->getId(), $rateType));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['cubicfeet'] = $row[0];
				$loadedDetails['months'] = $row[1];
				$loadedDetails['rate'] = $row[2];
			} else {
				$loadedDetails['cubicfeet'] = '';
				$loadedDetails['months'] = '';
				$loadedDetails['rate'] = '';
			}
        } elseif ($rateType == 'Per CWT') {
			$sql = "SELECT qty1, rate FROM `vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=? AND ratetype=?";
            $result = $db->pquery($sql, array($estimateId, $this->getId(), $rateType));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['weight'] = $row[0];
				$loadedDetails['rate'] = $row[1];
			} else {
				$loadedDetails['weight'] = '';
				$loadedDetails['rate'] = '';
			}
        } elseif ($rateType == 'Per CWT/Per Day') {
			$sql = "SELECT qty1, qty2, rate FROM `vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=? AND ratetype=?";
            $result = $db->pquery($sql, array($estimateId, $this->getId(), $rateType));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['weight'] = $row[0];
				$loadedDetails['days'] = $row[1];
				$loadedDetails['rate'] = $row[2];
			} else {
				$loadedDetails['weight'] = '';
				$loadedDetails['days'] = '';
				$loadedDetails['rate'] = '';
			}
        } elseif ($rateType == 'Per CWT/Per Month') {
			$sql = "SELECT qty1, qty2, rate FROM `vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=? AND ratetype=?";
            $result = $db->pquery($sql, array($estimateId, $this->getId(), $rateType));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['weight'] = $row[0];
				$loadedDetails['months'] = $row[1];
				$loadedDetails['rate'] = $row[2];
			} else {
				$loadedDetails['weight'] = '';
				$loadedDetails['months'] = '';
				$loadedDetails['rate'] = '';
			}
        } elseif ($rateType == 'Per Quantity') {
			$sql = "SELECT qty1, rate FROM `vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=? AND ratetype=?";
            $result = $db->pquery($sql, array($estimateId, $this->getId(), $rateType));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['quantity'] = $row[0];
				$loadedDetails['rate'] = $row[1];
			} else {
				$loadedDetails['quantity'] = '';
				$loadedDetails['rate'] = '';
			}
        } elseif ($rateType == 'Per Quantity/Per Day') {
			$sql = "SELECT qty1, qty2, rate FROM `vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=? AND ratetype=?";
            $result = $db->pquery($sql, array($estimateId, $this->getId(), $rateType));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['quantity'] = $row[0];
				$loadedDetails['days'] = $row[1];
				$loadedDetails['rate'] = $row[2];
			} else {
				$loadedDetails['quantity'] = '';
				$loadedDetails['days'] = '';
				$loadedDetails['rate'] = '';
			}
        } elseif ($rateType == 'Per Quantity/Per Month') {
			$sql = "SELECT qty1, qty2, rate FROM `vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=? AND ratetype=?";
            $result = $db->pquery($sql, array($estimateId, $this->getId(), $rateType));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['quantity'] = $row[0];
				$loadedDetails['months'] = $row[1];
				$loadedDetails['rate'] = $row[2];
			} else {
				$loadedDetails['quantity'] = '';
				$loadedDetails['months'] = '';
				$loadedDetails['rate'] = '';
			}
        } elseif ($rateType == 'Tabled Valuation') {
			$sql = "SELECT released, released_amount, amount, deductible, rate, multiplier FROM `vtiger_quotes_valuation` WHERE estimateid=? AND serviceid=?";
			$result = $db->pquery($sql, array($estimateId, $this->getId()));
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['released'] = $row['released'];
				$loadedDetails['released_amount'] = $row['released_amount'];
				$loadedDetails['amount'] = $row['amount'];
				$loadedDetails['deductible'] = $row['deductible'];
				$loadedDetails['rate'] = $row['rate'];
                $loadedDetails['multiplier'] = $row['multiplier'];
			} else {
				$loadedDetails['released'] = '';
				$loadedDetails['released_amount'] = '';
				$loadedDetails['amount'] = '';
				$loadedDetails['deductible'] = '';
				$loadedDetails['rate'] = '';

                $sql = 'SELECT multiplier FROM `vtiger_tariffvaluations` WHERE serviceid=?';
                $result = $db->pquery($sql, [$this->getId()]);
                if($result) {
                    $row = $result->fetchRow();
                    if($row != null) {
                        $loadedDetails['multiplier'] = $row['multiplier'];
                    }else {
                        $loadedDetails['multiplier'] = '';
                    }
                }
			}
        } elseif ($rateType == 'CWT by Weight' || $rateType == 'SIT Cartage') {
			$sql = "SELECT weight, rate FROM `vtiger_quotes_cwtbyweight` WHERE estimateid=? AND serviceid=?";
			$result = $db->pquery($sql, [$estimateId, $this->getId()]);
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
				$loadedDetails['weight'] = $row['weight'];
				$loadedDetails['rate'] = $row['rate'];
            } else {
				$loadedDetails['weight'] = '';
				$loadedDetails['rate'] = '';
            }
        } elseif ($rateType == 'SIT Additional Day Rate' || $rateType == 'SIT First Day Rate') {
            $sql = "SELECT qty1, qty2, rate FROM `vtiger_quotes_perunit` WHERE estimateid=? AND serviceid=?";
            $result = $db->pquery($sql, [$estimateId, $this->getId()]);
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
                $loadedDetails['weight'] = $row['qty1'];
                $loadedDetails['days'] = $row['qty2'];
                $loadedDetails['rate'] = $row['rate'];
            } else {
                $loadedDetails['weight'] = '';
                $loadedDetails['days'] = '';
            }
        } elseif ($rateType == 'CWT Per Quantity') {
            $sql = "SELECT quantity, rate, weight FROM `vtiger_quotes_cwtperqty` WHERE estimateid=? AND serviceid=?";
            $result = $db->pquery($sql, [$estimateId, $this->getId()]);
            if (!empty($result)) {
                $row = $result->fetchRow();
            }
            if ($row != null) {
                $loadedDetails['quantity'] = $row['quantity'];
                $loadedDetails['rate'] = $row['rate'];
                $loadedDetails['weight'] = $row['weight'];
            } else {
                $loadedDetails['quantity'] = '';

                // Grab default rate.
                $sql = "SELECT cwtperqty_rate FROM `vtiger_tariffservices` WHERE tariffservicesid=?";
                $result = $db->pquery($sql, [$this->getId()]);
                if (!empty($result)) {
                    $row = $result->fetchRow();
                }
                if ($row != null) {
                    $loadedDetails['rate'] = $row['cwtperqty_rate'];
                }else{
                    $loadedDetails['rate'] = '';
                }
            }
        } elseif ($rateType == 'Flat Rate By Weight') {
            $frbw = [];
            $sql = "SELECT * FROM `vtiger_quotes_flatratebyweight` WHERE serviceid = ? AND estimateid = ?";
            $result = $db->pquery($sql, [$this->getId(),$estimateId]);
            if($db->num_rows($result) > 0) {
              while ($row =& $result->fetchRow()) {
                 $frbw[] = $row;
               }
            } else {
              $sql = "SELECT * FROM `vtiger_tariffflatratebyweight` WHERE serviceid = ?";
              $result = $db->pquery($sql, [$this->getId()]);
              while ($row =& $result->fetchRow()) {
                 $frbw[] = $row;
               }
            }

          $loadedDetails['frbw'] = $frbw;
        }
        return $loadedDetails;
    }
}
