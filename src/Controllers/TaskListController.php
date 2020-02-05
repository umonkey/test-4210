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
        $form = $request->getParsedBody();

        $draw = $form['draw'];
        $start = (int)$form['start'];
        $limit = (int)$form['length'];

        $total = $this->tasks->count();

        $order = $this->getOrder($form);
        $tasks = $this->tasks->getList($start, $limit, $order);


        $data = [
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $this->formatTasks($tasks, $isAdmin),
            'draw' => $draw,
        ];

        return $this->sendJSON($data);
    }

    private function getOrder(array $form): array
    {
        $order = [];
        $columns = ['user', 'email', null, 'completed', null];

        foreach ($form['order'] as $col) {
            $dir = $col['dir'] === 'asc' ? 'ASC' : 'DESC';
            $column = $columns[(int)$col['column']];

            if ($column !== null) {
                $order[$column] = $dir;
            }
        }

        return $order;
    }

    private function formatTasks(array $tasks, bool $isAdmin): array
    {
        $tasks = array_map(function (Task $task) use ($isAdmin) {
            $text = htmlspecialchars($task->getText());

            if ($task->getEdited()) {
                $text .= "<br/>Отредактировано администратором";
            }

            return [
                htmlspecialchars($task->getUser()),
                htmlspecialchars($task->getEmail()),
                $text,
                $task->getCompleted() ? 'completed' : 'pending',
                $isAdmin ? "/tasks/{$task->getId()}/edit" : null,
            ];
        }, $tasks);

        return $tasks;
    }
}
