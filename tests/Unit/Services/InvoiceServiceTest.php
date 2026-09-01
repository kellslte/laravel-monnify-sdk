<?php

namespace Scwar\Monnify\Tests\Unit\Services;

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;
use Scwar\Monnify\MonnifyServiceProvider;
use Scwar\Monnify\Services\InvoiceService;

class InvoiceServiceTest extends TestCase
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
        $app['config']->set('monnify.contract_code', 'test_contract_code');
        $app['config']->set('monnify.base_url', 'https://sandbox.monnify.com');
    }

    /**
     * getDetails() must call Monnify's documented
     * GET /api/v1/invoice/{invoiceReference}/details endpoint, not
     * GET /api/v1/invoice/{invoiceReference}.
     *
     * @return void
     */
    public function test_get_details_hits_the_details_endpoint(): void
    {
        Http::fake([
            '*/api/v1/auth/login' => Http::response([
                'requestSuccessful' => true,
                'responseBody' => [
                    'accessToken' => 'fake-token',
                    'expiresIn' => 3600,
                ],
            ], 200),
            '*/api/v1/invoice/INV-123/details' => Http::response([
                'requestSuccessful' => true,
                'responseBody' => [
                    'invoiceReference' => 'INV-123',
                    'invoiceStatus' => 'PAID',
                    'amount' => 5100.0,
                    'amountPaid' => 5000.0,
                ],
            ], 200),
        ]);

        $invoiceService = $this->app->make(InvoiceService::class);
        $invoice = $invoiceService->getDetails('INV-123');

        $this->assertSame('INV-123', $invoice->invoiceReference);
        $this->assertSame(5100.0, $invoice->amount);
        $this->assertSame(5000.0, $invoice->amountPaid);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/api/v1/invoice/INV-123/details');
        });
    }
}
