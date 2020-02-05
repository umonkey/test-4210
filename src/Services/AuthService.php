<?php

/**
 * Authentication service.
 *
 * Deals with user authorization, checks permissions.
 *
 * Requires a separate session service.
 **/

declare(strict_types=1);

namespace App\Services;

use Psr\Http\Message\RequestInterface;
use App\Services\SessionService;
use App\Entities\User;
use App\Repositories\UserRepository;
use App\Errors\Unauthorized;

class AuthService
{
    /**
     * @var SessionService
     **/
    protected $session;

    /**
     * @var UserRepository
     **/

    public function __construct(SessionService $session, UserRepository $users)
    {
        $this->session = $session;
        $this->users = $users;
    }

    public function requireUser(RequestInterface $request): User
    {
        $user = $this->getUser($request);

        if (null === $user) {
            throw new Unauthorized();
        }

        return $user;
    }

    public function getUser(RequestInterface $request): ?User
    {
        $session = $this->session->getData($request);

        if (empty($session['user_id'])) {
            return null;
        }

        $user = $this->users->get($session['user_id']);
        return $user;
    }
}
