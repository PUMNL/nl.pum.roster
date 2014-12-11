<?php

require_once 'roster.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function roster_civicrm_config(&$config) {
  _roster_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function roster_civicrm_xmlMenu(&$files) {
  _roster_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function roster_civicrm_install() {
  return _roster_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function roster_civicrm_uninstall() {
  return _roster_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function roster_civicrm_enable() {
  return _roster_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function roster_civicrm_disable() {
  return _roster_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function roster_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _roster_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function roster_civicrm_managed(&$entities) {
  return _roster_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function roster_civicrm_caseTypes(&$caseTypes) {
  _roster_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function roster_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _roster_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implementation of hook civicrm_navigationMenu
 * Creates a menu entry to disclose a list of available rosters
 * 
 * @param array $params
 */
function roster_civicrm_navigationMenu( &$params ) {
	$maxKey = ( max( array_keys($params) ) );
	$params[$maxKey+1] = array (
		'attributes' => array (
			'label'      => 'Rosters',
			'name'       => 'Rosters',
			'url'        => null,
			'permission' => 'access CiviCRM',
			'operator'   => null,
			'separator'  => null,
			'parentID'   => null,
			'navID'      => $maxKey+1,
			'active'     => 1
		),
		'child' =>  array (
			'1' => array (
				'attributes' => array (
					'label'      => 'List Rosters',
					'name'       => 'List Rosters',
					'url'        => 'civicrm/rosterview',
					'operator'   => null,
					'separator'  => 0,
					'parentID'   => $maxKey+1,
					'navID'      => 1,
					'active'     => 1
				),
				'child' => null
			), 
		)
	);
}