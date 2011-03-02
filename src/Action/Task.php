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
        $valid = $this->hasRequiredParameters($this->requiredParams);
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }

        return $this->toArray();
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
        $task_departments = $task->getTaskDepartments($AppUI, $task_id);
        $task_contacts    = $task->getTaskContacts($AppUI, $task_id);
        $task             = (array)$task->load($task_id);

        $task['task_departments'] = array();
        foreach ($task_departments as $key => $value) {
            $task['task_departments'][] = $value['dept_id'];
        }

        $task['task_contacts'] = array();
        foreach ($task_contacts as $key => $value) {
            $task['task_contacts'][] = $value['contact_id'];
        }

        // Remove the data that is not for display
        unset(
            $task['_tbl_prefix'], $task['_tbl'], $task['_tbl_key'],
            $task['_error'], $task['_query'], $task['_tbl_module']
        );

        $this->data['task'] = $task;
        $this->data['success'] = true;

        $this->setTemplateFileName('Task');
        return $this->toArray();
    }

    /**
     * Post Request Handler
     *
     * This method is called when a request is a POST
     *
     * @return array
     */
    public function executePost()
    {
        $valid = $this->hasRequiredParameters($this->requiredParams);
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }

        return $this->toArray();
    }

    /**
     * Put Request Handler
     *
     * This method is called when a request is a PUT
     *
     * @return array
     */
    public function executePut()
    {
        $valid = $this->hasRequiredParameters($this->requiredParams);
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }

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

    /**
     * Head Request Handler
     *
     * This method is called when a request is a HEAD
     *
     * @return array
     */
    public function executeHead()
    {
        $valid = $this->hasRequiredParameters($this->requiredParams);
        if ($valid instanceof Frapi_Error) {
            return $valid;
        }

        return $this->toArray();
    }


}

