<?php

/**
 * Application settings.
 **/

declare(strict_types=1);

$settings = [
    'database' => [
        'dsn' => 'sqlite:' . __DIR__ . '/../var/database.sqlite',
    ],

    'templates' => [
        'root' => __DIR__ . '/../templates',
    ],
];

return $settings;
