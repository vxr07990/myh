<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 3/17/2017
 * Time: 12:39 PM
 */

namespace MoveCrm;

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

use MoveCrm\AccountingIntegration\QBOBridge;
use MoveCrm\AccountingIntegration\SelfBridge;

class AccountingIntegration {

    protected $SUPPORTED_SYSTEMS = ['Self'];

    protected $db;
    protected $aesKey;
    protected $system;
    protected $integrationId;
    protected $bridge;

    public function __construct($id) {
        if(getenv('QUICKBOOKS_ENABLED'))
        {
            $this->SUPPORTED_SYSTEMS[] = 'QBO';
        }
        $this->db = &\PearDatabase::getInstance();
        $this->aesKey = getenv('AES_ENCRYPTION_KEY');
        if($id)
        {
            $this->initializeFromId($id);
        }
    }

    protected function getVanlineFromAgent($id)
    {
        $res = $this->db->pquery('SELECT vanline_id FROM vtiger_agentmanager WHERE agentmanagerid=?',
                                 [$id]);
        if($res && $row = $res->fetchRow())
        {
            return $row['vanline_id'];
        }
        return null;
    }

    protected function getIntegration($agentOrVanlineId)
    {
        $res = $this->db->pquery('SELECT vtiger_accountingintegration.id,remote_system FROM vtiger_accountingintegration
                                    LEFT JOIN vtiger_accountingintegration_agents USING(id)
                                    LEFT JOIN vtiger_accountingintegration_vanlines USING(id)
                                    WHERE agentid=? OR vanlineid=?
                                    ORDER BY agentid,vanlineid
                                    LIMIT 1',
                                 [$agentOrVanlineId, $agentOrVanlineId]);
        if($res && $row = $res->fetchRow())
        {
            $this->integrationId = $row['id'];
            $this->system = $row['remote_system'];
        } else {
            $agentOrVanlineId = $this->getVanlineFromAgent($agentOrVanlineId);
            if($agentOrVanlineId)
            {
                $this->getIntegration($agentOrVanlineId);
            }
        }
    }

    public function isConnected($agentOrVanlineId)
    {
        $res = $this->db->pquery('SELECT 1 FROM vtiger_accountingintegration 
                                    LEFT JOIN vtiger_accountingintegration_agents USING(id)
                                    LEFT JOIN vtiger_accountingintegration_vanlines USING(id)
                                    WHERE agentid=? OR vanlineid=?
                                    LIMIT 1',
                           [$agentOrVanlineId, $agentOrVanlineId]);
        if($this->db->num_rows($res))
        {
            return true;
        }
        $agentOrVanlineId = $this->getVanlineFromAgent($agentOrVanlineId);
        if($agentOrVanlineId)
        {
            return $this->isConnected($agentOrVanlineId);
        }
        return false;
    }


    public function log($notes)
    {
        if(getenv('DB_SERVER') == 'localhost') {
            file_put_contents('logs/devlog2.log', __FILE__.':'.__LINE__.': notes'.PHP_EOL.print_r($notes, true).PHP_EOL, FILE_APPEND);
        }
    }

    public function encrypt($value)
    {
        $iv = openssl_random_pseudo_bytes(16);
        $raw = openssl_encrypt($value, 'aes-256-ctr', $this->aesKey, OPENSSL_RAW_DATA, $iv);
        $base64 = base64_encode($raw.$iv);
        return $base64;
    }

    public function decrypt($value)
    {
        $data = base64_decode($value);
        $iv = substr($data,strlen($data)-16);
        $data = substr($data,0, -16);
        $res = openssl_decrypt($data, 'aes-256-ctr', $this->aesKey, OPENSSL_RAW_DATA, $iv);
        return $res;
    }

