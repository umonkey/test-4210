<?php

/**
 * Dependency container setup.
 **/

declare(strict_types=1);

use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\DatabaseService;
use App\Services\SessionService;

$container['auth'] = function ($c) {
    $session = $c['session'];
    $users = $c['users'];
    return new AuthService($session, $users);
};

$container['db'] = function ($c) {
    $settings = $c['settings']['database'] ?? [];
    return new DatabaseService($settings);
};

$container['session'] = function ($c) {
    $db = $c['db'];
    return new SessionService($db);
};

$container['settings'] = function ($c) {
    return require __DIR__ . '/settings.php';
};

$container['users'] = function ($c) {
    $db = $c['db'];
    return new UserRepository($db);
};
