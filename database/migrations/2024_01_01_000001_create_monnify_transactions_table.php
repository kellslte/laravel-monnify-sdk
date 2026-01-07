<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = config('monnify.tables.transactions', 'monnify_transactions');

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('transaction_reference')->unique();
            $table->string('payment_reference')->nullable();
            $table->string('merchant_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_name')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('NGN');
            $table->string('status')->nullable();
            $table->string('contract_code')->nullable();
            $table->text('checkout_url')->nullable();
            $table->json('metadata')->nullable();
            $table->json('provider')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->timestamp('created_at_monnify')->nullable();
            $table->timestamp('completed_at_monnify')->nullable();
            $table->timestamps();

            $table->index('transaction_reference');
            $table->index('payment_reference');
            $table->index('status');
            $table->index('customer_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableName = config('monnify.tables.transactions', 'monnify_transactions');

        Schema::dropIfExists($tableName);
    }
};
