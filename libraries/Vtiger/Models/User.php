<?php

namespace Vtiger\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string	$user_name
 * @property string	$user_password
 * @property string	$user_hash
 * @property string	$cal_color
 * @property string	$first_name
 * @property string	$last_name
 * @property string	$reports_to_id
 * @property string	$is_admin
 * @property int    $currency_id
 * @property string	$description
 * @property string	$date_entered
 * @property string	$date_modified
 * @property string	$modified_user_id
 * @property string	$title
 * @property string	$department
 * @property string	$phone_home
 * @property string	$phone_mobile
 * @property string	$phone_work
 * @property string	$phone_other
 * @property string	$phone_fax
 * @property string	$email1
 * @property string	$email2
 * @property string	$secondaryemail
 * @property string	$status
 * @property string	$signature
 * @property string	$address_street
 * @property string	$address_city
 * @property string	$address_state
 * @property string	$address_country
 * @property string	$address_postalcode
 * @property string	$user_preferences
 * @property string	$tz
 * @property string	$holidays
 * @property string	$namedays
 * @property string	$workdays
 * @property int    $weekstart
 * @property string	$date_format
 * @property string	$hour_format
 * @property string	$start_hour
 * @property string	$end_hour
 * @property string	$activity_view
 * @property string	$lead_view
 * @property string	$imagename
 * @property int    $deleted
 * @property string	$confirm_password
 * @property string	$internal_mailer
 * @property string	$reminder_interval
 * @property string	$reminder_next_time
 * @property string	$crypt_type
 * @property string	$accesskey
 * @property string	$theme
 * @property string	$language
 * @property string	$time_zone
 * @property string	$currency_grouping_pattern
 * @property string	$currency_decimal_separator
 * @property string	$currency_grouping_separator
 * @property string	$currency_symbol_placement
 * @property string	$phone_crm_extension
 * @property string	$no_of_currency_decimals
 * @property string	$truncate_trailing_zeros
 * @property string	$dayoftheweek
 * @property string	$callduration
 * @property string	$othereventduration
 * @property string	$calendarsharedtype
 * @property string	$default_record_view
 * @property string	$leftpanelhide
 * @property string	$rowheight
 * @property string	$defaulteventstatus
 * @property string	$defaultactivitytype
 * @property int    $hidecompletedevents
 * @property string	$is_owner
 * @property string	$push_notification_token
 * @property string	$dbx_token
 * @property string	$oi_enabled
 * @property string	$dbx_userid
 * @property string	$oi_push_notification_token
 * @property string	$vanline
 * @property string	$custom_reports_pw
 * @property string	$user_server
 * @property string	$user_smtp_username
 * @property string	$user_smtp_password
 * @property string	$user_smtp_fromemail
 * @property string	$user_smtp_authentication
 */
class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vtiger_users';

    /**
     * Does this table utilize the default Laravel timestamp columns?
     *
     * @var boolean
     */
    public $timestamps = false;
}
