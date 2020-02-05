<?php

/**
 * Basic application.
 **/

namespace App;

use function FastRoute\simpleDispatcher;
use FastRoute\RouteCollector;
use Pimple\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class App
{
    /**
     * @var RouteCollector
     **/
    protected $routes;

    /**
     * Dependency container.
     *
     * @var Container
     **/
    protected $container;

    public function __construct()
    {
        $this->routes = $this->loadRoutes();
        $this->container = $this->loadContainer();
    }

    public function handleRequest(RequestInterface $request): ResponseInterface
    {
        set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
            if (!(error_reporting() & $errno)) {
                return false;
            }

            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        });

        $routeInfo = $this->matchRoute($request);

        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND;
                $res =$this->notFound($request);
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $res = $this->methodNotAllowed($request);
            case \FastRoute\Dispatcher::FOUND:
                $res = $this->dispatchRequest($request, $routeInfo[1], $routeInfo[2]);
        }

        restore_error_handler();

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
        try {
            $parts = explode(':', $handler, 2);
            if (count($parts) != 2) {
                throw new \RuntimeException('bad method handler');
            }

            $className = $parts[0];
            $methodName = $parts[1];

            if (!class_exists($parts[0])) {
                throw new \RuntimeException("class {$className} not found");
            }

            $instance = $this->getHandlerInstance($className);

            return $instance->$methodName($request, $args);
        } catch (\Throwable $e) {
            dd($e);  // FIXME: render an error message
        }
    }

    /**
     * Create the handler object, inject dependencies.
     **/
    protected function getHandlerInstance(string $className): object
    {
        $args = [];

        $refClass = new \ReflectionClass($className);
        if ($refClass->hasMethod('__construct')) {
            $refMethod = $refClass->getConstructor();
            foreach ($refMethod->getParameters() as $param) {
                $serviceName = $param->getName();
                $args[] = $this->container[$serviceName];
            }
        }

        $handlerInstance = $refClass->newInstanceArgs($args);
        return $handlerInstance;
    }

    protected function loadContainer(): Container
    {
        $container = new Container();
        require __DIR__ . '/../config/dependencies.php';
        return $container;
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

    protected function notFound(RequestInterface $request): ResponseInterface
    {
        dd('page not found');  // TODO: render template
    }

    protected function methodNotAllowed(RequestInterface $request): ResponseInterface
    {
        dd('method not allowed');  // TODO: render template
    }
}
