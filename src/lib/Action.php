<?php

declare(strict_types=1);

namespace FFPerera\Cubo;

use FFPerera\Cubo\Controller;


abstract class Action
{

  public abstract function run(Controller $controller): void;

  // get the exact class name
  // this is useful for debugging and logging
  public function getClass(): string
  {
    return get_class($this);
  }
}
