<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 3/20/2017
 * Time: 2:46 PM
 */

namespace MoveCrm\AccountingIntegration;


use MoveCrm\AccountingIntegration;

class QBOBridge implements IAccountingIntegrationBridge
{
    protected $baseURL;
    protected $integration;
    protected $authInfo;
    protected $oauth;

    protected $entityType;
    protected $searchKey;
    protected $searchValue;
    protected $startPosition;
    protected $limit;

    protected $fieldMapping;

    public function __construct(AccountingIntegration $integration) {
        $this->baseURL = getenv('QUICKBOOKS_BASEURL');
        $this->integration = $integration;
        $this->authInfo = $this->integration->getOAuth();
        $this->oauth = new \OAuth($this->authInfo['consumer_key'], $this->authInfo['consumer_secret'],
                            OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
        $this->oauth->enableDebug();
        $this->oauth->setToken($this->authInfo['token'], $this->authInfo['token_secret']);
        $this->fieldMapping = $this->integration->getConfig();
        $this->limit = 20;
    }

    public function setEntity($entityType) {
        $this->entityType = $entityType;
    }

    public function setSearchKey($searchKey, $searchValue) {
        $this->searchKey = $searchKey;
        $this->searchValue = $searchValue;
    }

    public function setStart($startIndex) {
        $this->startPosition = $startIndex + 1;
    }

    protected function getSelectQuery($fields)
    {
        if(!$fields)
        {
            $query = 'SELECT COUNT(*) ';
        } else {
            $query = 'SELECT * ';
        }
        $query = $query . $this->getEntityTypeQuery() . $this->getConditionsQuery();
        // only limit for non-count
        if($fields)
        {
            $query .= $this->getLimitQuery();
        }
        return $query;
    }

    protected function getEntityTypeQuery()
    {
        switch($this->entityType)
        {
            case 'Customer':
                return 'FROM Customer WHERE Job = False and Active = True ';
            case 'Vendor':
                return 'FROM Vendor WHERE Active = True ';
            case 'Department':
                return 'FROM Department WHERE Active = True ';
        }
        throw new \Exception('No entity type mapping found for query');
    }

    protected function getLimitQuery()
    {
        $query = '';
        if($this->startPosition)
        {
            $query.= 'STARTPOSITION '.$this->startPosition . ' ';
        }
        if($this->limit)
        {
            $query.= 'MAXRESULTS '.$this->limit . ' ';
        }
        return $query;
    }

    protected function getConditionsQuery()
    {
        if(!$this->searchValue)
        {
            return;
        }
        $fieldData = $this->fieldMapping[$this->entityType][$this->searchKey];
        $fieldName = $fieldData['field'];
        if(!$fieldName)
        {
            return;
        }
        switch($fieldData['type'])
        {
            case 'int':
                return 'AND '.$fieldName.' = \''.$this->searchValue.'\' ';
            case 'string':
                return 'AND '.$fieldName.' LIKE \'%'.$this->searchValue.'%\' ';
        }
        throw new \Exception('Unhandled data type');
    }

    public function getSingleObject($id) {
        $url = $this->baseURL . '/v3/company/' . $this->authInfo['realmid'] . '/' . strtolower($this->entityType) . '/' . $id;
        try {
            $this->oauth->fetch($url, [], OAUTH_HTTP_METHOD_GET, ['Accept' => 'application/json']);
        } catch (\Exception $e)
        {
            $this->integration->log($this->oauth->getLastResponse());
            throw $e;
        }
        $response = $this->oauth->getLastResponse();
        $data = json_decode($response, true);
        $result = $this->mapData($data[$this->entityType], false);
        return $result;
    }

    public function getResults() {
        $query = $this->getSelectQuery('*');
        $this->integration->log($query);
        $res = $this->executeQuery($query);
        $this->integration->log($res);
        $data = $res['QueryResponse'][$this->entityType];
        $result = $this->mapData($data, true);
        $result['total_count'] = $this->getTotalCount();
        return $result;
    }

    protected function mapData($data, $isArray)
    {
        $entryKey = $isArray ? 'entries' : 'entry';
        $result = [
            'headers' => [],
            $entryKey => [],
        ];
        foreach($this->fieldMapping[$this->entityType] as $dest => $info)
        {
            if($info['presence'] == 2)
            {
                $result['headers'][] = [
                    'title' => $dest,
                    'queryable' => $info['queryable'] ?: 'yes',
                ];
            }
        }
        if($isArray) {
            foreach ($data as $record) {
                $newData = [];
                foreach ($this->fieldMapping[$this->entityType] as $dest => $info) {
                    $newData[$dest] = $this->getMappedData($record, $info['field']);
                }
                $result['entries'][] = $newData;
            }
        } else {
            $newData = [];
            foreach ($this->fieldMapping[$this->entityType] as $dest => $info) {
                $newData[$dest] = $this->getMappedData($data, $info['field']);
            }
            $result['entry'] = $newData;
        }
        return $result;
    }

    protected function getMappedData($record, $field)
    {
        $fieldData = explode('##', $field);
        if(count($fieldData) == 1)
        {
            $fieldData = ['',$fieldData[0]];
        }
        for($i=1;$i<count($fieldData);$i+=2)
        {
            $indexes = explode('.', $fieldData[$i]);
            $result = $record;
            foreach($indexes as $index)
            {
                $result = $result[$index];
            }
            $fieldData[$i] = $result;
        }
        $result = implode('',$fieldData);
        return $result;
    }

    public function getTotalCount() {
        $query = $this->getSelectQuery();
        $res = $this->executeQuery($query);
        return $res['QueryResponse']['totalCount'];
    }

    protected function executeQuery($query)
    {
        $url = $this->baseURL . '/v3/company/' . $this->authInfo['realmid'] . '/query?query=' . rawurlencode($query);
        try {
            $this->oauth->fetch($url, [], OAUTH_HTTP_METHOD_GET, ['Accept' => 'application/json']);
        } catch (\Exception $e)
        {
            $this->integration->log($this->oauth->getLastResponse());
            throw $e;
        }
        $response = $this->oauth->getLastResponse();
        $data = json_decode($response, true);
        return $data;
    }
}

