<?php

declare(strict_types=1);

namespace Cubo\Eng;

use Cubo\Eng\Response;

class JsonResponse extends Response
{

  public function __construct(private string $data) {}

  public function send(?string $data = null): void
  {

    if ($data !== null) {
      $this->data = $data;
    }

    // Implement the logic for sending a JSON response here
    header('Content-Type: application/json');

    // TODO: other headers
    $this->headers();
    echo json_encode($this->data);
  }
}
