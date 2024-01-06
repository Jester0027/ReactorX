<?php

use ReactorX\HttpKernel;
use ReactorX\HttpKernelConfiguration;

require_once __DIR__ . '/vendor/autoload.php';

$config = new HttpKernelConfiguration(
    port: 3000,
    allowedCorsOrigins: '*',
    projectDir: __DIR__ . "/examples"
);

$server = HttpKernel::createServer($config);

$server->run();
