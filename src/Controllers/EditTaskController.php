<?php

/**
 * Home page handler.
 **/

declare(strict_types=1);

namespace App\Controllers;

use App\Errors\Forbidden;
use App\Repositories\TaskRepository;
use App\Services\AuthService;
use App\Services\TemplateService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class EditTaskController extends AbstractController
{
    /**
     * @var AuthService
     **/
    protected $auth;

    /**
     * @var TaskRepository
     **/
    protected $tasks;

    /**
     * @var TemplateService
     **/
    protected $template;

    public function __construct(AuthService $auth, TaskRepository $tasks, TemplateService $templates)
    {
        $this->auth = $auth;
        $this->tasks = $tasks;
        $this->template = $templates;
    }

    public function index(RequestInterface $request, array $args): ResponseInterface
    {
        $user = $this->auth->requireUser($request);
        $task = $this->tasks->get((int)$args['id']);
        $token = $this->auth->getCsrfToken($request);

        $html = $this->template->render('pages/edit.twig', [
            'user' => $user,
            'task' => $task,
            'csrf_token' => $token,
        ]);

        return $this->sendHtml($html);
    }

    public function save(RequestInterface $request, array $args): ResponseInterface
    {
        $user = $this->auth->getUser($request);

        if (null === $user) {
            return $this->failJSON('Для изменения задач нужно авторизоваться.  Пройдите на главную страницу.');
        }

        if (null === $user || $user->getName() != 'admin') {
            return $this->failJSON('Нет доступа.');
        }

        if (!$this->auth->checkCsrfToken($request)) {
            return $this->failJSON('Форма устарела, обнови страницу.');
        }

        $form = array_replace([
            'user' => '',
            'email' => '',
            'text' => '',
            'completed' => '',
        ], $request->getParsedBody());

        $task = $this->tasks->get((int)$args['id']);
        $task->setUser($form['user']);
        $task->setEmail($form['email']);
        $task->setText($form['text']);
        $task->setCompleted($form['completed'] === 'yes');
        $task->setEdited(true);

        $this->tasks->save($task);

        return $this->sendJSON([
            'result' => [
                'redirect' => '/',
            ],
        ]);
    }
}
