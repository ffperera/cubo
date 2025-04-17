<?php

declare(strict_types=1);

namespace Cubo\Eng;

use Cubo\Eng\ActionQueue;
use Cubo\Eng\Action;
use Cubo\Eng\Request;
use Cubo\Eng\View;



class Controller
{

  // TODO: is section necessary?
  private string $section;


  private ActionQueue $preQueue;
  private ActionQueue $mainQueue;
  private ActionQueue $postQueue;
  private Request $request;
  private ?View $view;

  /** 
   * @var array<string, mixed> 
   */
  private array $bag = [];


  /**
   * @param array<string, array<string, mixed>> $routes
   */
  public function __construct(
    private array $routes,
    private \Psr\Log\LoggerInterface $logger
  ) {

    // The type of $routes is already enforced, so no need to check if it's an array
    if (empty($routes)) {
      throw new \InvalidArgumentException('Routes cannot be empty');
    }

    $this->preQueue = new ActionQueue();
    $this->mainQueue = new ActionQueue();
    $this->postQueue = new ActionQueue();

    $this->view = null;
    

    $this->request = new Request();
    $this->routing();
  }


  public function run(): ?View
  {

    $counter = 0;
    while (!$this->preQueue->isEmpty()) {
      $action = $this->preQueue->pop();
      if ($action instanceof Action) {
        $action->run($this);
      }
      $counter++;
      if ($counter > 100) {
        throw new \RuntimeException('Infinite loop detected in action execution (preQueue)');
      }
    }

    $counter = 0;
    while (!$this->mainQueue->isEmpty()) {
      $action = $this->mainQueue->pop();
      if ($action instanceof Action) {
        $action->run($this);
      }
      $counter++;
      if ($counter > 100) {
        throw new \RuntimeException('Infinite loop detected in action execution');
      }
    }


    $counter = 0;
    while (!$this->postQueue->isEmpty()) {
      $action = $this->postQueue->pop();
      if ($action instanceof Action) {
        $action->run($this);
      }
      $counter++;
      if ($counter > 100) {
        throw new \RuntimeException('Infinite loop detected in action execution (postQueue)');
      }
    }    

    return $this->view;
  }

  public function addAction(Action $action, string $queue = 'main'): void
  {

    if ($queue === 'pre') {
      $this->preQueue->append($action);
    } elseif ($queue === 'post') {
      $this->postQueue->append($action);
    } else {
      $this->mainQueue->append($action);
    }
  }

  public function addActionFromRoute(string $section, string $name, string $queue='main'): void
  {
    if (isset($this->routes[$section][$name])) {
      $this->addAction ($this->routes[$section][$name]['action'], $queue);
    } else {
      throw new \InvalidArgumentException('Route not found');
    }
  }

  public function set(string $key, mixed $value): void
  {
    $this->bag[$key] = $value;
  }

  public function get(string $key): mixed
  {
    return $this->bag[$key] ?? null;
  }

  public function isset(string $key): bool
  {
    return isset($this->bag[$key]);
  }

  public function getRequest(): Request
  {
    return $this->request;
  }

  public function getView(): View
  {
    if (!isset($this->view)) {
      $this->view = new View();
    }
    return $this->view;
  }

  public function logger(): \Psr\Log\LoggerInterface
  {
    return $this->logger;
  }

  private function routing(): void
  {

    // $queryString = $this->request->getQueryString() ?? '';
    $path = $this->request->getPath();
    $method = $this->request->method();

    foreach ($this->routes as $section => $routes) {
      foreach ($routes as $name => $route) {

        if ($name === 'PRE' || $name === 'POS') {
          continue;
        }

        if ($route['path'] === $path && $route['method'] === $method) {

          $this->section = $section;

          if (isset($route['action']) && $route['action'] instanceof Action) {
            $this->mainQueue->push($route['action']);
          }

          // we use the first match, so break
          break 2;
        }
      }
    }

    if (!isset($this->section) || $this->section === '') {
      throw new \InvalidArgumentException('No route found for the given path');
    }

    $this->initPreQueue($this->routes[$this->section]['PRE']);
    $this->initPostQueue($this->routes[$this->section]['POS']);
  }


  /**
   * @param array<Action> $actions
   */
  private function initPreQueue(array $actions): void
  {

    foreach ($actions as $action) {
      $this->preQueue->append($action);
    }
  }

  /**
   * @param array<Action> $actions
   */
  private function initPostQueue(array $actions): void
  {

    foreach ($actions as $action) {
      $this->postQueue->append($action);
    }
  }
}
