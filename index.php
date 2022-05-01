<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';
require_once "functions.php";


if (!headers_sent()) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: *');
    header('Access-Control-Allow-Headers: *');
    header('Content-Type: application/json; charset=utf-8');

} else {
    // обработка ошибки или уведомление разработчикам
}


$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
//$dispatcher = FastRoute\cachedDispatcher(function(FastRoute\RouteCollector $r) {

    $r->addRoute('GET', '/', 'info');
    $r->addRoute('GET', '/items', 'getAllItems');
    $r->addRoute('GET', '/items/{uuid}', 'getOneItem');
    $r->addRoute('GET', '/bread_crumbs/{uuid}', 'breadCrumbs');
    $r->addRoute('POST', '/items', 'postItem');
    $r->addRoute('PUT', '/items/{uuid}', 'putOneItem');
    $r->addRoute('DELETE', '/items', 'deleteItems');

    $r->addRoute('GET', '/backup', 'makeBackup');
    $r->addRoute('POST', '/backup', 'makeBackup');

    // важливо! достатньо одного типу "files" для всього (zip, doc, excel, jpg, png, mp4).
    // Можна по формату автоматично визначити чи показувати превю для зображень


}, [
    'cacheFile' => __DIR__ . '/route.cache', /* required */
    'cacheDisabled' => false,     /* optional, enabled by default */
]);

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

$uri = rawurldecode($uri);
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);



switch ($routeInfo[0])
{
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1]; // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
		$handler($vars);
		
        break;
}