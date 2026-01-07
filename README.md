# Laravel Monnify SDK

[![Latest Version](https://img.shields.io/packagist/v/scwar/laravel-monnify-sdk.svg?style=flat-square)](https://packagist.org/packages/scwar/laravel-monnify-sdk)
[![Total Downloads](https://img.shields.io/packagist/dt/scwar/laravel-monnify-sdk.svg?style=flat-square)](https://packagist.org/packages/scwar/laravel-monnify-sdk)
[![License](https://img.shields.io/packagist/l/scwar/laravel-monnify-sdk.svg?style=flat-square)](https://packagist.org/packages/scwar/laravel-monnify-sdk)

A comprehensive Laravel package for integrating with the Monnify payment gateway. This package provides a clean, fluent interface for handling transactions, invoices, subaccounts, and webhooks.

## Features

- ✅ Complete Monnify API coverage (Transactions, Invoices, Subaccounts)
- ✅ Automatic token management with caching
- ✅ Webhook handling with signature verification
- ✅ Laravel Events for transaction status changes
- ✅ Database migrations for storing transactions/invoices locally
- ✅ Type-safe DTOs for all API responses
- ✅ Facade support for clean, expressive API
- ✅ Comprehensive exception handling
- ✅ Full test coverage structure

## Requirements

- PHP >= 8.1
- Laravel >= 10.0 or >= 11.0
- Guzzle HTTP Client

## Installation

Install the package via Composer:

```bash
composer require scwar/laravel-monnify-sdk
```

### Publish Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=monnify-config
```

This will create a `config/monnify.php` file in your application.

### Publish Migrations

Publish the database migrations:

```bash
php artisan vendor:publish --tag=monnify-migrations
```

Run the migrations:

```bash
php artisan migrate
```

### Environment Variables

Add the following environment variables to your `.env` file:

```env
MONNIFY_API_KEY=your_api_key
MONNIFY_SECRET_KEY=your_secret_key
MONNIFY_CONTRACT_CODE=your_contract_code
MONNIFY_WEBHOOK_SECRET=your_webhook_secret
MONNIFY_BASE_URL=https://api.monnify.com
MONNIFY_REDIRECT_URL=https://your-app.com/payment/callback
```

For sandbox/testing, use:

```env
MONNIFY_BASE_URL=https://sandbox.monnify.com
```

## Configuration

The configuration file is located at `config/monnify.php`. You can customize:

- API credentials
- Base URL (sandbox/production)
- Webhook secret key
- Cache configuration
- Database table names
- Route prefix and middleware
- Default transaction/invoice settings

## Usage

### Using the Facade

The package provides a facade for easy access:

```php
use Scwar\Monnify\Facades\Monnify;
```

### Transactions

#### Initialize a Transaction

```php
use Scwar\Monnify\Facades\Monnify;

$transaction = Monnify::transaction()->initialize([
    'amount' => 1000.00,
    'customerName' => 'John Doe',
    'customerEmail' => 'john@example.com',
    'paymentReference' => 'unique-payment-reference',
    'paymentDescription' => 'Payment for order #123',
    'currencyCode' => 'NGN',
    'redirectUrl' => 'https://your-app.com/payment/callback',
    'metadata' => [
        'order_id' => 123,
        'user_id' => 456,
    ],
]);

// Get the checkout URL
$checkoutUrl = $transaction->checkoutUrl;

// Redirect user to checkout
return redirect($checkoutUrl);
```

#### Get Transaction Status

```php
$transaction = Monnify::transaction()->getStatus('transaction-reference');

if ($transaction->status === 'PAID') {
    // Transaction is successful
}
```

#### Verify Transaction

```php
$transaction = Monnify::transaction()->verify('transaction-reference');
```

#### Refund Transaction

```php
$refund = Monnify::transaction()->refund(
    transactionReference: 'transaction-reference',
    amount: 1000.00,
    refundReason: 'Customer requested refund',
    customerNote: 'Refund processed'
);
```

#### Query Transaction History

```php
$transactions = Monnify::transaction()->queryHistory([
    'page' => 0,
    'size' => 20,
    'fromDate' => '2024-01-01',
    'toDate' => '2024-01-31',
]);
```

### Invoices

#### Create an Invoice

```php
$invoice = Monnify::invoice()->create([
    'amount' => 5000.00,
    'invoiceReference' => 'unique-invoice-reference',
    'description' => 'Invoice for services',
    'currencyCode' => 'NGN',
    'contractCode' => config('monnify.contract_code'),
    'customerEmail' => 'customer@example.com',
    'customerName' => 'Jane Doe',
    'expiryDate' => now()->addDays(7)->toIso8601String(),
    'lineItems' => [
        [
            'lineItemId' => 'item-1',
            'name' => 'Service Fee',
            'quantity' => 1,
            'unitPrice' => 5000.00,
        ],
    ],
]);

// Get the checkout URL
$checkoutUrl = $invoice->checkoutUrl;
```

#### Get Invoice Details

```php
$invoice = Monnify::invoice()->getDetails('invoice-reference');
```

#### Cancel Invoice

```php
Monnify::invoice()->cancel('invoice-reference');
```

#### List Invoices

```php
$invoices = Monnify::invoice()->list([
    'page' => 0,
    'size' => 20,
]);
```

### Subaccounts

#### Create Subaccount

```php
$subaccount = Monnify::subaccount()->create([
    'subAccountCode' => 'SUB_001',
    'subAccountName' => 'Main Subaccount',
    'email' => 'subaccount@example.com',
    'splitPercentage' => 10.0,
]);

// Get the subaccount code
$subaccountCode = $subaccount['subAccountCode'];
```

#### Update Subaccount

```php
Monnify::subaccount()->update('SUB_001', [
    'subAccountName' => 'Updated Name',
    'splitPercentage' => 15.0,
]);
```

#### Delete Subaccount

```php
Monnify::subaccount()->delete('SUB_001');
```

#### Get Subaccount Details

```php
$subaccount = Monnify::subaccount()->getDetails('SUB_001');
```

#### List Subaccounts

```php
$subaccounts = Monnify::subaccount()->list([
    'page' => 0,
    'size' => 20,
]);
```

## Webhooks

The package automatically sets up webhook routes. By default, the webhook endpoint is:

```
POST /api/monnify/webhook
```

You can customize this in the configuration file.

### Webhook Events

The package automatically handles the following webhook events:

- `SUCCESSFUL_TRANSACTION` - Fires `TransactionCompleted` event
- `FAILED_TRANSACTION` - Fires `TransactionFailed` event
- `OVERPAYMENT` - Fires `TransactionFailed` event
- `PARTIAL_OVERPAYMENT` - Fires `TransactionFailed` event
- `INVOICE_UPDATE` - Updates invoice record

### Listening to Events

You can listen to the events in your `EventServiceProvider`:

```php
use Scwar\Monnify\Events\TransactionCompleted;
use Scwar\Monnify\Events\TransactionFailed;
use Scwar\Monnify\Events\WebhookReceived;

protected $listen = [
    TransactionCompleted::class => [
        // Your listeners
    ],
    TransactionFailed::class => [
        // Your listeners
    ],
    WebhookReceived::class => [
        // Your listeners
    ],
];
```

Example listener:

```php
use Scwar\Monnify\Events\TransactionCompleted;

class SendPaymentConfirmation
{
    public function handle(TransactionCompleted $event)
    {
        $transaction = $event->transaction;
        
        // Send confirmation email
        // Update order status
        // etc.
    }
}
```

### Webhook Signature Verification

The package automatically verifies webhook signatures using HMAC SHA512. Make sure to set `MONNIFY_WEBHOOK_SECRET` in your `.env` file.

## Models

The package provides Eloquent models for transactions and invoices:

### MonnifyTransaction

```php
use Scwar\Monnify\Models\MonnifyTransaction;

// Get all successful transactions
$transactions = MonnifyTransaction::successful()->get();

// Get transactions for a customer
$transactions = MonnifyTransaction::forCustomer('customer@example.com')->get();

// Check transaction status
$transaction = MonnifyTransaction::where('transaction_reference', 'ref-123')->first();

if ($transaction->isSuccessful()) {
    // Transaction is paid
}
```

### MonnifyInvoice

```php
use Scwar\Monnify\Models\MonnifyInvoice;

// Get all paid invoices
$invoices = MonnifyInvoice::paid()->get();

// Get invoices for a customer
$invoices = MonnifyInvoice::forCustomer('customer@example.com')->get();

// Check invoice status
$invoice = MonnifyInvoice::where('invoice_reference', 'inv-123')->first();

if ($invoice->isPaid()) {
    // Invoice is paid
}

if ($invoice->isExpired()) {
    // Invoice has expired
}
```

## Exceptions

The package provides custom exceptions:

- `Scwar\Monnify\Exceptions\MonnifyException` - Base exception
- `Scwar\Monnify\Exceptions\AuthenticationException` - Authentication errors
- `Scwar\Monnify\Exceptions\RequestException` - API request errors

Example:

```php
use Scwar\Monnify\Exceptions\AuthenticationException;
use Scwar\Monnify\Exceptions\RequestException;

try {
    $transaction = Monnify::transaction()->initialize([...]);
} catch (AuthenticationException $e) {
    // Handle authentication error
} catch (RequestException $e) {
    // Handle request error
    $response = $e->getResponse();
}
```

## Testing

Run the tests:

```bash
composer test
```

Or using PHPUnit directly:

```bash
vendor/bin/phpunit
```

## API Documentation

For complete API documentation, visit the [Monnify Developer Documentation](https://developers.monnify.com/api).

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## Support

For issues and questions, please open an issue on [GitHub](https://github.com/scwar/laravel-monnify-sdk/issues).

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.
