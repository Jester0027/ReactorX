<?php

namespace Jester0027\Phuck;

use DirectoryIterator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;
use ReflectionClass;
use ReflectionException;

readonly class HttpKernel
{
    use HttpKernelTrait;

    protected HttpServer $server;
    protected SocketServer $socket;
    protected LoopInterface $loop;

    /**
     * @throws ReflectionException
     */
    public static function createServer(HttpKernelConfiguration $configuration = new HttpKernelConfiguration()): self
    {
        return new self($configuration);
    }

    public function run(): self
    {
        $this->loop->run();
        return $this;
    }

    /**
     * @throws ReflectionException
     */
    private function __construct(protected HttpKernelConfiguration $configuration)
    {
        $classes = $this->scanServices($this->configuration->projectDir);
        var_dump($classes);
//        TODO Create request handler mapped to http verb attributes
//        $this->loop = Loop::get();
//        $this->server = new HttpServer([$this, 'handleRequest']);
//
//        $this->socket = new SocketServer("0.0.0.0:$configuration->port", [], $this->loop);
//        $this->server->listen($this->socket);
    }
}

trait HttpKernelTrait
{
    /**
     * @return ReflectionClass[]
     * @throws ReflectionException
     */
    public function scanServices(string $directory): array
    {
        $dir = self::normalizePath($directory);
        $classes = [];

        foreach (new DirectoryIterator($dir) as $file) {
            if ($file->isDir() && !$file->isDot()) {
                $classes = [...$classes, ...$this->scanServices($file->getPathname())];
            } else if ($file->getExtension() !== 'php') {
                continue;
            } else {
                require_once $file->getPathname();
                $declaredClasses = get_declared_classes();
                $fileClasses = array_filter($declaredClasses, function ($class) use ($file) {
                    $reflection = new ReflectionClass($class);
                    return self::normalizePath($reflection->getFileName()) === self::normalizePath($file->getPathname());
                });
                $classes = [...$classes, ...array_map(fn($class) => new ReflectionClass($class), $fileClasses)];
            }
        }
        return [...$classes];
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    private function handleRequest(ServerRequestInterface $request): ResponseInterface
    {

        $path = $request->getUri()->getPath();

        if ($path === '/items' && $request->getMethod() === 'GET') {
            return new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode(["Hello" => "world", "machin" => "truc"])
            );
        }
        return new Response(405);
    }

    private static function normalizePath($path)
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('|(?<=.)/+|', '/', $path);
        if (':' === substr($path, 1, 1)) {
            $path = ucfirst($path);
        }
        return $path;
    }
}
