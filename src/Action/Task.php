<?php

/**
 * Action Task
 *
 * Handles requests for tasks.
 *
 * @link http://getfrapi.com
 * @author Frapi <frapi@getfrapi.com>
 * @link /task/:id
 */
class Action_Task extends Frapi_Action implements Frapi_Action_Interface
{

    /**
     * Required parameters
     *
     * @var An array of required parameters.
     */
    protected $requiredParams = array(
        'username',
        'password'
        );

    /**
     * The data container to use in toArray()
     *
     * @var A container of data to fill and return in toArray()
     */
    private $data = array();

    /**
     * To Array
     *
     * This method returns the value found in the database
     * into an associative array.
     *
     * @return array
     */
    public function toArray()
    {
        $this->data['username'] = $this->getParam('username', self::TYPE_OUTPUT);
        $this->data['password'] = $this->getParam('password', self::TYPE_OUTPUT);
        return $this->data;
    }

    /**
     * Default Call Method
     *
     * This method is called when no specific request handler has been found
     *
     * @return array
     */
    public function executeAction()
    {
        throw new Frapi_Error('METHOD_NOT_ALLOWED');
    }

    /**
     * Get Request Handler
     *
     * This method is called when a request is a GET
     *
     * @return array
     */
    public function executeGet()
    {
        $valid = $this->hasRequiredParameters($this->requiredParams);
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }

        $username = $this->getParam('username');
        $password = $this->getParam('password');
        $task_id = $this->getParam('task_id', self::TYPE_INT);

        // Attempt to login as user, a little bit of a hack as we currently
        // require the $_POST['login'] var to be set as well as a global AppUI
        $AppUI              = new CAppUI();
        $GLOBALS['AppUI']   = $AppUI;
        $_POST['login']     = 'login';

        if (!$AppUI->login($username, $password)) {
            throw new Frapi_Error('INVALID_LOGIN');
        }

        $task = new CTask();
        $allowed_tasks = $task->getAllowedRecords($AppUI->user_id);

        // Task ID  is the key, so lets get them in to an array so we can
        // easily check
        $allowed_tasks = array_keys($allowed_tasks);

        if (!in_array($task_id, $allowed_tasks)) {
            throw new Frapi_Error('PERMISSION_ERROR');
        }

        // User has permission so load the project for display
        $task->load($task_id);
        $task_departments = $task->getTaskDepartments($AppUI, $task_id);
        $task_contacts    = $task->getTaskContacts($AppUI, $task_id);
        $task             = get_object_vars($task);

        $task['task_departments'] = array();
        foreach ($task_departments as $key => $value) {
            $task['task_departments'][] = $value['dept_id'];
        }

        $task['task_contacts'] = array();
        foreach ($task_contacts as $key => $value) {
            $task['task_contacts'][] = $value['contact_id'];
        }

        $this->data['task'] = $task;
        $this->data['success'] = true;

