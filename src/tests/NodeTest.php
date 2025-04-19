<?php

use PHPUnit\Framework\TestCase;

class Action1 extends \FFPerera\Cubo\Action
{
    public function run(\FFPerera\Cubo\Controller $controller): void {}
}

class Action2 extends \FFPerera\Cubo\Action
{
    public function run(\FFPerera\Cubo\Controller $controller): void {}
}

/**
 * @covers \FFPerera\Cubo\Node
 */
class NodeTest extends TestCase
{
    protected \FFPerera\Cubo\Action $actionObjectOne;
    protected \FFPerera\Cubo\Action $actionObjectTwo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actionObjectOne = new Action1();
        $this->actionObjectTwo = new Action2();
    }


    public function testCreateNodeWithAnAction()
    {
        $node = new \FFPerera\Cubo\Node($this->actionObjectOne);

        $this->assertSame($this->actionObjectOne, $node->getAction());
    }



    public function testSettingNextNode()
    {
        $action1 = $this->actionObjectOne;
        $action2 = $this->actionObjectTwo;

        $node1 = new \FFPerera\Cubo\Node($action1);
        $node2 = new \FFPerera\Cubo\Node($action2);

        $node1->setNext($node2);

        $this->assertSame($node2, $node1->getNext());
    }


    public function testSetAction()
    {
        $node = new \FFPerera\Cubo\Node($this->actionObjectOne);

        $node->setAction($this->actionObjectOne);
        $this->assertSame($this->actionObjectOne, $node->getAction());
    }
}
