<?php

declare(strict_types=1);

namespace FFPerera\Cubo;

use FFPerera\Cubo\Controller;


abstract class Action
{

  public abstract function run(Controller $controller): void;

} 