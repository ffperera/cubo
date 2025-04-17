<?php

declare(strict_types=1);

namespace Cubo\Eng;



class Request
{

  private string $method;

/**
 * @var array<string, mixed> $post
 */
  private array $post;

  /**
 * @var array<string, mixed> $get
 */  
  private array $get;

  /** 
   * @var array<string, mixed> $cookies 
  */
  private array $cookies;

  /** 
   * @var array<string, mixed> $server 
  */
  private array $server;

    /** 
   * @var array<string, mixed> $uriInfo 
  */
  private array $uriInfo;


  public function __construct()
  {
    $this->post = $_POST;
    $this->get = $_GET;
    $this->cookies = $_COOKIE;
    $this->server = $_SERVER;

    $this->uriInfo = parse_url($_SERVER['REQUEST_URI']);

    $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

  }


  public function query(string $key): string|null
  {
    return $this->get[$key] ?? null;
  }

  public function post(string $key): string|null
  {
    return $this->post[$key] ?? null;
  }


  public function cookie(string $key): string|null
  {
    return $this->cookies[$key] ?? null;
  }

  public function server(string $key): string|null
  {
    return $this->server[$key] ?? null;
  }

  public function method(): string
  {
    return $this->method;
  }
  
  /**
   * @return array<string, mixed>
   */
  public function all(): array
  {
    return array_merge($this->post, $this->get, $this->cookies);
  }

  public function setQuery(string $key, string $value): void
  {
    $this->get[$key] = $value;
  }

  public function setPost(string $key, string $value): void
  {
    $this->post[$key] = $value;
  }



  public function getQueryString(): string|null
  {
    return $this->uriInfo['query'] ?? '';
  }

  public function getPath(): string
  {
    // TODO: extract path using regex (/admin/action/param/{id}/)

    return $this->uriInfo['path'] ?? '/';
  }


}
