<?php

namespace ReactorX\Examples\Services;

interface SomeServiceInterface
{
    function doStuff(): string;
}

final class SomeService implements SomeServiceInterface
{
    public function doStuff(): string
    {
        return "Hello world";
    }
}