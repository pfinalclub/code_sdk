<?php

/**
 * Author: PFinal南丞
 * Date: 2024/7/18
 * Email: <lampxiezi@163.com>
 */
declare(strict_types=1);

namespace CozeSdk\Tests;

use CozeSdk\OfficialAccount\Application;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected Application|null $app = null;

    protected function setUp(): void
    {
        $app       = new Application(
            config: [
                'kid'      => 'YUg-rl0HdvzCzgAGjO-28fX6LcHff07uuEtvwmujcgM',
                'iss'      => '1135933249080',
                'key_path' => __DIR__ . '/private_key.pem'
            ]
        );
        $this->app = $app;
    }
}