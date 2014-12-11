<?php

/**
 * Roster.IsPresent API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_roster_ispresent_spec(&$spec) {
	$spec['name'] = array(
		'title'			=> 'Name of the roster',
		'type'			=> 'string',
	);
	$spec['id'] = array(
		'title'			=> 'Record id',
		'type'			=> 'int',
	);
}

/**
 * Roster.IsPresent API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_roster_ispresent($params) {
	$returnValues = 0;
	if (array_key_exists('id', $params) && !empty($params['id'])) {
		$filter = ' WHERE id = \'' . $params['id'] . '\'';
	} elseif (array_key_exists('name', $params) && !empty($params['name'])) {
		$filter = ' WHERE name = \'' . $params['name'] . '\'';
	} else {
		$filter = '';
	}
	if ($filter == '') {
		throw new API_Exception('Either parameter id or name is required');
	} else {
		$sql = 'SELECT count(name) AS is_present FROM civicrm_pum_roster' . $filter;
		$dao = CRM_Core_DAO::executeQuery($sql);
		if ($dao->N == 1) {
			$dao->fetch();
			if ($dao->is_present > 0) {
				$returnValues = 1;
			}
		}
		return civicrm_api3_create_success($returnValues, $params);
	}
	
}

