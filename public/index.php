<?php

$app = require __DIR__ . '/../config/bootstrap.php';

$requestFactory = new \Nyholm\Psr7\Factory\Psr17Factory();

$requestCreator = new \Nyholm\Psr7Server\ServerRequestCreator(
    $requestFactory,
    $requestFactory,
    $requestFactory,
    $requestFactory
);

$request = $requestCreator->fromGlobals();
$response = $app->handleRequest($request);
$app->serveResponse($response);
