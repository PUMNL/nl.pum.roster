<?php

require_once 'CRM/Core/Form.php';
require_once 'utils/Const.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Roster_Form_BuildRoster extends CRM_Core_Form {

  function buildQuickForm() {
	// if the url contains an id, then retrieve this record from civicrm_pum_roster
	$roster = NULL;
	$id = NULL;
	if (empty($_GET['id'])) {
		$this->assign('warning', ts('Error: no id provided'));
	} else {
		$id = $_GET['id'];
		$params = array(
			'version' => 3,
			'q' => 'civicrm/ajax/rest',
			'sequential' => 1,
			'id' => $id,
		);
		$result = civicrm_api('Roster', 'Get', $params);
		if ($result['count']==1) {
			$roster = $result['values'][0];
		} else {
			$this->assign('warning', ts('Error: record not found'));
		}
	}

	// title
	if (is_null($roster)) {
		CRM_Utils_System::setTitle(ts('Roster'));
	} else {
		CRM_Utils_System::setTitle(ts('Roster: ' . $roster['name']));
	}

	// read or edit mode
	$mode = 'read';
	if ($roster['privilege']=='') {
		$mode = 'edit'; // no privilege restriction allows editing
	} else {
		if (CRM_Core_Permission::check($roster['privilege'])) {
			$mode = 'edit';
		}
	}
	$this->assign('mode', $mode);

    // add form elements (read and edit specific)
	$types = CRM_Roster_Const::rostertype();
	$weekdays = CRM_Roster_Const::weekdays();
	$buttons = array();

	if ($mode=='read') {
		// readmode: values as texts
		// type
		$this->assign('roster_type_txt', array(
			'label' => ts('Roster type'),
			'value' => $types[$roster['type']],
			'code'	=> $roster['type'],
		));

		switch($roster['type']) {
			case 'w':
				// selection day(s) in week
				$ar = explode(',', $roster['value']);
				foreach($ar as $i=>$v) {
					$ar[$i] = $weekdays[$ar[$i]];
				}
				$this->assign('roster_week_txt', array(
					'label' => ts('Day(s) in a week'),
					'value' => implode(', ', $ar),
				));
				break;
			case 'm':
				// selection day(s) in month
				$ar = explode(',', $roster['value']);
				$this->assign('roster_month_txt', array(
					'label' => ts('Day(s) in a month'),
					'value' => implode(', ', $ar),
				));
				break;
			default:
				// value(s) not included
		}

		// interval (in days)
		$this->assign('roster_interval_txt', array(
			'label' => ts('Minimum interval'),
			'value' => $roster['min_interval'],
		));

		// next run
		$this->assign('roster_nextrun_txt', array(
			'label' => ts('Next run'),
			'value' => $roster['next_run'],
		));

	} else {
		// editmode: values as fields (except name, last_run and privilege)
		// id
		$this->add(
		  'hidden', // field type
		  'id', // field name
		  NULL, // field label
		  array(
			'id' => 'id',
		  )
		);
		// name
		$this->add(
		  'hidden', // field type
		  'roster_name', // field name
		  NULL, // field label
		  array(
			'id' => 'roster_name',
		  )
		);
		// type
		$this->add(
			'select', // field type
			'roster_type', // field name
			ts('Roster type'), // field label
			$types, // list of options
			true // is required
		);

		// selection day(s) in week
		$fld = $this->addElement(
			'advmultiselect',
			'roster_week',
			ts('Day(s) in a week'),
			$weekdays,
			array(
				'size' => 7,
				'style' => 'width:150px',
				'class' => 'advmultiselect',
			)
		);
		$fld->setButtonAttributes('add', array('value' => ts('Add >>')));
		$fld->setButtonAttributes('remove', array('value' => ts('<< Remove')));

		// selection day(s) in month
		$fld = $this->addElement(
			'advmultiselect',
			'roster_month',
			ts('Day(s) in a month'),
			CRM_Roster_Const::monthdays(),
			array(
				'size' => 31,
				'style' => 'width:150px',
				'class' => 'advmultiselect',
			)
		);
		$fld->setButtonAttributes('add', array('value' => ts('Add >>')));
		$fld->setButtonAttributes('remove', array('value' => ts('<< Remove')));

		// interval (in days)
		$this->add(
		  'text', // field type
		  'roster_interval', // field name
		  ts('Minimum interval'), // field label
		  true // is required
		);

		// next run
		$yearsInPast   = 0;
		$yearsInFuture = 1;
		$dateParts     = implode( CRM_Core_DAO::VALUE_SEPARATOR, array('Y', 'M', 'D') );
		$this->add(
			'date',
			'roster_nextrun',
			ts('Next run'),
			CRM_Core_SelectValues::date('custom', $yearsInPast, $yearsInFuture, $dateParts)
		);

	}

	// add the last form elements (for both read and edit) that can never be edited
	// last run
	$this->assign('roster_lastrun_txt', array(
		'label' => ts('Last run'),
		'value' => $roster['last_run'],
    ));
	$this->assign('roster_privilege_txt', array(
		'label' => ts('Privilege'),
		'value' => $roster['privilege'],
	));

	// buttons
	if ($mode=='edit') {
		$buttons[] = array(
			'type' => 'submit',
			'name' => ts('Save'),
			'isDefault' => FALSE,
		);
	}
	$buttons[] = array(
		'type' => 'cancel',
		'name' => ts('Cancel'),
		'isDefault' => TRUE,
	);
    $this->addButtons($buttons);


	// default values
	$useDefault = TRUE;
	if (!is_null($roster)) {
		$defaults['id'] = $id;
		$defaults['roster_name'] = $roster['name'];
		$defaults['roster_type'] = array($roster['type']);
		$roster_value = $roster['value'];
		if ($roster_value == '') {
			$roster_value = array();
		} else {
			$roster_value = explode(',', $roster_value);
		}
		switch ($roster['type']) {
			case 'w':
				$defaults['roster_week'] = $roster_value;
				$useDefault = FALSE;
				break;
			case 'm':
				$defaults['roster_month'] = $roster_value;
				$useDefault = FALSE;
				break;
			default:
				// unexpected: use default values
		}
		if (!$useDefault) {
			$defaults['roster_nextrun'] = $roster['next_run'];
			$defaults['roster_interval'] = $roster['min_interval'];

		}
	}
	if ($useDefault) {
		$defaults['roster_type'] = array();
		$defaults['roster_month'] = array();
		$date = new DateTime();
		$date->setDate(2014, 11, 23); // change to either today or tomorrow
		$defaults['roster_nextrun'] = $date->format('Y-m-d');
		$defaults['roster_interval'] = 1;
				$this->assign('last_run', array(
					'label' => ts('Last run'),
					'value' => '',
				));
				$this->assign('privilege', array(
					'label' => ts('Privilege'),
					'value' => '',
				));
	}

	// apply default values
	if (isset($defaults)) {
		$this->setDefaults($defaults);
	}

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  /**
   * Validation callbacks
   */
  function addRules() {
    $this->addFormRule(array('CRM_Roster_Form_BuildRoster', 'RosterValidation'));
  }

  /**
   * Validation callback for BuildRoster
   */
  static function RosterValidation($values) {
    $errors = array();
	if ($values['roster_type'] == '') {
		$errors['roster_type'] = ts('Please select a roster type');
	}
	if ($values['roster_type'] == 'w' && !array_key_exists('roster_week', $values)) {
		$errors['roster_week'] = ts('Please select one or more days');
	}
	if ($values['roster_type'] == 'm' && !array_key_exists('roster_month', $values)) {
		$errors['roster_month'] = ts('Please select one or more days');
	}
	if ($values['roster_interval'] == '') {
		$errors['roster_interval'] = ts('Please enter the minimum interval (in days)');
	} elseif (!is_numeric($values['roster_interval'])) {
		$errors['roster_interval'] = ts('Please enter a valid minimum interval (in number of days)');
	}
	if ($values['roster_nextrun']['M'] == '' || $values['roster_nextrun']['d'] == '') {
		$errors['roster_nextrun'] = ts('Please select the next run date');
	} elseif ($values['roster_nextrun']['d'] == 31 && in_array($values['roster_nextrun']['M'], array(2, 4, 6, 9, 11))) {
		$errors['roster_nextrun'] = ts('The selected month does not support day 31');
	} elseif ($values['roster_nextrun']['d'] == 30 && in_array($values['roster_nextrun']['M'], array(2))) {
		$errors['roster_nextrun'] = ts('The selected month does not support day 30');
	}
	return empty($errors) ? TRUE : $errors;
  }


  /**
   * Process submitted data
   */
  function postProcess() {
    $values = $this->exportValues();
	$sql_values = array();

	$today = date_create();
	$y = $today->format('Y');
	$date = date_create();
	do {
		// advance one year until a valid (present/future) date is found
		date_date_set($date, $y, $values['roster_nextrun']['M'], $values['roster_nextrun']['d']);
		$y++;
	} while ( ($date->format('d') != $values['roster_nextrun']['d']) || ($today>$date) ); // cannot result in a different day or a date in the past
	$sql_values['name'] = $values['roster_name'];
	$sql_values['id'] = $values['id'];
	$sql_values['type'] = $values['roster_type'];
	switch ($values['roster_type']) {
		case 'w':
			sort($values['roster_week'], SORT_NUMERIC);
			$sql_values['value'] = implode(',', $values['roster_week']);
			break;
		case 'm':
			sort($values['roster_month'], SORT_NUMERIC);
			$sql_values['value'] = implode(',', $values['roster_month']);
			break;
		default:
			// should not occur unless other types got introduced
	}
	$sql_values['min_interval'] = $values['roster_interval'];
	$sql_values['next_run'] = $date->format('Y-m-d');

	$params = array(
	  'version' => 3,
	  'q' => 'civicrm/ajax/rest',
	  'sequential' => 1,
	);
	foreach($sql_values as $key=>$value) {
		$params[$key] = $value;
	}
	$result = civicrm_api('Roster', 'Set', $params);
	if (!empty($result['is_error'])) {
		$msg = ts('Error while saving');
		if (!empty($result['error_message'])) {
			$msg .= ': ' . ts($result['error_message']);
		}
		CRM_Core_Session::setStatus(ts($msg), 'error', array('expires'=>0));
	}
	CRM_Utils_System::redirect('civicrm/rosterview');
  }


  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
