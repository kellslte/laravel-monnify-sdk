<?php

namespace Scwar\Monnify\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MonnifyTransaction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_reference',
        'payment_reference',
        'merchant_name',
        'customer_email',
        'customer_name',
        'amount',
        'currency',
        'status',
        'contract_code',
        'checkout_url',
        'metadata',
        'provider',
        'expiry_date',
        'created_at_monnify',
        'completed_at_monnify',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'provider' => 'array',
        'expiry_date' => 'datetime',
        'created_at_monnify' => 'datetime',
        'completed_at_monnify' => 'datetime',
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('monnify.tables.transactions', 'monnify_transactions');
    }

    /**
     * Scope a query to only include successful transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('status', 'PAID');
    }

    /**
     * Scope a query to only include failed transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', '!=', 'PAID');
    }

    /**
     * Scope a query to only include pending transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'PENDING');
    }

    /**
     * Scope a query to filter by customer email.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $email
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCustomer(Builder $query, string $email): Builder
    {
        return $query->where('customer_email', $email);
    }

    /**
     * Check if the transaction is successful.
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'PAID';
    }

    /**
     * Check if the transaction is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'PENDING';
    }

    /**
     * Check if the transaction is failed.
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return ! $this->isSuccessful() && ! $this->isPending();
    }
}
