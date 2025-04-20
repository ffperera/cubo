<?php

use PHPUnit\Framework\TestCase;

class ActionC1 extends \FFPerera\Cubo\Action
{
    public function run(\FFPerera\Cubo\Controller $controller): void
    {
        $controller->set('sequence-of-actions', ($controller->get('sequence-of-actions') ?? '') . 'C1 ');
    }
}

class ActionC2 extends \FFPerera\Cubo\Action
{
    public function run(\FFPerera\Cubo\Controller $controller): void
    {
        $controller->set('sequence-of-actions', ($controller->get('sequence-of-actions') ?? '') . 'C2 ');
    }
}

class ActionC3 extends \FFPerera\Cubo\Action
{
    public function run(\FFPerera\Cubo\Controller $controller): void
    {
        $controller->set('sequence-of-actions', ($controller->get('sequence-of-actions') ?? '') . 'C3 ');
    }
}

class InfiniteLoopAction extends \FFPerera\Cubo\Action
{
    public function run(\FFPerera\Cubo\Controller $controller): void
    {
        $queue = $controller->get('queue') ?? 'MAIN';
        $controller->addAction(new InfiniteLoopAction(), $queue);
    }
}


class ControllerTest extends TestCase
{
    protected $routes = [];
    protected $logger = null;

    public function setUp(): void
    {
        $this->routes = [
            'app' => [
                'home' => [
                    'action' => new ActionC1(),
                    'path' =>   '/',
                    'method' => 'GET',
                ],
                'blog' => [
                    'action' => new ActionC2(),
                    'path' =>   '/blog/',
                    'method' => 'GET',
                ],
                'login' => [
                    'action' => new ActionC3(),
                    'path' =>   '/login/',
                    'method' => 'GET',
                ],
                'logout' => [
                    'action' => new ActionC1(),
                    'path' =>   '/logout/',
                    'method' => 'GET',
                ],
                'PRE' => [
                    new ActionC1(),
                    new ActionC2(),
                    new ActionC3(),
                ],
                'POS' => [
                    new ActionC3(),
                ],
            ],
        ];

        $this->logger = $this->createMock(\Psr\Log\LoggerInterface::class);
    }


    /**
     * @covers FFPerera\Cubo\Controller
     */
    public function testControllerInitialization()
    {

        $controller = new \FFPerera\Cubo\Controller($this->routes, $this->logger);

        $this->assertInstanceOf(\Psr\Log\LoggerInterface::class, $controller->logger());
    }

    // test empty routes
    public function testEmptyRoutes()
    {
        $this->expectException(\InvalidArgumentException::class);

        $controller = new \FFPerera\Cubo\Controller([], $this->logger);
    }

    // test run
    public function testRun()
    {
        $controller = new \FFPerera\Cubo\Controller($this->routes, $this->logger);

        $controller->run();


        // actual route is /, so it should run the action for home
        $this->assertEquals('C1 C2 C3 C1 C3 ', $controller->get('sequence-of-actions'));
    }

    // test addAction
    public function testAddAction()
    {
        $controller = new \FFPerera\Cubo\Controller($this->routes, $this->logger);

        $controller->addAction(new ActionC1(), 'main');

        $controller->run();

        // actual route is /, so it should run the action for home
        $this->assertEquals('C1 C2 C3 C1 C1 C3 ', $controller->get('sequence-of-actions'));
    }

    public function testAddActionFromRoute()
    {
        $controller = new \FFPerera\Cubo\Controller($this->routes, $this->logger);

        $controller->addActionFromRoute('app', 'blog', 'main');

        $controller->run();

        // actual route is /, so it should run the action for home
        // and the action for blog is C2, so main queue should be C1 C2
        $this->assertEquals('C1 C2 C3 C1 C2 C3 ', $controller->get('sequence-of-actions'));
    }

    public function testInfiniteLooOnPreQueue()
    {
        $this->expectException(\RuntimeException::class);

        $routes = [
            'app' => [
                'home' => [
                    'action' => new ActionC1(),
                    'path' =>   '/',
                    'method' => 'GET',
                ],
                'PRE' => [
                    new InfiniteLoopAction(),
                ],
            ],
        ];

        $controller = new \FFPerera\Cubo\Controller($routes, $this->logger);
        $controller->set('queue', 'PRE');

        $controller->run();
    }

    public function testInfiniteLooOnPosQueue()
    {
        $this->expectException(\RuntimeException::class);

        $routes = [
            'app' => [
                'home' => [
                    'action' => new ActionC1(),
                    'path' =>   '/',
                    'method' => 'GET',
                ],
                'POS' => [
                    new InfiniteLoopAction(),
                ],
            ],
        ];

        $controller = new \FFPerera\Cubo\Controller($routes, $this->logger);
        $controller->set('queue', 'POS');

        $controller->run();
    }

    public function testInfiniteLooOnMainQueue()
    {
        $this->expectException(\RuntimeException::class);

        $routes = [
            'app' => [
                'home' => [
                    'action' => new InfiniteLoopAction(),
                    'path' =>   '/',
                    'method' => 'GET',
                ],
            ],
        ];

        $controller = new \FFPerera\Cubo\Controller($routes, $this->logger);
        // $controller->set('queue', 'POS');

        $controller->run();
    }

    public function testGetRequest()
    {
        $controller = new \FFPerera\Cubo\Controller($this->routes, $this->logger);

        $request = $controller->getRequest();

        $this->assertInstanceOf(\FFPerera\Cubo\Request::class, $request);
    }

    // test set, get and isset
    public function testSetGetIsset()
    {
        $controller = new \FFPerera\Cubo\Controller($this->routes, $this->logger);

        $controller->set('test', 'value');

        $this->assertEquals('value', $controller->get('test'));
        $this->assertTrue($controller->isset('test'));
        $this->assertFalse($controller->isset('non_existing_key'));
    }

    // test not section found
    public function testNotSectionFoundFromAddActionFromRoute()
    {
        $this->expectException(\InvalidArgumentException::class);

        $controller = new \FFPerera\Cubo\Controller($this->routes, $this->logger);

        $controller->addActionFromRoute('not_existing_section', 'home', 'main');
    }


    public function testNotRouteFound404()
    {
        $this->expectException(\InvalidArgumentException::class);

        $routes = [
            'app' => [
                'home' => [
                    'action' => new ActionC1(),
                    'path' =>   '/anyroute/',
                    'method' => 'ANYMETHOD',
                ],
            ],
        ];

        $controller = new \FFPerera\Cubo\Controller($routes, $this->logger);
    }

    // test getView
    public function testGetView()
    {
        $controller = new \FFPerera\Cubo\Controller($this->routes, $this->logger);

        $view = $controller->getView();

        $this->assertInstanceOf(\FFPerera\Cubo\View::class, $view);
    }

    // test only pre and pos actions
    public function testOnlyPreAndPosActions()
    {

        $this->expectException(\InvalidArgumentException::class);

        $routes = [
            'app' => [
                'PRE' => [
                    new ActionC1(),
                ],
                'POS' => [
                    new ActionC2(),
                ],
            ],
        ];

        $controller = new \FFPerera\Cubo\Controller($routes, $this->logger);

        $controller->run();
    }
}
