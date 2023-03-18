<?php

namespace ReactorX\Examples\Services;

interface PasswordHasherInterface
{
    public function hash(string $password): string;
    public function verify(string $hash, string $password): bool;
}

class BogusPasswordHasher implements PasswordHasherInterface
{
    public function hash(string $password): string
    {
        return '1234';
    }

    public function verify(string $hash, string $password): bool
    {
        return $hash === '1234';
    }
}