        $this->setTemplateFileName('Task');
        return $this->toArray();
    }

    /**
     * Pur Request Handler
     *
     * This method is called when a request is a PUT
     *
     * @return array
     */
    public function executePut()
    {
        /**
         * @todo Remove this once we figure out how to reference vars in file
         * that is autoloaded
         */
        global $tracking_dynamics;

        $valid = $this->hasRequiredParameters($this->requiredParams);
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }

        $username        = $this->getParam('username');
        $password        = $this->getParam('password');
        $project_id      = $this->getParam('project_id', self::TYPE_INT);
        $hassign         = $this->getParam('hassign');
        $hdependencies   = $this->getParam('hdependencies');
        $notify          = $this->getParam('task_notify');
        $comment         = $this->getParam('email_comment');
        $task_id         = $this->getParam('task_id');
        $adjustStartDate = $this->getParam('set_task_start_date');
        $task            = new CTask;
        $project_id      = $this->getParam('project_id');

        if (!$project_id || !is_numeric($project_id)) {
            throw new Frapi_Error('ERROR_MISSING_REQUEST_ARG');
        }

        // Attempt to login as user, a little bit of a hack as we currently
        // require the $_POST['login'] var to be set as well as a global AppUI
        $AppUI              = new CAppUI();
        $GLOBALS['AppUI']   = $AppUI;
        $_POST['login']     = 'login';

        if (!$AppUI->login($username, $password)) {
            throw new Frapi_Error('INVALID_LOGIN');
        }

        $post_data = array(
            'task_id'                        => $this->getParam('task_id'),
            'task_name'                      => $this->getParam('task_name'),
            'task_status'                    => $this->getParam('task_status'),
            'task_percent_complete'          => $this->getParam('task_percent_complete'),
            'task_milestone'                 => $this->getParam('task_milestone'),
            'task_owner'                     => $this->getParam('task_owner'),
            'task_access'                    => $this->getParam('task_access'),
            'task_related_url'               => $this->getParam('task_related_url'),
            'task_parent'                    => $this->getParam('task_parent'),
            'task_type'                      => $this->getParam('task_type'),
            'task_target_budget'             => $this->getParam('task_target_budget'),
            'task_description'               => $this->getParam('task_description'),
            'task_start_date'                => $this->getParam('task_start_date'),
            'task_end_date'                  => $this->getParam('task_end_date'),
            'task_duration'                  => $this->getParam('task_duration'),
            'task_duration_type'             => $this->getParam('task_duration_type'),
            'task_dynamic'                   => $this->getParam('task_dynamic'),
            'task_allow_other_user_tasklogs' => $this->getParam('task_allow_other_user_tasklogs'),
            'task_project'                   => $project_id,
            'task_priority'                  => $this->getParam('task_priority'),
        );

        // Include any files for handling module-specific requirements
        foreach (findTabModules('tasks', 'addedit') as $mod) {
            $fname = W2P_BASE_DIR . '/modules/' . $mod . '/tasks_dosql.addedit.php';
            if (file_exists($fname)) {
                require_once $fname;
            }
        }

        // Find the task if we are set
        $task_end_date = null;
        if ($task_id) {
            $task->load($task_id);
            $task_end_date = new w2p_Utilities_Date($task->task_end_date);
        }

        $task = new CTask();

        if(!$task->bind($post_data)) {
            throw new Frapi_Error('SAVE_ERROR', $task->getError());
        }

        if ($task->task_dynamic != 1) {
            $task_dynamic_delay = ($this->getParam('task_dynamic_nodelay')) ? $this->getParam('task_dynamic_nodelay') : '0';
            if (in_array($task->task_dynamic, $tracking_dynamics)) {
                $task->task_dynamic = $task_dynamic_delay ? 21 : 31;
            } else {
                $task->task_dynamic = $task_dynamic_delay ? 11 : 0;
            }
        }

        // Let's check if task_dynamic is unchecked
        if (!$this->getParam('task_dynamic')) {
            $task->task_dynamic = false;
        }

        // Make sure task milestone is set or reset as appropriate
        if ($this->getParam('task_milestone')) {
            $task->task_milestone = false;
        }

        //format hperc_assign user_id=percentage_assignment;user_id=percentage_assignment;user_id=percentage_assignment;
        $tmp_ar = explode(';', $this->getParam('hperc_assign'));
        $i_cmp = sizeof($tmp_ar);

        $hperc_assign_ar = array();
        for ($i = 0; $i < $i_cmp; $i++) {
            $tmp = explode('=', $tmp_ar[$i]);
            if (count($tmp) > 1) {
                $hperc_assign_ar[$tmp[0]] = $tmp[1];
            } elseif ($tmp[0] != '') {
                $hperc_assign_ar[$tmp[0]] = 100;
            }
        }

        // let's check if there are some assigned departments to task
        $task->task_departments = implode(',', $this->getParam('dept_ids', self::TYPE_ARRAY));

        // convert dates to SQL format first
        if ($task->task_start_date) {
            $date = new w2p_Utilities_Date($task->task_start_date);
            $task->task_start_date = $date->format(FMT_DATETIME_MYSQL);
        }
        $end_date = null;
        if ($task->task_end_date) {
            if (strpos($task->task_end_date, '2400') !== false) {
                $task->task_end_date = str_replace('2400', '2359', $task->task_end_date);
            }
            $end_date = new w2p_Utilities_Date($task->task_end_date);
            $task->task_end_date = $end_date->format(FMT_DATETIME_MYSQL);
        }

        $error_array = $task->store($AppUI);

        // Return all the validation messages
        if ($error_array !== true) {
            $error_message = '';
            foreach ($error_array as $error) {
                $error_message .= $error . '. ';
            }

            throw new Frapi_Error('SAVE_ERROR', $error_message);
        }

        $task_parent     = ($this->getParam('task_parent')) ? $this->getParam('task_parent', SELF::TYPE_INT) : 0;
        $old_task_parent = ($this->getParam('old_task_parent')) ? $this->getParam('old_task_parent', SELF::TYPE_INT) : 0;

        if ($task_parent != $old_task_parent) {
            $oldTask = new CTask();
            $oldTask->load($old_task_parent);
            $oldTask->updateDynamics(false);
        }

        // How to handle custom fields? Do we support it in api?

        // Now add any task reminders
        // If there wasn't a task, but there is one now, and
        // that task date is set, we need to set a reminder.
        if (empty($task_end_date) || (!empty($end_date) && $task_end_date->dateDiff($end_date))) {
            $task->addReminder();
        }

        if (isset($hassign)) {
            $task->updateAssigned($hassign, $hperc_assign_ar);
        }

        if (isset($hdependencies)) { // && !empty($hdependencies)) {
                // there are dependencies set!

                // backup initial start and end dates
                $tsd = new w2p_Utilities_Date($task->task_start_date);
                $ted = new w2p_Utilities_Date($task->task_end_date);

                // updating the table recording the
                // dependency relations with this task
                $task->updateDependencies($hdependencies, $task_parent);

                // we will reset the task's start date based upon dependencies
                // and shift the end date appropriately
                if ($adjustStartDate && !is_null($hdependencies)) {

                // load already stored task data for this task
                $tempTask = new CTask();
                $tempTask->load($task->task_id);

                // shift new start date to the last dependency end date
                $nsd = new w2p_Utilities_Date($tempTask->get_deps_max_end_date($tempTask));

                // prefer Wed 8:00 over Tue 16:00 as start date
                $nsd = $nsd->next_working_day();

                // prepare the creation of the end date
                $ned = new w2p_Utilities_Date();
                $ned->copy($nsd);

                if (empty($task->task_start_date)) {
                    // appropriately calculated end date via start+duration
                    $ned->addDuration($task->task_duration, $task->task_duration_type);

                } else {
                    // calc task time span start - end
                    $d = $tsd->calcDuration($ted);

                    // Re-add (keep) task time span for end date.
                    // This is independent from $obj->task_duration.
                    // The value returned by Date::Duration() is always in hours ('1')
                    $ned->addDuration($d, '1');

                }

                // prefer tue 16:00 over wed 8:00 as an end date
                $ned = $ned->prev_working_day();

                $task->task_start_date = $nsd->format(FMT_DATETIME_MYSQL);
                $task->task_end_date = $ned->format(FMT_DATETIME_MYSQL);

                $q = new w2p_Database_Query;
                $q->addTable('tasks', 't');
                $q->addUpdate('task_start_date', $task->task_start_date);
                $q->addUpdate('task_end_date', $task->task_end_date);
                $q->addWhere('task_id = ' . (int)$task->task_id);
                $q->addWhere('task_dynamic <> 1');
                $q->exec();
                $q->clear();
                }
                $task->pushDependencies($task->task_id, $task->task_end_date);
        }

        $task->load($task_id);

        $this->data['task'] = get_object_vars($task);
        $this->data['success'] = true;

        return $this->toArray();
    }

    /**
     * Delete Request Handler
     *
     * This method is called when a request is a DELETE
     *
     * @return array
     */
    public function executeDelete()
    {
        $valid = $this->hasRequiredParameters($this->requiredParams);
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }

        $username = $this->getParam('username');
        $password = $this->getParam('password');
        $task_id  = $this->getParam('task_id', self::TYPE_INT);

        // Attempt to login as user, a little bit of a hack as we currently
        // require the $_POST['login'] var to be set as well as a global AppUI
        $AppUI              = new CAppUI();
        $GLOBALS['AppUI']   = $AppUI;
        $_POST['login']     = 'login';

        if (!$AppUI->login($username, $password)) {
            throw new Frapi_Error('INVALID_LOGIN');
        }

        $task = new CTask();
        $task->load($task_id);
        if (!$task->delete($AppUI)) {
            throw new Frapi_Error('PERMISSION_ERROR');
        }

        $this->data['success'] = true;

        return $this->toArray();
    }
}

