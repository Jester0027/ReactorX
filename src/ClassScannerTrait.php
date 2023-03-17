<?php

namespace ReactorX;

use DirectoryIterator;
use ReflectionClass;
use ReflectionException;

/**
 * @author Paul N. Etienne <paul.ned@outlook.com>
 */
trait ClassScannerTrait
{
    use PathTrait;

    /**
     * @return ReflectionClass[]
     */
    private function scanClasses(string $directory): array
    {
        $dir = self::normalizePath($directory);
        $classes = [];

        try {
            foreach (new DirectoryIterator($dir) as $file) {
                if ($file->isDir() && !$file->isDot()) {
                    $classes = [...$classes, ...self::scanClasses($file->getPathname())];
                } else if ($file->getExtension() !== 'php') {
                    continue;
                } else {
                    require_once $file->getPathname();
                    $declaredClasses = get_declared_classes();
                    $fileClasses = array_filter($declaredClasses, function ($class) use ($file) {
                        $reflection = new ReflectionClass($class);
                        return self::normalizePath($reflection->getFileName()) === self::normalizePath($file->getPathname());
                    });
                    $classes = [...$classes, ...array_map(fn($class) => new ReflectionClass($class), $fileClasses)];
                }
            }
        } catch (ReflectionException $e) {
        }
        return [...$classes];
    }
}