# 🏗️ Work in progress

<p align="center">
  <img height="350" src="https://media.tenor.com/dLZ4cQ91MRgAAAAC/im-working-on-it-stan-marsh.gif">
</p>

---

## Installation

Install the project with composer
```shell
composer require reactorx/reactorx:dev-master
```

Create an entry file, configure and start the server
```php
<?php

use ReactorX\HttpKernel;
use ReactorX\HttpKernelConfiguration;

// Don't forget the autoloader
require_once __DIR__ . '/vendor/autoload.php';

$config = new HttpKernelConfiguration(
    // Scan the classes in the "./src" directory
    projectDir: __DIR__ . "/src"
);

// Create the server and pass it the configuration
$server = HttpKernel::createServer($config);

$server->run();
```

## Ping request example

Anywhere in the `src` directory, create a `PingController.php` class.<br/>
The startup process will automatically pick up the class and register it in the DI container as a controller.
```php
<?php

use ReactorX\Attributes\{Controller, HttpGet};
use React\Http\Message\Response;

#[Controller]
final class PingController
{
    #[HttpGet("ping")]
    public final function ping(): Response
    {
        return new Response(
            200,
            ['Content-Type' => 'text/plain'],
            "pong"
        );
    }
}
```

Now sending a request to `/ping` should respond with "pong"
```http request
GET http://localhost:3000/ping
```