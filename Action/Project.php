<?php

/**
 * Action Project
 *
 * Handles request for projects.
 *
 * @link http://getfrapi.com
 * @author Frapi <frapi@getfrapi.com>
 * @link /project/:project_id
 */
class Action_Project extends Frapi_Action implements Frapi_Action_Interface
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
        $project_id = $this->getParam('project_id', self::TYPE_INT);

        if (!$project_id) {
            throw new Frapi_Error('PARAM_ERROR', 'Missing Project ID', 401);
        }

        // Attempt to login as user, a little bit of a hack as we currently
        // require the $_POST['login'] var to be set as well as a global AppUI
        $AppUI              = new CAppUI;
        $GLOBALS['AppUI']   = $AppUI;
        $_POST['login']     = 'login';

        if (!$AppUI->login($username, $password)) {
            throw new Frapi_Error('INVALID_LOGIN', 'Invalid Username or Password', 401);
        }

        $project = new CProject();
        $allowed_projects = $project->getAllowedProjects($AppUI->user_id);

        // Project ID  is the key, so lets get them in to an array so we can
        // easily check
        $allowed_projects = array_keys($allowed_projects);

        if (!in_array($project_id, $allowed_projects)) {
            throw new Frapi_Error('PERMISSION_ERROR', 'You do not have permission to view this', 401);
        }

        // User has permission so load the project for display
        $project->load($project_id);
        $this->data['project'] = (array)$project;

        // Remove the data that is not for display
        unset($this->data['project']['_tbl_prefix'], $this->data['project']['_tbl'],
            $this->data['project']['_tbl_key'], $this->data['project']['_error'],
            $this->data['project']['_query']);

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

