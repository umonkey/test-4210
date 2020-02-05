<?php

/**
 * Log out.
 **/

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class LogoutController extends AbstractController
{
    /**
     * @var AuthService
     **/
    protected $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }

    public function index(RequestInterface $request): ResponseInterface
    {
        $this->auth->logOut($request);

        return $this->sendJSON([
            'result' => [
                'redirect' => '/',
            ],
        ]);
    }
}
