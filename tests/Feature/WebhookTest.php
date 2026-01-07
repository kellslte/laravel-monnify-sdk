<?php

namespace Scwar\Monnify\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase;
use Scwar\Monnify\MonnifyServiceProvider;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

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
        $app['config']->set('monnify.webhook_secret', 'test_webhook_secret');
    }

    /**
     * Test webhook route exists.
     *
     * @return void
     */
    public function test_webhook_route_exists(): void
    {
        $response = $this->postJson('/api/monnify/webhook', []);

        // Should return 400 or 401 without proper signature, not 404
        $this->assertNotEquals(404, $response->status());
    }
}
