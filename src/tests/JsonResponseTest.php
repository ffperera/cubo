<?php

use PHPUnit\Framework\TestCase;

class JsonResponseTest extends TestCase
{
    public function testJsonResponse()
    {
        $response = new \FFPerera\Cubo\JsonResponse('');
        $data = ['message' => 'Hello, World!'];

        ob_start();
        $response->send($data, false);
        $output = ob_get_clean();

        $this->assertEquals('{"message":"Hello, World!"}', trim($output));
    }
}
