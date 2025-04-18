<?php

use PHPUnit\Framework\TestCase;

class RenderTest extends TestCase
{
    public function testMethods()
    {
        $render = new \FFPerera\Cubo\Render(new \FFPerera\Cubo\View());

        // test default  src directory
        $this->assertEquals($_SERVER['DOCUMENT_ROOT'], $render->getRootDirectory());

        // test custom src directory
        $customDirectory = '/path/to/custom/directory';
        $render = new \FFPerera\Cubo\Render(new \FFPerera\Cubo\View(), $customDirectory);
        $this->assertEquals($customDirectory, $render->getRootDirectory());

        // test set and get view
        $view = new \FFPerera\Cubo\View();
        $render = new \FFPerera\Cubo\Render($view);
        $this->assertEquals($view, $render->getView());
    }

    public function testSend()
    {
        $view = new \FFPerera\Cubo\View();
        $view->setLayout('layout.php');

        // mock the insert method
        $render = $this->getMockBuilder(\FFPerera\Cubo\Render::class)
            ->setConstructorArgs([$view])
            ->onlyMethods(['insert'])
            ->getMock();
        $render->expects($this->once())
            ->method('insert')
            ->with($this->equalTo('layout.php'));
        $render->send();
    }

    public function testBlock()
    {
        $view = new \FFPerera\Cubo\View();
        $view->setLayout('layout.php');
        $view->setTemplate('header', 'header.php');

        // mock the insert method
        $render = $this->getMockBuilder(\FFPerera\Cubo\Render::class)
            ->setConstructorArgs([$view])
            ->onlyMethods(['insert'])
            ->getMock();
        $render->expects($this->once())
            ->method('insert')
            ->with($this->equalTo('header.php'));
        $render->block('header');
    }
}
