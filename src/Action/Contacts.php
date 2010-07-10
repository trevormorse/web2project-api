<?php

/**
 * Action Contacts
 *
 * Lists all the projects that the user is able to access.
 *
 * @link http://getfrapi.com
 * @author Frapi <frapi@getfrapi.com>
 * @link /contacts
 */
class Action_Contacts extends Frapi_Action implements Frapi_Action_Interface
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

        $contact = new CContact();
        $this->data['contacts'] = $contact->getAllowedRecords($AppUI->user_id);
        $this->data['success']  = true;

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
            'contact_id' => 0,
            'contact_first_name' => $this->getParam('contact_first_name'),
            'contact_last_name' => $this->getParam('contact_last_name'),
            'contact_order_by' => $this->getParam('contact_order_by'),
            'contact_private' => $this->getParam('contact_private', self::TYPE_INT),
            'contact_job' => $this->getParam('contact_job'),
            'contact_company_name' => $this->getParam('contact_company_name'),
            'contact_company' => $this->getParam('contact_company', self::TYPE_INT),
            'contact_department_name' => $this->getParam('contact_department_name'),
            'contact_department' => $this->getParam('contact_department', self::TYPE_INT),
            'contact_title' => $this->getParam('contact_title'),
            'contact_type' => $this->getParam('contact_type'),
            'contact_address1' => $this->getParam('contact_address1'),
            'contact_address2' => $this->getParam('contact_address2'),
            'contact_city' => $this->getParam('contact_city'),
            'contact_state' => $this->getParam('contact_state'),
            'contact_zip' => $this->getParam('contact_zip'),
            'contact_country' => $this->getParam('contact_country'),
            'contact_birthday' => $this->getParam('contact_birthday'),
            'contact_notes' => $this->getParam('contact_notes'),
        );

        // Ugh, the store method uses $_POST directly for contact methods :(
        $_POST['contact_methods'] = $this->getParam('contact_methods');

        $contact = new CContact();
        $contact->bind($post_data);

        $error_array = $contact->store($AppUI);

        if ($error_array !== true) {
            $error_message = '';
            foreach ($error_array as $error) {
                $error_message .= $error . '. ';
            }

            throw new Frapi_Error('SAVE_ERROR', $error_message);
        }

        /*
         * TODO: How do we handle extra fields?
         */

        $contact = (array)$contact;

        // Remove the data that is not for display
        unset(
            $contact['tbl_prefix'], $contact['_tbl'], $contact['_tbl_key'],
            $contact['_error'], $contact['_query']
        );

        $this->data['contact'] = $contact;
        $this->data['success'] = true;

        return new Frapi_Response(array(
            'code' => 201,
            'data' => $this->data
        ));
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

