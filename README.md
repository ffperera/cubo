# Cubo

Cubo is a lightweight PHP framework designed for building web applications with simplicity and flexibility.

It provides a minimalist but solid foundation for the development process, with zero hidden magic features, and avoiding overengineered techniques, while ensuring a clean and modular architecture.

**NOTE**: While Cubo can certainly be used in real-world production projects, there are more established frameworks with strong community support like [Symfony](https://symfony.com/) and [Laravel](https://laravel.com/). We recommend considering these alternatives for enterprise-level applications requiring extensive ecosystem support.

## Features

- **Lightweight and Fast**: Minimal overhead for optimal performance.
- **Modular Design**: Easily extendable and customizable.
- **Queue of Actions**: Queue-based workflow where requests are processed through sequential actions (_controller-like_ components).
- **PSR-4 Autoloading**: Follows modern PHP standards for autoloading.
- **View Rendering**: Built-in support for rendering views and layouts.
- **HTTP Response Handling**: Simplifies response management.

## Requirements

- PHP 8.1 or higher
- Composer

## Installation

Install using Composer:

```bash
composer install ffperera/cubo
```

## Usage

Think of Cubo as a tool designed to manage request routing and the tasks and services linked to those routes.

As we'll explore later, Cubo operates through executing sequences of tasks, encapsulated as `Action` objects.

A Cubo-based project can be organized in countless ways. The framework is intentionally project-structure agnostic. You’re free to adopt whatever project layout best suits your needs, and you can use external components, packages, or services as required.

However, it’s important to note that Cubo’s core philosophy centers on building ultra-lightweight applications.

### Project structure

A typical project using Cubo looks like this:

```
├── dev
│   └── scss
├── root
│   └── assets
│   │   └── pub
│   │       └── css
│   └── index.php
├── src
│   ├── config
│   │   └── routing.php
│   ├── Api
│   │
│   ├── Adm
│   │   ├── Global
│   │   │   └── Actions
│   │   ├── Home
│   │   │   └── Actions
│   │   ├── Login
│   │   │   └── Actions
│   │   ├── Menu
│   │   │   └── Actions
│   │   ├── PDO
│   │   │   └── Actions
│   │   ├── User
│   │   │    ├── Actions
│   │   │    ├── Model
│   │   │    └── Repository
│   │   └── layout
│   └── Pub
│       ├── Global
│       │   └── Actions
│       ├── Home
│       │   └── Actions
│       ├── Menu
│       │   └── Actions
│       ├── User
│       │   └── Actions (Register, Login ...)
│       └── layout
│
└── tests
```

### Entry point

The app entry point is **/root/index.php**

This file is located in the **public** directory (root folder) of the HTTP server.

Public resources and assets like images and CSS files should be placed in the **/root** directory.

All other folders reside outside the root directory and cannot be accessed directly from external sources.

### Sections

Each section can maintain its own:

- Layout templates
- Actions
- Data layers
- ...and other components

These sections can be converted into independent services (eg. micro services) with minimal refactoring.

### Action queues

An `Action` is a class that performs specific tasks.

Developers can:

- Define action sequences (e.g., using a routing file) to handle requests
- Implement controller-like actions that manage request-specific operations
- Create middleware actions that execute before primary actions
- Utilize actions for dependency injection

There are three action queues:

- _pre_: Handles setup tasks, dependency injections, and middleware
- _main_: _Controller-like_ actions tied to specific routes
- _pos_: Manages cleanup operations

Action queues are dynamic ones.

- Add new actions _mid-execution_, we can insert new Actions in the queue from the actual Action.
- Remove existing actions dynamically
- Implement error recovery flows

Example: If a request fails, abort the current action and insert a fallback action to handle the failure.

### Action

Example of one `Action` class.

```php
class PostList extends Action
{
  public function __construct(private IPostRepository $repo) {}

  public function run(Controller $controller): void
  {

    // initialize the repository with the PDO connection
    try {
        $this->repo->init(['PDO' => $controller->get('PDO')]);
    }
    catch (\Exception $e) {
        // TODO: handle the exception
    }

    // fetch posts from the repository
    $posts = $this->repo->getPosts();

    $view = $controller->getView();

    $view->set('posts', $posts);

    $view->set('title', 'POSTS');
    $view->set('post-list-intro', 'This is the list of posts');


    $view->setLayout('/Pub/layout/main.php');
    $view->setTemplate('main', '/Pub/layout/post_list.php');

    $view->set('metatitle', 'List of Posts');
    $view->set('metadesc', 'Published posts');
    $view->set('canonical', '/blog/');

  }

}
```

### Main controller

The `Controller` object acts as Cubo's orchestration center. It is like the **kernel** component of other frameworks.

It handles:

- Request routing management
- Action queues
- Dependency injection (e.g., services)
- Access to core Cubo components (`Request`, `View`, `Render`, `Response`)

Once configured, your application primarily operates through the `Controller::run()` method call.

```php
$controller = new Controller($routes, $logger, $debug=true);

try {
    $view = $controller->run();

    if ($view) {
        $render = new Render($view, $srcDir);
        $render->send();
    }

} catch (Exception $e) {
    // Handle exceptions and errors
    echo 'Error: ' . $e->getMessage();
}
```

### Routing

The routing file contains an array defining:

- All possible entry points for each section
- Associated actions for pre and post queues

Example:

```php

    'app' => [
        'home' => [
            'action' => new App\Pub\Home\Actions\App(new App\Pub\Post\Repository\PostRepositoryPDO()),
            'path' =>   '/',
            'method' => 'GET',
        ],
        'blog' => [
            'action' => new App\Pub\Post\Actions\PostList(new App\Pub\Post\Repository\PostRepositoryPDO()),
            'path' =>   '/blog/',
            'method' => 'GET',
        ],
        'user-add' => [
            'action' => new App\Pub\User\Actions\UserRegisterForm(),
            'path' =>   '/register/',
            'method' => 'GET',
        ],
        'user-add-sav' => [
            'action' => new App\Pub\User\Actions\UserRegister(new App\Adm\User\Repository\UserRepositoryPDO()),
            'path' =>   '/register/',
            'method' => 'POST',
        ],
        'login' => [
            'action' => new App\Pub\User\Actions\UserLoginForm(),
            'path' =>   '/login/',
            'method' => 'GET',
        ],
        'login-in' => [
            'action' => new App\Pub\User\Actions\UserLogin(new App\Adm\User\Repository\UserRepositoryPDO()),
            'path' =>   '/login/',
            'method' => 'POST',
        ],
        'logout' => [
            'action' => new App\Pub\User\Actions\UserLogout(),
            'path' =>   '/logout/',
            'method' => 'GET',
        ],
        'PRE' => [
                    new App\Pub\Global\Actions\Session(new \FFPerera\Lib\Session(1800)),
                    new App\Adm\PDO\Actions\PDOConnection(),  // inject the PDO connection
                    new App\Pub\Menu\Actions\Menu()  // dynamic menu
                 ],
        'POS' => [],
    ],

```

### Rendering Views

The **Render** class is responsible for rendering views and layouts.

We can render directly to the client or render to a `Response` object depending on our needs.

Here's an example of how to use it:

```php

// inside the Action::run() method
$view = $controller->getView();
$view->setLayout('layout.php');
$view->setTemplate('content', 'content.php');

// inside the Action or in the main entry point
if ($view && $view instanceof \FFPerera\Cubo\View) {
    $render = new Render($view, $srcDir);
    $render->send();
}
```

Render can use PHP templates:

```html
<div class="container">
  <header><?php if (isset($this)) { $this->block('menu'); } ?></header>

  <?php if (isset($this)) { $this->block('content'); } ?>

  <footer class="row">
    <p>This is the footer</p>
  </footer>
</div>
```

In the `View` object, you can configure a single layout (e.g., an HTML skeleton) and include multiple templates and variables as needed.

Templates can be invoked from the layout or other templates using the `Render::block()` method.

Data injected into the `View` by `Actions` can be accessed using the `View::get()` method.

Additionally, you can create a custom `Render` subclass to integrate third-party template engines seamlessly.

In this [demo](https://github.com/ffperera/cubo-demo) project, we use the [Latte](https://latte.nette.org/en/) template engine and PHP templates working together.

### HTTP Responses

The **Response** class allows you to manage HTTP headers, status codes, and redirections.

```php
$response = new Response('{"key": "value"}', $options =[
        'headers' => [
            'Content-Type' => 'application/json; charset=UTF-8',
        ],
        'statusCode' => 200,
        'statusText' => 'OK',
        'protocolVersion' => '1.1',
]);

$response = $response->withHeader('X-Custom-Header', 'CustomValue');

$response->send();
```

## Contributing

Contributions are welcome!

Please fork the repository, create a feature branch, and submit a pull request.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
