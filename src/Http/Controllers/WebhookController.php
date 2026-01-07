<?php

namespace Scwar\Monnify\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Scwar\Monnify\Dto\TransactionResponse;
use Scwar\Monnify\Dto\WebhookPayload;
use Scwar\Monnify\Events\TransactionCompleted;
use Scwar\Monnify\Events\TransactionFailed;
use Scwar\Monnify\Events\WebhookReceived;
use Scwar\Monnify\Models\MonnifyInvoice;
use Scwar\Monnify\Models\MonnifyTransaction;

class WebhookController
{
    /**
     * Handle incoming webhook from Monnify.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $payload = WebhookPayload::fromArray($request->all());

            // Dispatch webhook received event
            event(new WebhookReceived($payload));

            // Handle based on event type
            switch ($payload->eventType) {
                case 'SUCCESSFUL_TRANSACTION':
                    $this->handleSuccessfulTransaction($payload);
                    break;

                case 'FAILED_TRANSACTION':
                case 'OVERPAYMENT':
                case 'PARTIAL_OVERPAYMENT':
                    $this->handleFailedTransaction($payload);
                    break;

                case 'INVOICE_UPDATE':
                    $this->handleInvoiceUpdate($payload);
                    break;

                default:
                    Log::info('Unhandled Monnify webhook event type', [
                        'eventType' => $payload->eventType,
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error processing Monnify webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing webhook',
            ], 500);
        }
    }

    /**
     * Handle successful transaction webhook.
     *
     * @param  \Scwar\Monnify\Dto\WebhookPayload  $payload
     * @return void
     */
    protected function handleSuccessfulTransaction(WebhookPayload $payload): void
    {
        $eventData = $payload->getEventData();

        if (! $eventData) {
            return;
        }

        $transaction = TransactionResponse::fromArray($eventData);

        // Dispatch transaction completed event
        event(new TransactionCompleted($transaction));

        // Update or create transaction record
        MonnifyTransaction::updateOrCreate(
            ['transaction_reference' => $transaction->transactionReference],
            [
                'payment_reference' => $transaction->paymentReference,
                'merchant_name' => $transaction->merchantName,
                'customer_email' => $transaction->customerEmail,
                'customer_name' => $transaction->customerName,
                'amount' => $transaction->amount,
                'currency' => $transaction->currency ?? 'NGN',
                'status' => $transaction->status ?? 'PAID',
                'contract_code' => $transaction->contractCode,
                'metadata' => $transaction->metaData,
                'provider' => $transaction->provider,
                'completed_at_monnify' => $transaction->completedOn ? \Carbon\Carbon::parse($transaction->completedOn) : null,
            ]
        );
    }

    /**
     * Handle failed transaction webhook.
     *
     * @param  \Scwar\Monnify\Dto\WebhookPayload  $payload
     * @return void
     */
    protected function handleFailedTransaction(WebhookPayload $payload): void
    {
        $eventData = $payload->getEventData();

        if (! $eventData) {
            return;
        }

        $transaction = TransactionResponse::fromArray($eventData);

        // Dispatch transaction failed event
        event(new TransactionFailed($transaction));

        // Update or create transaction record
        MonnifyTransaction::updateOrCreate(
            ['transaction_reference' => $transaction->transactionReference],
            [
                'payment_reference' => $transaction->paymentReference,
                'merchant_name' => $transaction->merchantName,
                'customer_email' => $transaction->customerEmail,
                'customer_name' => $transaction->customerName,
                'amount' => $transaction->amount,
                'currency' => $transaction->currency ?? 'NGN',
                'status' => $transaction->status ?? 'FAILED',
                'contract_code' => $transaction->contractCode,
                'metadata' => $transaction->metaData,
                'provider' => $transaction->provider,
                'completed_at_monnify' => $transaction->completedOn ? \Carbon\Carbon::parse($transaction->completedOn) : null,
            ]
        );
    }

    /**
     * Handle invoice update webhook.
     *
     * @param  \Scwar\Monnify\Dto\WebhookPayload  $payload
     * @return void
     */
    protected function handleInvoiceUpdate(WebhookPayload $payload): void
    {
        $eventData = $payload->getEventData();

        if (! $eventData) {
            return;
        }

        $invoice = \Scwar\Monnify\Dto\InvoiceResponse::fromArray($eventData);

        // Update or create invoice record
        MonnifyInvoice::updateOrCreate(
            ['invoice_reference' => $invoice->invoiceReference],
            [
                'invoice_status' => $invoice->invoiceStatus,
                'checkout_url' => $invoice->checkoutUrl,
                'amount' => $invoice->amount,
                'currency' => $invoice->currency ?? 'NGN',
                'line_items' => $invoice->lineItems,
                'customer_name' => $invoice->customerName,
                'customer_email' => $invoice->customerEmail,
                'metadata' => $invoice->metadata,
                'expiry_date' => $invoice->expiryDate ? \Carbon\Carbon::parse($invoice->expiryDate) : null,
                'updated_at_monnify' => now(),
            ]
        );
    }
}
