<?php

/**
 * Roster.Set API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_roster_set_spec(&$spec) {
	$spec['id'] = array(
		'title'			=> 'Record id - used only when updating',
		'type'			=> 'int',
	);
	$spec['name'] = array(
		'title'			=> 'Name of the roster (required)',
		'type'			=> 'string',
		'api.required'	=> 1,
	);
	$spec['type'] = array(
		'title'			=> 'Roster type (w for week, m for month)',
		'type'			=> 'string',
	);
	$spec['value'] = array(
		'title'			=> 'Days, concateted by \':\' (\'0:1:3\' for Sun, Mon en Wed or \'1:15\' for 1st and 15th in the month',
		'type'			=> 'string',
	);
	$spec['interval'] = array(
		'title'			=> 'Interval (days) before next run is allowed',
		'type'			=> 'int',
	);
	$spec['next_run'] = array(
		'title'			=> 'Next run date (YYYY-MM-DD)',
		'type'			=> 'date',
	);
	$spec['privilege'] = array(
		'title'			=> 'Privilege required to edit for rescheduling',
		'type'			=> 'string',
	);
}

/**
 * Roster.Set API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_roster_set($params) {
  $sql_cols = array();
  $sql_vals = array();
  $sql_keys = array();
  $api_result = NULL;
  if (array_key_exists('name', $params)) {
	$sql_keys['name'] = '\'' . $params['name'] . '\'';
	$api_params = array(
		'version' => 3,
		'q' => 'civicrm/ajax/rest',
		'sequential' => 1,
		'name' => $params['name'],
	);
	$api_result = civicrm_api('Roster', 'IsPresent', $api_params);
	if ($api_result['count']==0) {
		$method = 'insert';
		$sql_cols[] = 'name';
		$sql_vals[] = '\'' . $params['name'] . '\'';
	} else {
		$method = 'update';
	}
  } else {
	throw new API_Exception('name is a mandatory parameter');
  }
  if (($method == 'update') && (array_key_exists('id', $params))) {
	if (!empty($params['id'])) {
		$sql_keys['id'] = $params['id'];
		// generate a warning if name and id do not match
		$verify_params = array(
			'version' => 3,
			'q' => 'civicrm/ajax/rest',
			'sequential' => 1,
			'name' => $params['name'],
		);
		$verify_result = civicrm_api('Roster', 'Get', $verify_params);
		$verify_success = FALSE;
		foreach($verify_result['values'] as $vkey=>$vvalue) {
			if ($verify_result['values'][$vkey]['id']==$params['id']) {
				$verify_success = TRUE;
			}
		}
		if (!$verify_success) {
			throw new API_Exception('name and id do not match');
		}
	}
  }
  if (array_key_exists('type', $params)) {
	if (in_array($params['type'], array('w', 'm'))) { 
		$sql_cols[] = 'type';
		$sql_vals[] = '\'' . $params['type'] . '\'';
	} else {
		throw new API_Exception('Illegal value for type');
	}
  }
  if (array_key_exists('value', $params)) {
	// not validated
	$sql_cols[] = 'value';
	$sql_vals[] = '\'' . $params['value'] . '\'';
  }
  if (array_key_exists('min_interval', $params)) {
    // not validated
	$sql_cols[] = 'min_interval';
	$sql_vals[] = $params['min_interval'];
  }
  if (array_key_exists('next_run', $params)) {
    // not validated
	$sql_cols[] = 'next_run';
	$sql_vals[] = '\'' . $params['next_run'] . '\'';
  }
  if (array_key_exists('last_run', $params)) {
    // not validated
	$sql_cols[] = 'last_run';
	$sql_vals[] = '\'' . $params['last_run'] . '\'';
  }
  if (array_key_exists('privilege', $params)) {
	$sql_cols[] = 'privilege';
	$sql_vals[] = '\'' . $params['privilege'] . '\'';
  }
  
  if ($method=='insert') {
	$sql = 'INSERT INTO civicrm_pum_roster (' . implode(', ', $sql_cols) . ') VALUES (' . implode(', ', $sql_vals) . ')';
  } else {
	if (count($sql_cols)<1) {
		throw new API_Exception('No columns provided to update');
	}
	for($n=0; $n<count($sql_cols); $n++) {
		$sql_vals[$n] = $sql_cols[$n] . '=' . $sql_vals[$n];
	}
	foreach($sql_keys as $key=>$value) {
		$sql_keys[$key] = $key . '=' . $value;
	}
	$sql = 'UPDATE civicrm_pum_roster SET ' . implode(', ', $sql_vals) . ' WHERE ' . implode(' AND ', $sql_keys);
  }
  $dao = CRM_Core_DAO::executeQuery($sql);
  
  if (!property_exists($dao , 'is_error')) {
	$returnValues = array();
	return civicrm_api3_create_success($returnValues, $params);
  } elseif ($dao->is_error == 1) {
	throw new API_Exception($dao->error_message);
  } else {
	$returnValues = array();
	return civicrm_api3_create_success($returnValues, $params);
  }
}

