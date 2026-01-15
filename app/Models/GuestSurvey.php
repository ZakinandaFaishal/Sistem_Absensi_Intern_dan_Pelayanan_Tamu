<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestSurvey extends Model
{
    protected $fillable = [
        'visit_id',
        'rating',
        'q1',
        'q2',
        'q3',
        'q4',
        'q5',
        'q6',
        'q7',
        'q8',
        'q9',
        'comment',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(GuestVisit::class, 'visit_id');
    }
}
