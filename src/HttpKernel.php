<?php

namespace Jester0027\Phuck;

use Closure;
use DI\Container;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;

class HttpKernel
{
    use HttpKernelTrait;

    protected HttpServer $server;
    protected SocketServer $socket;
    protected LoopInterface $loop;
    protected ClassScanner $classScanner;

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
        $this->classScanner = new ClassScanner();
        $this->classScanner->scanDirectory($configuration->projectDir);
        $actions = $this->classScanner->getActionsMappings();
        $container = $this->classScanner->serviceContainer;
        // TODO Create request handler mapped to http verb attributes
        $this->loop = Loop::get();
        $this->server = new HttpServer($this->getRequestHandler($container, $actions));

        $this->socket = new SocketServer("0.0.0.0:$configuration->port", [], $this->loop);
        $this->server->listen($this->socket);
    }

    /**
     * @param Container $container
     * @param array $actions
     * @return Closure
     */
    public function getRequestHandler(Container $container, array $actions): Closure
    {
        return function (ServerRequestInterface $request) use ($container, $actions) {
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

