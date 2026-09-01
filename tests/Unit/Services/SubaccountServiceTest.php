<?php

namespace Scwar\Monnify\Tests\Unit\Services;

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;
use Scwar\Monnify\MonnifyServiceProvider;
use Scwar\Monnify\Services\SubaccountService;

class SubaccountServiceTest extends TestCase
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
     * All subaccount endpoints must use Monnify's documented, hyphenated
     * /api/v1/sub-accounts path, not /api/v1/subaccounts.
     *
     * @return void
     */
    public function test_create_hits_the_hyphenated_sub_accounts_endpoint(): void
    {
        Http::fake([
            '*/api/v1/auth/login' => Http::response([
                'requestSuccessful' => true,
                'responseBody' => [
                    'accessToken' => 'fake-token',
                    'expiresIn' => 3600,
                ],
            ], 200),
            '*/api/v1/sub-accounts' => Http::response([
                'requestSuccessful' => true,
                'responseBody' => [
                    'subAccountCode' => 'SUB_001',
                ],
            ], 200),
        ]);

        $subaccountService = $this->app->make(SubaccountService::class);
        $subaccount = $subaccountService->create([
            'currencyCode' => 'NGN',
            'bankCode' => '058',
            'accountNumber' => '0123456789',
            'email' => 'school@example.com',
        ]);

        $this->assertSame('SUB_001', $subaccount['subAccountCode']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/api/v1/sub-accounts')
                && ! str_contains($request->url(), '/api/v1/subaccounts');
        });
    }

    public function test_get_details_hits_the_hyphenated_sub_accounts_endpoint(): void
    {
        Http::fake([
            '*/api/v1/auth/login' => Http::response([
                'requestSuccessful' => true,
                'responseBody' => [
                    'accessToken' => 'fake-token',
                    'expiresIn' => 3600,
                ],
            ], 200),
            '*/api/v1/sub-accounts/SUB_001' => Http::response([
                'requestSuccessful' => true,
                'responseBody' => [
                    'subAccountCode' => 'SUB_001',
                ],
            ], 200),
        ]);

        $subaccountService = $this->app->make(SubaccountService::class);
        $subaccountService->getDetails('SUB_001');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/api/v1/sub-accounts/SUB_001');
        });
    }
}
