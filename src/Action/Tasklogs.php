<?php

/**
 * Action Tasklogs
 *
 * Lists all tasklogs for this user.
 *
 * @link http://getfrapi.com
 * @author Frapi <frapi@getfrapi.com>
 * @link /tasklog
 */
class Action_Tasklogs extends Frapi_Action implements Frapi_Action_Interface
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

        $username   = $this->getParam('username');
        $password   = $this->getParam('password');

        // Attempt to login as user, a little bit of a hack as we currently
        // require the $_POST['login'] var to be set as well as a global AppUI
        $AppUI              = new CAppUI();
        $GLOBALS['AppUI']   = $AppUI;
        $_POST['login']     = 'login';

        if (!$AppUI->login($username, $password)) {
            throw new Frapi_Error('INVALID_LOGIN');
        }

        $task_log         = new CTaskLog;
        $task_logs        = $task_log->getAllowedRecords($AppUI->user_id);
        $return_task_logs = array();

        foreach ($task_logs as $task_log_id) {
            $temp_task_log = new CTaskLog;
            $temp_task_log->load($task_log_id);
            unset(
                $temp_task_log->_tbl_prefix, $temp_task_log->_tbl,
                $temp_task_log->_tbl_key, $temp_task_log->_error,
                $temp_task_log->_query, $temp_task_log->_tbl_module
            );
            $return_task_logs[] = (array)$temp_task_log;
        }
        $this->data['task_logs'] = $return_task_logs;
        $this->data['success']   = true;

        $this->setTemplateFileName('TaskLogsGet');

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
        return $this->toArray();
    }


}

