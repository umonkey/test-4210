<?php

/**
 * Application settings.
 **/

declare(strict_types=1);

$settings = [
    'database' => [
        'dsn' => 'sqlite:' . __DIR__ . '/../var/database.sqlite',
    ],
];

return $settings;
