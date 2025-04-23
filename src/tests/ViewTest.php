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

        // test isset
        $this->assertTrue($view->isset('user'));
        $this->assertFalse($view->isset('non_existing_key'));

        // test has
        $this->assertTrue($view->has('user'));
        $this->assertFalse($view->has('non_existing_key'));

        // test remove
        $view->remove('user');
        $this->assertFalse($view->has('user'));
        $this->assertNull($view->get('user'));

        // test getAll()   
        $view->clear();
        $view->set('key1', 'value1');
        $view->set('key2', 'value2');
        $this->assertEquals(['key1', 'key2'], $view->getAll());
    }
}
