<?php

use Jester0027\Phuck\HttpKernel;
use Jester0027\Phuck\HttpKernelConfiguration;

require_once __DIR__ . '/vendor/autoload.php';

$config = new HttpKernelConfiguration(
    port: 3000,
    allowedCorsOrigins: '*',
    projectDir: __DIR__ . "/examples"
);

$server = HttpKernel::createServer($config);

$server->run();
