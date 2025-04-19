<?php

declare(strict_types=1);

namespace FFPerera\Cubo;


class Response
{

  // private string $data;

  /**
   * @var array<string, string> $headers
   */
  private array $headers = [];
  private int $statusCode = 200;
  private string $statusText = 'OK';
  private string $contentType = 'text/html';
  private string $charset = 'UTF-8';
  private string $protocolVersion = '1.1';


  public function __construct(private ?string $data) {}


  public function send(?string $data = null): void
  {
    if ($data !== null) {
      $this->data = $data;
    }

    $this->headers();

    echo $this->data;
  }

  public function headers(): void
  {
    header(sprintf('HTTP/%s %d %s', $this->protocolVersion, $this->statusCode, $this->statusText));
    header(sprintf('Content-Type: %s; charset=%s', $this->contentType, $this->charset));

    foreach ($this->headers as $name => $value) {
      header("$name: $value");
    }
  }

  public function setHeader(string $name, string $value): void
  {
    $this->headers[$name] = $value;
  }

  public function removeHeader(string $name): void
  {
    unset($this->headers[$name]);
  }
  public function setCharset(string $charset): void
  {
    $this->charset = $charset;
  }

  public function setContentType(string $contentType): void
  {
    $this->contentType = $contentType;
  }



  public function setProtocolVersion(string $version): void
  {
    $this->protocolVersion = $version;
  }


  public function setStatus(int $code = 200, string $text = 'OK'): void
  {
    $this->statusCode = $code;
    $this->statusText = $text;
  }

  public function redirect(string $route): void
  {
    // send redirection header
    header('Location: ' . $route, true, 302);
    die();
  }
}
