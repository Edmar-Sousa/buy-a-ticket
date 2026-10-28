<?php declare(strict_types=1);

use Phalcon\Mvc\Router;


$router = new Router(false);



$router->addPost(
    '/reservations',
    [
        'namespace' => 'Api\\Controllers',
        'controller' => 'reservation',
        'action' => 'index'
    ]
);

$router->addGet(
    '/batch',
    [
        'namespace' => 'Api\\Controllers',
        'controller' => 'batch',
        'action' => 'index'
    ]
);

$router->addPost(
    '/batch/reset',
    [
        'namespace' => 'Api\\Controllers',
        'controller' => 'batch',
        'action' => 'reset'
    ]
);

$router->addPost(
    '/webhook/payment',
    [
        'namespace' => 'Api\\Controllers',
        'controller' => 'webhook',
        'action' => 'index',
    ]
);

return $router;
