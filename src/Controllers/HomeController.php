<?php

/**
 * Home page handler.
 **/

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Services\AuthService;

class HomeController extends AbstractController
{
    protected $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }

    public function index(RequestInterface $request): ResponseInterface
    {
        $user = $this->auth->requireUser($request);

        return $this->sendText("OK, it works");
    }
}
