<?php

namespace Jester0027\Examples;

use Jester0027\Phuck\Attributes\{Controller, HttpDelete, HttpGet, HttpPost, HttpPut};
use Jester0027\Examples\Services\FooService;
use React\Http\Message\Response;

#[Controller('/items')]
class HomeController
{
    public function __construct(private readonly FooService $fooService)
    {
    }

    #[HttpGet]
    public function getList(): Response
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