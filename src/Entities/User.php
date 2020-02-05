<?php

/**
 * User entity.
 **/

declare(strict_types=1);

namespace App\Entities;

class User
{
    protected $id;

    protected $name;

    protected $password;

    public function __construct(array $row)
    {
        $this->id = (int)$row['id'];
        $this->name = $row['name'];
        $this->password = $row['password'];
    }

    public function asArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'password' => $this->password,
        ];
    }

    public function checkPassword(string $given): bool
    {
        // Password login disabled.
        if (empty($this->password)) {
            return false;
        }

        return password_verify($given, $this->getPassword());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($passwor, PASSWORD_DEFAULT);
    }
}
