<?php

namespace Vtiger\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $activityid
 * @property string $subject
 * @property string $semodule
 * @property string $activitytype
 * @property string $date_start
 * @property string $due_date
 * @property string $time_start
 * @property string $time_end
 * @property string $sendnotification
 * @property string $duration_hours
 * @property string $duration_minutes
 * @property string $status
 * @property string $eventstatus
 * @property string $priority
 * @property string $location
 * @property string $notime
 * @property string $visibility
 * @property string $recurringtype
 */
class Activity extends Model
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
    protected $primaryKey = 'activityid';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vtiger_activity';

    /**
     * Does this table utilize the default Laravel timestamp columns?
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Get the activity's CRM entity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function crmEntity()
    {
        return $this->belongsTo('Vtiger\Models\CrmEntity', 'activityid', 'crmid');
    }
}
