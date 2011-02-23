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
     * Sets up for each test
     *
     * @access public
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

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
            'task_project'                   => 1,
            'task_priority'                  => 1,
            'task_notify'                    => 0,

        );

        $result        = parent::makeRequest('task', array(), 'PUT',  $this->post_data);
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
        $result     = parent::makeRequest('task', array());
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
}
