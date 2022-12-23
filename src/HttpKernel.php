<?php

namespace Jester0027\Phuck;

use Jester0027\Phuck\Attributes\Controller;
use Jester0027\Phuck\Attributes\Component;
use React\EventLoop\LoopInterface;
use React\Http\HttpServer;
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
        $this->classScanner = new ClassScanner($configuration->projectDir);
        // TODO Create request handler mapped to http verb attributes
//        $this->loop = Loop::get();
//        $this->server = new HttpServer([self::class, 'handleRequest']);
//
//        $this->socket = new SocketServer("0.0.0.0:$configuration->port", [], $this->loop);
//        $this->server->listen($this->socket);
    }
}

