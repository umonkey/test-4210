<?php

/**
 * Creates a new task.
 **/

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Task;
use App\Repositories\TaskRepository;
use App\Services\AuthService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AddTaskController extends AbstractController
{
    /**
     * @var AuthService
     **/
    protected $auth;

    /**
     * @var TaskRepository
     **/
    protected $tasks;

    public function __construct(TaskRepository $tasks, AuthService $auth)
    {
        $this->tasks = $tasks;
        $this->auth = $auth;
    }

    public function index(RequestInterface $request): ResponseInterface
    {
        $form = $request->getParsedBody();
        $form['completed'] = false;

        if (!$this->auth->checkCsrfToken($request)) {
            return $this->sendJSON([
                'error' => 'Форма устарела, обнови страницу.',
            ]);
        }

        $task = new Task($form);
        $this->tasks->add($task);

        return $this->sendJSON([
            'result' => [
                'success' => true,
                'refresh' => true,
                'message' => 'Задача добавлена, спасибо.',
            ],
        ]);
    }
}
