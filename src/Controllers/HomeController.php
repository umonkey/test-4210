<?php

/**
 * Home page handler.
 **/

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HomeController extends AbstractController
{
    public function index(RequestInterface $request): ResponseInterface
    {
        return $this->sendText("OK, it works");
    }
}
