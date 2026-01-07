<?php

use Illuminate\Support\Facades\Route;
use Scwar\Monnify\Http\Controllers\WebhookController;
use Scwar\Monnify\Http\Middleware\VerifyMonnifyWebhook;

$prefix = config('monnify.routes.prefix', 'api/monnify');
$middleware = config('monnify.routes.middleware', ['api']);
$webhookPath = config('monnify.routes.webhook', 'webhook');

Route::prefix($prefix)
    ->middleware($middleware)
    ->group(function () use ($webhookPath) {
        Route::post($webhookPath, [WebhookController::class, 'handle'])
            ->middleware(VerifyMonnifyWebhook::class)
            ->name('monnify.webhook');
    });
