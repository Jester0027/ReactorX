<?php

namespace Jester0027\Examples;

use Jester0027\Examples\Services\FakeCrudService;
use Jester0027\Phuck\Attributes\Controller;
use Jester0027\Phuck\Attributes\HttpGet;
use Jester0027\Phuck\Attributes\HttpPost;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

#[Controller('todos')]
class TodoController
{
    #[HttpGet]
    public function getTodos(FakeCrudService $fakeCrudService): Response
    {
        return Response::json($fakeCrudService->getTodos());
    }

    #[HttpPost]
    public function addTodo(ServerRequestInterface $request, FakeCrudService $fakeCrudService): Response
    {
        var_dump($request->getBody()->getContents());
//        $body = json_decode(, true);
        var_dump($request->getParsedBody());
//        $fakeCrudService->addTodo($body);
        return new Response(201);
    }
}