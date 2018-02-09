<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 3/20/2017
 * Time: 2:48 PM
 */

namespace MoveCrm\AccountingIntegration;


use MoveCrm\AccountingIntegration;

interface IAccountingIntegrationBridge {
    public function __construct(AccountingIntegration $integration);

    public function setEntity($entityType);
    public function setSearchKey($searchKey, $searchValue);
    public function setStart($startIndex);

    public function getSingleObject($id);
    public function getResults();
    public function getTotalCount();
}

