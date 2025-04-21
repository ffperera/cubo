<?php

use PHPUnit\Framework\TestCase;


class ActionA1 extends \FFPerera\Cubo\Action
{
    public function run(\FFPerera\Cubo\Controller $controller): void {}
}

class ActionTest extends TestCase
{

    protected \FFPerera\Cubo\Action $actionObjectOne;

    public function setUp(): void
    {
        parent::setUp();
        $this->actionObjectOne = new ActionA1();
    }

    public function testGetClass()
    {
        $this->assertEquals(
            $this->actionObjectOne::class,
            $this->actionObjectOne->getClass()
        );
    }
}
