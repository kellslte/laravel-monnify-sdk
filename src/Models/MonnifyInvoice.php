<?php

namespace Scwar\Monnify\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MonnifyInvoice extends Model
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
        'invoice_reference',
        'invoice_status',
        'checkout_url',
        'amount',
        'currency',
        'line_items',
        'customer_name',
        'customer_email',
        'metadata',
        'expiry_date',
        'created_at_monnify',
        'updated_at_monnify',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'line_items' => 'array',
        'metadata' => 'array',
        'expiry_date' => 'datetime',
        'created_at_monnify' => 'datetime',
        'updated_at_monnify' => 'datetime',
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

        $this->table = config('monnify.tables.invoices', 'monnify_invoices');
    }

    /**
     * Scope a query to only include paid invoices.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('invoice_status', 'PAID');
    }

    /**
     * Scope a query to only include unpaid invoices.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('invoice_status', 'UNPAID');
    }

    /**
     * Scope a query to only include cancelled invoices.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('invoice_status', 'CANCELLED');
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
     * Check if the invoice is paid.
     *
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->invoice_status === 'PAID';
    }

    /**
     * Check if the invoice is unpaid.
     *
     * @return bool
     */
    public function isUnpaid(): bool
    {
        return $this->invoice_status === 'UNPAID';
    }

    /**
     * Check if the invoice is cancelled.
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->invoice_status === 'CANCELLED';
    }

    /**
     * Check if the invoice is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        if (! $this->expiry_date) {
            return false;
        }

        return $this->expiry_date->isPast();
    }
}
