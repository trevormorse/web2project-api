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
        $AppUI              = new CAppUI();
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
        $project = (array)$project->load($project_id);

        // Remove the data that is not for display
        unset(
            $project['tbl_prefix'], $project['_tbl'], $project['_tbl_key'],
            $project['_error'], $project['_query']
        );

        $this->data['project'] = $project;
        $this->data['success'] = true;

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

        $username   = $this->getParam('username');
        $password   = $this->getParam('password');
        $project_id = $this->getParam('project_id', self::TYPE_INT);

        if (!$project_id) {
            throw new Frapi_Error('PARAM_ERROR', 'Missing Project ID', 401);
        }
        // Attempt to login as user, a little bit of a hack as we currently
        // require the $_POST['login'] var to be set as well as a global AppUI
        $AppUI              = new CAppUI();
        $GLOBALS['AppUI']   = $AppUI;
        $_POST['login']     = 'login';

        if (!$AppUI->login($username, $password)) {
            throw new Frapi_Error('INVALID_LOGIN', 'Invalid Username or Password', 401);
        }

        $post_data = array(
			'dosql'                     => 'do_project_aed',
            'project_id'                => $project_id,
            'project_creator'           => $this->getParam('project_creator', self::TYPE_INT),
            'project_contacts'          => $this->getParam('project_contacts'),
            'project_name'              => $this->getParam('project_name'),
            'project_parent'            => $this->getParam('project_parent', self::TYPE_INT),
            'project_owner'             => $this->getParam('project_owner', self::TYPE_INT),
            'project_company'           => $this->getParam('project_company', self::TYPE_INT),
            'project_location'          => $this->getParam('project_location'),
            'project_start_date'        => $this->getParam('project_start_date'),
            'project_end_date'          => $this->getParam('project_end_date'),
            'project_target_budget'     => $this->getParam('project_target_budget', self::TYPE_FLOAT),
            'project_actual_budget'     => $this->getParam('project_actual_budget', self::TYPE_FLOAT),
            'project_url'               => $this->getParam('project_url'),
            'project_demo_url'          => $this->getParam('project_demo_url'),
            'project_priority'          => $this->getParam('project_priority', self::TYPE_INT),
            'project_short_name'        => $this->getParam('project_short_name'),
            'project_color_identifier'  => $this->getParam('project_color_identifier'),
            'project_type'              => $this->getParam('project_type', self::TYPE_INT),
            'project_status'            => $this->getParam('project_status', self::TYPE_INT),
            'project_description'       => $this->getParam('project_description'),
            'project_department'        => $this->getParam('project_department', self::TYPE_INT),
            'project_active'            => $this->getParam('project_active', self::TYPE_INT),
            'company_id'                => $this->getParam('project_company', self::TYPE_INT),
        );

        $project = new CProject();
        $project->bind($post_data);

        $error_array = $project->store($AppUI);

        if ($error_array !== true) {

            // Don't like how this works but need to come up with a better way
            // to handle multiple error messages
            $error_string = '';
            foreach ($error_array as $error) {
                $error_string .= $error . '. ';
            }

            throw new FRAPI_ERROR('SAVE_ERROR', $error_string, 401);
        }

        $project = (array)$project;

        // Remove the data that is not for display
        unset(
            $project['tbl_prefix'], $project['_tbl'], $project['_tbl_key'],
            $project['_error'], $project['_query']
        );

        $this->data['project'] = $project;
        $this->data['success'] = true;

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

        $username   = $this->getParam('username');
        $password   = $this->getParam('password');

        // Attempt to login as user, a little bit of a hack as we currently
        // require the $_POST['login'] var to be set as well as a global AppUI
        $AppUI              = new CAppUI();
        $GLOBALS['AppUI']   = $AppUI;
        $_POST['login']     = 'login';

        if (!$AppUI->login($username, $password)) {
            throw new Frapi_Error('INVALID_LOGIN', 'Invalid Username or Password', 401);
        }

        $post_data = array(
			'dosql'                     => 'do_project_aed',
            'project_id'                =>  0,
            'project_creator'           => $AppUI->user_id,
            'project_contacts'          => $this->getParam('project_contacts'),
            'project_name'              => $this->getParam('project_name'),
            'project_parent'            => $this->getParam('project_parent', self::TYPE_INT),
            'project_owner'             => $this->getParam('project_owner', self::TYPE_INT),
            'project_company'           => $this->getParam('project_company', self::TYPE_INT),
            'project_location'          => $this->getParam('project_location'),
            'project_start_date'        => $this->getParam('project_start_date'),
            'project_end_date'          => $this->getParam('project_end_date'),
            'project_target_budget'     => $this->getParam('project_target_budget', self::TYPE_FLOAT),
            'project_actual_budget'     => $this->getParam('project_actual_budget', self::TYPE_FLOAT),
            'project_url'               => $this->getParam('project_url'),
            'project_demo_url'          => $this->getParam('project_demo_url'),
            'project_priority'          => $this->getParam('project_priority', self::TYPE_INT),
            'project_short_name'        => $this->getParam('project_short_name'),
            'project_color_identifier'  => $this->getParam('project_color_identifier'),
            'project_type'              => $this->getParam('project_type', self::TYPE_INT),
            'project_status'            => $this->getParam('project_status', self::TYPE_INT),
            'project_description'       => $this->getParam('project_description'),
            'project_department'        => $this->getParam('project_department', self::TYPE_INT),
            'project_active'            => $this->getParam('project_active', self::TYPE_INT),
        );

        $project = new CProject();
        $project->bind($post_data);
        $error_array = $project->store($AppUI);

        if ($error_array !== true) {

            // Don't like how this works but need to come up with a better way
            // to handle multiple error messages
            $error_string = '';
            foreach ($error_array as $error) {
                $error_string .= $error . '. ';
            }

            throw new FRAPI_ERROR('SAVE_ERROR', $error_string, 401);
        }

        $project = (array)$project;

        // Remove the data that is not for display
        unset(
            $project['tbl_prefix'], $project['_tbl'], $project['_tbl_key'],
            $project['_error'], $project['_query']
        );

        $this->data['project'] = $project;
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

        $username   = $this->getParam('username');
        $password   = $this->getParam('password');
        $project_id = $this->getParam('project_id', self::TYPE_INT);

        if (!$project_id) {
            throw new Frapi_Error('PARAM_ERROR', 'Missing Project ID', 401);
        }

        // Attempt to login as user, a little bit of a hack as we currently
        // require the $_POST['login'] var to be set as well as a global AppUI
        $AppUI              = new CAppUI();
        $GLOBALS['AppUI']   = $AppUI;
        $_POST['login']     = 'login';

        if (!$AppUI->login($username, $password)) {
            throw new Frapi_Error('INVALID_LOGIN', 'Invalid Username or Password', 401);
        }

        $project = new CProject();
        $project->load($project_id);
        if (!$project->delete($AppUI)) {
            throw new Frapi_Error('PERMISSION_ERROR', 'You do not have permission to delete this', 401);
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
        return $this->toArray();
    }


}

