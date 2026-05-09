<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    protected $fillable = [
    'name',
    'phone',
    'email',
    'company',
    'source',
    'status',
    'value',
    'notes',
    'address',
    'city',
    'wa_phone',
    'assigned_to',
    'created_by',
    'last_contacted_at',
    'product_id',
    'interest_notes',
    // Kolom properti baru
    'budget_range',
    'interest_type',
    'location_interest',
    'follow_up_date',
    'survey_plan',
    'survey_result',
    'utj_status',
    'utj_date',
    'cancel_reason',
];

protected $casts = [
    'value'            => 'decimal:2',
    'last_contacted_at'=> 'datetime',
    'follow_up_date'   => 'date',
    'utj_date'         => 'date',
    'utj_status'       => 'boolean',
];

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}