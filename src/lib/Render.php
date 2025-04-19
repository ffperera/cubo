<?php

declare(strict_types=1);

namespace FFPerera\Cubo;

use FFPerera\Cubo\Response;
use FFPerera\Cubo\View;

class Render
{

  private string $rootDirectory;


  public function __construct(private View $view, string $rootDirectory = '')
  {

    if ($rootDirectory !== '') {
      $this->rootDirectory = $rootDirectory;
      return;
    }

    // if root directory is not set, use the document root
    $this->rootDirectory = $_SERVER['DOCUMENT_ROOT'];
  }



  // render view and sends directly to the client
  public function send(): void
  {
    $view = $this->view;

    // get layout
    $layout = trim($view->getLayout());

    if (!$layout) {
      return;
    }

    $this->insert($layout);
  }

  // render view and return the response
  public function render(): Response
  {
    ob_start();
    $this->send();
    $content = ob_get_clean();

    return new Response($content);
  }

  public function block(string $blockKey): void
  {
    $block = $this->view->getTemplate($blockKey);
    if (!$block) {
      return;
    }

    $this->insert($block);
  }

  public function getView(): View
  {
    return $this->view;
  }

  public function getRootDirectory(): string
  {
    return $this->rootDirectory;
  }


  private function insert(string $template): void
  {
    // check if the template file exists
    if (file_exists($this->rootDirectory . $template)) {
      include $this->rootDirectory . $template;
    } else {
      throw new \RuntimeException("Layout file not found: " . $this->rootDirectory . $template);
    }
  }
}
