# Local Testing Guide

This guide explains how to test the Laravel Monnify SDK package locally during development.

## Table of Contents

1. [Running Package Tests](#running-package-tests)
2. [Testing in a Laravel Application](#testing-in-a-laravel-application)
3. [Using Composer Path Repository](#using-composer-path-repository)
4. [Running Specific Tests](#running-specific-tests)

## Running Package Tests

The package includes its own test suite using PHPUnit and Orchestra Testbench.

### Prerequisites

Install dependencies:

```bash
composer install
```

### Run All Tests

```bash
composer test
```

Or directly with PHPUnit:

```bash
vendor/bin/phpunit
```

### Run Tests with Coverage

```bash
vendor/bin/phpunit --coverage-html coverage/
```

Open `coverage/index.html` in your browser to view coverage report.

### Run Specific Test Suite

Run only Feature tests:

```bash
vendor/bin/phpunit tests/Feature
```

Run only Unit tests:

```bash
vendor/bin/phpunit tests/Unit
```

### Run Specific Test File

```bash
vendor/bin/phpunit tests/Feature/AuthenticationTest.php
```

### Run Specific Test Method

```bash
vendor/bin/phpunit --filter test_auth_service_can_be_resolved
```

## Testing in a Laravel Application

To test the package in a real Laravel application:

### Method 1: Using Composer Path Repository (Recommended)

1. **Create or use an existing Laravel application:**

```bash
composer create-project laravel/laravel test-monnify
cd test-monnify
```

2. **Add path repository to `composer.json`:**

Edit `composer.json` in your Laravel app and add:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../laravel-monnify-sdk"
        }
    ],
    "require": {
        "scwar/laravel-monnify-sdk": "@dev"
    }
}
```

**Note:** Adjust the path `../laravel-monnify-sdk` to match the relative path from your Laravel app to the package directory.

3. **Require the package:**

```bash
composer require scwar/laravel-monnify-sdk:@dev
```

Composer will create a symlink to your local package.

4. **Publish configuration:**

```bash
php artisan vendor:publish --tag=monnify-config
```

5. **Publish migrations:**

```bash
php artisan vendor:publish --tag=monnify-migrations
php artisan migrate
```

6. **Configure environment:**

Add to your `.env`:

```env
MONNIFY_API_KEY=your_api_key
MONNIFY_SECRET_KEY=your_secret_key
MONNIFY_CONTRACT_CODE=your_contract_code
MONNIFY_WEBHOOK_SECRET=your_webhook_secret
MONNIFY_BASE_URL=https://sandbox.monnify.com
MONNIFY_REDIRECT_URL=http://localhost/payment/callback
```

7. **Test in your application:**

You can now use the package in your Laravel app:

```php
use Scwar\Monnify\Facades\Monnify;

// In a controller or route
$transaction = Monnify::transaction()->initialize([
    'amount' => 1000.00,
    'customerName' => 'Test User',
    'customerEmail' => 'test@example.com',
    'paymentReference' => 'test-' . time(),
    'paymentDescription' => 'Test Payment',
]);
```

### Method 2: Using Composer Symlink

1. **Navigate to your package directory:**

```bash
cd laravel-monnify-sdk
```

2. **Create a symlink in your Laravel app's vendor directory:**

```bash
# From your Laravel app directory
ln -s /path/to/laravel-monnify-sdk vendor/scwar/laravel-monnify-sdk
```

3. **Update autoloader:**

```bash
composer dump-autoload
```

### Method 3: Using Packagist (After Publishing)

Once the package is published to Packagist:

```bash
composer require scwar/laravel-monnify-sdk:dev-main
```

Then add your repository to `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/yourusername/laravel-monnify-sdk"
        }
    ]
}
```

## Development Tips

### Watch for Changes

When using path repository, changes to your package code are immediately available. Just make sure to:

- Run `composer dump-autoload` after adding new classes
- Clear Laravel cache: `php artisan cache:clear`
- Clear config cache: `php artisan config:clear`

### Using Tinker

Test the package interactively:

```bash
php artisan tinker
```

```php
use Scwar\Monnify\Facades\Monnify;

// Test authentication
Monnify::transaction()->initialize([...]);

// Check if service is registered
app('monnify');
```

### Mocking API Calls

For testing without hitting the actual Monnify API, you can mock HTTP responses:

```php
use Illuminate\Support\Facades\Http;

// In your test
Http::fake([
    'api.monnify.com/*' => Http::response([
        'requestSuccessful' => true,
        'responseBody' => [
            'transactionReference' => 'test-ref',
            'checkoutUrl' => 'https://checkout.monnify.com/test',
        ],
    ], 200),
]);
```

### Testing Webhooks Locally

Use tools like [ngrok](https://ngrok.com/) to expose your local server:

```bash
ngrok http 8000
```

Then configure the webhook URL in Monnify dashboard:
```
https://your-ngrok-url.ngrok.io/api/monnify/webhook
```

## Troubleshooting

### Package Not Found

If Composer can't find the package:

1. Check the path in `composer.json` repositories section
2. Run `composer dump-autoload`
3. Check file permissions

### Changes Not Reflecting

1. Clear all caches:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
composer dump-autoload
```

2. Restart your development server if running

### Autoload Issues

If classes are not autoloading:

```bash
# From package directory
composer dump-autoload

# From Laravel app directory
composer dump-autoload
```

### Test Failures

If tests are failing:

1. Make sure all dependencies are installed:
```bash
composer install
```

2. Check PHP version matches requirements:
```bash
php -v
```

3. Verify database is configured (for model tests):
```bash
# Check phpunit.xml for database configuration
```

## Continuous Integration

The package includes GitHub Actions workflows that run tests automatically on:
- Every push to main/develop branches
- Every pull request

See `.github/workflows/tests.yml` for the test matrix configuration.
