<?php

declare(strict_types=1);

namespace FFPerera\Cubo;

use FFPerera\Cubo\Action;
use FFPerera\Cubo\Node;


class ActionQueue
{

  private ?Node $head;

  public function __construct()
  {
    $this->head = null;
  }


  public function push(Action $action): void
  {
    // insert at the beginning
    $newNode = new Node($action);

    if ($this->head === null) {
      $this->head = $newNode;
      return;
    }

    $newNode->setNext($this->head);
    $this->head = $newNode;
  }


  public function append(Action $action): void
  {
    if ($this->head === null) {
      $this->head = new Node($action);
    } else {
      $current = $this->head;
      while ($current->getNext() !== null) {
        $current = $current->getNext();
      }
      $current->setNext(new Node($action));
    }
  }


  public function insertBefore(Action $newAction, Action $targetAction): void
  {
    if ($this->head === null) {
      return;
    }

    if ($this->head->getAction()::class === $targetAction::class) {
      $newNode = new Node($newAction);
      $newNode->setNext($this->head);
      $this->head = $newNode;
      return;
    }

    $current = $this->head;
    while ($current->getNext() !== null && $current->getNext()->getAction()::class !== $targetAction::class) {
      $current = $current->getNext();
    }

    if ($current->getNext() !== null) {
      $newNode = new Node($newAction);
      $newNode->setNext($current->getNext());
      $current->setNext($newNode);
    }
  }


  public function insertAfter(Action $newAction, Action $targetAction): void
  {
    if ($this->head === null) {
      return;
    }

    $current = $this->head;
    while ($current !== null) {

      if ($current->getAction()::class === $targetAction::class) {
        break;
      }

      $current = $current->getNext();
    }

    if ($current !== null && $current->getAction()::class === $targetAction::class) {
      $newNode = new Node($newAction);
      $newNode->setNext($current->getNext());
      $current->setNext($newNode);
    }
  }


  public function pop(): ?Action
  {
    if ($this->head === null) {
      return null;
    }

    $action = $this->head->getAction();
    $this->head = $this->head->getNext();
    return $action;
  }

  public function isEmpty(): bool
  {
    return $this->head === null;
  }

  public function getHead(): ?Node
  {
    return $this->head;
  }
}
