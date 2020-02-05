<?php

/**
 * Home page handler.
 **/

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\TaskRepository;
use App\Services\AuthService;
use App\Services\TemplateService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HomeController extends AbstractController
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

    public function index(RequestInterface $request): ResponseInterface
    {
        $tasks = $this->tasks->getList(0, 3);

        $html = $this->template->render('pages/home.twig', [
            'tasks' => $tasks,
        ]);

        return $this->sendHtml($html);
    }
}
