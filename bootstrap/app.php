<?php

use Respect\Validation\Validator as v;

session_start();

require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
    
    'db' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'slim-fit',
        'username' => 'root',
        'password' => 'mysql',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
    ]
],
]);

$container = $app->getContainer();


$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

$container['auth'] = function ($constainer) {
    return new \App\Auth\Auth;
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
        'cache' => false,

    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    //twig extension for debugging
    $view->addExtension(new Twig_Extension_Debug());

    $view->getEnvironment()->addGlobal('auth', [
        // so the view can access auth.check and auth.user
        'check' => $container->auth->check(), //from Auth Class method check()
        'user' => $container->auth->user(), //
    ]);

    $view->getEnvironment()->addGlobal('flash', $container->flash); 

    return $view;
};

$container['validator'] = function($container){
    return new App\Validation\Validator;
};

$container['HomeController'] = function ($container) {
    return new \App\Controllers\HomeController($container);
};

$container['AuthController'] = function ($container) {
    return new \App\Controllers\Auth\AuthController($container);
};

$container['csrf'] = function ($constainer) {
    return new \Slim\Csrf\Guard;
};

$container['flash'] = function ($container) {
    return new \Slim\Flash\Messages;
};

$container['PasswordController'] = function ($container) {
    return new \App\Controllers\Auth\PasswordController($container);
};



$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));


$app->add($container->csrf);

//pointing to custom validation rule path
v::with('App\\Validation\\Rules\\');


require __DIR__ . '/../app/routes.php';
