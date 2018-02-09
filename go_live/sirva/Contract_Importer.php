<?php
set_time_limit(0);
require_once('includes/main/WebUI.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');

function contractImport()
{
    echo "Start Contract Import <br>";
    $db = PearDatabase::getInstance();
    if (getenv('INSTANCE_NAME') == 'sirva') {
        //file_put_contents('logs/devLog.log', "\n IT IS SIRVA", FILE_APPEND);
        $filePath = 'go_live/sirva/Contracts.csv';
        //file_put_contents('logs/devLog.log', "\n FILE PATH: ".$filePath, FILE_APPEND);
        $headerMapping = [
            'AccountName' => 'accountname',
            'AgreementID' => 'contract_no',
            'SubAgreement' => 'sub_contract_no',
            'SalesAPN' => 'apn',
            'BillingAPN' => 'billing_apn',
            'Address1' => 'address1',
            'Address2' => 'address2',
            'City'  => 'city',
            'State' => 'state',
            'ZipCode' => 'zip',
            'EffectiveDate' => 'begin_date',
            'Brand' => 'brand',
        ];
        $sql = "SELECT vanlinemanagerid FROM `vtiger_vanlinemanager` WHERE vanline_name = 'North American Van Lines'";
        $result = $db->pquery($sql, array());
        $row = $result->fetchRow();
        $northAmericanId = $row[0];
        $sql = "SELECT vanlinemanagerid FROM `vtiger_vanlinemanager` WHERE vanline_name = 'Allied'";
        $result = $db->pquery($sql, array());
        $row = $result->fetchRow();
        $alliedId = $row[0];
        $alliedAdminId = Users::getActiveAdminId();
        $naAdminId = Users::getActiveAdminId();
        $sql = "SELECT id FROM `vtiger_ws_entity` WHERE name = 'VanlineManager'";
        $result = $db->pquery($sql, array());
        $row = $result->fetchRow();
        $vanlineWsId = $row[0];
        $sql = "SELECT id FROM `vtiger_ws_entity` WHERE name = 'Users'";
        $result = $db->pquery($sql, array());
        $row = $result->fetchRow();
        $UsersWsId = $row[0];
        //file_put_contents('logs/devLog.log', "\n alliedAdminId: ".$alliedAdminId, FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n naAdminId: ".$naAdminId, FILE_APPEND);
    }

    $values = array();

    $rosterList = fopen($filePath, 'r');

    $headers = fgetcsv($rosterList);

    $csv = fgetcsv($rosterList);

    while (!empty($csv)) {
        $values[] = $csv;
        $csv = fgetcsv($rosterList);
    }

    //map headers
    foreach ($headers as $key => $header) {
        $headers[$key] = $headerMapping[$header];
    }

    $user = new Users();
    $current_user_model = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());

    $faild_records = 0;

    foreach ($values as $valuesKey => $value) {
        $brand = $value[10];

        $data = array();
        foreach ($value as $fieldKey => $fieldValue) {
            if ($headers[$fieldKey]) {
                $data[$headers[$fieldKey]] = $fieldValue;
            }
        }

        $vanlineID = $data['brand'] == 'AVL' ? $alliedId : $northAmericanId;

        //Since the table can be locked if someone is saving a record, we will attempt to save each record 10 times before moving on
        //You will notice the "while($error <= 10){" which does this

        //Parent account
        $sql = "SELECT accountid FROM `vtiger_account` JOIN vtiger_crmentity ON accountid = crmid WHERE apn = ? AND deleted = 0";
        $result = $db->pquery($sql, [$data['apn']]);

        $accountID = 0;

        if ($db->num_rows($result) > 0) {
            $accountID = $db->fetch_row($result)[0];
        } else {
            $error = 0;
            while ($error <= 10) {
                if ($error == 10) {
                    $error = 11;
                    $faild_records++;
                    continue;
                }
                try {
                    //I don't want to talk about why I had to do this.
                    $_REQUEST['repeat'] = false;
                    $account = vtws_create('Accounts',
                        [
                            'accountname' => $data['accountname'],
                            'apn' => $data['apn'],
                            'agentid' => $vanlineID,
                            'invoice_format' => 'Bottom Line Invoice',
                            'invoice_pkg_format' => 'COD HHGs standard',
                            'assigned_user_id' => 1,
                        ],
                        $current_user_model);
                    $sql = "SELECT accountid FROM `vtiger_account` JOIN vtiger_crmentity ON accountid = crmid WHERE apn = ? AND deleted = 0";
                    $result = $db->pquery($sql, [$data['apn']]);
                    $accountID = $db->fetch_row($result)[0];
                    $error = 11;
                } catch (WebServiceException $ex) {
                    //echo $ex->getMessage();
                    //echo "<br><br>";
                    $error++;
                }
            }
        }

        if ($accountID == 0) {
            continue;
        }

        if ($data['apn'] == $data['billing_apn']) {
            $billingID = $accountID;
        } else {
            //billing account
            $sql = "SELECT accountid FROM `vtiger_account` JOIN vtiger_crmentity ON accountid = crmid WHERE apn = ? AND deleted = 0";
            $result = $db->pquery($sql, [$data['billing_apn']]);

            $billingID = 0;

            if ($db->num_rows($result) > 0) {
                $billingID = $db->fetch_row($result)[0];
            } else {
                $error = 0;
                while ($error <= 10) {
                    if ($error == 10) {
                        $error = 11;
                        $faild_records++;
                        continue;
                    }
                    try {
                        //I don't want to talk about why I had to do this.
                        $_REQUEST['repeat'] = false;
                        $account = vtws_create('Accounts',
                            [
                                'accountname' => $data['accountname'],
                                'apn' => $data['billing_apn'],
                                'agentid' => $vanlineID,
                                'invoice_format' => 'Bottom Line Invoice',
                                'invoice_pkg_format' => 'COD HHGs standard',
                                'assigned_user_id' => 1,
                            ],
                            $current_user_model);
                        $sql = "SELECT accountid FROM `vtiger_account` JOIN vtiger_crmentity ON accountid = crmid WHERE apn = ? AND deleted = 0";
                        $result = $db->pquery($sql, [$data['billing_apn']]);
                        $billingID = $db->fetch_row($result)[0];
                        $error = 11;
                    } catch (WebServiceException $ex) {
                        //echo $ex->getMessage();
                        //echo "<br><br>";
                        $error++;
                    }
                }
            }
        }

        if ($billingID == 0) {
            continue;
        }

        //Parent contract
        $sql = "SELECT contractsid FROM `vtiger_contracts` JOIN vtiger_crmentity ON contractsid = crmid WHERE contract_no = ? AND account_id = ? AND deleted = 0";
        $result = $db->pquery($sql, [$data['contract_no'], $accountID]);

        $parentID = 0;

        if ($db->num_rows($result) > 0) {
            $parentID = $db->fetch_row($result)[0];
        } else {
            $error = 0;
            while ($error <= 10) {
                if ($error == 10) {
                    $error = 11;
                    $faild_records++;
                    continue;
                }
                try {
                    //I don't want to talk about why I had to do this.
                    $_REQUEST['repeat'] = false;
                    $contract = vtws_create('Contracts',
                        [
                            'contract_no' => $data['contract_no'],
                            'account_id' => vtws_getWebserviceEntityId('Accounts', $accountID),
                            'agentid' => $vanlineID,
                            'begin_date' => date("Y-m-d", strtotime($data['begin_date'])),
                            'assigned_user_id' => 1,
                        ],
                        $current_user_model);
                    $sql = "SELECT contractsid FROM `vtiger_contracts` JOIN vtiger_crmentity ON contractsid = crmid WHERE contract_no = ? AND account_id = ? AND deleted = 0";
                    $result = $db->pquery($sql, [$data['contract_no'], $accountID]);
                    $parentID = $db->fetch_row($result)[0];
                    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_crmentityrel` (crmid, module, relcrmid, relmodule) VALUES ('$accountID', 'Accounts', '$parentID', 'Contracts')");
                    $error = 11;
                } catch (WebServiceException $ex) {
                    //echo $ex->getMessage();
                    //echo "<br><br>";
                    $error++;
                }
            }
        }

        if ($parentID == 0) {
            continue;
        }

        //Sub contract
        $sql = "SELECT contractsid FROM `vtiger_contracts` JOIN vtiger_crmentity ON contractsid = crmid WHERE contract_no = ? AND parent_contract = ? AND deleted = 0";
        $result = $db->pquery($sql, [$data['sub_contract_no'], $parentID]);

        if ($db->num_rows($result) == 0) {
            $error = 0;
            while ($error <= 10) {
                if ($error == 10) {
                    $error = 11;
                    $faild_records++;
                    continue;
                }
                try {
                    //I don't want to talk about why I had to do this.
                    $_REQUEST['repeat'] = false;
                    vtws_create('Contracts',
                        [
                            'contract_no' => $data['sub_contract_no'],
                            'account_id' => vtws_getWebserviceEntityId('Accounts', $accountID),
                            'parent_contract' => vtws_getWebserviceEntityId('Contracts', $parentID),
                            'agentid' => $vanlineID,
                            'begin_date' => date("Y-m-d", strtotime($data['begin_date'])),
                            'billing_address1' => $data['address1'],
                            'billing_address2' => $data['address2'],
                            'billing_city' => $data['city'],
                            'billing_state' => $data['state'],
                            'billing_zip' => $data['zip'],
                            'billing_apn' => vtws_getWebserviceEntityId('Accounts', $billingID),
                            'assigned_user_id' => 1,
                        ],
                        $current_user_model);
                    $sql = "SELECT contractsid FROM `vtiger_contracts` JOIN vtiger_crmentity ON contractsid = crmid WHERE contract_no = ? AND parent_contract = ? AND deleted = 0";
                    $result = $db->pquery($sql, [$data['sub_contract_no'], $parentID]);
                    $currentID = $db->fetch_row($result)[0];
                    Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_crmentityrel` (crmid, module, relcrmid, relmodule) VALUES ('$parentID', 'Contracts', '$currentID', 'Contracts')");
                    $error = 11;
                } catch (WebServiceException $ex) {
                    //echo $ex->getMessage();
                    //echo "<br><br>";
                    $error++;
                }
            }
        }
    }

    fclose($rosterList);

    echo "<br> End Contract Import<br>";
    echo "Failed Records: " . $faild_records;
}
