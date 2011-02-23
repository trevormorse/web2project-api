<?php
/**
 * This is base class for other test classes in the web2project-api.
 * It contains functionality shared between all the test classes
 *
 * @package web2project-api
 * @subpackage unit-tests
 *
 * @author Trevor Morse <trevor.morse@gmail.com>
 */

/**
 * Pear HTTP_Request2
 */
require_once 'HTTP/Request2.php';

/**
 * Base Class for web2project-api unit tests
 *
 * @package web2project-api
 * @subpackage unit-tests
 */
class Test_Base extends PHPUnit_Framework_TestCase
{

    /**
     * Data for posting/putting to api
     *
     * @access protected
     * @var array
     */
    protected $post_data;

    /**
     * Make a request to the web2project api
     *
     * @access protected
     *
     * @param  string action to be called
     * @param  array of parameters to pass to service
     * @param  string http_method type. GET, POST, PUT, DELETE, HEAD
     * @param  string type of request to make. Valid values = json, xml, html, printr, php, cli
     * @param  array of post/put vars
     * @param  array of credentials. array('username' => 'username', 'password' => 'password');
     * @param  string the base url of the tests
     *
     * @return HTTP_Request2_Response the response
     */
    protected function makeRequest($action, $parameters, $http_method = 'GET', $post_array = null, $credentials=null, $url='http://w2p.api.frapi/', $type='json')
    {
        $url .= $action . '/';

        foreach ($parameters as $param_value) {
            $url .= $param_value . '/';
        }

        // Remove excess /
        $url = substr($url, 0, strlen($url) - 1);

        // add our type
        $url .= '.' . $type;

        $http_request = new HTTP_Request2($url);

        switch (strtoupper($http_method)) {

            case 'PUT':
                $http_request->setMethod(HTTP_Request2::METHOD_PUT);
                break;

            case 'POST':
                $http_request->setMethod(HTTP_Request2::METHOD_POST);
                break;

            case 'DELETE':
                $http_request->setMethod(HTTP_Request2::METHOD_DELETE);
                break;

            case 'HEAD':
                $http_request->setMethod(HTTP_Request2::METHOD_HEAD);
                break;

            case 'GET':
            default:
                break;
        }
        $url = $http_request->getUrl();

        if (is_null($credentials)) {
            $url->setQueryVariables(array(
                'username' => 'admin',
                'password' => 'passwd'
            ));
        } else {
            $url->setQueryVariables(array(
                'username' => $credentials['username'],
                'password' => $credentials['password']
            ));
        }

        if (!is_null($post_array) && count($post_array)) {
            foreach ($post_array as $key => $value) {
                $url->setQueryVariable($key, $value);
            }
        }

        return $http_request->send();
    }

    /**
     * Tests that the value passed matches the regular expression or is null
     *
     * @access protected
     *
     * @param  string regular expression to match against
     * @param  mixed value to match
     * @param  string message to return if it doesn't match
     *
     * @return
     */
    protected function assertRegExpOrNull($reg_exp, $value, $message=null)
    {
        if (!is_null($value)) {
           return $this->assertRegExp($reg_exp, $value, $message);
        }

        return;
    }

    /**
     * Tests that the value passed is numeric or an empty string
     *
     * @access protected
     *
     * @param mixed  value to check
     * @param string message to return if if doesn't match
     *
     * @return
     */
    protected function isNumericOrEmptyString($value, $message=null)
    {
        if (strlen($value)) {
            return $this->assertTrue(is_numeric($value), $message);
        }

        return;
    }

    /**
     * Tests that the value passed matches the regular expression or is blank
     *
     * @access protected
     *
     * @param  string regular expression to match against
     * @param  mixed value to match
     * @param  string message to return if it doesn't match
     *
     * @return
     */
    protected function assertRegExpOrBlank($reg_exp, $value, $message=null)
    {
        if ($value != '') {
           return $this->assertRegExp($reg_exp, $value, $message);
        }

        return;
    }
}
?>
