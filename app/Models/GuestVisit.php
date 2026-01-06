<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GuestVisit extends Model
{
    protected $fillable = [
        'name',
        'institution',
        'phone',
        'purpose',
        'arrived_at',
        'completed_at',
        'handled_by',
    ];

    protected $casts = [
        'arrived_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function survey(): HasOne
    {
        return $this->hasOne(GuestSurvey::class, 'visit_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
