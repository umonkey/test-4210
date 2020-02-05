<?php

/**
 * Render templates using Twig.
 **/

declare(strict_types=1);

namespace App\Services;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TemplateService
{
    /**
     * @var array
     **/
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function render(string $templateName, array $data = []): string
    {
        $root = $this->config['root'];

        $loader = new Filesystemloader($root);
        $twig = new Environment($loader);

        $template = $twig->load($templateName);
        $html = $template->render($data);

        return $html;
    }
}
