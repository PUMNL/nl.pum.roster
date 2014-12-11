<?php

/**
 * Roster.ScheduleNext API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_roster_schedulenext_spec(&$spec) {
  	$spec['name'] = array(
		'title'			=> 'Name of the roster (required)',
		'type'			=> 'string',
		'api.required'	=> 1,
	);
}

/**
 * Roster.ScheduleNext API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_roster_schedulenext($params) {
	if (!array_key_exists('name', $params) || empty($params['name'])) {
		throw new API_Exception('name is a mandatory parameter');
	}
	
	// see if job is/was allowed to run -> if not, do not reschedule!
	$allowed_params = array(
		'version' => 3,
		'q' => 'civicrm/ajax/rest',
		'sequential' => 1,
		'name' => $params['name'],
	);
	$allowed_result = civicrm_api('Roster', 'IsAllowed', $allowed_params);
	
	if (!$allowed_result['values']==1) {
		// reschedule is not allowed
		throw new API_Exception('not allowed to run or invalid name parameter');
	} else {
		// reschedule is allowed
		// retrieve current roster values
		$get_params = array(
			'version' => 3,
			'q' => 'civicrm/ajax/rest',
			'sequential' => 1,
			'name' => $params['name'],
		);
		$get_result = civicrm_api('Roster', 'Get', $get_params);
		
		// calculate next run date
		// 1st based on current next_run date and minimum interval
		$values = $get_result['values'][0];
		$dt = date('Y-m-d', strtotime($values['next_run'] . '+' . $values['min_interval'] . ' days'));
		// 2nd: if today or still in the past, use tomorrow
		if ($dt <= date('Y-m-d')) {
			$dt = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
		}
		// check if the day/date matches the week- or month based roster
		$days = explode(',', $values['value']);
		$match = FALSE;
		$emercency_break = 366; // days ahead - if all days in a year have been tried, there is no solution;
		while (!$match) {
			switch ($values['type']) {
			case 'w':
				if (in_array(date('w', strtotime($dt)), $days)) {
					$match = TRUE;
				}
				break;
			case 'm':
				if (in_array(date('d', strtotime($dt)), $days)) {
					$match = TRUE;
				}
				break;
			default:
				// should not occur unless other types got introduced
				throw new API_Exception('unknown roster type: cannot calculate next run date');
			}
			if (!$match) {
				// advance 1 day
				$dt = date('Y-m-d', strtotime($dt . '+1 day'));
				$emercency_break--;
				if ($emercency_break<0) {
					throw new API_Exception('emergency exit to avoid an eternal loop');
				}
			}
		}
	}
	
	$set_params = array(
		'version' => 3,
		'q' => 'civicrm/ajax/rest',
		'sequential' => 1,
		'name' => $params['name'],
		'id' => $values['id'],
		'last_run' => date('Y-m-d'),
		'next_run' => $dt,
	);
	$result = civicrm_api('Roster', 'set', $set_params);
	
	$returnValues = 0;
	if (empty($result['is_error'])) {
		$returnValues = 1;
		return civicrm_api3_create_success($returnValues, $params);
	} elseif (!empty($result['error_message'])) {
		throw new API_Exception($result['error_message']);
	} else {
		throw new API_Exception('Update to next scheduled run date failed');
	}
}

