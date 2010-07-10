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
     * Test getting list of projects from the w2p-api via json
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
    }
}
?>