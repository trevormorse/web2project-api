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
     * id of project we are working with
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

        $result             = parent::makeRequest('project', array(), 'PUT',  $this->post_data);
        $body               = json_decode($result->getBody());
        $this->project_id   = $body->project->project_id;
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
        parent::makeRequest('projects/' . $this->project_id, array(), 'DELETE');

        unset($this->post_data, $this->project_id);
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
        $result     = parent::makeRequest('project', array());
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
     * Test getting list of projects from the w2p-api via xml
     *
     * @access public
     *
     * @return void
     */
    public function testGetNoIdXML()
    {
        $result     = parent::makeRequest('project', array(), 'GET', null, null, 'http://w2p.api.frapi/', 'xml');
        $headers    = $result->getHeader();
        $body       = $result->getBody();

        // Check our headers
        $this->assertEquals(200,                                $result->getStatus());
        $this->assertEquals('OK',                               $result->getReasonPhrase());
        $this->assertEquals('application/xml; charset=utf-8',   $headers['content-type']);

        //$projects = (array)json_decode($body)->projects;
        $projects = simplexml_load_string($body);

        foreach ($projects->projects->project as $project) {
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_OBJECT, $project);

            $this->assertObjectHasAttribute('project_id',               $project);
            $this->assertObjectHasAttribute('project_color_identifier', $project);
            $this->assertObjectHasAttribute('project_name',             $project);
            $this->assertObjectHasAttribute('project_start_date',       $project);
            $this->assertObjectHasAttribute('project_end_date',         $project);
            $this->assertObjectHasAttribute('project_company',          $project);

            $this->assertTrue(is_numeric((string)$project->project_id));
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING,                     (string)$project->project_color_identifier);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING,                     (string)$project->project_name);
            $this->assertRegExpOrBlank('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',   (string)$project->project_start_date);
            $this->assertRegExpOrBlank('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',   (string)$project->project_end_date);
            $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING,                     (string)$project->project_name);
        }

        $this->assertEquals(1, (int)$projects->success);
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
        $result     = parent::makeRequest('project', array(), 'GET', null, array('username' => '', 'password' => ''));
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
     * Testing a get with invalid login
     *
     * @access public
     *
     * @return void
     */
    public function testGetNoIdInvalidLoginXML()
    {
        $result     = parent::makeRequest('project', array(), 'GET', null, array('username' => '', 'password' => ''), 'http://w2p.api.frapi/', 'xml');
        $headers    = $result->getHeader();
        $body       = $result->getBody();
        $xml        = simplexml_load_string($body);

        $this->assertEquals(401,                                $result->getStatus());
        $this->assertEquals('Authorization Required',           $result->getReasonPhrase());
        $this->assertEquals('application/xml; charset=utf-8',   $headers['content-type']);
        $this->assertEquals('Invalid Username or Password.',    (string)$xml->errors->error->message);
        $this->assertEquals('INVALID_LOGIN',                    (string)$xml->errors->error->name);
        $this->assertEquals('',                                 (string)$xml->errors->error->at);
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
        $result     = parent::makeRequest('project', array(), 'PUT',  $this->post_data);
        $headers    = $result->getHeader();
        $body       = json_decode($result->getBody());

        $this->assertEquals(201,                                $result->getStatus());
        $this->assertEquals('Created',                          $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);

        $project = $body->project;

        $this->assertTrue(is_numeric($project->project_id));
        $this->assertEquals(32,                                                         count(get_object_vars($project)));
        $this->assertEquals(1,                                                          $project->project_company);
        $this->assertEquals(array(1),                                                   $project->project_departments);
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
        $this->assertEquals(array(1),                                                   $project->project_departments);
        $this->assertEquals(1,                                                          $project->project_contacts);
        $this->assertEquals(1,                                                          $project->project_priority);
        $this->assertEquals(1,                                                          $project->project_type);
        $this->assertEquals($project->project_id,                                       $project->project_parent);
        $this->assertEquals($project->project_id,                                       $project->project_original_parent);
        $this->assertEquals('*API* Some Location',                                      $project->project_location);
        $this->assertRegExp('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',  $project->project_updated);
        $this->assertRegExp('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',  $project->project_created);
        $this->assertTrue($body->success);

        // Clean up after ourselves
        parent::makeRequest('projects/' . $project->project_id, array(), 'DELETE');
    }

    /**
     * Test putting a project
     *
     * @access public
     *
     * @return void
     */
    public function testPutXML()
    {
        $result     = parent::makeRequest('project', array(), 'PUT',  $this->post_data, null, 'http://w2p.api.frapi/', 'xml');
        $headers    = $result->getHeader();
        $body       = $result->getBody();

        $this->assertEquals(201,                                $result->getStatus());
        $this->assertEquals('Created',                          $result->getReasonPhrase());
        $this->assertEquals('application/xml; charset=utf-8',   $headers['content-type']);

        $project = simplexml_load_string($body)->project;

        $this->assertTrue(is_numeric((string)$project->project_id));
        $this->assertEquals(32,                                                         count(get_object_vars($project)));
        $this->assertEquals(1,                                                          (string)$project->project_company);
        $this->assertEquals(array('project_department' => 1),                           (array)$project->project_departments);
        $this->assertEquals('*API* Project Name',                                       (string)$project->project_name);
        $this->assertEquals('*API*',                                                    (string)$project->project_short_name);
        $this->assertEquals(1,                                                          (string)$project->project_owner);
        $this->assertEquals('http://api.example.org',                                   (string)$project->project_url);
        $this->assertEquals('http://demo.api.example.org',                              (string)$project->project_demo_url);
        $this->assertEquals('2010-07-10 00:00:00',                                      (string)$project->project_start_date);
        $this->assertEquals('2010-07-11 23:59:59',                                      (string)$project->project_end_date);
        $this->assertEquals('',                                                         (string)$project->project_actual_end_date);
        $this->assertEquals(1,                                                          (string)$project->project_status);
        $this->assertEquals('',                                                         (string)$project->project_percent_complete);
        $this->assertEquals('AAAAAA',                                                   (string)$project->project_color_identifier);
        $this->assertEquals('*API* long project description.',                          (string)$project->project_description);
        $this->assertEquals(15400.37,                                                   (string)$project->project_target_budget);
        $this->assertEquals(14000,                                                      (string)$project->project_actual_budget);
        $this->assertEquals('',                                                         (string)$project->project_scheduled_hours);
        $this->assertEquals('',                                                         (string)$project->project_worked_hours);
        $this->assertEquals('',                                                         (string)$project->project_task_count);
        $this->assertEquals(1,                                                          (string)$project->project_creator);
        $this->assertEquals(1,                                                          (string)$project->project_active);
        $this->assertEquals(0,                                                          (string)$project->project_private);
        $this->assertEquals(1,                                                          (string)$project->project_contacts);
        $this->assertEquals(1,                                                          (string)$project->project_priority);
        $this->assertEquals(1,                                                          (string)$project->project_type);
        $this->assertEquals((string)$project->project_id,                               (string)$project->project_parent);
        $this->assertEquals((string)$project->project_id,                               (string)$project->project_original_parent);
        $this->assertEquals('*API* Some Location',                                      (string)$project->project_location);
        $this->assertRegExp('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',  (string)$project->project_updated);
        $this->assertRegExp('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',  (string)$project->project_created);
        $this->assertEquals(1,                                                          (string)simplexml_load_string($body)->success);

        // Clean up after ourselves
        parent::makeRequest('projects/' . $project->project_id, array(), 'DELETE');
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
        $result     = parent::makeRequest('project', array(), 'PUT', null, array('username' => '', 'password' => ''));
        $headers    = $result->getHeader();
        $body       = json_decode($result->getBody());

        $this->assertEquals(401,                                $result->getStatus());
        $this->assertEquals('Authorization Required',           $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);
        $this->assertEquals('Invalid Username or Password.',    $body->errors[0]->message);
        $this->assertEquals('INVALID_LOGIN',                    $body->errors[0]->name);
        $this->assertEquals('',                                 $body->errors[0]->at);
    }

    /*
     * Testing a put with invalid login
     *
     * @access public
     *
     * @return void
     */
    public function testPutInvalidLoginXML()
    {
        $result     = parent::makeRequest('project', array(), 'PUT', null, array('username' => '', 'password' => ''), 'http://w2p.api.frapi/', 'xml');
        $headers    = $result->getHeader();
        $body       = simplexml_load_string($result->getBody())->errors->error;

        $this->assertEquals(401,                                $result->getStatus());
        $this->assertEquals('Authorization Required',           $result->getReasonPhrase());
        $this->assertEquals('application/xml; charset=utf-8',   $headers['content-type']);
        $this->assertEquals('Invalid Username or Password.',    (string)$body->message);
        $this->assertEquals('INVALID_LOGIN',                    (string)$body->name);
        $this->assertEquals('',                                 (string)$body->at);
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
            $this->post_data['project_owner'], $this->post_data['project_priority'],
            $this->post_data['project_color_identifier'], $this->post_data['project_type'],
            $this->post_data['project_status']
        );

        $result     = parent::makeRequest('project', array(), 'PUT',  $this->post_data);
        $headers    = $result->getHeader();
        $body       = json_decode($result->getBody());

        $this->assertEquals(401,                                $result->getStatus());
        $this->assertEquals('Authorization Required',           $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);

        $this->assertEquals(
            'CProject::store-check failed - project name is not set. CProject::store-check failed - project short name is not set. CProject::store-check failed - project owner is not set. CProject::store-check failed - project priority is not set. CProject::store-check failed - project color identifier is not set. CProject::store-check failed - project type is not set. CProject::store-check failed - project status is not set. ',
            $body->errors[0]->message
        );
        $this->assertEquals('SAVE_ERROR',   $body->errors[0]->name);
        $this->assertEquals('',             $body->errors[0]->at);
    }

    /**
     * Testing a put with missing parameters
     *
     * @access public
     *
     * @return void
     */
    public function testPutIvalidParamsXML()
    {
        unset(
            $this->post_data['project_name'], $this->post_data['project_short_name'],
            $this->post_data['project_owner'], $this->post_data['project_priority'],
            $this->post_data['project_color_identifier'], $this->post_data['project_type'],
            $this->post_data['project_status']
        );

        $result     = parent::makeRequest('project', array(), 'PUT',  $this->post_data, null, 'http://w2p.api.frapi/', 'xml');
        $headers    = $result->getHeader();
        $body       = simplexml_load_string($result->getBody())->errors->error;

        $this->assertEquals(401,                                $result->getStatus());
        $this->assertEquals('Authorization Required',           $result->getReasonPhrase());
        $this->assertEquals('application/xml; charset=utf-8',   $headers['content-type']);

        $this->assertEquals(
            'CProject::store-check failed - project name is not set. CProject::store-check failed - project short name is not set. CProject::store-check failed - project owner is not set. CProject::store-check failed - project priority is not set. CProject::store-check failed - project color identifier is not set. CProject::store-check failed - project type is not set. CProject::store-check failed - project status is not set. ',
            (string)$body->message
        );
        $this->assertEquals('SAVE_ERROR',   (string)$body->name);
        $this->assertEquals('',             (string)$body->at);
    }

    /**
     * Test getting list of projects from the w2p-api via json
     *
     * @access public
     *
     * @return void
     */
    public function testGetJSON()
    {
        $result     = parent::makeRequest('project', array($this->project_id));
        $headers    = $result->getHeader();
        $body       = $result->getBody();

        // Check our headers
        $this->assertEquals(200,                                $result->getStatus());
        $this->assertEquals('OK',                               $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);

        $project = json_decode($body)->project;

        $this->assertEquals($this->project_id,                  $project->project_id);
        $this->assertEquals(1,                                  $project->project_company);
        $this->assertEquals('*API* Project Name',               $project->project_name);
        $this->assertEquals('*API*',                            $project->project_short_name);
        $this->assertEquals(1,                                  $project->project_owner);
        $this->assertEquals('http://api.example.org',           $project->project_url);
        $this->assertEquals('http://demo.api.example.org',      $project->project_demo_url);
        $this->assertEquals('2010-07-10 00:00:00',              $project->project_start_date);
        $this->assertEquals('2010-07-11 23:59:59',              $project->project_end_date);
        $this->assertEquals('',                                 $project->project_actual_end_date);
        $this->assertEquals(1,                                  $project->project_status);
        $this->assertEquals(0,                                  $project->project_percent_complete);
        $this->assertEquals('AAAAAA',                           $project->project_color_identifier);
        $this->assertEquals('*API* long project description.',  $project->project_description);
        $this->assertEquals(15400.37,                           $project->project_target_budget);
        $this->assertEquals(14000.00,                           $project->project_actual_budget);
        $this->assertEquals(0,                                  $project->project_scheduled_hours);
        $this->assertEquals(0,                                  $project->project_worked_hours);
        $this->assertEquals(0,                                  $project->project_task_count);
        $this->assertEquals(1,                                  $project->project_creator);
        $this->assertEquals(1,                                  $project->project_active);
        $this->assertEquals(0,                                  $project->project_private);
        $this->assertEquals(array(1),                           $project->project_departments);
        $this->assertEquals(1,                                  $project->project_contacts);
        $this->assertEquals(1,                                  $project->project_priority);
        $this->assertEquals(1,                                  $project->project_type);
        $this->assertEquals($this->project_id,                  $project->project_parent);
        $this->assertEquals($this->project_id,                  $project->project_original_parent);
        $this->assertEquals('*API* Some Location',              $project->project_location);
        $this->assertTrue(json_decode($body)->success);

    }

    /**
     * Test getting list of projects from the w2p-api via xml
     *
     * @access public
     *
     * @return void
     */
    public function testGetXML()
    {
        $result     = parent::makeRequest('project', array($this->project_id), 'GET', null, null, 'http://w2p.api.frapi/', 'xml');
        $headers    = $result->getHeader();
        $body       = $result->getBody();

        // Check our headers
        $this->assertEquals(200,                                $result->getStatus());
        $this->assertEquals('OK',                               $result->getReasonPhrase());
        $this->assertEquals('application/xml; charset=utf-8',   $headers['content-type']);

        $project = simplexml_load_string($body)->project;

        $this->assertEquals($this->project_id,                  (string)$project->project_id);
        $this->assertEquals(1,                                  (string)$project->project_company);
        $this->assertEquals('*API* Project Name',               (string)$project->project_name);
        $this->assertEquals('*API*',                            (string)$project->project_short_name);
        $this->assertEquals(1,                                  (string)$project->project_owner);
        $this->assertEquals('http://api.example.org',           (string)$project->project_url);
        $this->assertEquals('http://demo.api.example.org',      (string)$project->project_demo_url);
        $this->assertEquals('2010-07-10 00:00:00',              (string)$project->project_start_date);
        $this->assertEquals('2010-07-11 23:59:59',              (string)$project->project_end_date);
        $this->assertEquals('',                                 (string)$project->project_actual_end_date);
        $this->assertEquals(1,                                  (string)$project->project_status);
        $this->assertEquals(0,                                  (string)$project->project_percent_complete);
        $this->assertEquals('AAAAAA',                           (string)$project->project_color_identifier);
        $this->assertEquals('*API* long project description.',  (string)$project->project_description);
        $this->assertEquals(15400.37,                           (string)$project->project_target_budget);
        $this->assertEquals(14000.00,                           (string)$project->project_actual_budget);
        $this->assertEquals(0,                                  (string)$project->project_scheduled_hours);
        $this->assertEquals(0,                                  (string)$project->project_worked_hours);
        $this->assertEquals(0,                                  (string)$project->project_task_count);
        $this->assertEquals(1,                                  (string)$project->project_creator);
        $this->assertEquals(1,                                  (string)$project->project_active);
        $this->assertEquals(0,                                  (string)$project->project_private);
        $this->assertEquals(array('project_department' => 1),   (array)$project->project_departments);
        $this->assertEquals(1,                                  (string)$project->project_contacts);
        $this->assertEquals(1,                                  (string)$project->project_priority);
        $this->assertEquals(1,                                  (string)$project->project_type);
        $this->assertEquals($this->project_id,                  (string)$project->project_parent);
        $this->assertEquals($this->project_id,                  (string)$project->project_original_parent);
        $this->assertEquals('*API* Some Location',              (string)$project->project_location);
        $this->assertEquals(1,                                  (string)simplexml_load_string($body)->success);

    }

    /**
     * Testing a get with invalid login
     *
     * @access public
     *
     * @return void
     */
    public function testGetInvalidLoginJSON()
    {
        $result     = parent::makeRequest('project', array($this->project_id), 'GET', null, array('username' => '', 'password' => ''));
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
     * Testing a get with invalid login
     *
     * @access public
     *
     * @return void
     */
    public function testGetInvalidLoginXML()
    {
        $result     = parent::makeRequest('project', array($this->project_id), 'GET', null, array('username' => '', 'password' => ''), 'http://w2p.api.frapi/', 'xml');
        $headers    = $result->getHeader();
        $body       = simplexml_load_string($result->getBody())->errors->error;

        $this->assertEquals(401,                                $result->getStatus());
        $this->assertEquals('Authorization Required',           $result->getReasonPhrase());
        $this->assertEquals('application/xml; charset=utf-8',   $headers['content-type']);
        $this->assertEquals('Invalid Username or Password.',    (string)$body->message);
        $this->assertEquals('INVALID_LOGIN',                    (string)$body->name);
        $this->assertEquals('',                                 (string)$body->at);
    }

    /**
     *  Testing a post
     *
     * @access public
     *
     * @return void
     */
    public function testPostJSON()
    {
        $this->post_data = array(
            'project_id'                => $this->project_id,
            'project_contacts'          => 2,
            'project_name'              => '*API* Project Name Updated',
            'project_parent'            => 1,
            'project_owner'             => 2,
            'project_company'           => 1,
            'project_location'          => '*API* Some Location Updated',
            'project_start_date'        => '20100713',
            'project_end_date'          => '20100714',
            'project_target_budget'     => 15400.00,
            'project_actual_budget'     => 14000.37,
            'project_url'               => 'http://updated.api.example.org',
            'project_demo_url'          => 'http://updated.demo.api.example.org',
            'project_priority'          => 0,
            'project_short_name'        => '*APIU*',
            'project_color_identifier'  => 'ABBBBB',
            'project_type'              => 2,
            'project_status'            => 2,
            'project_description'       => '*API* long project description updated.',
            'project_departments'       => array(2),
            'project_active'            => 0,
            'project_creator'           => 1
        );

        $result     = parent::makeRequest('project', array($this->project_id), 'POST', $this->post_data);
        $headers    = $result->getHeader();
        $body       = json_decode($result->getBody());

        $this->assertEquals(200,                                $result->getStatus());
        $this->assertEquals('OK',                               $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);
        $project = $body->project;

        $this->assertEquals($this->project_id,                                                  $project->project_id);
        $this->assertEquals(1,                                                                  $project->project_company);
        $this->assertEquals(array(2),                                                           $project->project_departments);
        $this->assertEquals('*API* Project Name Updated',                                       $project->project_name);
        $this->assertEquals('*APIU*',                                                           $project->project_short_name);
        $this->assertEquals(2,                                                                  $project->project_owner);
        $this->assertEquals('http://updated.api.example.org',                                   $project->project_url);
        $this->assertEquals('http://updated.demo.api.example.org',                              $project->project_demo_url);
        $this->assertEquals('2010-07-13 00:00:00',                                              $project->project_start_date);
        $this->assertEquals('2010-07-14 23:59:59',                                              $project->project_end_date);
        $this->assertEquals('',                                                                 $project->project_actual_end_date);
        $this->assertEquals(2,                                                                  $project->project_status);
        $this->assertEquals('',                                                                 $project->project_percent_complete);
        $this->assertEquals('ABBBBB',                                                           $project->project_color_identifier);
        $this->assertEquals('*API* long project description updated.',                          $project->project_description);
        $this->assertEquals(15400,                                                              $project->project_target_budget);
        $this->assertEquals(14000.37,                                                           $project->project_actual_budget);
        $this->assertEquals('',                                                                 $project->project_scheduled_hours);
        $this->assertEquals('',                                                                 $project->project_worked_hours);
        $this->assertEquals('',                                                                 $project->project_task_count);
        $this->assertEquals(1,                                                                  $project->project_creator);
        $this->assertEquals(0,                                                                  $project->project_active);
        $this->assertEquals(2,                                                                  $project->project_contacts);
        $this->assertEquals(0,                                                                  $project->project_priority);
        $this->assertEquals(2,                                                                  $project->project_type);
        $this->assertEquals(1,                                                                  $project->project_parent);
        $this->assertEquals($this->project_id,                                                  $project->project_original_parent);
        $this->assertEquals('*API* Some Location Updated',                                      $project->project_location);
        $this->assertRegExpOrNull('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',    $project->project_updated);
    }

    /**
     *  Testing a post
     *
     * @access public
     *
     * @return void
     */
    public function testPostXML()
    {
        $this->post_data = array(
            'project_id'                => $this->project_id,
            'project_contacts'          => 2,
            'project_name'              => '*API* Project Name Updated',
            'project_parent'            => 1,
            'project_owner'             => 2,
            'project_company'           => 1,
            'project_location'          => '*API* Some Location Updated',
            'project_start_date'        => '20100713',
            'project_end_date'          => '20100714',
            'project_target_budget'     => 15400.00,
            'project_actual_budget'     => 14000.37,
            'project_url'               => 'http://updated.api.example.org',
            'project_demo_url'          => 'http://updated.demo.api.example.org',
            'project_priority'          => 0,
            'project_short_name'        => '*APIU*',
            'project_color_identifier'  => 'ABBBBB',
            'project_type'              => 2,
            'project_status'            => 2,
            'project_description'       => '*API* long project description updated.',
            'project_departments'       => array(2),
            'project_active'            => 0,
            'project_creator'           => 1,
        );

        $result     = parent::makeRequest('project', array($this->project_id), 'POST', $this->post_data, null, 'http://w2p.api.frapi/', 'xml');
        $headers    = $result->getHeader();
        $body       = $result->getBody();

        $this->assertEquals(200,                                $result->getStatus());
        $this->assertEquals('OK',                               $result->getReasonPhrase());
        $this->assertEquals('application/xml; charset=utf-8',  $headers['content-type']);

        $project = simplexml_load_string($body)->project;

        $this->assertEquals($this->project_id,                                                  (string)$project->project_id);
        $this->assertEquals(1,                                                                  (string)$project->project_company);
        $this->assertEquals(array('project_department' => 2),                                   (array)$project->project_departments);
        $this->assertEquals('*API* Project Name Updated',                                       (string)$project->project_name);
        $this->assertEquals('*APIU*',                                                           (string)$project->project_short_name);
        $this->assertEquals(2,                                                                  (string)$project->project_owner);
        $this->assertEquals('http://updated.api.example.org',                                   (string)$project->project_url);
        $this->assertEquals('http://updated.demo.api.example.org',                              (string)$project->project_demo_url);
        $this->assertEquals('2010-07-13 00:00:00',                                              (string)$project->project_start_date);
        $this->assertEquals('2010-07-14 23:59:59',                                              (string)$project->project_end_date);
        $this->assertEquals('',                                                                 (string)$project->project_actual_end_date);
        $this->assertEquals(2,                                                                  (string)$project->project_status);
        $this->assertEquals('',                                                                 (string)$project->project_percent_complete);
        $this->assertEquals('ABBBBB',                                                           (string)$project->project_color_identifier);
        $this->assertEquals('*API* long project description updated.',                          (string)$project->project_description);
        $this->assertEquals(15400,                                                              (string)$project->project_target_budget);
        $this->assertEquals(14000.37,                                                           (string)$project->project_actual_budget);
        $this->assertEquals('',                                                                 (string)$project->project_scheduled_hours);
        $this->assertEquals('',                                                                 (string)$project->project_worked_hours);
        $this->assertEquals('',                                                                 (string)$project->project_task_count);
        $this->assertEquals(1,                                                                  (string)$project->project_creator);
        $this->assertEquals(0,                                                                  (string)$project->project_active);
        $this->assertEquals(2,                                                                  (string)$project->project_contacts);
        $this->assertEquals(0,                                                                  (string)$project->project_priority);
        $this->assertEquals(2,                                                                  (string)$project->project_type);
        $this->assertEquals(1,                                                                  (string)$project->project_parent);
        $this->assertEquals($this->project_id,                                                  (string)$project->project_original_parent);
        $this->assertEquals('*API* Some Location Updated',                                      (string)$project->project_location);
        $this->assertRegExpOrNull('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',    (string)$project->project_updated);
        $this->assertEquals(1,                                                                  (string)simplexml_load_string($body)->success);
    }

    /**
     * Testing a post with invalid login
     *
     * @access public
     *
     * @return void
     */
    public function testPostInvalidLoginJSON()
    {
        $result     = parent::makeRequest('project', array($this->project_id), 'POST', null, array('username' => '', 'password' => ''));
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
     * Testing a post with invalid login
     *
     * @access public
     *
     * @return void
     */
    public function testPostInvalidLoginXML()
    {
        $result     = parent::makeRequest('project', array($this->project_id), 'POST', null, array('username' => '', 'password' => ''), 'http://w2p.api.frapi/', 'xml');
        $headers    = $result->getHeader();
        $body       = simplexml_load_string($result->getBody())->errors->error;

        $this->assertEquals(401,                                $result->getStatus());
        $this->assertEquals('Authorization Required',           $result->getReasonPhrase());
        $this->assertEquals('application/xml; charset=utf-8',   $headers['content-type']);
        $this->assertEquals('Invalid Username or Password.',    (string)$body->message);
        $this->assertEquals('INVALID_LOGIN',                    (string)$body->name);
        $this->assertEquals('',                                 (string)$body->at);
    }

    /**
     * Testing a post with missing parameters
     *
     * @access public
     *
     * @return void
     */
    public function testPostIvalidParamsJSON()
    {
        unset(
            $this->post_data['project_name'], $this->post_data['project_short_name'],
            $this->post_data['project_owner'], $this->post_data['project_priority'],
            $this->post_data['project_color_identifier'], $this->post_data['project_type'],
            $this->post_data['project_status']
        );

        $result     = parent::makeRequest('project', array($this->project_id), 'POST',  $this->post_data);
        $headers    = $result->getHeader();
        $body       = json_decode($result->getBody());

        $this->assertEquals(401,                                $result->getStatus());
        $this->assertEquals('Authorization Required',           $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);

        $this->assertEquals(
            'CProject::store-check failed - project name is not set. CProject::store-check failed - project short name is not set. CProject::store-check failed - project owner is not set. CProject::store-check failed - project priority is not set. CProject::store-check failed - project color identifier is not set. CProject::store-check failed - project type is not set. CProject::store-check failed - project status is not set. ',
            $body->errors[0]->message
        );
        $this->assertEquals('SAVE_ERROR',   $body->errors[0]->name);
        $this->assertEquals('',             $body->errors[0]->at);
    }

    /**
     * Testing a post with missing parameters
     *
     * @access public
     *
     * @return void
     */
    public function testPostIvalidParamsXML()
    {
        unset(
            $this->post_data['project_name'], $this->post_data['project_short_name'],
            $this->post_data['project_owner'], $this->post_data['project_priority'],
            $this->post_data['project_color_identifier'], $this->post_data['project_type'],
            $this->post_data['project_status']
        );

        $result     = parent::makeRequest('project', array($this->project_id), 'POST',  $this->post_data, null, 'http://w2p.api.frapi/', 'xml');
        $headers    = $result->getHeader();
        $body       = simplexml_load_string($result->getBody())->errors->error;

        $this->assertEquals(401,                                $result->getStatus());
        $this->assertEquals('Authorization Required',           $result->getReasonPhrase());
        $this->assertEquals('application/xml; charset=utf-8',   $headers['content-type']);

        $this->assertEquals(
            'CProject::store-check failed - project name is not set. CProject::store-check failed - project short name is not set. CProject::store-check failed - project owner is not set. CProject::store-check failed - project priority is not set. CProject::store-check failed - project color identifier is not set. CProject::store-check failed - project type is not set. CProject::store-check failed - project status is not set. ',
            (string)$body->message
        );
        $this->assertEquals('SAVE_ERROR',   (string)$body->name);
        $this->assertEquals('',             (string)$body->at);
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
        $result     = parent::makeRequest('project', array($this->project_id), 'DELETE');
        $headers    = $result->getHeader();
        $body       = json_decode($result->getBody());

        $this->assertEquals(200,                                $result->getStatus());
        $this->assertEquals('OK',                               $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);
        $this->assertTrue($body->success);
    }

    /**
     * Testing delete
     *
     * @access public
     *
     * @return void
     */
    public function testDeleteXML()
    {
        $result     = parent::makeRequest('project', array($this->project_id), 'DELETE', null, null, 'http://w2p.api.frapi/', 'xml');
        $headers    = $result->getHeader();
        $body       = $result->getBody();
        $body       = simplexml_load_string($body);

        $this->assertEquals(200,                                $result->getStatus());
        $this->assertEquals('OK',                               $result->getReasonPhrase());
        $this->assertEquals('application/xml; charset=utf-8',   $headers['content-type']);
        $this->assertEquals(1,                                  (int)$body->success);
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
        $result     = parent::makeRequest('project', array($this->project_id), 'DELETE', null, array('username' => '', 'password' => ''));
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
     * Testing a delete with invalid login
     *
     * @access public
     *
     * @return void
     */
    public function testDeleteInvalidLoginXML()
    {
        $result     = parent::makeRequest('project', array($this->project_id), 'DELETE', null, array('username' => '', 'password' => ''), 'http://w2p.api.frapi/', 'xml');
        $headers    = $result->getHeader();
        $body       = simplexml_load_string($result->getBody())->errors->error;

        $this->assertEquals(401,                                $result->getStatus());
        $this->assertEquals('Authorization Required',           $result->getReasonPhrase());
        $this->assertEquals('application/xml; charset=utf-8',   $headers['content-type']);
        $this->assertEquals('Invalid Username or Password.',    (string)$body->message);
        $this->assertEquals('INVALID_LOGIN',                    (string)$body->name);
        $this->assertEquals('',                                 (string)$body->at);
    }
}
?>
