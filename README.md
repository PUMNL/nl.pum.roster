nl.pum.roster
=============

This civicrm extension allows you to control your scheduled jobs to run only on certain days.

Instructions:
=============

**Step 1:** Use the API to define a roster for your job on a weekly or monthly base. Register a privilege (added by your own extension) to restrict who can alter your jobs roster.

*Example privilege:*

     function hook_civicrm_permission( &$permissions ) {
       $prefix = ts('Example') . ': '; // name of extension or module
       $permissions['myPrivilege'] = $prefix . ts('my privilege');
     }

*Example roster definition (call from install or update):*

     function initiateRoster() {
       $params = array(
         'version' => 3,
         'q' => 'civicrm/ajax/rest',
         'sequential' => 1,
         'name' => 'myRoster',
         'type' => 'w',
         'value' => '2,4',
         'min_interval' => 1,
         'next_run' => date('Y-m-d', strtotime('-1 days')),
         'privilege' => 'myPrivilege',
       );
       $result = civicrm_api('Roster', 'set', $params);
       if (!empty($result['is_error'])) {
         return FALSE;
       }
     }

Note: value '2,4' for a week (w) type roster means "Tuesday and Thursday". For a month (m) type roster, that would mean the 2nd and 4th or the month

Edit the roster through /civicrm/rosterview. Saving the roster will automatically set the next run date to the first upcoming day/month as indicated.
The last run date and the privilege can not be modified this was as they both are controlled by code.

Note: if a privilege is registered in a roster, you will need to have that privilege assigned in order to edit the roster.
If you don't have that privilege, the rosters definition will only be displayed.
If no privilege was registered, everyone is allowed to edit the roster.


**Step 2:** In your own scheduled job (API), add a check at the start of your job to see if complete execution is allowed.

*Example:*

     $params = array(
       'version' => 3,
       'q' => 'civicrm/ajax/rest',
       'sequential' => 1,
       'name' => 'myRoster',
     );
     $result = civicrm_api('Roster', 'isallowed', $params);

Note: if $result['values'] equals 1, you should allow your job to continue. If it equals 0, bring further execution to a halt.


**Step 3:** At the end of your job, make sure to update the next_run date (and implicitly set the last run date).

*Example:*

    $params = array(
      'version' => 3,
      'q' => 'civicrm/ajax/rest',
      'sequential' => 1,
      'name' => 'myRoster',
    );
    $result = civicrm_api('Roster', 'schedulenext', $params);

Note: this API will includes an additional check at IsAllowed and return an error if execution is/was not supposed to fire. On success, you will see that $result['values']==1.
   