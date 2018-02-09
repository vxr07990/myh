<?php

namespace Vtiger\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $crmid
 * @property int    $smcreatorid
 * @property int    $smownerid
 * @property int    $modifiedby
 * @property string $setype
 * @property string $description
 * @property string $createdtime
 * @property string $modifiedtime
 * @property string $viewedtime
 * @property string $status
 * @property int    $version
 * @property int    $presence
 * @property int    $deleted
 * @property string $label
 */
class CrmEntity extends Model
{
    /**
     * Does this table utilize an auto-incrementing primary key?
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The primary key for the table.
     *
     * @var int
     */
    protected $primaryKey = 'crmid';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vtiger_crmentity';

    /**
     * Does this table utilize the default Laravel timestamp columns?
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Get the related activity record if it exists.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function activity()
    {
        return $this->hasOne('Vtiger\Models\Activity', 'activityid', 'crmid');
    }
}
