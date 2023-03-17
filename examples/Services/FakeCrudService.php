<?php

namespace ReactorX\Examples\Services;

use ReactorX\Attributes\Component;

#[Component]
class FakeCrudService
{
    private array $todos = [
        ["label" => "Do things", "done" => false]
    ];

    public function getTodos(): array
    {
        return [...$this->todos];
    }

    public function addTodo(array $todo): array
    {
        $this->todos[] = $todo;
        return $todo;
    }
}