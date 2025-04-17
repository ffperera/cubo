<?php

declare(strict_types=1);

namespace Cubo\Eng;

use Cubo\Eng\Action;


class Node
{

  private ?Node $next = null;
  private ?Action $action = null;

  public function __construct(Action $action)
  {
    $this->action = $action;
  }

  public function getAction(): Action
  {
    return $this->action;
  }

  public function setAction(Action $action): void
  {
    $this->action = $action;
  }

  public function getNext(): ?Node
  {
    return $this->next;
  }

  public function setNext(Node $node): void
  {
    $this->next = $node;
  }

  public function isEmpty(): bool
  {
    return $this->action === null;
  }

} 