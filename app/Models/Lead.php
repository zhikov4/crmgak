<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\ScopesByRole;

class Lead extends Model
{
    use ScopesByRole;

    /**
     * Daftar status sesuai alur kerja properti GAK.
     * Satu sumber kebenaran — dipakai di dropdown, badge, dan validasi.
     * key (disimpan di DB) => label (ditampilkan ke user)
     */
    public const STATUSES = [
        'no_respon' => 'No Respon',
        'respon'    => 'Respon',
        'kirim_pl'  => 'Kirim Price List',
        'survey'    => 'Survey',
        'utj'       => 'UTJ',
        'closing'   => 'Closing',
        'batal'     => 'Batal',
    ];

    /** Warna badge per status (kelas Tailwind). */
    public const STATUS_COLORS = [
        'no_respon' => 'bg-gray-100 text-gray-700',
        'respon'    => 'bg-blue-100 text-blue-700',
        'kirim_pl'  => 'bg-purple-100 text-purple-700',
        'survey'    => 'bg-yellow-100 text-yellow-700',
        'utj'       => 'bg-orange-100 text-orange-700',
        'closing'   => 'bg-green-100 text-green-700',
        'batal'     => 'bg-red-100 text-red-700',
    ];

    /** Label status untuk lead ini. */
    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /** Kelas warna badge untuk lead ini. */
    public function statusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'bg-gray-100 text-gray-700';
    }

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
    'input_date',
];

protected $casts = [
    'value'            => 'decimal:2',
    'last_contacted_at'=> 'datetime',
    'follow_up_date'   => 'date',
    'utj_date'         => 'date',
    'utj_status'       => 'boolean',
    'input_date' => 'date',
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
