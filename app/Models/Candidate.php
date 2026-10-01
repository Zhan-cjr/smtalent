<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vacancy_id',
        'name',
        'email',
        'phone',
        'access_token',
        'nik',
        'gender',
        'birth_date',
        'education',
        'address',
        'status',
        'final_psychotest_score',
        'final_interview_score',
        'final_score',
        'final_rank',
        'final_status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'final_psychotest_score' => 'decimal:2',
            'final_interview_score' => 'decimal:2',
            'final_score' => 'decimal:2',
            'final_rank' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function testAttempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function latestAttempt(): HasOne
    {
        return $this->hasOne(TestAttempt::class)->latestOfMany();
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function latestInterview(): HasOne
    {
        return $this->hasOne(Interview::class)->latestOfMany();
    }

    public function activeInterview(): HasOne
    {
        return $this->hasOne(Interview::class)->ofMany([
            'id' => 'max',
        ], function ($query) {
            $query->whereIn('status', ['scheduled', 'in_progress']);
        });
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? $this->user?->name ?? 'Kandidat';
    }

    public function getDisplayEmailAttribute(): string
    {
        return $this->email ?? $this->user?->email ?? '-';
    }
}
