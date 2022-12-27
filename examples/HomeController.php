<?php

namespace Jester0027\Examples;

use Jester0027\Phuck\Attributes\{Controller, HttpDelete, HttpGet, HttpPost, HttpPut};
use Jester0027\Examples\Services\FooService;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

/**
 * Classes annotated with <code>#[Controller]</code> are picked up by the class scanner, added in the service container and configured as controllers
 */
#[Controller('/items')]
final readonly class HomeController
{
    public function __construct(private FooService $fooService)
    {
        var_dump("home controller constructed");
    }

    #[HttpGet]
    public final function getList(ServerRequestInterface $request): Response
    {
        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode($this->fooService->bar())
        );
    }

    #[HttpPost]
    public function create(): Response
    {
        return new Response(201);
    }

    #[HttpPut("{id}")]
    public function update(): Response
    {
        return new Response(204);
    }

    #[HttpDelete("{id}")]
    public function delete(): Response
    {
        return new Response(204);
    }
}