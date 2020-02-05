<?php

/**
 * Basic utility methods for all controllers.
 **/

declare(strict_types=1);

namespace App\Controllers;

use Nyholm\Psr7\Response;

abstract class AbstractController
{
    protected function sendJSON(array $data): Response
    {
        dd($data);
    }

    protected function sendHtml(string $html, int $status = 200): Response
    {
        dd($html);
    }

    protected function sendText(string $body, int $status = 200): Response
    {
        $headers = [
            'Content-Type' => 'text/plain',
        ];

        return new Response($status, $headers, $body);
    }
}
