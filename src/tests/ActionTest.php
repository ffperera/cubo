<?php

use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{

    protected \FFPerera\Cubo\Action $actionObjectOne;

    public function setUp(): void
    {
        parent::setUp();
        $this->actionObjectOne = new class extends \FFPerera\Cubo\Action {
            public function run(\FFPerera\Cubo\Controller $controller): void {}
        };
    }


    public function testChildClassHasRunMethod()
    {
        $this->assertTrue(
            method_exists($this->actionObjectOne, 'run'),
            'Child class does not have the run method'
        );
        $this->assertTrue(
            is_callable([$this->actionObjectOne, 'run']),
            'Child class run method is not callable'
        );
    }
}
