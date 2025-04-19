<?php

use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    /**
     * @covers FFPerera\Cubo\View
     */
    public function testMethods()
    {
        $view = new \FFPerera\Cubo\View();

        // Test setLayout and getLayout
        $view->setLayout('main');
        $this->assertEquals('main', $view->getLayout());

        // Test setTemplate and getTemplate
        $view->setTemplate('header', 'header.php');
        $this->assertEquals('header.php', $view->getTemplate('header'));

        // Test setHeader and getHeaders
        $view->setHeader('Content-Type', 'text/html');
        $this->assertEquals(['Content-Type' => 'text/html'], $view->getHeaders());

        // Test set and get (bag)
        $view->set('user', 'John Doe');
        $this->assertEquals('John Doe', $view->get('user'));

        // Test get with non-existing key
        $this->assertNull($view->get('non_existing_key'));
    }
}
