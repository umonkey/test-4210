<?php

/**
 * Task repository.
 **/

declare(strict_types=1);

namespace App\Repositories;

use App\Services\DatabaseService;
use App\Entities\Task;

class TaskRepository
{
    protected $db;

    public function __construct(DatabaseService $db)
    {
        $this->db = $db;
    }

    public function add(Task $task): void
    {
        $id = $this->db->insert('tasks', [
            'id' => $task->getId(),
            'user' => $task->getUser(),
            'email' => $task->getEmail(),
            'text' => $task->getText(),
            'completed' => $task->getCompleted(),
        ]);

        $task->setId($id);
    }

    public function count(): int
    {
        return (int)$this->db->fetchCell('SELECT COUNT(1) FROM tasks');
    }

    public function get(int $id): ?Task
    {
        $row = $this->db->fetchOne('SELECT * FROM tasks WHERE id = ?', [$id]);
        return new Task($row);
    }

    public function getList(int $offset, int $limit, array $order = []): array
    {
        $query = "SELECT * FROM tasks";

        if (!empty($order)) {
            $_order = [];
            foreach ($order as $k => $v) {
                $_order[] = sprintf('%s %s', $k, $v);
            }
            $query .= ' ORDER BY ' . implode(', ', $_order);
        }

        $query .= sprintf(" LIMIT %u, %u", $offset, $limit);

        $rows = $this->db->fetch($query);

        return array_map(function (array $row) {
            return new Task($row);
        }, $rows);
    }

    public function save(Task $task): void
    {
        $this->db->update('tasks', [
            'user' => $task->getUser(),
            'email' => $task->getEmail(),
            'text' => $task->getText(),
            'completed' => $task->getCompleted(),
            'edited' => $task->getEdited(),
        ], [
            'id' => $task->getId(),
        ]);
    }
}
