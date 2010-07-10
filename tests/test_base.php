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
 * PHPUnit base class
 */
require_once 'PHPUnit/Framework.php';
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
     * Make a request to the web2project api
     *
     * @param  string action to be called
     * @param  array of parameters to pass to service
     * @param  string http_method type. GET, POST, PUT, DELETE, HEAD
     * @param  string type of request to make. Valid values = json, xml, html, printr, php, cli
     * @param  array of credentials. array('username' => 'username', 'password' => 'password');
     * @param  string the base url of the tests
     *
     * @return HTTP_Request2_Response the response
     */
    protected function makeRequest($action, $parameters, $http_method = 'GET', $credentials=null, $url='http://api.web2project.local/', $type='json')
    {
        $url .= $action . '/';

        foreach ($parameters as $param_value) {
            $url .= $param_value . '/';
        }

        // Remove excess /
        $url = substr($url, 0, strlen($url) - 1);

        // add our type
        $url .= '.' . $type;

        // Call the api and return results
        $http_request = new HTTP_Request2($url);
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

        return $http_request->send();
    }

    protected function assertRegExpOrNull($reg_exp, $value, $message=null)
    {
        if (!is_null($value)) {
           return $this->assertRegExp($reg_exp, $value, $message);
        }

        return;
    }
}
?>