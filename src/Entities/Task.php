<?php

/**
 * Task entity.
 **/

declare(strict_types=1);

namespace App\Entities;

class Task
{
    /**
     * @var int
     **/
    protected $id;

    /**
     * @var string
     **/
    protected $user;

    /**
     * @var string
     **/
    protected $email;

    /**
     * @var string
     **/
    protected $text;

    /**
     * @var bool
     **/
    protected $completed;

    public function __construct(array $row)
    {
        $this->id = (int)$row['id'];
        $this->user = $row['user'];
        $this->email = $row['email'];
        $this->text = $row['text'];
        $this->completed = (bool)$row['completed'];
    }

    public function asArray(): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user,
            'email' => $this->email,
            'text' => $this->text,
            'completed' => $this->completed,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getCompleted(): bool
    {
        return (bool)$this->completed;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }
}
