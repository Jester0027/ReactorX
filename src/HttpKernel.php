<?php

namespace Jester0027\Phuck;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;

/**
 * @author Paul N. Etienne <paul.ned@outlook.com>
 */
class HttpKernel
{
    protected HttpServer $server;
    protected SocketServer $socket;
    protected LoopInterface $loop;
    protected ComponentScanner $classScanner;

    protected array $controllers;

    public static function createServer(HttpKernelConfiguration $configuration = new HttpKernelConfiguration()): self
    {
        return new self($configuration);
    }

    public function run(): self
    {
        $this->loop->run();
        return $this;
    }

    private function __construct(protected HttpKernelConfiguration $configuration)
    {
        $this->classScanner = new ComponentScanner();
        $this->classScanner->scanDirectory($configuration->projectDir);
        $this->loop = Loop::get();
        $this->server = new HttpServer($this->getRequestHandler());

        $this->socket = new SocketServer("0.0.0.0:$configuration->port", [], $this->loop);
        $this->server->listen($this->socket);
    }

    /**
     * @return Closure
     */
    public function getRequestHandler(): Closure
    {
        return function (ServerRequestInterface $request) {
            $actions = $this->classScanner->getActionsMappings();
            $container = $this->classScanner->container;
            $httpVerb = $request->getMethod();
            $path = rtrim($request->getUri()->getPath(), '/');
            $action = $actions[$httpVerb][$path];
            if (!isset($action)) {
                return new Response($request->getMethod() === 'GET' ? 404 : 405);
            }
            $className = $action[0];
            $actionName = $action[1];
            $controller = $container->get($className);
            return $controller->$actionName($request);
        };
    }
}

