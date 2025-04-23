<?php

use PHPUnit\Framework\TestCase;

class ActionQueueTest extends TestCase
{

    protected \FFPerera\Cubo\Action $actionObjectOne;
    protected \FFPerera\Cubo\Action $actionObjectTwo;
    protected \FFPerera\Cubo\Action $actionObjectThree;

    public function setUp(): void
    {
        parent::setUp();
        $this->actionObjectOne = new class extends \FFPerera\Cubo\Action {
            public function run(\FFPerera\Cubo\Controller $controller): void {}
        };
        $this->actionObjectTwo = new class extends \FFPerera\Cubo\Action {
            public function run(\FFPerera\Cubo\Controller $controller): void {}
        };
        $this->actionObjectThree = new class extends \FFPerera\Cubo\Action {
            public function run(\FFPerera\Cubo\Controller $controller): void {}
        };
    }
    public function testCreateActionQueue()
    {
        $actionQueue = new \FFPerera\Cubo\ActionQueue();

        $this->assertInstanceOf(\FFPerera\Cubo\ActionQueue::class, $actionQueue);
    }

    public function testPushAction()
    {
        $actionQueue = new \FFPerera\Cubo\ActionQueue();
        $actionQueue->push($this->actionObjectOne);
        $actionQueue->push($this->actionObjectTwo);

        $this->assertSame($this->actionObjectTwo, $actionQueue->getHead()->getAction());
    }


    public function testAppendAction()
    {
        $actionQueue = new \FFPerera\Cubo\ActionQueue();
        $actionQueue->append($this->actionObjectOne);
        $actionQueue->append($this->actionObjectTwo);

        $this->assertSame($this->actionObjectTwo, $actionQueue->getHead()->getNext()->getAction());
    }

    public function testAppendActionToEmptyQueue()
    {
        $actionQueue = new \FFPerera\Cubo\ActionQueue();
        $actionQueue->append($this->actionObjectOne);

        $this->assertSame($this->actionObjectOne, $actionQueue->getHead()->getAction());
    }


    public function testInsertBeforeAction()
    {
        $actionQueue = new \FFPerera\Cubo\ActionQueue();
        $actionQueue->push($this->actionObjectOne);
        $actionQueue->insertBefore($this->actionObjectThree, $this->actionObjectOne);

        // first item is now ObjectThree
        $this->assertSame($this->actionObjectThree, $actionQueue->pop());

        $actionQueue->push($this->actionObjectOne);
        $actionQueue->append($this->actionObjectTwo);
        $actionQueue->insertBefore($this->actionObjectThree, $this->actionObjectTwo);

        $this->assertSame($this->actionObjectOne, $actionQueue->pop());
        $this->assertSame($this->actionObjectOne, $actionQueue->pop());
        $this->assertSame($this->actionObjectThree, $actionQueue->pop());
    }

    public function testInsertBeforeNotFoundAction()
    {
        $actionQueue = new \FFPerera\Cubo\ActionQueue();

        // insert before an action in an empty queue
        $actionQueue->insertBefore($this->actionObjectTwo, $this->actionObjectOne);

        $this->assertNull($actionQueue->getHead());

        // insert before an action not in the queue
        $actionQueue->push($this->actionObjectOne);
        $actionQueue->append($this->actionObjectTwo);
        $actionQueue->insertBefore($this->actionObjectTwo, new class extends \FFPerera\Cubo\Action {
            public function run(\FFPerera\Cubo\Controller $controller): void {}
        });

        // should not change the queue
        $this->assertSame($this->actionObjectOne, $actionQueue->pop());
        $this->assertSame($this->actionObjectTwo, $actionQueue->pop());
    }


    public function testInsertAfterAction()
    {
        $actionQueue = new \FFPerera\Cubo\ActionQueue();

        // insert after an action in an empty queue
        $actionQueue->insertAfter($this->actionObjectTwo, $this->actionObjectOne);
        $this->assertNull($actionQueue->getHead());


        $actionQueue->push($this->actionObjectOne);
        $actionQueue->append($this->actionObjectTwo);
        $actionQueue->insertAfter($this->actionObjectOne, $this->actionObjectTwo);

        // after the second action, so it should be in the last position
        $first = $actionQueue->pop();
        $second = $actionQueue->pop();
        $last = $actionQueue->pop();
        $this->assertSame($this->actionObjectOne, $last);
    }

    public function testIsEmpty()
    {
        $actionQueue = new \FFPerera\Cubo\ActionQueue();
        $this->assertTrue($actionQueue->isEmpty());

        $this->assertNull($actionQueue->pop());
        $this->assertNull($actionQueue->getHead());

        $actionQueue->push($this->actionObjectOne);
        $this->assertFalse($actionQueue->isEmpty());

        $actionQueue->pop();
        $this->assertTrue($actionQueue->isEmpty());
    }
}
