<?php

/**
 * Basic utility methods for all controllers.
 **/

declare(strict_types=1);

namespace App\Controllers;

use Nyholm\Psr7\Response;

abstract class AbstractController
{
    protected function failJSON($message): Response
    {
        return $this->sendJSON([
            'error' => $message,
        ]);
    }

    protected function sendJSON(array $data): Response
    {
        $data = json_encode($data);

        $headers = [
            'Content-Type' => 'application/json',
        ];

        return new Response(200, $headers, $data);
    }

    protected function sendHtml(string $html, int $status = 200): Response
    {
        $headers = [
            'Content-Type' => 'text/html',
        ];

        return new Response($status, $headers, $html);
    }

    protected function sendText(string $body, int $status = 200): Response
    {
        $headers = [
            'Content-Type' => 'text/plain',
        ];

        return new Response($status, $headers, $body);
    }
}
