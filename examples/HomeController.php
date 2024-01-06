<?php

namespace ReactorX\Examples;

use ReactorX\Attributes\{Controller, HttpDelete, HttpGet, HttpPost, HttpPut};
use ReactorX\Examples\Services\FooService;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

/**
 * Classes annotated with <code>#[Controller]</code> are picked up by the class scanner, added in the service container and configured as controllers
 */
#[Controller('/items')]
final readonly class HomeController
{
    public function __construct()
    {
        var_dump("home controller constructed");
    }

    #[HttpGet]
    public final function getList(FooService $fooService, ServerRequestInterface $request): Response
    {
        $queryParams = $request->getQueryParams();
        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([...$fooService->bar(), "queryParams" => $queryParams])
        );
    }

    #[HttpPost]
    public function create(ServerRequestInterface $request): Response
    {
        var_dump($request->getBody()->getContents());
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