<?php

namespace Scwar\Monnify\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase;
use Scwar\Monnify\MonnifyServiceProvider;

class TransactionTest extends TestCase
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
        $app['config']->set('monnify.api_key', 'test_api_key');
        $app['config']->set('monnify.secret_key', 'test_secret_key');
        $app['config']->set('monnify.contract_code', 'test_contract_code');
        $app['config']->set('monnify.base_url', 'https://sandbox.monnify.com');
    }

    /**
     * Test transaction service can be accessed via facade.
     *
     * @return void
     */
    public function test_transaction_service_can_be_accessed_via_facade(): void
    {
        $transactionService = \Scwar\Monnify\Facades\Monnify::transaction();

        $this->assertNotNull($transactionService);
    }
}
