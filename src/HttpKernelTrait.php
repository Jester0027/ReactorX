<?php

namespace Jester0027\Phuck;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

trait HttpKernelTrait
{
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
                json_encode(["Hello" => "world"])
            );
        }
        return new Response($request->getMethod() === 'GET' ? 404 : 405);
    }
}
