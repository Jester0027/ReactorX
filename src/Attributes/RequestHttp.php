<?php

namespace Jester0027\Phuck\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        protected string $method,
        protected string $path = ''
    )
    {
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}

#[\Attribute(\Attribute::TARGET_METHOD)]
class HttpGet extends Route
{
    public function __construct(
        string $path = ''
    )
    {
        parent::__construct("GET", $path);
    }
}

#[\Attribute(\Attribute::TARGET_METHOD)]
class HttpPost extends Route
{
    public function __construct(
        string $path = ''
    )
    {
        parent::__construct("POST", $path);
    }
}

#[\Attribute(\Attribute::TARGET_METHOD)]
class HttpPut extends Route
{
    public function __construct(
        string $path = ''
    )
    {
        parent::__construct("PUT", $path);
    }
}

#[\Attribute(\Attribute::TARGET_METHOD)]
class HttpPatch extends Route
{
    public function __construct(
        string $path = ''
    )
    {
        parent::__construct("PATCH", $path);
    }
}

#[\Attribute(\Attribute::TARGET_METHOD)]
class HttpDelete extends Route
{
    public function __construct(
        string $path = ''
    )
    {
        parent::__construct("DELETE", $path);
    }
}