<?php

declare(strict_types=1);

namespace FFPerera\Cubo;

use FFPerera\Cubo\ActionQueue;
use FFPerera\Cubo\Action;
use FFPerera\Cubo\Request;
use FFPerera\Cubo\View;



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
    private \Psr\Log\LoggerInterface $logger,
    private bool $debuggingMode = false
  ) {

    // The type of $routes is already enforced, so no need to check if it's an array
    if (empty($routes)) {
      $this->logger->error('ERROR: Routes cannot be empty');
      throw new \FFPerera\Cubo\Exceptions\RoutesNotDefinedException('Routes cannot be empty');
    }

    $this->preQueue = new ActionQueue();
    $this->mainQueue = new ActionQueue();
    $this->postQueue = new ActionQueue();

    $this->view = null;


    $this->request = new Request();
    // or $this->request = Request::createFromGlobals();
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

  public function addAction(Action $action, string $queue = 'MAIN'): void
  {

    if ($queue === 'PRE') {
      $this->preQueue->append($action);
    } elseif ($queue === 'POS') {
      $this->postQueue->append($action);
    } else {
      $this->mainQueue->append($action);
    }
  }

  public function addActionFromRoute(string $section, string $name, string $queue = 'MAIN'): void
  {
    if (isset($this->routes[$section][$name])) {

      $this->addAction($this->routes[$section][$name]['action'], $queue);

      if ($this->debuggingMode) {
        $this->logger->debug('Adding action from route: ' . $section . ' ' . $name);
      }
    } else {
      throw new \FFPerera\Cubo\Exceptions\NotFoundException('Route not found');
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

    // real path     : /some/path/mysection/2/
    // routing path  : /some/path/{section}/{id}/
    $path = $this->request->getPath();
    $method = $this->request->method();

    foreach ($this->routes as $section => $routes) {
      foreach ($routes as $name => $route) {

        if ($name === 'PRE' || $name === 'POS') {
          continue;
        }

        if ($route['method'] === $method && $this->extractPath($path, $route['path'])) {

          $this->section = $section;

          if (isset($route['action']) && $route['action'] instanceof Action) {
            $this->mainQueue->push($route['action']);

            if ($this->debuggingMode) {
              $this->logger->debug('Action added to main queue', ['action' => $route['action']]);
            }
          }

          // we use the first match, so break
          break 2;
        }
      }
    }

    if (!isset($this->section) || $this->section === '') {
      $this->logger->error('No route found for the given path', ['path' => $path]);
      throw new \FFPerera\Cubo\Exceptions\NotFoundException('No route found for the given path');
    }

    $this->initPreQueue($this->routes[$this->section]['PRE'] ?? []);
    $this->initPostQueue($this->routes[$this->section]['POS'] ?? []);
  }



  private function extractPath(string $realPath, string $definedPath): bool
  {

    // Normalize paths by trimming slashes and splitting into segments
    $definedSegments = explode('/', trim($definedPath, '/'));
    $realSegments = explode('/', trim($realPath, '/'));

    // Check if segment counts match
    if (count($definedSegments) !== count($realSegments)) {
      return false;
    }

    $params = [];

    foreach ($definedSegments as $i => $segment) {
      $realValue = $realSegments[$i];

      // Check for placeholder pattern {parameter}
      if (preg_match('/^{(\w+)}$/', $segment, $matches)) {
        $paramName = $matches[1];
        $params[$paramName] = $realValue;
      } else {
        // Verify static segments match exactly
        if ($segment !== $realValue) {
          return false;
        }
      }
    }

    // it seems ok
    // save the params as $_GET params
    foreach ($params as $name => $value) {
      $this->request->setQuery($name, $value);
    }

    return true;
  }


  /**
   * @param array<Action> $actions
   */
  private function initPreQueue(?array $actions): void
  {
    foreach ($actions as $action) {
      $this->preQueue->append($action);

      if ($this->debuggingMode) {
        $this->logger->debug("PRE queue > action added: " . $action->getClass());
      }
    }
  }

  /**
   * @param array<Action> $actions
   */
  private function initPostQueue(?array $actions): void
  {
    foreach ($actions as $action) {
      $this->postQueue->append($action);

      if ($this->debuggingMode) {
        $this->logger->debug("POS queue > action added: " . $action->getClass());
      }
    }
  }
}
