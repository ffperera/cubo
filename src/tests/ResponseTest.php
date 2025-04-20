<?php

use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{

    public function testSetAndGetHeaders()
    {
        $response = new \FFPerera\Cubo\Response('');
        $response->setHeader('Content-Type', 'application/json');
        $this->assertEquals(['Content-Type' => 'application/json'], $response->getHeaders());
    }

    public function testSetAndGetStatusCode()
    {
        $response = new \FFPerera\Cubo\Response('');
        $response->setStatus(200);

        $this->assertEquals(200, $response->getStatus()['code']);
        $this->assertEquals('OK', $response->getStatus()['text']);

        $response->setStatus(404, 'Not Found');
        $this->assertEquals(404, $response->getStatus()['code']);
        $this->assertEquals('Not Found', $response->getStatus()['text']);
    }
    public function testSetAndGetContentType()
    {
        $response = new \FFPerera\Cubo\Response('');
        $response->setContentType('application/json');
        $this->assertEquals('application/json', $response->getContentType());
    }


    public function testSetAndGetCharset()
    {
        $response = new \FFPerera\Cubo\Response('');
        $response->setCharset('UTF-8');
        $this->assertEquals('UTF-8', $response->getCharset());
    }

    public function testSend()
    {
        $response = new \FFPerera\Cubo\Response('');

        $response->setData('Hello, World!');
        $this->assertEquals('Hello, World!', $response->getData());


        ob_start();
        $response->send('{"message": "Hello, World!"}', false);
        $output = ob_get_clean();

        $this->assertEquals('{"message": "Hello, World!"}', $output);
    }

    public function testRemoveHeader()
    {
        $response = new \FFPerera\Cubo\Response('');
        $response->setHeader('Content-Type', 'application/json');
        $response->removeHeader('Content-Type');
        $this->assertEquals([], $response->getHeaders());
    }

    public function testSetAndGetProtocolVersion()
    {
        $response = new \FFPerera\Cubo\Response('');
        $response->setProtocolVersion('1.1');
        $this->assertEquals('1.1', $response->getProtocolVersion());
    }
}
