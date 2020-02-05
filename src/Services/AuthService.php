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

    public function checkCsrfToken(RequestInterface $request): bool
    {
        $form = $request->getParsedBody();

        if (empty($form['csrf_token'])) {
            return false;
        }

        $session = $this->session->getData($request);
        if (empty($session['csrf_token'])) {
            return false;
        }

        return $form['csrf_token'] === $session['csrf_token'];
    }

    public function getCsrfToken(RequestInterface $request): string
    {
        $session = $this->session->getData($request);

        if (empty($session['csrf_token'])) {
            $session['csrf_token'] = $this->getNewCsrfToken();
            $this->session->setData($request, $session);
        }

        return $session['csrf_token'];
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

    public function logOut(RequestInterface $request): void
    {
        $session = $this->session->getData($request);

        unset($session['user_id']);
        unset($session['csrf_token']);

        $this->session->setData($request, $session);
    }

    public function requireUser(RequestInterface $request): User
    {
        $user = $this->getUser($request);

        if (null === $user) {
            throw new Unauthorized();
        }

        return $user;
    }

    public function setUser(RequestInterface $request, User $user): void
    {
        $session = $this->session->getData($request);
        $session['user_id'] = $user->getId();
        $this->session->setData($request, $session);
    }

    protected function getNewCsrfToken(): string
    {
        $token = bin2hex(random_bytes(16));
        return $token;
    }
}
