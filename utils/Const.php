<?php

class CRM_Roster_Const {

	static function rostertype() {
		return array(
			'' => ts('- select -'),
			'w' => ts('Week-based'),
			'm' => ts('Month-based'),
		);
	}

	static function weekdays() {
		return array(
			0 => ts('Sunday'),
			1 => ts('Monday'),
			2 => ts('Tuesday'),
			3 => ts('Wednesday'),
			4 => ts('Thursday'),
			5 => ts('Friday'),
			6 => ts('Saturday'),
		);
	}
  
	static function monthdays() {
		$result = array();
		for($n=1; $n<32; $n++) {
			$result[$n] = (string)$n;
		}
		return $result;
	}
	
}
