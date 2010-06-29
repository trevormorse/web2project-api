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

        $username   = $this->getParam('username', self::TYPE_STRING);
        $password   = $this->getParam('password', self::TYPE_STRING);
        $project_id = $this->getParam('project_id', self::TYPE_INT);

        if (!$project_id) {
            throw new Frapi_Error('PARAM_ERROR', 'Missing Project ID', 500);
        }

        // Attempt to login as user, a little bit of a hack as we currently
        // require the $_POST['login'] var to be set as well as a global AppUI
        $AppUI              = new CAppUI;
        $GLOBALS['AppUI']   = $AppUI;
        $_POST['login']     = 'login';

        if (!$AppUI->login($username, $password)) {
            throw new Frapi_Error('AUTH_ERROR', 'Invalid Username or Password', 401);
        }

        $project = new CProject();
        $allowed_projects = $project->getAllowedProjects($AppUI->user_id);

        // Project ID  is the key, so lets get them in to an array so we can
        // easily check
        $allowed_projects = array_keys($allowed_projects);

        if (!in_array($project_id, $allowed_projects)) {
            throw new Frapi_Error('AUTH_ERROR', 'You do not have permission to view this project', 401);
        }

        // User has permission so load the project for display
        $project->load($project_id);

        $this->data['project_id']               = $project->project_id;
        $this->data['project_company']          = $project->project_company;
        $this->data['project_department']       = $project->project_department;
        $this->data['project_name']             = $project->project_name;
        $this->data['project_short_name']       = $project->project_short_name;
        $this->data['project_owner']            = $project->project_owner;
        $this->data['project_url']              = $project->project_url;
        $this->data['project_demo_url']         = $project->project_demo_url;
        $this->data['project_start_date']       = $project->project_start_date;
        $this->data['project_end_date']         = $project->project_end_date;
        $this->data['project_actual_end_date']  = $project->project_actual_end_date;
        $this->data['project_status']           = $project->project_status;
        $this->data['project_percent_complete'] = $project->project_percent_complete;
        $this->data['project_color_identifier'] = $project->project_color_identifier;
        $this->data['project_description']      = $project->project_description;
        $this->data['project_target_budget']    = $project->project_target_budget;
        $this->data['project_scheduled_hours']  = $project->project_scheduled_hours;
        $this->data['project_worked_hours']     = $project->project_worked_hours;
        $this->data['project_task_count']       = $project->project_task_count;
        $this->data['project_creator']          = $project->project_creator;
        $this->data['project_active']           = $project->project_active;
        $this->data['project_private']          = $project->project_private;
        $this->data['project_departments']      = $project->project_departments;
        $this->data['project_contacts']         = $project->project_contacts;
        $this->data['project_priority']         = $project->project_priority;
        $this->data['project_type']             = $project->project_type;
        $this->data['project_parent']           = $project->project_parent;
        $this->data['project_original_parent']  = $project->project_original_parent;
        $this->data['project_location']         = $project->project_location;

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

