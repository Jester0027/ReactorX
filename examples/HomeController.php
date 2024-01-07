<?php

namespace ReactorX\Examples;

use ReactorX\Attributes\{Controller, HttpDelete, HttpGet, HttpPost, HttpPut};
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use ReactorX\Examples\Services\ConfigurationInterface;
use ReactorX\Examples\Services\PasswordHasherInterface;

/**
 * Classes annotated with <code>#[Controller]</code> are picked up by the class scanner, added in the service container and configured as controllers
 */
#[Controller('/items')]
final readonly class HomeController
{
    public function __construct()
    {
        var_dump("home controller constructed");
    }

    #[HttpGet]
    public final function getList(
        ConfigurationInterface  $config,
        ServerRequestInterface  $request,
        PasswordHasherInterface $passwordHasher
    ): Response
    {
        $queryParams = $request->getQueryParams();
        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'singleton_configuration_test' => [$config->getConfiguration()],
                'configuration_di_test' => [
                    'instance' => var_export($passwordHasher, true),
                    'hash' => $passwordHasher->hash("test"),
                    'isValid' => $passwordHasher->verify('1234', 'test')
                ]
            ]),
        );
    }

    #[HttpPost]
    public function create(ServerRequestInterface $request): Response
    {
        var_dump($request->getBody()->getContents());
        return new Response(201);
    }

    #[HttpPut("{id}")]
    public function update(): Response
    {
        return new Response(204);
    }

    #[HttpDelete("{id}")]
    public function delete(): Response
    {
        return new Response(204);
    }
}