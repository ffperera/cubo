<?php

declare(strict_types=1);

namespace FFPerera\Cubo;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

// Response should be an implemantation of PSR-7
// https://www.php-fig.org/psr/psr-7/
// Response is an inmutable object, so we need to return a new instance
// when we change the state of the object

class Response implements \Psr\Http\Message\ResponseInterface
{


  // private string $data;

  /**
   * @var array<string, array<string>> $headers
   */
  private array   $headers = [];
  private int     $statusCode = 200;
  private string  $statusText = 'OK';
  private string  $protocolVersion = '1.1';

  private \Psr\Http\Message\StreamInterface $body;



  /**
   * @param array<string, mixed> $options
   */
  public function __construct(private string $content, array $options = [
    'headers' => [
      'Content-Type' => 'text/html; charset=UTF-8',
    ],
    'statusCode' => 200,
    'statusText' => 'OK',
    'protocolVersion' => '1.1',

  ])
  {


    if (isset($options['headers']) && is_array($options['headers'])) {
      foreach ($options['headers'] as $key => $value) {
        if (is_array($value)) {
          // headers['key'] = ['value1', 'value2']
          $this->headers[$key] = $value;
        } else {
          // headers['key'] = 'value'
          $this->headers[$key] = [$value];
        }
      }
    }

    $this->statusCode = $options['statusCode'] ?? 200;
    $this->statusText = $options['statusText'] ?? 'OK';
    $this->protocolVersion = $options['protocolVersion'] ?? '1.1';


    $this->body = new BodyStringStream($this->content);
  }

  public function __clone()
  {
    // This method is called when the object is cloned
    // You can set up any common state here
    $this->body = clone $this->body;
  }


  public function send(bool $withHeaders = true): void
  {
    if ($withHeaders) {
      $this->sendHeaders();
    }

    echo $this->body->getContents();
  }

  public function getBody(): \Psr\Http\Message\StreamInterface
  {
    return $this->body;
  }

  public function withBody(StreamInterface $body): MessageInterface
  {
    $response = clone ($this);
    $response->body = $body;

    return $response;
  }



  /**
   * @return array<string, array<string>>
   */
  public function getHeaders(): array
  {
    return $this->headers;
  }

  public function hasHeader(string $name): bool
  {
    foreach ($this->headers as $key => $value) {
      if (strtolower($key) === strtolower($name)) {
        return true;
      }
    }

    return false;
  }

  public function getHeader(string $name): array
  {
    foreach ($this->headers as $key => $value) {
      if (strtolower($key) === strtolower($name)) {
        return $value;
      }
    }
    return [];
  }

  public function getHeaderLine(string $name): string
  {
    foreach ($this->headers as $key => $value) {
      if (strtolower($key) === strtolower($name)) {
        return implode(', ', $value);
      }
    }
    return '';
  }

  public function withHeader(string $name, mixed $value): ResponseInterface
  {

    if (is_string($value)) {
      $value = [$value];
    }

    $response = clone ($this);

    foreach ($response->headers as $key => $headerValue) {
      if (strtolower($key) === strtolower($name)) {
        if (is_array($value)) {
          $response->headers[$key] = $value;
        }
        return $response;
      }
    }

    // if the header does not exist, add it
    if (is_array($value)) {
      $response->headers[$name] = $value;
    }

    return $response;
  }

  public function withAddedHeader(string $name, mixed $value): MessageInterface
  {

    if (is_string($value)) {
      $value = [$value];
    }

    $response = clone ($this);

    foreach ($response->headers as $key => $headerValue) {
      if (strtolower($key) === strtolower($name)) {
        if (is_array($value)) {
          $response->headers[$key] = array_merge($response->headers[$key], $value);
        }
        return $response;
      }
    }

    // if the header does not exist, add it
    if (is_array($value)) {
      $response->headers[$name] = $value;
    }

    return $response;
  }

  public function withoutHeader(string $name): MessageInterface
  {
    $response = clone ($this);

    foreach ($response->headers as $key => $value) {
      if (strtolower($key) === strtolower($name)) {
        unset($response->headers[$key]);
        return $response;
      }
    }

    return $response;
  }

  public function withProtocolVersion(string $version): ResponseInterface
  {
    $response = clone ($this);
    $response->protocolVersion = $version;

    return $response;
  }

  public function getProtocolVersion(): string
  {
    return $this->protocolVersion;
  }


  public function getStatusCode(): int
  {
    return $this->statusCode;
  }

  public function getReasonPhrase(): string
  {
    return $this->statusText;
  }

  public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
  {
    $response = clone ($this);
    $response->statusCode = $code;
    $response->statusText = $reasonPhrase ?: 'OK';

    return $response;
  }




  public function redirect(string $route): void
  {
    // send redirection header
    header('Location: ' . $route, true, 302);
    die();
  }




  protected function sendHeaders(): void
  {
    // status line
    header(sprintf('HTTP/%s %d %s', $this->protocolVersion, $this->statusCode, $this->statusText));

    // header(sprintf('Content-Type: %s; charset=%s', $this->contentType, $this->charset));

    foreach ($this->headers as $name => $value) {
      header(sprintf('%s: %s', $name, $this->getHeaderLine($name)));
    }
  }
}
