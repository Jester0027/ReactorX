<?php

namespace Jester0027\Examples\Services;

use Jester0027\Phuck\Attributes\Component;

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