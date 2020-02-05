<?php

/**
 * Returns task list in JSON.
 **/

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Task;
use App\Repositories\TaskRepository;
use App\Services\AuthService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class TaskListController extends AbstractController
{
    /**
     * @var TaskRepository
     **/
    protected $tasks;

    /**
     * @var AuthService
     **/
    protected $auth;

    public function __construct(TaskRepository $tasks, AuthService $auth)
    {
        $this->tasks = $tasks;
        $this->auth = $auth;
    }

    public function index(RequestInterface $request): ResponseInterface
    {
        $user = $this->auth->getUser($request);
        $isAdmin = $user && $user->getName() === 'admin';

        $total = $this->tasks->count();

        $tasks = $this->tasks->getList(0, 300);

        $tasks = array_map(function (Task $task) use ($isAdmin) {
            return [
                $task->getUser(),
                $task->getEmail(),
                $task->getText(),
                $task->getCompleted() ? 'completed' : 'pending',
                $isAdmin ? "/tasks/{$task->getId()}/edit" : null,
            ];
        }, $tasks);

        $data = [
            'iTotalRecords' => $total,
            'iTotalDisplayRecords' => count($tasks),
            'aaData' => $tasks,
        ];

        return $this->sendJSON($data);
    }
}
