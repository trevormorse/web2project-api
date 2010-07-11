<?php
/**
 * Contains tests for the functionality of the projects action
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
 * Class for testing the functionality of the projects action
 *
 * @package web2project-api
 * @subpackage unit-tests
 *
 * @author Trevor Morse <trevor.morse@gmail.com>
 */
class Projects_Test extends Test_Base {

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
            'project_department'        => 1,
            'project_active'            => 1,
        );
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

        unset($this->post_data);
    }

    /**
     * Test getting list of projects from the w2p-api via json
     *
     * @access public
     *
     * @return void
     */
    public function testGetNoIdJSON()
    {
        $result     = parent::makeRequest('projects', array());
        $headers    = $result->getHeader();
        $body       = $result->getBody();

        // Check our headers
        $this->assertEquals(200,                                $result->getStatus());
        $this->assertEquals('OK',                               $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);

        $projects = (array)json_decode($body)->projects;

        foreach ($projects as $project_id => $project) {
            $this->assertTrue(is_numeric($project_id));
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_OBJECT, $project);

            $this->assertObjectHasAttribute('project_id',               $project);
            $this->assertObjectHasAttribute('project_color_identifier', $project);
            $this->assertObjectHasAttribute('project_name',             $project);
            $this->assertObjectHasAttribute('project_start_date',       $project);
            $this->assertObjectHasAttribute('project_end_date',         $project);
            $this->assertObjectHasAttribute('project_company',          $project);
            $this->assertObjectHasAttribute('0',                        $project);
            $this->assertObjectHasAttribute('1',                        $project);
            $this->assertObjectHasAttribute('2',                        $project);
            $this->assertObjectHasAttribute('3',                        $project);
            $this->assertObjectHasAttribute('4',                        $project);
            $this->assertObjectHasAttribute('5',                        $project);

            $this->assertTrue(is_numeric($project->project_id));
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING,                     $project->project_color_identifier);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING,                     $project->project_name);
            $this->assertRegExpOrNull('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',    $project->project_start_date);
            $this->assertRegExpOrNull('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',    $project->project_end_date);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING,                     $project->project_name);
            $this->assertTrue(is_numeric($project->{0}));
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING,                     $project->{1});
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING,                     $project->{2});
            $this->assertRegExpOrNull('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',    $project->{3});
            $this->assertRegExpOrNull('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',    $project->{4});
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING,                     $project->{5});
        }

        $this->assertTrue(json_decode($body)->success);
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
        $result     = parent::makeRequest('projects', array(), 'GET', null, array());
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
     * Test putting a project
     *
     * @access public
     *
     * @return void
     */
    public function testPutJSON()
    {
        $result     = parent::makeRequest('projects', array(), 'PUT',  $this->post_data);
        $headers    = $result->getHeader();
        $body       = json_decode($result->getBody());

        $this->assertEquals(201,                                $result->getStatus());
        $this->assertEquals('Created',                          $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);

        $project = $body->project;

        $this->assertTrue(is_numeric($project->project_id));
        $this->assertEquals(32,                                                         count(get_object_vars($project)));
        $this->assertEquals(1,                                                          $project->project_company);
        $this->assertEquals(1,                                                          $project->project_department);
        $this->assertEquals('*API* Project Name',                                       $project->project_name);
        $this->assertEquals('*API*',                                                    $project->project_short_name);
        $this->assertEquals(1,                                                          $project->project_owner);
        $this->assertEquals('http://api.example.org',                                   $project->project_url);
        $this->assertEquals('http://demo.api.example.org',                              $project->project_demo_url);
        $this->assertEquals('2010-07-10 00:00:00',                                      $project->project_start_date);
        $this->assertEquals('2010-07-11 23:59:59',                                      $project->project_end_date);
        $this->assertEquals('',                                                         $project->project_actual_end_date);
        $this->assertEquals(1,                                                          $project->project_status);
        $this->assertEquals('',                                                         $project->project_percent_complete);
        $this->assertEquals('AAAAAA',                                                   $project->project_color_identifier);
        $this->assertEquals('*API* long project description.',                          $project->project_description);
        $this->assertEquals(15400.37,                                                   $project->project_target_budget);
        $this->assertEquals(14000,                                                      $project->project_actual_budget);
        $this->assertEquals('',                                                         $project->project_scheduled_hours);
        $this->assertEquals('',                                                         $project->project_worked_hours);
        $this->assertEquals('',                                                         $project->project_task_count);
        $this->assertEquals(1,                                                          $project->project_creator);
        $this->assertEquals(1,                                                          $project->project_active);
        $this->assertEquals(0,                                                          $project->project_private);
        $this->assertEquals('',                                                         $project->project_departments);
        $this->assertEquals(1,                                                          $project->project_contacts);
        $this->assertEquals(1,                                                          $project->project_priority);
        $this->assertEquals(1,                                                          $project->project_type);
        $this->assertEquals($project->project_id,                                       $project->project_parent);
        $this->assertEquals($project->project_id,                                       $project->project_original_parent);
        $this->assertEquals('*API* Some Location',                                      $project->project_location);
        $this->assertRegExp('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',  $project->project_updated);
        $this->assertRegExp('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',  $project->project_created);
        $this->assertTrue($body->success);
    }

    /**
     * Testing a get with invalid login
     *
     * @access public
     *
     * @return void
     */
    public function testPutInvalidLoginJSON()
    {
        $result     = parent::makeRequest('projects', array(), 'PUT', null, array());
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
            $this->post_data['project_name'], $this->post_data['project_short_name'],
            $this->post_data['project_owner'], $this->post_data['project_owner'],
            $this->post_data['project_creator'], $this->post_data['project_priority'],
            $this->post_data['project_color_identifier'], $this->post_data['project_type'],
            $this->post_data['project_status'], $this->post_data['project_url'],
            $this->post_data['project_demo_url']
        );

        $result     = parent::makeRequest('projects', array(), 'PUT',  $this->post_data);
        $headers    = $result->getHeader();
        $body       = json_decode($result->getBody());

        $this->assertEquals(401,                                $result->getStatus());
        $this->assertEquals('Authorization Required',           $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);

        $this->assertEquals(
            'CProject::store-check failed - project name is not set. CProject::store-check failed - project short name is not set. CProject::store-check failed - project owner is not set. CProject::store-check failed - project color identifier is not set. ',
            $body->errors[0]->message
        );
        $this->assertEquals('SAVE_ERROR',   $body->errors[0]->name);
        $this->assertEquals('',             $body->errors[0]->at);
    }
}
?>