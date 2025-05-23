<?php

declare(strict_types=1);

namespace FFPerera\Cubo;

use FFPerera\Cubo\Action;


class Node
{

  private ?Node $next = null;
  private ?Action $action = null;

  public function __construct(Action $action)
  {
    $this->action = $action;
    $this->next = null;
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

  public function setNext(?Node $node): void
  {
    $this->next = $node;
  }
}
