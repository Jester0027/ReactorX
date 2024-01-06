<?php

namespace ReactorX;

/**
 * @author Paul N. Etienne <paul.ossdev@proton.me>
 */
readonly class HttpKernelConfiguration
{
    /**
     * @param int $port Server port where the application is served
     * @param string $allowedCorsOrigins Allowed cors origins
     * @param string $projectDir Project directory where the classes should be scanned and added to the service container
     */
    public function __construct(
        public int    $port = 8000,
        public string $allowedCorsOrigins = '*',
        public string $projectDir = __DIR__,
    )
    {
    }
}