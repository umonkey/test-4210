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

    public function get(int $id): ?Task
    {
        $row = $this->db->fetchOne('SELECT * FROM tasks WHERE id = ?', [$id]);
        return new Task($row);
    }

    public function getList(int $offset, int $limit): array
    {
        $rows = $this->db->fetch('SELECT * FROM tasks ORDER BY user');

        return array_map(function (array $row) {
            return new Task($row);
        }, $rows);
    }
}
