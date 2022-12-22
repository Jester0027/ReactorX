<?php

namespace Jester0027\Examples;

use Jester0027\Phuck\Attributes\{ApiController, HttpDelete, HttpGet, HttpPost, HttpPut};
use React\Http\Message\Response;

#[ApiController('/items')]
class HomeController
{
    #[HttpGet]
    public function getList(): Response
    {
        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode(["Hello" => "world"])
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