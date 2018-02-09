<?php

require_once ('include/Webservices/Revise.php');


function calculateDaysToSettle($entity) {
    
    $db = PearDatabase::getInstance();
    $user = Users_Record_Model::getCurrentUserModel();
    $days['calendar'] = 0;
    $days['business'] = 0;
    $claimsWSId = $entity->get('id');
    $claimsId = vtws_getIdComponents($claimsWSId)[1];
    
    $sql = 'SELECT status,effective_date FROM vtiger_claims_status_change WHERE claimsid = ? ORDER BY effective_date ASC';
    $result = $db->pquery($sql, [$claimsId]);
    if ($result && $db->num_rows($result) > 1) {
        $foundStatusActive = false;
        while ($row = $result->fetchRow()) {
            if ($row['status'] == 'Active' && !$foundStatusActive) {
                $foundStatusActive = true;
                $start = $row['effective_date'];
            }
            if ($row['status'] != 'Active' && $foundStatusActive) {
                $foundStatusActive = false;
                $end = $row['effective_date'];
                // now i have the two consecutive dates so
                // i calculate calendar days
                $date1 = new DateTime($start);
                $date2 = new DateTime($end);
                $interval = $date1->diff($date2);
                $calendarDays = $interval->days + 1;
                $days['calendar'] += $calendarDays;
                // and i calculate business days
                $businessDays = getWorkingDays($start, $end, getNationalAmericanHolidays($date1->format('Y')));
                $days['business'] += $businessDays;
            }
        }
    }
    
    $claimArray = [
        'id' => $entity->get('id'),
        'claims_calendar_days_settle' => $days['calendar'],
        'claims_business_days_settle' => $days['business']
    ];
    
    try {
        $claim = vtws_revise($claimArray, $user);
    } catch (WebServiceException $ex) {
	MoveCrm\LogUtils::LogToFile('LOG_CRM_FAILS', "Failed to update Claim - VTWS ERROR = ". print_r($exc, true), true);
    }
}

/**
 * National American Holidays
 * @param string $year
 * @return array
 */
function getNationalAmericanHolidays($year) {


	//  January 1 - New Year’s Day (Observed)
	//  Calc Last Monday in May - Memorial Day  strtotime("last Monday of May 2011");
	//  July 4 Independence Day
	//  First monday in september - Labor Day strtotime("first Monday of September 2011")
	//  November 11 - Veterans’ Day (Observed)
	//  Fourth Thursday in November Thanksgiving strtotime("fourth Thursday of November 2011");
	//  December 25 - Christmas Day
	$bankHolidays = array(
		$year . "-01-01" // New Years
		, "" . date("Y-m-d", strtotime("last Monday of May " . $year)) // Memorial Day
		, $year . "-07-04" // Independence Day (corrected)
		, "" . date("Y-m-d", strtotime("first Monday of September " . $year)) // Labor Day
		, $year . "-11-11" // Veterans Day
		, "" . date("Y-m-d", strtotime("fourth Thursday of November " . $year)) // Thanksgiving
		, $year . "-12-25" // XMAS
	);

	return $bankHolidays;
}

//The function returns the no. of business days between two dates and it skips the holidays
function getWorkingDays($start, $end, $holidays) {
    // do strtotime calculations just once
    $endDate = strtotime($end);
    $startDate = strtotime($start);


    //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
    //We add one to inlude both dates in the interval.
    $days = ($endDate - $startDate) / 86400 + 1;

    $no_full_weeks = floor($days / 7);
    $no_remaining_days = fmod($days, 7);

    //It will return 1 if it's Monday,.. ,7 for Sunday
    $the_first_day_of_week = date("N", $startDate);
    $the_last_day_of_week = date("N", $endDate);

    //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
    //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
    if ($the_first_day_of_week <= $the_last_day_of_week) {
        if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) {
                $no_remaining_days--;
        }
        if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) {
                $no_remaining_days--;
        }
    } else {
        // (edit by Tokes to fix an edge case where the start day was a Sunday
        // and the end day was NOT a Saturday)
        // the day of the week for start is later than the day of the week for end
        if ($the_first_day_of_week == 7) {
            // if the start date is a Sunday, then we definitely subtract 1 day
            $no_remaining_days--;

            if ($the_last_day_of_week == 6) {
                    // if the end date is a Saturday, then we subtract another day
                    $no_remaining_days--;
            }
        } else {
            // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
            // so we skip an entire weekend and subtract 2 days
            $no_remaining_days -= 2;
        }
    }

    //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
    //---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
    $workingDays = $no_full_weeks * 5;
    if ($no_remaining_days > 0) {
        $workingDays += $no_remaining_days;
    }

    //We subtract the holidays
    foreach ($holidays as $holiday) {
        $time_stamp = strtotime($holiday);
        //If the holiday doesn't fall in weekend
        if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N", $time_stamp) != 6 && date("N", $time_stamp) != 7) {
                $workingDays--;
        }
    }

    return $workingDays;
}