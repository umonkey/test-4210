<?php

/**
 * Display and handle authentication form.
 **/

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\TemplateService;
use App\Repositories\UserRepository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class LoginController extends AbstractController
{
    /**
     * @var AuthService
     **/
    protected $auth;

    /**
     * @var UserRepository
     **/
    protected $users;

    /**
     * @var TemplateService
     **/
    protected $template;

    public function __construct(AuthService $auth, TemplateService $templates, UserRepository $users)
    {
        $this->auth = $auth;
        $this->template = $templates;
        $this->users = $users;
    }

    public function index(RequestInterface $request): ResponseInterface
    {
        $html = $this->template->render('pages/login.twig', [
            'csrf_token' => $this->auth->getCsrfToken($request),
        ]);

        return $this->sendHtml($html);
    }

    public function process(RequestInterface $request): ResponseInterface
    {
        $form = $request->getParsedBody();

        if (!$this->auth->checkCsrfToken($request)) {
            return $this->sendJSON([
                'error' => 'Форма устарела, обнови страницу.',
            ]);
        }

        $form = array_replace([
            'login' => null,
            'password' => null,
        ], $form);

        $user = $this->users->getByLogin($form['login']);

        if (null === $user) {
            return $this->sendJSON([
                'error' => 'Нет такого пользователя.',
            ]);
        }

        if (!$user->checkPassword($form['password'])) {
            return $this->sendJSON([
                'error' => 'Пароль не подходит.',
            ]);
        }

        return $this->sendJSON([
            'result' => [
                'redirect' => '/',
            ],
        ]);
    }
}
