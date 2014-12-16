<?php

require_once 'CRM/Core/Page.php';
require_once 'utils/Const.php';

class CRM_Roster_Page_RosterView extends CRM_Core_Page {
  function run() {
	$session = CRM_Core_Session::singleton();
    $session->pushUserContext(CRM_Utils_System::url('civicrm/rosterview'));
	
	CRM_Utils_System::setTitle(ts('RosterView'));
	
	$params = array(
		'version' => 3,
		'q' => 'civicrm/ajax/rest',
		'sequential' => 1,
	);
	$result = civicrm_api('Roster', 'Get', $params);
	
	if($result['count']<1) {
		$values = array(
			array(
				'id'		=> '',
				'name'		=> ts('No details found'),
			),
		);
	} else {
		$values = $result['values'];
		$types = CRM_Roster_Const::rostertype();
		$week = CRM_Roster_Const::weekdays();
		foreach($values as $key=>$value) {
			// translate type
			$values[$key]['type_translated'] = $types[$value['type']];
			// translate value
			if ($value['value']=='') {
				$values[$key]['value_translated'] = '';
			} else {
				switch($value['type']) {
					case 'w':
						$days = explode(',', $value['value']);
						foreach($days as $index=>$day) {
							$days[$index] = $week[$day];
						}
						$values[$key]['value_translated'] = implode(', ', $days);
						break;
					case 'm':
						// use default
					default:
						$days = explode(',', $value['value']);
						$values[$key]['value_translated'] = implode(', ', $days);
				} // switch($value['type'])
			} // is_null($value['value']) / else
			// add links
			$values[$key]['links'] = '<a href="/index.php?q=civicrm/buildroster&id=' . $values[$key]['id'] . '">' . ts('edit') . '</a>';
		} // foreach $values
	} // $result['count'] / else
	$this->assign('rows', $values);
	
	$this->assign('labels', array(
			'name'		=> ts('Name'),
			'type'		=> ts('Type'),
			'value'		=> ts('Run on'),
			'next_run'	=> ts('Next run'),
			'last_run'	=> ts('Last run'),
			'privilege'	=> ts('Privilege'),
			'links'		=> ts(''),
		)
	);
	
    parent::run();
  }
}
