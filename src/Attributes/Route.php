<?php

namespace ReactorX\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
readonly class Route
{
    public function __construct(
        public string $method,
        public string $path = ''
    )
    {
    }
}

#[\Attribute(\Attribute::TARGET_METHOD)]
readonly class HttpGet extends Route
{
    public function __construct(
        string $path = ''
    )
    {
        parent::__construct("GET", $path);
    }
}

#[\Attribute(\Attribute::TARGET_METHOD)]
readonly class HttpPost extends Route
{
    public function __construct(
        string $path = ''
    )
    {
        parent::__construct("POST", $path);
    }
}

#[\Attribute(\Attribute::TARGET_METHOD)]
readonly class HttpPut extends Route
{
    public function __construct(
        string $path = ''
    )
    {
        parent::__construct("PUT", $path);
    }
}

#[\Attribute(\Attribute::TARGET_METHOD)]
readonly class HttpPatch extends Route
{
    public function __construct(
        string $path = ''
    )
    {
        parent::__construct("PATCH", $path);
    }
}

#[\Attribute(\Attribute::TARGET_METHOD)]
readonly class HttpDelete extends Route
{
    public function __construct(
        string $path = ''
    )
    {
        parent::__construct("DELETE", $path);
    }
}