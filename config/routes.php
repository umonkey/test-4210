<?php

/**
 * Set up the routing table.
 **/

declare(strict_types=1);

use FastRoute\RouteCollector;

return function (RouteCollector $r) {
    $r->get('/', 'App\Controllers\HomeController:index');
    $r->get('/foo/{id:[0-9]+}', 'App\Controllers\HomeController:index');
};
