<?php

declare(strict_types=1);

namespace Cubo\Eng;
use Cubo\Eng\Controller;
use Cubo\Eng\View;


abstract class Action
{

  public function __construct() {

  }

  public abstract function run(Controller $controller): void;
  // public abstract function getView(): ?View;

} 