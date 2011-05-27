<?php
/**
 * Contains tests for the functionality of the tasks action
 *
 * @package web2project-api
 * @subpackage unit-tests
 *
 * @author Trevor Morse <trevor.morse@gmail.com>
 */

/**
 * Api test base class
 */
require_once 'test_base.php';


/**
 * Class for testing the functionality of the tasks action
 *
 * @package web2project-api
 * @subpackage unit-tests
 *
 * @author Trevor Morse <trevor.morse@gmail.com>
 */
class Tasks_Test extends Test_Base {

    /**
     * id of task we are working with
     *
     * @var int
     * @access private
     */
    private $task_id;

    /**
     *
     * id of project for dealing with tasks
     *
     * @var int
     * @access private
     */
    private $project_id;

    /**
     * Sets up for each test
     *
     * @access public
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->post_data = array(
            'project_contacts'          => 1,
            'project_name'              => '*API* Project Name',
            'project_parent'            => 0,
            'project_owner'             => 1,
            'project_company'           => 1,
            'project_location'          => '*API* Some Location',
            'project_start_date'        => '20100710',
            'project_end_date'          => '20100711',
            'project_target_budget'     => 15400.37,
            'project_actual_budget'     => 14000.00,
            'project_url'               => 'http://api.example.org',
            'project_demo_url'          => 'http://demo.api.example.org',
            'project_priority'          => 1,
            'project_short_name'        => '*API*',
            'project_color_identifier'  => 'AAAAAA',
            'project_type'              => 1,
            'project_status'            => 1,
            'project_description'       => '*API* long project description.',
            'project_departments'       => array(1),
            'project_active'            => 1,
            'project_creator'           => 1,
        );

        $result             = parent::makeRequest('project', array(), 'POST',  $this->post_data);
        $this->project_id   = array_pop(explode('/', $result->getHeader('location')));

        $this->post_data = array (
            'task_name'                      => '*API* Task Name',
            'task_status'                    => 0,
            'task_percent_complete'          => 50,
            'task_milestone'                 => 0,
            'task_owner'                     => 1,
            'task_access'                    => 1,
            'task_related_url'               => 'http://api.example.org',
            'task_parent'                    => 0,
            'task_type'                      => 1,
            'task_target_budget'             => 500.00,
            'task_description'               => '*API* Task Description',
            'task_start_date'                => '2011-02-22 11:00:00',
            'task_end_date'                  => '2011-02-25 11:00:00',
            'task_duration'                  => 3,
            'task_duration_type'             => 24,
            'task_dynamic'                   => 0,
            'task_allow_other_user_tasklogs' => 0,
            'task_project'                   => $this->project_id,
            'task_priority'                  => 1,
            'task_notify'                    => 0,
        );

        $result        = parent::makeRequest('/project/' . $this->project_id . '/task', array(), 'POST',  $this->post_data);
        $body          = json_decode($result->getBody());

        $this->task_id = $body->task->task_id;
    }

    /**
     * Tears down after each test
     *
     * @access public
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        parent::makeRequest('task/' . $this->task_id, array(), 'DELETE');
        parent::makeRequest('project/' . $this->project_id, array(), 'DELETE');

        unset($this->post_data, $this->project_id);
    }

    /**
     * Test getting list of tasks from the w2p-api via json
     *
     * @access public
     *
     * @return void
     */
    public function testGetNoIdJSON()
    {
        $result     = parent::makeRequest('/project/' . $this->project_id . '/task', array());
        $headers    = $result->getHeader();
        $body       = $result->getBody();

        // Check our headers
        $this->assertEquals(200,                                $result->getStatus());
        $this->assertEquals('OK',                               $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);

        $tasks = (array)json_decode($body)->tasks;

        foreach ($tasks as $task_id => $task) {
            $this->assertTrue(is_numeric($task_id));
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_OBJECT, $task);

            $this->assertObjectHasAttribute('task_id',                        $task);
            $this->assertObjectHasAttribute('task_name',                      $task);
            $this->assertObjectHasAttribute('task_milestone',                 $task);
            $this->assertObjectHasAttribute('task_project',                   $task);
            $this->assertObjectHasAttribute('task_owner',                     $task);
            $this->assertObjectHasAttribute('task_start_date',                $task);
            $this->assertObjectHasAttribute('task_duration',                  $task);
            $this->assertObjectHasAttribute('task_duration_type',             $task);
            $this->assertObjectHasAttribute('task_hours_worked',              $task);
            $this->assertObjectHasAttribute('task_end_date',                  $task);
            $this->assertObjectHasAttribute('task_status',                    $task);
            $this->assertObjectHasAttribute('task_priority',                  $task);
            $this->assertObjectHasAttribute('task_percent_complete',          $task);
            $this->assertObjectHasAttribute('task_description',               $task);
            $this->assertObjectHasAttribute('task_target_budget',             $task);
            $this->assertObjectHasAttribute('task_related_url',               $task);
            $this->assertObjectHasAttribute('task_creator',                   $task);
            $this->assertObjectHasAttribute('task_order',                     $task);
            $this->assertObjectHasAttribute('task_client_publish',            $task);
            $this->assertObjectHasAttribute('task_dynamic',                   $task);
            $this->assertObjectHasAttribute('task_access',                    $task);
            $this->assertObjectHasAttribute('task_notify',                    $task);
            $this->assertObjectHasAttribute('task_departments',               $task);
            $this->assertObjectHasAttribute('task_contacts',                  $task);
            $this->assertObjectHasAttribute('task_custom',                    $task);
            $this->assertObjectHasAttribute('task_type',                      $task);
            $this->assertObjectHasAttribute('task_created',                   $task);
            $this->assertObjectHasAttribute('task_updated',                   $task);
            $this->assertObjectHasAttribute('task_updator',                   $task);
            $this->assertObjectHasAttribute('task_allow_other_user_tasklogs', $task);
            $this->assertObjectHasAttribute('task_dep_reset_dates',           $task);
            $this->assertObjectHasAttribute('task_represents_project',        $task);
            $this->assertObjectHasAttribute('company_name',                   $task);
            $this->assertObjectHasAttribute('project_name',                   $task);
            $this->assertObjectHasAttribute('project_color_identifier',       $task);
            $this->assertObjectHasAttribute('username',                       $task);

            $this->assertTrue(is_numeric($task->task_id));
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $task->task_name);
            $this->assertTrue(is_numeric($task->task_milestone));
            $this->assertTrue(is_numeric($task->task_project));
            $this->assertTrue(is_numeric($task->task_owner));
            $this->assertRegExpOrNull('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $task->task_start_date);
            $this->assertTrue(is_numeric($task->task_duration));
            $this->assertTrue(is_numeric($task->task_duration_type));
            $this->assertTrue(is_numeric($task->task_hours_worked));
            $this->assertRegExpOrNull('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $task->task_end_date);
            $this->assertTrue(is_numeric($task->task_status));
            $this->assertTrue(is_numeric($task->task_priority));
            $this->assertTrue(is_numeric($task->task_percent_complete));
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $task->task_description);
            $this->assertTrue(is_numeric($task->task_target_budget));
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $task->task_related_url);
            $this->assertTrue(is_numeric($task->task_creator));
            $this->assertTrue(is_numeric($task->task_order));
            $this->assertTrue(is_numeric($task->task_client_publish));
            $this->assertTrue(is_numeric($task->task_dynamic));
            $this->assertTrue(is_numeric($task->task_access));
            $this->assertTrue(is_numeric($task->task_notify));
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $task->task_departments);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $task->task_contacts);
            $this->isNumericOrEmptyString($task->task_custom);
            $this->assertTrue(is_numeric($task->task_type));
            $this->assertRegExpOrNull('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $task->task_created);
            $this->assertRegExpOrNull('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $task->task_updated);
            $this->assertTrue(is_numeric($task->task_allow_other_user_tasklogs));
            $this->assertTrue(is_numeric($task->task_dep_reset_dates));
            $this->assertTrue(is_numeric($task->task_represents_project));
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $task->company_name);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $task->project_name);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $task->project_color_identifier);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $task->username);
        }
    }

    /**
     * Testing a get with invalid login
     *
     * @access public
     *
     * @return void
     */
    public function testGetNoIdInvalidLoginJSON()
    {
        $result     = parent::makeRequest('/project/' . $this->project_id . '/task', array(), 'GET', null, array('username' => '', 'password' => ''));
        $headers    = $result->getHeader();
        $body       = json_decode($result->getBody());

        $this->assertEquals(401,                                $result->getStatus());
        $this->assertEquals('Authorization Required',           $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);
        $this->assertEquals('Invalid Username or Password.',    $body->errors[0]->message);
        $this->assertEquals('INVALID_LOGIN',                    $body->errors[0]->name);
        $this->assertEquals('',                                 $body->errors[0]->at);
    }

    /**
     * Test posting a project
     *
     * @access public
     *
     * @return void
     */
    public function testPostJSON()
    {
        $result     = parent::makeRequest('/project/' . $this->project_id . '/task', array(), 'POST',  $this->post_data);
        $headers    = $result->getHeader();
        $body       = json_decode($result->getBody());

        $this->assertEquals(201,                                $result->getStatus());
        $this->assertEquals('Created',                          $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);

        $task = $body->task;

        $this->assertTrue(is_numeric($task->task_id));
        $this->assertEquals('http://w2p.api.frapi/project/' . $this->project_id . '/task/' . $task->task_id,     $headers['location']);
        $this->assertEquals(32,                                                        count(get_object_vars($task)));
        $this->assertEquals('*API* Task Name',                                         $task->task_name);
        $this->assertEquals(0,                                                         $task->task_parent);
        $this->assertEquals(0,                                                         $task->task_milestone);
        $this->assertEquals($this->project_id,                                         $task->task_project);
        $this->assertEquals(1,                                                         $task->task_owner);
        $this->assertEquals('2011-02-22 16:00:00',                                     $task->task_start_date);
        $this->assertEquals(3,                                                         $task->task_duration);
        $this->assertEquals(24,                                                        $task->task_duration_type);
        $this->assertEquals('',                                                        $task->task_hours_worked);
        $this->assertEquals('2011-02-25 16:00:00',                                     $task->task_end_date);
        $this->assertEquals(0,                                                         $task->task_status);
        $this->assertEquals(1,                                                         $task->task_priority);
        $this->assertEquals(50,                                                        $task->task_percent_complete);
        $this->assertEquals('*API* Task Description',                                  $task->task_description);
        $this->assertEquals(500,                                                       $task->task_target_budget);
        $this->assertEquals('http://api.example.org',                                  $task->task_related_url);
        $this->assertEquals(1,                                                         $task->task_creator);
        $this->assertEquals('',                                                        $task->task_order);
        $this->assertEquals('',                                                        $task->task_client_publish);
        $this->assertEquals(0,                                                         $task->task_dynamic);
        $this->assertEquals(1,                                                         $task->task_access);
        $this->assertEquals(0,                                                         $task->task_notify);
        $this->assertEquals('',                                                        $task->task_departments);
        $this->assertEquals(array(0 => ''),                                            $task->task_contacts);
        $this->assertEquals('',                                                        $task->task_custom);
        $this->assertEquals(1,                                                         $task->task_type);
        $this->assertRegExp('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $task->task_created);
        $this->assertRegExp('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $task->task_updated);
        $this->assertGreaterThanOrEqual(time() - 10,                                   strtotime($task->task_created));
        $this->assertGreaterThanOrEqual(time() - 10,                                   strtotime($task->task_updated));
        $this->assertEquals('',                                                        $task->task_updator);
        $this->assertEquals(0,                                                         $task->task_allow_other_user_tasklogs);
        $this->assertTrue($body->success);

        // Clean up after ourselves
        parent::makeRequest('task', array('task_id' => $task->task_id), 'DELETE');
    }

    /**
     * Testing a put with invalid login
     *
     * @access public
     *
     * @return void
     */
    public function testPostInvalidLoginJSON()
    {
        $result     = parent::makeRequest('/project/' . $this->project_id . '/task', array(), 'POST', null, array('username' => '', 'password' => ''));
        $headers    = $result->getHeader();
        $body       = json_decode($result->getBody());

        $this->assertEquals(401,                                $result->getStatus());
        $this->assertEquals('Authorization Required',           $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);
        $this->assertEquals('Invalid Username or Password.',    $body->errors[0]->message);
        $this->assertEquals('INVALID_LOGIN',                    $body->errors[0]->name);
        $this->assertEquals('',                                 $body->errors[0]->at);
    }

    /**
     * Testing a put with missing parameters
     *
     * @access public
     *
     * @return void
     */
    public function testPostIvalidParamsJSON()
    {
        unset(
            $this->post_data['task_priority'], $this->post_data['task_name'],
            $this->post_data['task_project'], $this->post_data['task_start_date'],
            $this->post_data['task_end_date']
        );

        $result     = parent::makeRequest('/project/' . $this->project_id . '/task', array(), 'POST',  $this->post_data);
        $headers    = $result->getHeader();
        $body       = json_decode($result->getBody());

        $this->assertEquals(401,                                $result->getStatus());
        $this->assertEquals('Authorization Required',           $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);

        $this->assertEquals(
            'CTask::store-check failed - task name is NULL. CTask::store-check failed - task start date is NULL. CTask::store-check failed - task end date is NULL. ',
            $body->errors[0]->message
        );
        $this->assertEquals('SAVE_ERROR',   $body->errors[0]->name);
        $this->assertEquals('',             $body->errors[0]->at);
    }

    /**
     *  Testing a put
     *
     * @access public
     *
     * @return void
     */
    public function testPutJSON()
    {
        $this->post_data = array (
            'task_name'                      => '*API* Task Name Updated',
            'task_status'                    => 1,
            'task_percent_complete'          => 60,
            'task_milestone'                 => 0,
            'task_owner'                     => 1,
            'task_access'                    => 2,
            'task_related_url'               => 'http://updated.api.example.org',
            'task_parent'                    => 0,
            'task_type'                      => 1,
            'task_target_budget'             => 600.00,
            'task_description'               => '*API* Task Description Updated',
            'task_start_date'                => '2011-03-06 11:00:00',
            'task_end_date'                  => '2011-03-10 11:00:00',
            'task_duration'                  => 4,
            'task_duration_type'             => 24,
            'task_dynamic'                   => 0,
            'task_allow_other_user_tasklogs' => 1,
            'task_project'                   => 1,
            'task_priority'                  => 1,
            'task_notify'                    => 1,
            'dept_ids'                       => array(1),
            'hperc_assign'                   => '1;100'
        );

        $result     = parent::makeRequest('/project/' . $this->project_id . '/task', array($this->task_id), 'PUT', $this->post_data);
        $headers    = $result->getHeader();
        $body       = json_decode($result->getBody());

        $this->assertEquals(200,                                $result->getStatus());
        $this->assertEquals('OK',                               $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);
        $task = $body->task;

        $this->assertEquals($this->task_id,                                            $task->task_id);
        $this->assertEquals(31,                                                        count(get_object_vars($task)));
        $this->assertEquals('*API* Task Name Updated',                                 $task->task_name);
        $this->assertEquals($this->task_id,                                            $task->task_parent);
        $this->assertEquals(0,                                                         $task->task_milestone);
        $this->assertEquals($this->project_id,                                         $task->task_project);
        $this->assertEquals(1,                                                         $task->task_owner);
        $this->assertEquals('2011-03-06 16:00:00',                                     $task->task_start_date);
        $this->assertEquals(4,                                                         $task->task_duration);
        $this->assertEquals(24,                                                        $task->task_duration_type);
        $this->assertEquals(0,                                                         $task->task_hours_worked);
        $this->assertEquals('2011-03-10 16:00:00',                                     $task->task_end_date);
        $this->assertEquals(1,                                                         $task->task_status);
        $this->assertEquals(1,                                                         $task->task_priority);
        $this->assertEquals(60,                                                        $task->task_percent_complete);
        $this->assertEquals('*API* Task Description Updated',                          $task->task_description);
        $this->assertEquals(600,                                                       $task->task_target_budget);
        $this->assertEquals('http://updated.api.example.org',                          $task->task_related_url);
        $this->assertEquals(1,                                                         $task->task_creator);
        $this->assertEquals(0,                                                         $task->task_order);
        $this->assertEquals(0,                                                         $task->task_client_publish);
        $this->assertEquals(0,                                                         $task->task_dynamic);
        $this->assertEquals(2,                                                         $task->task_access);
        $this->assertEquals(0,                                                         $task->task_notify);
        $this->assertEquals(1,                                                         $task->task_departments);
        $this->assertEquals(array(0 => ''),                                            $task->task_contacts);
        $this->assertEquals('',                                                        $task->task_custom);
        $this->assertEquals(1,                                                         $task->task_type);
        $this->assertRegExp('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $task->task_created);
        $this->assertRegExp('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $task->task_updated);
        $this->assertGreaterThanOrEqual(time() - 10,                                   strtotime($task->task_created));
        $this->assertGreaterThanOrEqual(time() - 10,                                   strtotime($task->task_updated));
        $this->assertEquals(0,                                                         $task->task_updator);
        $this->assertEquals(1,                                                         $task->task_allow_other_user_tasklogs);
        $this->assertTrue($body->success);
    }

    /**
     * Testing a put with invalid login
     *
     * @access public
     *
     * @return void
     */
    public function testPutInvalidLoginJSON()
    {
        $result     = parent::makeRequest('/project/' . $this->project_id . '/task', array($this->task_id), 'PUT', null, array('username' => '', 'password' => ''));
        $headers    = $result->getHeader();
        $body       = json_decode($result->getBody());

        $this->assertEquals(401,                                $result->getStatus());
        $this->assertEquals('Authorization Required',           $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);
        $this->assertEquals('Invalid Username or Password.',    $body->errors[0]->message);
        $this->assertEquals('INVALID_LOGIN',                    $body->errors[0]->name);
        $this->assertEquals('',                                 $body->errors[0]->at);
    }

    /**
     * Testing a put with missing parameters
     *
     * @access public
     *
     * @return void
     */
    public function testPutIvalidParamsJSON()
    {
        unset(
            $this->post_data['task_name'], $this->post_data['task_project']
        );

        $result     = parent::makeRequest('/project/' . $this->project_id . '/task', array($this->task_id), 'PUT',  $this->post_data);

        $headers    = $result->getHeader();
        $body       = json_decode($result->getBody());

        $this->assertEquals(401,                                $result->getStatus());
        $this->assertEquals('Authorization Required',           $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);

        $this->assertEquals(
            'CTask::store-check failed - task name is NULL. ',
            $body->errors[0]->message
        );
        $this->assertEquals('SAVE_ERROR',   $body->errors[0]->name);
        $this->assertEquals('',             $body->errors[0]->at);
    }

    /**
     * Testing delete
     *
     * @access public
     *
     * @return void
     */
    public function testDeleteJSON()
    {
        $result     = parent::makeRequest('project/' . $this->project_id . '/task', array($this->task_id), 'DELETE');
        $headers    = $result->getHeader();
        $body       = json_decode($result->getBody());

        $this->assertEquals(200,                                $result->getStatus());
        $this->assertEquals('OK',                               $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);
        $this->assertTrue($body->success);
    }

    /**
     * Testing a delete with invalid login
     *
     * @access public
     *
     * @return void
     */
    public function testDeleteInvalidLoginJSON()
    {
        $result     = parent::makeRequest('project/' . $this->project_id . '/task', array($this->project_id), 'DELETE', null, array('username' => '', 'password' => ''));
        $headers    = $result->getHeader();
        $body       = json_decode($result->getBody());

        $this->assertEquals(401,                                $result->getStatus());
        $this->assertEquals('Authorization Required',           $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);
        $this->assertEquals('Invalid Username or Password.',    $body->errors[0]->message);
        $this->assertEquals('INVALID_LOGIN',                    $body->errors[0]->name);
        $this->assertEquals('',                                 $body->errors[0]->at);
    }
}
