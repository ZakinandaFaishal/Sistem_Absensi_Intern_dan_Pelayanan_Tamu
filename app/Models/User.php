<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nik',
        'phone',
        'username',
        'email',
        'password',
        'role',
        'dinas_id',
        'active',
        'intern_status',
        'internship_start_date',
        'internship_end_date',
        'internship_location_id',
        'score_override',
        'score_override_note',
        'final_evaluation',
        'final_evaluation_at',
        'certificate_signatory_name',
        'certificate_signatory_title',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
            'score_override' => 'integer',
            'internship_start_date' => 'date',
            'internship_end_date' => 'date',
            'final_evaluation' => 'array',
            'final_evaluation_at' => 'datetime',
        ];
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function dinas(): BelongsTo
    {
        return $this->belongsTo(Dinas::class);
    }

    public function internshipLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'internship_location_id');
    }

    public function isSuperAdmin(): bool
    {
        return ($this->role ?? null) === 'super_admin';
    }

    public function isAdminDinas(): bool
    {
        return ($this->role ?? null) === 'admin_dinas';
    }
}
