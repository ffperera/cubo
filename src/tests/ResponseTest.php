<?php

use FFPerera\Cubo\BodyStringStream;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{

    protected $options = [
        'headers' => [
            'Content-Type' => 'application/json; charset=UTF-8',
        ],
        'statusCode' => 200,
        'statusText' => 'OK',
        'contentType' => 'application/json',
        'charset' => 'UTF-8',
        'protocolVersion' => '1.1',
    ];

    protected function setUp(): void
    {
        // This method is called before each test
        // You can set up any common state here
    }

    public function testConstructor()
    {
        $response = new \FFPerera\Cubo\Response('{"key": "value"}', $this->options);
        $this->assertInstanceOf(\FFPerera\Cubo\Response::class, $response);
    }

    public function testGetHeaders()
    {
        $response = new \FFPerera\Cubo\Response('{"key": "value"}', $this->options);


        // getHeaders()
        $this->assertEquals([
            'Content-Type' => ['application/json; charset=UTF-8'],
        ], $response->getHeaders());

        $response = $response->withHeader('X-Custom-Header', 'CustomValue');
        $this->assertEquals([
            'Content-Type' => ['application/json; charset=UTF-8'],
            'X-Custom-Header' => ['CustomValue'],
        ], $response->getHeaders());

        $this->assertEquals('CustomValue', $response->getHeaderLine('X-Custom-Header'));

        $response = $response->withHeader('FancyHeaderStringNoArray', 'FancyStringValue');
        $this->assertEquals('FancyStringValue', $response->getHeaderLine('FancyHeaderStringNoArray'));

        $response = $response->withHeader('X-Custom-Header', ['SecondCustomValue']);
        $this->assertEquals('SecondCustomValue', $response->getHeaderLine('X-Custom-Header'));

        $response = $response->withAddedHeader('KEY-Header', 'KEY-Value');
        $this->assertEquals('KEY-Value', $response->getHeaderLine('KEY-Header'));

        $response = $response->withAddedHeader('X-Custom-Header', ['AnotherValue']);
        $this->assertEquals('SecondCustomValue, AnotherValue', $response->getHeaderLine('X-Custom-Header'));

        $response = $response->withoutHeader('X-Custom-Header');
        $this->assertEquals('', $response->getHeaderLine('X-Custom-Header'));

        $response = $response->withoutHeader('NOEXISTING-Header');
        $this->assertEquals('', $response->getHeaderLine('NOEXISTING-Header'));

        // getHeader()
        $this->assertEquals(['application/json; charset=UTF-8'], $response->getHeader('Content-Type'));
        $this->assertEquals([], $response->getHeader('NOEXISTING-Header'));

        // hasHeader()
        $this->assertTrue($response->hasHeader('Content-Type'));
        $this->assertFalse($response->hasHeader('NOEXISTING-Header'));
    }

    public function testStatus()
    {
        $response = new \FFPerera\Cubo\Response('{"key": "value"}', $this->options);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());

        $response = $response->withStatus(404, 'Not Found');
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Not Found', $response->getReasonPhrase());
    }




    public function testProtocolVersion()
    {
        $response = new \FFPerera\Cubo\Response('{"key": "value"}', $this->options);
        $this->assertEquals('1.1', $response->getProtocolVersion());

        $response = $response->withProtocolVersion('2.0');
        $this->assertEquals('2.0', $response->getProtocolVersion());
    }

    public function testBody()
    {
        $response = new \FFPerera\Cubo\Response('{"key": "value"}', $this->options);
        $this->assertEquals('{"key": "value"}', $response->getBody());

        $response = $response->withBody(new BodyStringStream('{"newKey": "newValue"}'));
        $this->assertEquals('{"newKey": "newValue"}', $response->getBody()->getContents());
    }
}
