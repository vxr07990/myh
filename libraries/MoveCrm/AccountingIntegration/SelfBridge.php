<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 3/21/2017
 * Time: 9:26 AM
 */

namespace MoveCrm\AccountingIntegration;

// This class will just do internal lookups for agents without external integration
use MoveCrm\AccountingIntegration;

class SelfBridge implements IAccountingIntegrationBridge
{
    protected $integration;

    public function __construct(AccountingIntegration $integration) {
        $this->integration = $integration;
    }

    public function setEntity($entityType) {
        // TODO: Implement setEntity() method.
    }

    public function setSearchKey($searchKey, $searchValue) {
        // TODO: Implement setSearchKey() method.
    }

    public function setStart($startIndex) {
        // TODO: Implement setStart() method.
    }

    public function getSingleObject($id) {
        return [
            'headers' => ['Name'],
            'entry' => [],
        ];
        // TODO: Implement getSingleObject() method.
    }

    public function getResults() {
        // TODO: Implement getResults() method.
        return [
            'headers' => ['Name'],
            'entries' => [],
            'total_count' => 0,
        ];
    }

    public function getTotalCount() {
        // TODO: Implement getTotalCount() method.
        return 0;
    }
}

