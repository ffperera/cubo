<?php

use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testRequest()
    {
        $_GET['key'] = 'value';
        $_POST['key'] = 'value';
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_COOKIE['key'] = 'value';


        $_SERVER['REQUEST_URI'] = '/some/route/21/';
        $request = new \FFPerera\Cubo\Request();

        // Test get method
        $this->assertEquals('value', $request->query('key'));

        // Test post method
        $this->assertEquals('value', $request->post('key'));

        // Test server method
        $this->assertEquals('localhost', $request->server('HTTP_HOST'));

        // Test cookie method
        $this->assertEquals('value', $request->cookie('key'));

        // Test method
        $this->assertEquals('GET', $request->method());

        // Test getQueryString method
        $this->assertEquals('', $request->getQueryString());

        // test getPath method
        $this->assertEquals('/some/route/21/', $request->getPath());

        $_SERVER['REQUEST_URI'] = '/';
    }

    public function testSetAndGetQueryVariables()
    {
        $request = new \FFPerera\Cubo\Request();

        // Set query variables
        $request->setQuery('key1', 'value1');
        $request->setQuery('key2', 'value2');

        // Get query variables
        $this->assertEquals('value1', $request->query('key1'));
        $this->assertEquals('value2', $request->query('key2'));
    }

    public function testSetAndGetPostVariables()
    {
        $request = new \FFPerera\Cubo\Request();

        // Set post variables
        $request->setPost('key1', 'value1');
        $request->setPost('key2', 'value2');

        // Get post variables
        $this->assertEquals('value1', $request->post('key1'));
        $this->assertEquals('value2', $request->post('key2'));
    }

    public function testGetAllVariables()
    {
        $request = new \FFPerera\Cubo\Request();

        // Set some variables
        $request->setQuery('key1', 'value1');
        $request->setPost('key2', 'value2');
        $request->setQuery('key3', 'value3');

        // Get all variables
        $all = $request->all();

        // Check if all variables are present
        $this->assertEquals('value1', $all['key1']);
        $this->assertEquals('value2', $all['key2']);
        $this->assertEquals('value3', $all['key3']);
    }
}
