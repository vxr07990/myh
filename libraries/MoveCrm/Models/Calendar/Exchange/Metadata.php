<?php

namespace MoveCrm\Models\Calendar\Exchange;

use Illuminate\Database\Eloquent\Model;

class Metadata extends Model
{
    /**
     * Does this table utilize an auto-incrementing primary key?
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'calendar_exchange_metadata';

    /**
     * Does this table utilize the default Laravel timestamp columns?
     *
     * @var boolean
     */
    public $timestamps = false;
}
