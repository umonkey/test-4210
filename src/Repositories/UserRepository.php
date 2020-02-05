<?php

/**
 * User repository.
 **/

declare(strict_types=1);

namespace App\Repositories;

use App\Services\DatabaseService;
use App\Entities\User;

class UserRepository
{
    /**
     * @var DatabaseService
     **/
    protected $db;

    public function __construct(DatabaseService $db)
    {
        $this->db = $db;
    }

    public function get(int $id): ?User
    {
        $row = $this->db->fetchOne('SELECT * FROM users WHERE id = ?', [$id]);
        return $row ? new User($row) : null;
    }

    public function getByLogin(string $login): ?User
    {
        $row = $this->db->fetchOne('SELECT * FROM users WHERE name = ?', [$login]);
        return $row ? new User($row) : null;
    }
}
