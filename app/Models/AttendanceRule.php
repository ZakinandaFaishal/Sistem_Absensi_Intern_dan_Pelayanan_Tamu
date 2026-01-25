<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRule extends Model
{
    protected $fillable = [
        'dinas_id',
        'location_id',
        'office_lat',
        'office_lng',
        'radius_m',
        'max_accuracy_m',
        'checkin_start',
        'checkin_end',
        'checkout_start',
        'checkout_end',
    ];

    protected $casts = [
        'office_lat' => 'decimal:7',
        'office_lng' => 'decimal:7',
        'radius_m' => 'integer',
        'max_accuracy_m' => 'integer',
    ];

    public function dinas(): BelongsTo
    {
        return $this->belongsTo(Dinas::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
