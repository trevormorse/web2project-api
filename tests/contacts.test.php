<?php
/**
 * Contains tests for the functionality of the contacts action
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
 * Class for testing the functionality of the contacts action
 *
 * @package web2project-api
 * @subpackage unit-tests
 *
 * @author Trevor Morse <trevor.morse@gmail.com>
 */
class Contacts_Test extends Test_Base {

    /**
     * id of contact we are working with
     *
     * @var int
     * @access private
     */
    private $contact_id;

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

        $this->post_data = array();

        // Need to have PUT done before we can populate the id, etc for the rest of the tests
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
        unset($this->post_data, $this->contact_id);
    }

    /**
     * Test getting list of contacts from the w2p-api via json
     *
     * @access public
     *
     * @return void
     */
    public function testGetNoIdJSON()
    {
        $result     = parent::makeRequest('contact', array());
        $headers    = $result->getHeader();
        $body       = $result->getBody();

        // Check our headers
        $this->assertEquals(200,                                $result->getStatus());
        $this->assertEquals('OK',                               $result->getReasonPhrase());
        $this->assertEquals('application/json; charset=utf-8',  $headers['content-type']);

        $contacts = (array)json_decode($body)->contacts;

        foreach ($contacts as $contact_id => $contact_name) {
            $this->assertTrue(is_numeric($contact_id));
            $this->assertInternalType('string', $contact_name);
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
        $result     = parent::makeRequest('contact', array(), 'GET', null, array('username' => '', 'password' => ''));
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
