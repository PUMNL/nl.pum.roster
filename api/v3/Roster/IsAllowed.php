<?php

/**
 * Roster.IsAllowed API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_roster_isallowed_spec(&$spec) {
	$spec['name'] = array(
		'title'			=> 'Name of the roster (required)',
		'type'			=> 'string',
		'api.required'	=> 1,
	);
}

/**
 * Roster.IsAllowed API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_roster_isallowed($params) {
	if (!array_key_exists('name', $params) || empty($params['name'])) {
		throw new API_Exception('name is a mandatory parameter');
	}
	
	$params_get = array(
		'version' => 3,
		'q' => 'civicrm/ajax/rest',
		'sequential' => 1,
		'name' => $params['name'],
	);
	$result = civicrm_api('Roster', 'Get', $params_get);
	
	if ($result['count']<0) {
		throw new API_Exception('no roster named "' . $params['name'] . '" found');
	} elseif ($result['count']>1) {
		// should not occur
		throw new API_Exception('multiple rosters named "' . $params['name'] . '" found');
	}
	
	$values = $result['values'][0];
	if (date('Y-m-d', strtotime($values['next_run'])) == date('Y-m-d')) {
		$result = 1;
	} else {
		$result = 0;
	}
	
	return civicrm_api3_create_success($result, $params);
}

