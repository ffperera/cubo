<?php

declare(strict_types=1);

namespace FFPerera\Cubo;

use FFPerera\Cubo\Response;

class JsonResponse extends Response
{

  public function __construct(private mixed $data, $options = [
    'headers' => [
      'Content-Type' => ['application/json; charset=UTF-8'],
    ],
    'statusCode' => 200,
    'statusText' => 'OK',
    'contentType' => 'application/json',
    'charset' => 'UTF-8',
    'protocolVersion' => '1.1',
  ])
  {
    // Call the parent constructor to initialize the base Response class
    parent::__construct(json_encode($this->data), $options);
  }
}
