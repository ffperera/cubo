<?php

use PHPUnit\Framework\TestCase;

class JsonResponseTest extends TestCase
{

    protected $options = [
        'headers' => [
            'Content-Type' => ['application/json; charset=UTF-8'],
        ],
        'statusCode' => 200,
        'statusText' => 'OK',
        'contentType' => 'application/json',
        'charset' => 'UTF-8',
        'protocolVersion' => '1.1',
    ];
    public function testJsonResponse()
    {
        $response = new \FFPerera\Cubo\JsonResponse(['message' => 'Hello, World!'], $this->options);

        ob_start();
        $response->send(false);
        $output = ob_get_clean();

        $this->assertEquals('{"message":"Hello, World!"}', trim($output));
    }
}
