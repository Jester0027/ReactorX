<?php

namespace Jester0027\Examples\Admin;

use Jester0027\Phuck\Attributes\{Controller, HttpGet};
use React\Http\Message\Response;

#[Controller]
class UsersController
{
    public function __construct()
    {
        var_dump("users controller constructed");
    }

    #[HttpGet]
    public function index(): Response
    {
        return new Response();
    }
}