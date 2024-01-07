<?php

namespace ReactorX;

use Closure;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;
use ReactorX\DependencyInjection\Container;
use ReactorX\DependencyInjection\DependencyException;
use ReflectionMethod;

/**
 * @author Paul N. Etienne <paul.ossdev@proton.me>
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
            $container = $this->classScanner->container->newRequestScopedContainer();
            $actions = $this->classScanner->getActionsMappings();
            $httpVerb = $request->getMethod();
            $path = rtrim($request->getUri()->getPath(), '/');
            $action = $actions[$httpVerb][$path];
            if (!isset($action)) {
                return new Response($request->getMethod() === 'GET' ? 404 : 405);
            }
            $className = $action[0];
            $actionName = $action[1];
            $reflectionMethod = new ReflectionMethod($className, $actionName);
            $parameters = $this->autowireParameters($container, $reflectionMethod, $request);
            $controller = $container->get($className);
            return $controller->$actionName(...$parameters);
        };
    }

    /**
     * Returns an array of the reflected method arguments
     *
     * @param Container $container
     * @param ReflectionMethod $reflectionMethod
     * @param ServerRequestInterface $request
     * @return array
     */
    private function autowireParameters(
        Container              $container,
        ReflectionMethod       $reflectionMethod,
        ServerRequestInterface $request
    ): array
    {
        $parameters = [];
        foreach ($reflectionMethod->getParameters() as $parameter) {
            $type = $parameter->getType()->getName();
            try {
                $parameters[] = match ($type) {
                    ServerRequestInterface::class => $request,
                    ContainerInterface::class => $container,
                    default => $container->get($type),
                };
            } catch (DependencyException $e) {
                // TODO handle the exception
                var_dump($e);
            }
        }
        return $parameters;
    }
}

