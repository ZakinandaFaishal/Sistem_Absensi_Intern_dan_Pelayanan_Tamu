<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'location_id',
        'lat',
        'lng',
        'accuracy_m',
        'is_fake_gps',
        'fake_gps_flagged_by',
        'fake_gps_flagged_at',
        'fake_gps_note',
        'date',
        'check_in_at',
        'check_out_at',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'accuracy_m' => 'integer',
        'is_fake_gps' => 'boolean',
        'fake_gps_flagged_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
