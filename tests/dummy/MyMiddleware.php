<?php

namespace VSHF\Bus\Tests\dummy;

use VSHF\Bus\Middleware;

class MyMiddleware extends Middleware
{
    public static bool $beforeCalled = false;
    public static bool $afterCalled  = false;

    public static bool $mustBlock = false;

    public function before(): void
    {
        self::$beforeCalled = true;
        if (!self::$mustBlock) {
            $this->next();
        }
    }

    public function after($result = null): void
    {
        self::$afterCalled = true;
    }
}
