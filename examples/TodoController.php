<?php

namespace ReactorX\Examples;

use ReactorX\Examples\Services\FakeCrudService;
use ReactorX\Attributes\Controller;
use ReactorX\Attributes\HttpGet;
use ReactorX\Attributes\HttpPost;
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