<?php

namespace MoveCrm\Models;

use PearDatabase;
use Users_Record_Model;
use Vtiger\User as VtigerUser;

class User
{
    /** @var string */
    public $email;

    /** @var string Ofttimes referred to as "Last Name" */
    public $familyName;

    /** @var string Ofttimes referred to as "First Name" */
    public $givenName;

    /** @var int */
    public $id;

    /** @var bool */
    public $isAdmin;

    /** @var string */
    public $name;

    /** @var string */
    public $state;

    /** @var Users_Record_Model */
    protected $_vtiger;

    /**
     * Initialize the provided user.
     *
     * @param Users_Record_Model $user
     */
    public function __construct($user)
    {
        $this->email      = $user->get('email1');
        $this->familyName = $user->get('last_name');
        $this->givenName  = $user->get('user_name');
        $this->id         = $user->getId();
        $this->isAdmin    = ($user->get('is_admin') == 'on') ? true : false;
        $this->name       = $user->get('user_name');
        $this->state      = $user->get('state');
        $this->_vtiger    = $user;
    }

    /**
     * Factory method for the current user.
     *
     * @return self
     */
    public static function current()
    {
        return new self(Users_Record_Model::getCurrentUserModel());
    }

    /**
     * Save the overarching 'syncState' we get from the synchronization
     * call to the exchange server. This is used to decide whether or not
     * any new changes have happened on the remote end that need to be pulled
     *
     * @param $sync_state // big fat hash
     * @return mixed
     */
    public function persistSyncState($sync_state)
    {
        $db = PearDatabase::getInstance();

        $param      = [$sync_state, $this->id];
        $sql        = "UPDATE calendar_exchange_sync SET state = ? WHERE user_id = ?";
        $result     = $db->pquery($sql, $param);

        return $result;
    }
}
