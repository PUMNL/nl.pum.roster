<?php

/**
 * Roster.Get API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_roster_get_spec(&$spec) {
	$spec['name'] = array(
		'title'			=> 'Name of the roster',
		'type'			=> 'string',
	);
}

/**
 * Roster.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_roster_get($params) {
  $api_params = array(
	'version' => 3,
	'q' => 'civicrm/ajax/rest',
	'sequential' => 1,
  );
  $filter = array();
  // attempt to fetch only a single record by id or name, otherwise return all
  if (array_key_exists('id', $params) && !empty($params['id'])) {
	$filter['column'] = 'id';
	$filter['key'] = $params['id'];
	$filter['sql'] = ' WHERE id = \'' . $params['id'] . '\'';
	$api_params['id'] = $params['id'];
  } elseif (array_key_exists('name', $params) && !empty($params['name'])) {
	$filter['column'] = 'name';
	$filter['key'] = $params['name'];
	$filter['sql'] = ' WHERE name = \'' . $params['name'] . '\'';
	$api_params['name'] = $params['name'];
  } else {
	$filter['column'] = '';
	$filter['sql'] = '';
  }
  if ($filter['column'] != '') {
	// check presence of requested roster id or name
	$api_result = civicrm_api('Roster', 'IsPresent', $api_params);
	if ($api_result['count']==0) {
		// record does not exist
		return civicrm_api3_create_success(NULL, $params);
	}
  }
  $sql = 'SELECT * FROM civicrm_pum_roster' . $filter['sql'] . ' ORDER BY name';
  $dao = CRM_Core_DAO::executeQuery($sql);
  
  $result = array();
  while($dao->fetch()) {
	$rec = array(
		'id' => $dao->id,
		'name' => $dao->name,
		'type' => $dao->type,
		'value' => $dao->value,
		'min_interval' => $dao->min_interval,
		'last_run' => $dao->last_run,
		'next_run' => $dao->next_run,
		'privilege' => $dao->privilege,
	);
	$result[] = $rec;
  }
  return civicrm_api3_create_success($result, $params);
}

