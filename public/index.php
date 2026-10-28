<?php declare(strict_types=1);


require_once __DIR__ . '/../vendor/autoload.php';



use Api\Repository\WebhookRepository;
use Phalcon\Autoload\Loader;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;
use Phalcon\Http\Response;
use Phalcon\Db\Adapter\Pdo\Postgresql as PdoPostgre;
use Phalcon\Mvc\View;

use Api\Repository\ReservationRepository;
use Api\Repository\BatchRepository;
use Api\Exceptions\CustomException;


$loader = new Loader();
$loader->setNamespaces([
    'Api\Models' => __DIR__ . '/../src/Models/'
]);
$loader->register();


$api = new FactoryDefault();
$routes = require_once __DIR__ . '/../src/routes.php';

$api->setShared('router', $router);
$api->setShared('view', fn () => (new View())->disable());
$api->setShared('db', function() {
    return new PdoPostgre([
        'host'     => $_ENV['DB_HOST'],
        'username' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASS'],
        'dbname'   => $_ENV['DB_DATABASE'],
    ]);
});

$api->setShared('reservationRepository', fn () => new ReservationRepository());
$api->setShared('batchRepository', fn () => new BatchRepository());
$api->setShared('webhookRepository', fn () => new WebhookRepository());

$app = new Application($api);


/// Try put this code in the middleware
$response = new Response();
$response->setContentType('application/json', 'UTF-8');


try 
{
    $response = $app->handle($_SERVER["REQUEST_URI"]);
    $response->send();
}

catch (CustomException $err) 
{
    $response->setStatusCode($err->getCode());
    $content = ['error' => $err->getStatusText()];

    if ($err->hasErrors())
        $content['messages'] = $err->getErrors();

    $response->setJsonContent($content);
    $response->send();
}

catch (Exception $err)
{
    $response->setStatusCode(500);
    $response->setJsonContent(['error' => 'INTERNAL_ERROR']);
    $response->send();
}

