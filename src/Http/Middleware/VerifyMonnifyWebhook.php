<?php

namespace Scwar\Monnify\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Scwar\Monnify\Exceptions\MonnifyException;

class VerifyMonnifyWebhook
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Scwar\Monnify\Exceptions\MonnifyException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secretKey = config('monnify.webhook_secret');

        if (! $secretKey) {
            throw new MonnifyException('Webhook secret key is not configured');
        }

        $signature = $request->header('monnify-signature');

        if (! $signature) {
            Log::warning('Monnify webhook received without signature header');

            return response()->json([
                'success' => false,
                'message' => 'Missing signature header',
            ], 400);
        }

        // Get the raw request body
        $payload = $request->getContent();

        // Verify the signature using HMAC SHA512
        $expectedSignature = hash_hmac('sha512', $payload, $secretKey);

        if (! hash_equals($expectedSignature, $signature)) {
            Log::warning('Monnify webhook signature verification failed', [
                'received' => $signature,
                'expected' => $expectedSignature,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid signature',
            ], 401);
        }

        return $next($request);
    }
}