    public function initializeFromId($id)
    {
        $this->getIntegration($id);
        if($this->integrationId)
        {
            $system = $this->system;
            if(!in_array($system, $this->SUPPORTED_SYSTEMS))
            {
                $this->log("Remote system $system not supported");
                return;
            }
            switch($system)
            {
                case 'QBO':
                    $this->bridge = new QBOBridge($this);
                    break;
            }
        } else {
            $this->system = 'Self';
            $this->integrationId = null;
            $this->bridge = new SelfBridge($this);
        }
    }

    public function getOAuth()
    {
        $res = $this->db->pquery('SELECT oauth_consumer_key,oauth_consumer_secret,realmid,oauth_token,oauth_token_secret FROM vtiger_accountingintegration
                                  WHERE id=?',
                                 [$this->integrationId]);
        if($res && $row = $res->fetchRow())
        {
            $result = [
                'realmid' => $this->decrypt($row['realmid']),
                'consumer_key' => $this->decrypt($row['oauth_consumer_key']),
                'consumer_secret' => $this->decrypt($row['oauth_consumer_secret']),
                'token' => $this->decrypt($row['oauth_token']),
                'token_secret' => $this->decrypt($row['oauth_token_secret']),
            ];
            return $result;
        }
        return null;
    }

    public function setSearch($entityType, $pagingModel, $searchKey, $searchValue)
    {
        if(!$this->bridge)
        {
            return null;
        }
        $this->bridge->setEntity($entityType);
        if($pagingModel)
        {
            $this->bridge->setStart($pagingModel->getStartIndex());
        }
        if($searchKey && $searchValue)
        {
            $this->bridge->setSearchKey($searchKey, $searchValue);
        } else {
            $this->bridge->setSearchKey('', '');
        }
    }

    public function getOne($entityType, $id)
    {
        if(!$this->bridge)
        {
            return null;
        }
        $this->bridge->setEntity($entityType);
        return $this->bridge->getSingleObject($id);
    }

    public function getResults()
    {
        if(!$this->bridge)
        {
            return null;
        }
        return $this->bridge->getResults();
    }

    public function getTotalCount()
    {
        if(!$this->bridge)
        {
            return null;
        }
        return $this->bridge->getTotalCount();
    }


    public function saveEntity(&$crmentity, $fieldName, $fieldId)
    {
        $entityType = $this->getFieldEntityType($fieldId);
        if(!$entityType)
        {
            $crmentity->column_fields[$fieldName] = null;
            return;
        }
        $id = $crmentity->column_fields[$fieldName];
        if(!$id)
        {
            $crmentity->column_fields[$fieldName] = null;
            return;
        }
        $data = $this->getOne($entityType, $id)['entry'];
        if(!$data)
        {
            $crmentity->column_fields[$fieldName] = null;
            return;
        }

        $res = $this->db->pquery('SELECT id FROM vtiger_accountingintegration_entity WHERE integrationid=? AND remoteid=?',
                          [$this->integrationId, $id]);
        if($res && $row = $res->fetchRow())
        {
            $id = $row['id'];
            $crmentity->column_fields[$fieldName] = $id;
            $this->db->pquery('UPDATE vtiger_accountingintegration_entity SET label=? WHERE id=?',
                              [$data['label'], $id]);
        } else {
            // we should have a unique key on (integrationid,remoteid,type) so that this could fail if it runs concurrently
            // in which case we just try again to get the id inserted by the other process
            $res = $this->db->pquery('INSERT INTO vtiger_accountingintegration_entity (integrationid,remoteid,`type`,label)
                                 VALUES (?,?,?,?)',
                              [$this->integrationId, $id, $entityType, $data['label']]);
            if($res) {
                $id                                   = $this->db->getLastInsertID();
                $crmentity->column_fields[$fieldName] = $id;
            } else {
                $this->saveEntity($crmentity,$fieldName,$fieldId);
            }
        }
    }

