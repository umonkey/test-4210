<?php

/**
 * Basic application.
 **/

namespace App;

use function FastRoute\simpleDispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class App
{
    /**
     * @var
     **/
    protected $router;

    public function __construct()
    {
        $this->routes = $this->loadRoutes();
    }

    public function handleRequest(RequestInterface $request): ResponseInterface
    {
        $routeInfo = $this->matchRoute($request);

        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND;
                $res =$this->notFound($request);
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $res = $this->methodNotAllowed($request);
            case \FastRoute\Dispatcher::FOUND:
                $res = $this->dispatchRequest($request, $routeInfo[1], $routeInfo[2]);
        }

        return $res;
    }

    public function serveResponse(ResponseInterface $response): void
    {
        if (headers_sent()) {
            throw new \RuntimeException('response workflow broken');
        }

        header(sprintf('HTTP/1.1 %u %s', $response->getStatusCode(), $response->getReasonPhrase()));

        foreach ($response->getHeaders() as $name => $values) {
            $values = \implode(', ', $values);
            header(sprintf('%s: %s', $name, $values));
        }

        $body = strval($response->getBody());
        die($body);
    }

    protected function dispatchRequest(RequestInterface $request, string $handler, array $args)
    {
        $parts = explode(':', $handler, 2);
        if (count($parts) != 2) {
            throw new \RuntimeException('bad method handler');
        }

        $className = $parts[0];
        $methodName = $parts[1];

        if (!class_exists($parts[0])) {
            throw new \RuntimeException("class {$className} not found");
        }

        $handlerInstance = new $className();
        return $handlerInstance->$methodName($request, $args);
    }

    protected function loadRoutes()
    {
        $routes = require __DIR__ . '/../config/routes.php';
        return $routes;
    }

    protected function matchRoute(RequestInterface $request)
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();

        $dispatcher = simpleDispatcher($this->routes);

        $routeInfo = $dispatcher->dispatch($method, $path);
        return $routeInfo;
    }

    protected function notFound(RequestInterface $request)
    {
        dd($request);
    }
}
