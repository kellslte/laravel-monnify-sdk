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
        $tableName = config('monnify.tables.invoices', 'monnify_invoices');

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('invoice_reference')->unique();
            $table->string('invoice_status')->nullable();
            $table->text('checkout_url')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('NGN');
            $table->json('line_items')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->timestamp('created_at_monnify')->nullable();
            $table->timestamp('updated_at_monnify')->nullable();
            $table->timestamps();

            $table->index('invoice_reference');
            $table->index('invoice_status');
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
        $tableName = config('monnify.tables.invoices', 'monnify_invoices');

        Schema::dropIfExists($tableName);
    }
};