    public function retrieveEntity(&$crmentity, $fieldName, $fieldId)
    {
        $entityType = $this->getFieldEntityType($fieldId);
        if(!$entityType)
        {
            return;
        }
        $id = $crmentity->column_fields[$fieldName];
        if(!$id)
        {
            return;
        }
        // In theory we shouldn't need the integrationid check here, but it won't hurt
        $res = $this->db->pquery('SELECT remoteid FROM vtiger_accountingintegration_entity WHERE id=? AND integrationid=?',
                                 [$id, $this->integrationId]);
        if($res && $row = $res->fetchRow())
        {
            $id = $row['remoteid'];
            $crmentity->column_fields[$fieldName] = $id;
        } else {
            $crmentity->column_fields[$fieldName] = null;
        }
    }

    public function getLabel($id)
    {
        $res = $this->db->pquery('SELECT label FROM vtiger_accountingintegration_entity WHERE integrationid=? AND remoteid=?',
                                 [$this->integrationId, $id]);
        if($res && $row=$res->fetchRow())
        {
            return $row['label'];
        }
        return $id;
    }

    protected function getFieldEntityType($fieldId)
    {
        $res = $this->db->pquery('SELECT `type`,subtype FROM vtiger_accountingintegration_fieldrel WHERE fieldid=?',
                                 [$fieldId]);
        if($res && $row = $res->fetchRow())
        {
            return $row['type'];
        }
        return null;
    }

    public function getConfig()
    {
        // we add the IS NULL to get the default config if this agent/vanline doesn't have one
        $res = $this->db->pquery('SELECT id FROM vtiger_accountingintegration_config 
                                    WHERE integrationid=? OR integrationid IS NULL
                                    ORDER BY integrationid DESC LIMIT 1',
                                 [$this->integrationId]);
        if($res && $row=$res->fetchRow())
        {
            $id = $row['id'];
            $res = $this->db->pquery('SELECT entitytype,localname,remotename,datatype,presence,queryable FROM vtiger_accountingintegration_configinfo
                                        WHERE id=?',
                                     [$id]);
            $result = [];
            while($res && $row=$res->fetchRow())
            {
                $data = [
                    'field' => $row['remotename'],
                    'type' => $row['datatype'],
                    'presence' => $row['presence'],
                    'queryable' => $row['queryable'],
                ];
                $result[$row['entitytype']][$row['localname']] = $data;
            }
            return $result;
        }
        return null;
    }

    // Static functions

    public static function setFieldUIType($id, $type, $subtype)
    {
        $db = &\PearDatabase::getInstance();
        $db->pquery('UPDATE vtiger_field SET uitype=? WHERE fieldid=?',
                    ['172', $id]);
        $res = $db->pquery('SELECT tablename,columnname FROM vtiger_field WHERE fieldid=?',
                           [$id]);
        $row = $res->fetchRow();
        $tableName = $row['tablename'];
        $columnName = $row['columnname'];
        $res = $db->pquery('SELECT TABLE_NAME,CONSTRAINT_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME 
                              FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                              WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_COLUMN_NAME IS NOT NULL',
                           [getenv('DB_NAME'), $tableName, $columnName]);
        while($res && $row=$res->fetchRow())
        {
            $db->pquery('ALTER TABLE `'.$tableName.'` DROP FOREIGN KEY `'.$row['CONSTRAINT_NAME'].'`');
        }
        $db->pquery('ALTER TABLE `'.$tableName.'` MODIFY COLUMN `'.$columnName.'` INT(11)');
        $db->pquery('UPDATE `'.$tableName.'` SET `'.$columnName.'`=NULL');
        $db->pquery('ALTER TABLE `'.$tableName.'` ADD FOREIGN KEY (`'.$columnName.'`) REFERENCES vtiger_accountingintegration_entity(id) ON DELETE SET NULL');
        // Ahahahaha!!  How will you put garbage in my table now?
        $db->pquery('INSERT INTO vtiger_accountingintegration_fieldrel (fieldid,`type`,`subtype`) VALUES (?,?,?)',
                    [$id, $type, $subtype]);
    }

}