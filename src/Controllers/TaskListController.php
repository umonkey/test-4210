<?php

/**
 * Returns task list in JSON.
 **/

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Task;
use App\Repositories\TaskRepository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class TaskListController extends AbstractController
{
    /**
     * @var TaskRepository
     **/
    protected $tasks;

    public function __construct(TaskRepository $tasks)
    {
        $this->tasks = $tasks;
    }

    public function index(RequestInterface $request): ResponseInterface
    {
        $total = $this->tasks->count();

        $tasks = $this->tasks->getList(0, 300);

        $tasks = array_map(function (Task $task) {
            return [
                $task->getUser(),
                $task->getEmail(),
                $task->getText(),
                $task->getCompleted() ? 'completed' : 'pending',
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
