<?php

/**
 * Set up the routing table.
 **/

declare(strict_types=1);

use FastRoute\RouteCollector;

return function (RouteCollector $r) {
    $r->get('/', 'App\Controllers\HomeController:index');
    $r->get('/tasks.json', 'App\Controllers\TaskListController:index');
    $r->post('/tasks/add', 'App\Controllers\AddTaskController:index');
};
