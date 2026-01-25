<?php

namespace App\Models;

use App\Models\Dinas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GuestVisit extends Model
{
    protected $fillable = [
        'dinas_id',
        'name',
        'gender',
        'email',
        'education',
        'institution',
        'phone',
        'job',
        'jabatan',
        'service_type',
        'purpose_detail',
        'purpose',
        'arrived_at',
        'completed_at',
        'handled_by',
        'visit_type',
        'group_count',
        'group_names',
    ];

    protected $casts = [
        'arrived_at' => 'datetime',
        'completed_at' => 'datetime',
        'group_names' => 'array',
    ];

    public function survey(): HasOne
    {
        return $this->hasOne(GuestSurvey::class, 'visit_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function dinas(): BelongsTo
    {
        return $this->belongsTo(Dinas::class);
    }
}
