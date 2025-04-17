<?php

declare(strict_types=1);

namespace Cubo\Eng;

use Cubo\Eng\Response;
use Cubo\Eng\View;

class Render
{

  private string $rootDirectory;
  private View $view;

  public function __construct() {
    $this->rootDirectory = $_SERVER['DOCUMENT_ROOT'];
  }



  // render view and sends directly to the client
  public function send(View $view): void 
  {

    $this->view = $view;

    // get layout
    $layout = trim($view->getLayout());

    if ($layout === '') {
      return;
    }

    $this->insert($layout);

  }

  public function block(string $blockKey): void 
  {
    $block = $this->view->getTemplate($blockKey);
    if ($block === '') {
      return;
    }

    $this->insert($block);
  }


  public function insert(string $template): void 
  {

    $view = $this->view;

    if(file_exists($this->rootDirectory . $template)) {
      include $this->rootDirectory . $template;
    } else {
      throw new \RuntimeException("Layout file not found: " . $template);
    }    
  }

  public function getView(): View
  {
    return $this->view;
  }


  // render view and return the response
  public function render(View $view): Response
  {

    // TODO: implement


    return new Response();
  }
}
