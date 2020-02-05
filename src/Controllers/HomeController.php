<?php

/**
 * Home page handler.
 **/

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Services\AuthService;
use App\Repositories\TaskRepository;

class HomeController extends AbstractController
{
    /**
     * @var AuthService
     **/
    protected $auth;

    /**
     * @var TaskRepository
     **/
    protected $tasks;

    public function __construct(AuthService $auth, TaskRepository $tasks)
    {
        $this->auth = $auth;
        $this->tasks = $tasks;
    }

    public function index(RequestInterface $request): ResponseInterface
    {
        $tasks = $this->tasks->getList(0, 3);

        dd($tasks);

        return $this->sendText("OK, it works");
    }
}
