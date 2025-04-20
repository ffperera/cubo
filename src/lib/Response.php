<?php

declare(strict_types=1);

namespace FFPerera\Cubo;


// TODO: Response should be an implemantation of PSR-7
// https://www.php-fig.org/psr/psr-7/

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
  private bool $withHeaders = true;


  public function __construct(private ?string $data) {}


  public function send(mixed $data = null, bool $withHeaders = true): void
  {
    if ($data !== null) {
      $this->data = $data;
    }

    if (!is_string($this->data)) {
      throw new \InvalidArgumentException('Response data must be a string');
    }

    $this->withHeaders($withHeaders);
    if ($this->withHeaders) {
      $this->sendHeaders();
    }

    echo $this->data;
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

  public function getCharset(): string
  {
    return $this->charset;
  }


  /**
   * @return array<string, string>
   */
  public function getHeaders(): array
  {
    return $this->headers;
  }


  public function setContentType(string $contentType): void
  {
    $this->contentType = $contentType;
  }

  public function getContentType(): string
  {
    return $this->contentType;
  }

  public function setData(string $data): void
  {
    $this->data = $data;
  }

  public function getData(): ?string
  {
    return $this->data;
  }

  public function setProtocolVersion(string $version): void
  {
    $this->protocolVersion = $version;
  }

  public function getProtocolVersion(): string
  {
    return $this->protocolVersion;
  }

  public function setStatus(int $code = 200, string $text = 'OK'): void
  {
    $this->statusCode = $code;
    $this->statusText = $text;
  }

  /**
   * @return array<string, mixed>
   */
  public function getStatus(): array
  {
    return [
      'code' => $this->statusCode,
      'text' => $this->statusText,
    ];
  }


  public function redirect(string $route): void
  {
    // send redirection header
    header('Location: ' . $route, true, 302);
    die();
  }

  public function withHeaders(bool $withHeaders = true): void
  {
    $this->withHeaders = $withHeaders;
  }

  protected function sendHeaders(): void
  {
    header(sprintf('HTTP/%s %d %s', $this->protocolVersion, $this->statusCode, $this->statusText));
    header(sprintf('Content-Type: %s; charset=%s', $this->contentType, $this->charset));

    foreach ($this->headers as $name => $value) {
      header("$name: $value");
    }
  }
}
