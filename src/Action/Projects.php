<?php

/**
 * Action Projects
 *
 * Lists all the projects that the user is able to access.
 *
 * @link http://getfrapi.com
 * @author Frapi <frapi@getfrapi.com>
 * @link /projects
 */
class Action_Projects extends Frapi_Action implements Frapi_Action_Interface
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

        $project        = new CProject();
        $projects       = $project->getAllowedProjects($AppUI->user_id);
        $final_projects = array();
        $cache          = new Frapi_Internal();
        $cache          = $cache->getCachedDbConfig();

        foreach( $projects as $key =>$project) {
            unset($projects[$key][0], $projects[$key][1], $projects[$key][2], $projects[$key][3], $projects[$key][4], $projects[$key][5], $projects[$key][6]);

            $projects[$key]['project_uri'] = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $projects[$key]['project_uri'] .= $cache['api_url'] . '/project/' . $project['project_id'];
        }

        $this->data['projects'] = $projects;
        $this->data['success']  = true;

        $this->setTemplateFileName('ProjectsGet');

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

        // Attempt to login as user, a little bit of a hack as we currently
        // require the $_POST['login'] var to be set as well as a global AppUI
        $AppUI              = new CAppUI();
        $GLOBALS['AppUI']   = $AppUI;
        $_POST['login']     = 'login';

        if (!$AppUI->login($username, $password)) {
            throw new Frapi_Error('INVALID_LOGIN');
        }

        $post_data = array(
            'project_id'                =>  0,
            'project_creator'           => $AppUI->user_id,
            'project_contacts'          => $this->getParam('project_contacts', self::TYPE_ARRAY),
            'project_name'              => $this->getParam('project_name'),
            'project_parent'            => $this->getParam('project_parent'),
            'project_owner'             => $this->getParam('project_owner'),
            'project_company'           => $this->getParam('project_company'),
            'project_location'          => $this->getParam('project_location'),
            'project_start_date'        => $this->getParam('project_start_date'),
            'project_end_date'          => $this->getParam('project_end_date'),
            'project_target_budget'     => $this->getParam('project_target_budget'),
            'project_actual_budget'     => $this->getParam('project_actual_budget'),
            'project_url'               => $this->getParam('project_url'),
            'project_demo_url'          => $this->getParam('project_demo_url'),
            'project_priority'          => $this->getParam('project_priority'),
            'project_short_name'        => $this->getParam('project_short_name'),
            'project_color_identifier'  => $this->getParam('project_color_identifier'),
            'project_type'              => $this->getParam('project_type'),
            'project_status'            => $this->getParam('project_status'),
            'project_description'       => $this->getParam('project_description'),
            'project_departments'       => $this->getParam('project_departments', self::TYPE_ARRAY),
            'project_active'            => $this->getParam('project_active'),
        );

        $project = new CProject();
        $project->bind($post_data);
        $error_array = $project->store($AppUI);

        // Return all the validation messages
        if ($error_array !== true) {
            $error_message = '';
            foreach ($error_array as $error) {
                $error_message .= $error . '. ';
            }

            throw new Frapi_Error('SAVE_ERROR', $error_message);
        }

        $project             = get_object_vars($project);
        $pd                  = CProject::getDepartments($AppUI, $project['project_id']);
        $project_departments = array();

        foreach ($pd as $key => $value) {
            $project_departments[] = $value['dept_id'];
        }

        $project['project_departments'] = $project_departments;

        $this->data['project'] = $project;
        $this->data['success'] = true;

        $cache                                = new Frapi_Internal();
        $cache                                = $cache->getCachedDbConfig();
        $project_uri                          = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $project_uri                         .= $cache['api_url'] . '/project/' . $project['project_id'];
        $this->data['project']['project_uri'] = $project_uri;

        return new Frapi_Response(array(
            'code'    => 201,
            'data'    => $this->data,
            'headers' => array('location' => $project_uri),
        ));
    }
}

