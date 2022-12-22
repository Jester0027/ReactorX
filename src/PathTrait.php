<?php

namespace Jester0027\Phuck;

trait PathTrait
{
    private static function normalizePath($path): array|string|null
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('|(?<=.)/+|', '/', $path);
        if (':' === substr($path, 1, 1)) {
            $path = ucfirst($path);
        }
        return $path;
    }
}