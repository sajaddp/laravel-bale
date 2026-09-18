<?php

declare(strict_types=1);

namespace Sajaddp\Bale\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Sajaddp\Bale\BaleServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            BaleServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('bale.token', 'test-token');
    }
}
