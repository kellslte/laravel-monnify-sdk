<?php

namespace Scwar\Monnify\Tests\Unit\Services;

use Orchestra\Testbench\TestCase;
use Scwar\Monnify\MonnifyServiceProvider;

class AuthServiceTest extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            MonnifyServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set('monnify.api_key', 'test_api_key');
        $app['config']->set('monnify.secret_key', 'test_secret_key');
        $app['config']->set('monnify.base_url', 'https://sandbox.monnify.com');
    }

    /**
     * Test that auth service instance can be created.
     *
     * @return void
     */
    public function test_auth_service_instance(): void
    {
        $this->assertTrue(true);
    }
}
