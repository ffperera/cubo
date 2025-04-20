<?php

declare(strict_types=1);

namespace FFPerera\Cubo;

use FFPerera\Cubo\Response;

class JsonResponse extends Response
{

  public function __construct(private mixed $data) {}

  public function send(mixed $data = null, bool $withHeaders = true): void
  {

    if ($data !== null) {
      $this->data = $data;
    }

    // Implement the logic for sending a JSON response here
    $this->setHeader('Content-Type', 'application/json');

    parent::send(json_encode($this->data), $withHeaders);
  }
}
